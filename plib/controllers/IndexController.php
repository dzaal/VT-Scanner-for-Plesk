<?php
// Load our non-autoloaded library classes
$_vtLib = '/usr/local/psa/admin/plib/modules/stingyscan/library';
if (!is_dir($_vtLib)) {
    $_vtLib = realpath(dirname(__FILE__) . '/../library');
}
require_once $_vtLib . '/VtDataDir.php';
require_once $_vtLib . '/VtSettings.php';
require_once $_vtLib . '/VtCache.php';
require_once $_vtLib . '/VtRateLimiter.php';
require_once $_vtLib . '/VtApi.php';
require_once $_vtLib . '/PleskDomains.php';
require_once $_vtLib . '/VtNotifier.php';
require_once $_vtLib . '/VtHistory.php';
require_once $_vtLib . '/VtManualQueue.php';
require_once $_vtLib . '/VtEvents.php';
require_once $_vtLib . '/VtReviews.php';
require_once $_vtLib . '/VtSiteLog.php';
require_once $_vtLib . '/VtAvastStore.php';
require_once $_vtLib . '/VtLogRotate.php';
require_once $_vtLib . '/VtSafeBrowsing.php';

/**
 * IndexController — Plesk extension controller (extends pm_Controller_Action for layout).
 */
class IndexController extends pm_Controller_Action
{
    /**
     * PayPal.Me username for the "Buy me a coffee" button on the About tab.
     * Leave empty to hide the button.
     */
    private const PAYPAL_ME = 'zirk020';
    private const COFFEE_PRICE = '3USD';

    /** @var VtSettings */
    private $settings;
    /** @var VtApi */
    private $api;
    /** @var VtCache */
    private $cache;
    /** Effective API key (own key, or admin's key as fallback) */
    private string $effectiveApiKey = '';
    /** True when using the admin's key as a fallback */
    private bool $usingAdminKey = false;

    public function init(): void
    {
        parent::init();
        $userId         = $this->resolveUserId();
        $this->settings = new VtSettings($userId);
        // Use own API key; fall back to admin's key if the user hasn't configured one
        $ownKey = $this->settings->get('api_key') ?: '';
        if ($ownKey) {
            $this->effectiveApiKey = $ownKey;
        } elseif ($userId !== 'admin') {
            $adminKey = (new VtSettings('admin'))->get('api_key') ?: '';
            if ($adminKey) {
                $this->effectiveApiKey = $adminKey;
                $this->usingAdminKey   = true;
            }
        }
        $this->api   = new VtApi($this->effectiveApiKey);
        $this->cache = new VtCache($this->settings);
        // Use .php view extension (not .phtml)
        $this->getHelper('ViewRenderer')->setViewSuffix('php');
    }

    private function resolveUserId(): string
    {
        try {
            $client = pm_Session::getClient();
            if ($client !== null) {
                return $client->getLogin();
            }
        } catch (\Throwable $e) {
            // pm_Session not available or admin context
        }
        return 'admin';
    }

    // ── Page ────────────────────────────────────────────────────

    public function indexAction(): void
    {
        // Browser tab title; Plesk's own page header is hidden by the view (branded header instead)
        $this->view->pageTitle = 'StingyScan';
        $this->view->baseUrl   = pm_Context::getBaseUrl();
        $version = $this->installedVersion();
        $this->view->version   = $version;
        // The old extension (VT Site Scanner 1.x) still installed next to StingyScan: both would scan
        $this->view->oldVtInstalled = $this->role() === 'admin' && basename(dirname(__DIR__)) !== 'virustotal-extension'
            && is_dir('/usr/local/psa/admin/plib/modules/virustotal-extension');
        $this->view->paypalUrl = self::PAYPAL_ME !== ''
            ? 'https://www.paypal.com/paypalme/' . rawurlencode(self::PAYPAL_ME) . '/' . self::COFFEE_PRICE
            : '';
        // All scan data is fetched via AJAX
    }

    // ── AJAX ────────────────────────────────────────────────────

    public function scanAction(): void
    {
        $domain = $this->sanitize($this->_getParam('domain', ''));
        if (!$domain) { $this->respond(['error' => 'invalid']); return; }
        if (!$this->mayCheck($domain)) {
            $this->respond(['error' => 'forbidden']); return;
        }

        $cached = $this->cache->get($domain);

        // force=1: bypass cache (used right after a manual rescan completes)
        $force = (bool)$this->_getParam('force', false);
        if ($cached && !$force && !$this->cache->isStale($domain)) {
            $this->respond($cached); return;
        }

        if (!$this->effectiveApiKey) {
            $this->respond(['status' => 'error', 'domain' => $domain, 'message' => 'No API key configured']); return;
        }

        $canSpend = $this->cache->autoBudgetRemaining() > 0
            || ($force && $this->cache->manualBudgetRemaining() > 0);
        if ($canSpend) {
            $result = $this->api->getReport($domain);
            // Don't cache or charge budget for auth errors (401) — bad API key
            $isAuthError = $result['status'] === 'error' && ($result['http_code'] ?? 0) === 401;
            if ($isAuthError) {
                $this->appendLog("[scan] error $domain — invalid API key (401)");
                $this->respond(['status' => 'error', 'domain' => $domain, 'message' => 'Invalid API key (401)']); return;
            }
            $status = $result['status'];
            if ($status === 'infected') {
                $score = isset($result['malicious'], $result['total']) ? " ({$result['malicious']}/{$result['total']})" : '';
                $vendors = !empty($result['flagged_by']) ? ' — ' . implode(', ', array_slice($result['flagged_by'], 0, 3)) : '';
                $this->appendLog("[scan] INFECTED $domain$score$vendors");
            } elseif ($status === 'suspicious') {
                $score = isset($result['malicious'], $result['total']) ? " ({$result['malicious']}/{$result['total']})" : '';
                $this->appendLog("[scan] suspicious $domain$score");
            } elseif ($status === 'clean') {
                $score = isset($result['total']) ? " (0/{$result['total']})" : '';
                $this->appendLog("[scan] clean $domain$score");
            } elseif ($status === 'error') {
                $code = $result['http_code'] ?? '?';
                $this->appendLog("[scan] error $domain HTTP $code");
            } elseif ($status === 'ratelimit') {
                $this->appendLog("[scan] ratelimit (per-minute) $domain");
            } elseif ($status === 'quota_exceeded') {
                $ra = $result['retry_after'] ?? null;
                $resets = $ra ? ' resets in ' . gmdate('H\hi', $ra) . ' UTC' : '';
                $this->appendLog("[scan] quota_exceeded$resets $domain");
            } else {
                $this->appendLog("[scan] $status $domain");
            }
            if ($result['status'] !== 'ratelimit' && $result['status'] !== 'quota_exceeded') {
                $this->cache->set($domain, $result, false);
                try {
                    $mailed = VtNotifier::flush(false); // summaries whose oldest change waited 10 min
                    if ($mailed) $this->appendLog('[mail] summary sent to ' . implode(', ', $mailed));
                } catch (\Throwable $e) {
                }
                $this->respond($result); return;
            }
            // Pass ratelimit/quota_exceeded through to frontend — do NOT cache
            $this->respond($result); return;
        }

        if ($cached) {
            $cached['stale'] = true;
            $this->respond($cached);
        } else {
            $this->respond(['status' => 'ratelimit', 'domain' => $domain, 'message' => 'VT rate limit — try again shortly']);
        }
    }

    public function reanalyzeAction(): void
    {
        $domain = $this->sanitize($this->_getParam('domain', ''));
        if (!$domain) { $this->respond(['error' => 'invalid']); return; }
        if (!$this->mayCheck($domain)) {
            $this->respond(['error' => 'forbidden']); return;
        }

        if ($this->cache->manualBudgetRemaining() <= 0) {
            $this->respond(['status' => 'budget_exceeded', 'domain' => $domain,
                            'message' => 'Daily limit for manual rescans reached']);
            return;
        }

        $result = $this->api->reanalyze($domain);
        if ($result['status'] === 'queued') {
            // Keep the previous result in cache (no "pending" overwrite) — the UI
            // fetches the fresh report with scan&force=1 once VT has finished.
            $this->cache->charge(true);
            $this->appendLog("[manual] Rescan started: $domain");
        } elseif ($result['status'] === 'ratelimit') {
            $this->appendLog("[manual] Rate-limit (per-minute) on rescan: $domain");
        } elseif ($result['status'] === 'quota_exceeded') {
            $this->appendLog("[manual] Quota exceeded on rescan: $domain");
        } else {
            $code = $result['http_code'] ?? '?';
            $this->appendLog("[manual] Error on rescan: $domain (HTTP $code)");
        }
        $this->respond($result);
    }

    public function pollAnalysisAction(): void
    {
        $id = $this->_getParam('id', '');
        // Analysis IDs look like: u-<hex>-<timestamp> or similar alphanumeric
        if (!$id || !preg_match('/^[a-zA-Z0-9_\-]+$/', $id)) {
            $this->respond(['error' => 'invalid']); return;
        }
        $this->respond($this->api->getAnalysisStatus($id));
    }

    public function domainsQuickAction(): void
    {
        // Fast path: just the domain list + budget — no results file, no history write.
        // Used by the JS init to render the domain table immediately while full data loads.
        $domains = PleskDomains::forCurrentUser($this->wpOnly());
        $budgetInfo = $this->cache->budgetInfo();
        $budgetInfo['vt_quota_used'] = VtRateLimiter::todayCountForKey($this->effectiveApiKey);
        $this->respond([
            'domains' => $domains,
            'count'   => count($domains),
            'budget'  => $budgetInfo,
        ]);
    }

    public function allAction(): void
    {
        $domains    = PleskDomains::forCurrentUser($this->wpOnly());
        $related    = PleskDomains::relatedForCurrentUser();
        $allResults = $this->cache->all();
        // Filter cached results to only domains this user has access to
        $results = array_intersect_key($allResults, array_flip(array_merge($domains, $related)));
        $budgetInfo = $this->cache->budgetInfo();
        $budgetInfo['vt_quota_used'] = VtRateLimiter::todayCountForKey($this->effectiveApiKey);
        // Today's totals for the trend graph: the whole server for the admin, otherwise the user's own sites
        VtHistory::scope($this->historyScope());
        VtHistory::record(array_map(fn($d) => (string)($allResults[$d]['status'] ?? 'unknown'), array_combine($domains, $domains)));
        $this->respond([
            'domains' => $domains,
            'related' => $related,
            'owners'  => $this->role() === 'client' ? [] : PleskDomains::owners(array_merge($domains, $related)),
            'orphans' => $this->role() === 'admin' ? self::orphanInfo($related) : [],
            'results' => $results,
            'budget'  => $budgetInfo,
            'scans'    => self::scanResults($domains),
            'scanners' => self::enabledScanners(),
            'reviews'  => VtReviews::forDomains(array_merge($domains, $related)),
            'history'  => array_values(array_intersect(VtSiteLog::domains(), array_merge($domains, $related))),
            'avast'    => self::avastInfo($domains),
            'gsb'      => VtSafeBrowsing::enabled() ? ['enabled' => true, 'fresh' => VtSafeBrowsing::FRESH, 'review' => VtSafeBrowsing::REVIEW_URL,
                                                       'results' => VtSafeBrowsing::results($domains)] : ['enabled' => false],
        ]);
    }

    /** Avast / AVG classification: the products switched on and the latest results for these sites */
    private static function avastInfo(array $domains): array
    {
        $ps = VtAvastStore::products();
        if (!$ps) return ['products' => []];
        return ['products' => array_map(fn($p) => ['id' => $p, 'name' => VtAvast::PRODUCTS[$p], 'form' => VtAvast::FP_FORMS[$p]], $ps),
                'results'  => VtAvastStore::results($domains)];
    }

    /** Live Google Safe Browsing check of one site: when its details are opened (reuses a result of the last 30 minutes) or after a rescan (force=1) */
    public function gsbLiveAction(): void
    {
        $domain = $this->sanitize($this->_getParam('domain', ''));
        if (!$domain || !$this->mayCheck($domain)) { $this->respond(['error' => 'forbidden']); return; }
        if (!VtSafeBrowsing::enabled()) { $this->respond(['error' => 'This check is switched off']); return; }
        $this->respond(['result' => VtSafeBrowsing::live($domain, (bool)$this->_getParam('force', false))]);
    }

    /** Live Avast / AVG check of one site: when its details are opened (reuses a result of the last 5 minutes) or after a rescan (force=1) */
    public function avastLiveAction(): void
    {
        $domain = $this->sanitize($this->_getParam('domain', ''));
        if (!$domain || !$this->mayCheck($domain)) { $this->respond(['error' => 'forbidden']); return; }
        $p = (int)$this->_getParam('product', 0);
        if (!in_array($p, VtAvastStore::products(), true)) { $this->respond(['error' => 'This check is switched off']); return; }
        $this->respond(['product' => $p, 'result' => VtAvastStore::live($domain, $p, (bool)$this->_getParam('force', false))]);
    }

    /** Remember (or forget, with remove=1) that a review was requested from a vendor for a site */
    public function reviewMarkAction(): void
    {
        if (!$this->getRequest()->isPost()) { $this->respond(['error' => 'POST required']); return; }
        $domain = $this->sanitize($this->_getParam('domain', ''));
        $vendor = trim(substr((string)$this->_getParam('vendor', ''), 0, 80));
        if (!$domain || $vendor === '') { $this->respond(['error' => 'invalid']); return; }
        if (!$this->mayCheck($domain)) { $this->respond(['error' => 'forbidden']); return; }
        $by     = $this->resolveUserId();
        $denied = (bool)$this->_getParam('denied', false); // the vendor turned it down: forget the request, log why
        $remove = $denied || (bool)$this->_getParam('remove', false);
        $reason = $denied ? trim(mb_substr((string)$this->_getParam('reason', ''), 0, 300)) : null;
        // The review target is shown as a link in the site history: only an http(s) address or an e-mail address
        $to = trim(substr((string)$this->_getParam('to', ''), 0, 300));
        if ($to !== '' && !filter_var($to, FILTER_VALIDATE_EMAIL)
            && !(preg_match('#^https?://#i', $to) && filter_var($to, FILTER_VALIDATE_URL))) $to = '';
        VtSiteLog::review($domain, $vendor, (string)$this->_getParam('via', 'form'), $to, $by, $remove, $reason);
        $this->respond(['ok' => true, 'reviews' => VtReviews::mark($domain, $vendor, $by, $remove)]);
    }

    /** History of one site: detections, vendors that started or stopped flagging it, status changes, review requests */
    public function siteHistoryAction(): void
    {
        $domain = $this->sanitize($this->_getParam('domain', ''));
        if (!$domain || !$this->mayCheck($domain)) { $this->respond(['error' => 'forbidden']); return; }
        // Once: the first detections from before the history existed, found in the scan logs
        $marker = VtDataDir::path('vt_sitelog_backfilled');
        if (!file_exists($marker) && @touch($marker)) VtSiteLog::backfillFromLogs();
        $this->respond(['domain' => $domain, 'history' => VtSiteLog::history($domain)]);
    }

    // ── File scanners (ClamAV, maldet, …), on demand ───────────

    /** All known scanners with 'installed' (asked from the root helper, cached for an hour) */
    private static function scanners(bool $fresh = false): array
    {
        $cache = VtDataDir::path('vt_scanners.json');
        if (!$fresh && is_file($cache) && time() - filemtime($cache) < 3600) {
            $d = json_decode((string)file_get_contents($cache), true);
            if (is_array($d) && (!$d || isset($d[0]['installed']))) return $d;
        }
        try {
            $res  = pm_ApiCli::callSbin('site-scan', ['list'], pm_ApiCli::RESULT_FULL);
            $list = json_decode(trim((string)($res['stdout'] ?? '')), true)['scanners'] ?? null;
        } catch (\Throwable $e) {
            $list = null;
        }
        if (!is_array($list)) return [];
        @file_put_contents($cache, json_encode($list));
        return $list;
    }

    /** Scanners switched off by the administrator (server-wide) */
    private static function disabledScanners(): array
    {
        $d = (new VtSettings('admin'))->get('scanners_disabled', []);
        return is_array($d) ? $d : [];
    }

    /** Scanners that are installed and switched on: these get a button on every site */
    private static function enabledScanners(): array
    {
        $off = array_flip(self::disabledScanners());
        return array_values(array_filter(self::scanners(), fn($s) => !empty($s['installed']) && !isset($off[$s['id']])));
    }

    /** All scanners with installed/enabled state, for the settings page */
    private static function scannerSettings(bool $fresh = false): array
    {
        $off = array_flip(self::disabledScanners());
        return array_map(fn($s) => $s + ['enabled' => !isset($off[$s['id']])], self::scanners($fresh));
    }

    /** Detect the installed scanners again (after installing or removing one) */
    public function detectScannersAction(): void
    {
        if (!$this->getRequest()->isPost()) { $this->respond(['error' => 'POST required']); return; }
        $this->respond(['scanners' => self::scannerSettings(true)]);
    }

    /** Switch a scanner on or off for the whole server (administrator only) */
    public function saveScannerAction(): void
    {
        if (!$this->getRequest()->isPost()) { $this->respond(['error' => 'POST required']); return; }
        if ($this->role() !== 'admin') { $this->respond(['error' => 'Only the administrator can change the scanners']); return; }
        $id = (string)$this->_getParam('scanner', '');
        if (!in_array($id, array_column(self::scanners(), 'id'), true)) { $this->respond(['error' => 'Unknown scanner']); return; }
        $on  = filter_var($this->_getParam('enabled', ''), FILTER_VALIDATE_BOOLEAN);
        $off = array_values(array_diff(self::disabledScanners(), [$id]));
        if (!$on) $off[] = $id;
        (new VtSettings('admin'))->setAll(['scanners_disabled' => $off]);
        $this->appendLog('[files] ' . $id . ' scanner ' . ($on ? 'switched on' : 'switched off'));
        $this->respond(['ok' => true, 'scanners' => self::scannerSettings()]);
    }

    private function scanParams(): ?array
    {
        $domain  = $this->sanitize($this->_getParam('domain', ''));
        $scanner = (string)$this->_getParam('scanner', '');
        if (!$domain || !preg_match('/^[a-z0-9]+$/', $scanner)) { $this->respond(['error' => 'invalid']); return null; }
        if (!in_array($scanner, array_column(self::enabledScanners(), 'id'), true)) {
            $this->respond(['error' => 'This scanner is switched off or not installed']); return null;
        }
        if (!in_array($domain, PleskDomains::forCurrentUser($this->wpOnly()), true)) {
            $this->respond(['error' => 'forbidden']); return null;
        }
        return [$domain, $scanner];
    }

    /** Start a file scan of one site (runs in the background as root) */
    public function fileScanAction(): void
    {
        if (!$this->getRequest()->isPost()) { $this->respond(['error' => 'POST required']); return; }
        if (!($p = $this->scanParams())) return;
        [$domain, $scanner] = $p;
        try {
            $res = pm_ApiCli::callSbin('site-scan', ['start', $scanner, $domain], pm_ApiCli::RESULT_FULL);
            $out = json_decode(trim((string)($res['stdout'] ?? '')), true);
        } catch (\Throwable $e) {
            $out = ['error' => 'Could not start the scan: ' . $e->getMessage()];
        }
        if (!is_array($out)) $out = ['error' => 'Could not start the scan'];
        if (!empty($out['started']) && empty($out['already'])) $this->appendLog("[files] {$out['name']} scan started: $domain");
        $this->respond($out + ['result' => self::scanResult($scanner, $domain)]);
    }

    /** Current file-scan result of one site and scanner */
    public function fileResultAction(): void
    {
        if (!($p = $this->scanParams())) return;
        [$domain, $scanner] = $p;
        $r = self::scanResult($scanner, $domain);
        if ($r && !in_array($r['status'] ?? '', ['running', 'queued'], true) && empty($r['logged'])) {
            // Log a finished scan once
            $this->appendLog(sprintf('[files] %s %s: %s', $r['name'] ?? $scanner, $domain, ($r['status'] ?? '') === 'done'
                ? (($r['infected'] ?? 0) ? $r['infected'] . ' infected file(s)' : 'no infected files') . ' in ' . ($r['files'] ?? 0) . ' files'
                : ($r['message'] ?? $r['status'] ?? 'error')));
            $r['logged'] = true;
            @file_put_contents(self::scanFile($scanner, $domain), json_encode($r, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        $this->respond(['result' => $r]);
    }

    private static function scanFile(string $scanner, string $domain): string
    {
        return VtDataDir::path('vt_scan_' . $scanner . '_' . preg_replace('/[^a-z0-9]/', '_', $domain) . '.json');
    }

    private static function scanResult(string $scanner, string $domain): ?array
    {
        $d = json_decode((string)@file_get_contents(self::scanFile($scanner, $domain)), true);
        if (!is_array($d)) return null;
        // A scan that has said "running" or "queued" for hours has died
        if (in_array($d['status'] ?? '', ['running', 'queued'], true) && time() - (int)($d['started'] ?? 0) > 4200 * 3) {
            $d['status'] = 'error'; $d['message'] = 'The scan stopped unexpectedly';
        }
        return $d;
    }

    /** File-scan results of the given domains (without file lists): [domain => [scanner => result]] */
    private static function scanResults(array $domains): array
    {
        $want = array_flip($domains);
        $on   = array_flip(array_column(self::enabledScanners(), 'id'));
        $out  = [];
        foreach (glob(VtDataDir::path('vt_scan_*.json')) ?: [] as $f) {
            $d = json_decode((string)@file_get_contents($f), true);
            if (!is_array($d) || !isset($d['domain'], $d['scanner'], $want[$d['domain']], $on[$d['scanner']])) continue;
            $r = self::scanResult($d['scanner'], $d['domain']);
            unset($r['found']);
            $out[$d['domain']][$d['scanner']] = $r;
        }
        return $out;
    }

    // ── Manual rescan server queue ───────────────────────────────

    /** Push a domain into the persistent manual rescan queue (called on Rescan click). */
    public function enqueueRescanAction(): void
    {
        if (!$this->getRequest()->isPost()) { $this->respond(['error' => 'POST required']); return; }
        $domain = $this->sanitize($this->_getParam('domain', ''));
        if (!$domain) { $this->respond(['error' => 'invalid']); return; }
        if (!$this->mayCheck($domain)) { $this->respond(['error' => 'forbidden']); return; }
        VtManualQueue::enqueue([$domain]);
        $this->respond(['ok' => true]);
    }

    /** Remove a domain from the queue (browser completed it before cron could). */
    public function dequeueRescanAction(): void
    {
        if (!$this->getRequest()->isPost()) { $this->respond(['error' => 'POST required']); return; }
        $domain = $this->sanitize($this->_getParam('domain', ''));
        if (!$domain) { $this->respond(['error' => 'invalid']); return; }
        if (!$this->mayCheck($domain)) { $this->respond(['error' => 'forbidden']); return; }
        VtManualQueue::dequeue([$domain]);
        $this->respond(['ok' => true]);
    }

    /** Heartbeat: this page still has these rescans (running or waiting), so cron leaves them alone */
    public function rescanAliveAction(): void
    {
        if (!$this->getRequest()->isPost()) { $this->respond(['error' => 'POST required']); return; }
        $list = array_slice(array_filter(array_map([$this, 'sanitize'], explode(',', (string)$this->_getParam('domains', '')))), 0, 500);
        VtManualQueue::touch(array_values(array_filter($list, fn($d) => $this->mayCheck($d))));
        $this->respond(['ok' => true]);
    }

    /** Current state of the server-side manual queue (filtered to this user's domains). */
    public function queueStatusAction(): void
    {
        $q    = VtManualQueue::all();
        $mine = array_flip(PleskDomains::forCurrentUser($this->wpOnly()));
        $pend = array_values(array_filter($q['pending']   ?? [], fn($d) => isset($mine[$d])));
        $done = array_values(array_filter($q['completed'] ?? [], fn($d) => isset($mine[$d])));
        $this->respond([
            'pending'      => $pend,
            'queued_at'    => array_intersect_key($q['queued_at']    ?? [], array_flip($pend)),
            'completed'    => $done,
            'completed_at' => array_intersect_key($q['completed_at'] ?? [], array_flip($done)),
        ]);
    }

    public function refreshDomainsAction(): void
    {
        PleskDomains::invalidateCache();
        $this->respond(['domains' => PleskDomains::forCurrentUser($this->wpOnly())]);
    }

    public function budgetAction(): void
    {
        $info = $this->cache->budgetInfo();
        $info['vt_quota_used'] = VtRateLimiter::todayCountForKey($this->effectiveApiKey);
        $this->respond($info);
    }

    public function installUpdateAction(): void
    {
        if (!$this->getRequest()->isPost()) { $this->respond(['error' => 'POST required']); return; }
        if ($this->role() !== 'admin') { $this->respond(['error' => 'Administrator only']); return; }

        try {
            $res = pm_ApiCli::callSbin('extension-update', ['start', (string)$this->_getParam('version', '')], pm_ApiCli::RESULT_FULL);
            $data = json_decode(trim((string)($res['stdout'] ?? '')), true);
            $this->respond(is_array($data) ? $data : ['error' => 'Could not start the privileged updater']);
        } catch (\Throwable $e) {
            $this->respond(['error' => $e->getMessage()]);
        }
    }

    public function updateStatusAction(): void
    {
        if ($this->role() !== 'admin') { $this->respond(['error' => 'Administrator only']); return; }
        try {
            $res = pm_ApiCli::callSbin('extension-update', ['status'], pm_ApiCli::RESULT_FULL);
            $data = json_decode(trim((string)($res['stdout'] ?? '')), true);
            $this->respond(is_array($data) ? $data : ['error' => 'Cannot read update status']);
        } catch (\Throwable $e) {
            $this->respond(['error' => $e->getMessage()]);
        }
    }

    public function checkUpdateAction(): void
    {
        // Current version is always read fresh — never cached — so it updates immediately after an upgrade
        $current = $this->installedVersion();

        // Only the GitHub API response is cached (avoid hitting the API on every page load)
        $cacheFile = VtDataDir::path('vt_update_check.json');
        $github    = null;
        if (is_file($cacheFile) && time() - filemtime($cacheFile) < 900) {
            $d = json_decode((string)file_get_contents($cacheFile), true);
            if (is_array($d) && isset($d['latest'])) $github = $d;
        }

        if (!$github) {
            $ch = curl_init('https://api.github.com/repos/dzaal/StingyScan-for-Plesk/releases/latest');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_HTTPHEADER     => ['User-Agent: StingyScan-Plesk/' . $current],
                CURLOPT_FOLLOWLOCATION => true,
            ]);
            $raw  = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code !== 200 || !$raw) {
                $this->respond(['error' => 'Could not reach GitHub', 'current' => $current]); return;
            }
            $data   = json_decode($raw, true);
            $latest = ltrim((string)($data['tag_name'] ?? ''), 'v');
            $url    = (string)($data['html_url'] ?? '');
            // Stable URL — works for every release as long as the asset is named
            // stingyscan.zip (without version number).
            $zip = 'https://github.com/dzaal/StingyScan-for-Plesk/releases/latest/download/stingyscan.zip';
            $github = ['latest' => $latest, 'url' => $url, 'zip' => $zip];
            @file_put_contents($cacheFile, json_encode($github));
        }

        $this->respond([
            'current'          => $current,
            'latest'           => $github['latest'],
            'url'              => $github['url'],
            'zip'              => $github['zip'],
            'update_available' => $github['latest'] && $current
                                  && version_compare($github['latest'], $current, '>'),
        ]);
    }

    public function getLogAction(): void
    {
        $logFile = $this->userLogFile();
        if (!file_exists($logFile)) {
            $this->respond(['log' => '', 'size' => 0]);
            return;
        }
        // Return last 200 lines to avoid huge payloads
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $lines = array_slice($lines, -200);
        $this->respond(['log' => implode("\n", array_reverse($lines)), 'size' => filesize($logFile)]);
    }

    public function clearLogAction(): void
    {
        if (!$this->getRequest()->isPost() && !$this->_getParam('confirm')) {
            $this->respond(['error' => 'POST required']); return;
        }
        $logFile = $this->userLogFile();
        if (file_exists($logFile)) file_put_contents($logFile, '');
        $this->respond(['ok' => true]);
    }

    public function getSettingsAction(): void
    {
        $s   = $this->settings->all();
        $ownKey = $s['api_key'] ?? '';
        $s['api_key_readable'] = $ownKey;
        $s['role']             = $this->role();
        $s['my_email']         = VtNotifier::emailOfLogin($this->settings->getUserId()) ?? '';
        $s['has_key']          = !empty($ownKey) || $this->usingAdminKey;
        $s['scanners']         = self::scannerSettings();
        $s['avast_products']   = VtAvastStore::products();
        $s['avast_calls']      = VtAvast::callsToday();
        if ($this->role() === 'admin') $s['log_rotation'] = VtLogRotate::settings() + VtLogRotate::info();
        unset($s['gsb_api_key']); // the Google key never goes to the browser
        if ($this->role() === 'admin') {
            $s['gsb'] = ['enabled' => (bool)(new VtSettings('admin'))->get('gsb_enabled', false),
                         'key' => VtSafeBrowsing::maskedKey(), 'calls' => VtSafeBrowsing::callsToday(), 'last' => VtSafeBrowsing::lastStatus()];
        }
        $s['check_rates']      = VtCheckRateLimiter::rates();
        unset($s['scanners_disabled']);
        $s['using_admin_key']  = false; // Never expose admin key fallback to non-admin users
        // Quick auth check using the effective key (own or admin fallback)
        if ($this->effectiveApiKey) {
            $testResult = $this->api->getReport('virustotal.com');
            $s['api_key_invalid'] = ($testResult['status'] === 'error' && ($testResult['http_code'] ?? 0) === 401);
        } else {
            $s['api_key_invalid'] = false;
        }
        $this->respond($s);
    }

    public function saveSettingsAction(): void
    {
        if (!$this->getRequest()->isPost()) {
            $this->respond(['error' => 'POST required']); return;
        }
        // Try JSON body first, fall back to form-encoded POST
        $raw = $this->getRequest()->getRawBody();
        $input = ($raw && $raw[0] === '{') ? (json_decode($raw, true) ?? []) : $this->getRequest()->getPost();

        $allowed = ['api_key', 'wordpress_only', 'notify_enabled', 'notify_events', 'notify_email', 'daily_limit', 'auto_scan_share', 'clean_ttl_days', 'infected_ttl_days'];
        $save    = [];
        foreach ($allowed as $k) {
            if (!isset($input[$k])) continue;
            if ($k === 'api_key') {
                $v = trim((string)$input[$k]);
                if ($v === '') continue;
                $save[$k] = $v;
            } elseif ($k === 'notify_events') {
                $list = array_filter(array_map('trim', explode(',', (string)$input[$k])));
                $save[$k] = array_values(array_intersect($list, VtNotifier::EVENTS));
            } elseif ($k === 'notify_email') {
                $v = trim((string)$input[$k]);
                if ($v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
                    $this->respond(['error' => 'Please enter a valid e-mail address']); return;
                }
                $save[$k] = $v;
            } elseif ($k === 'wordpress_only' || $k === 'notify_enabled') {
                $save[$k] = filter_var($input[$k], FILTER_VALIDATE_BOOLEAN);
            } elseif (in_array($k, ['daily_limit', 'clean_ttl_days', 'infected_ttl_days'])) {
                $save[$k] = max(1, (int)$input[$k]);
            } elseif ($k === 'auto_scan_share') {
                $save[$k] = max(0.1, min(0.95, (float)$input[$k]));
            }
        }

        if (isset($input['check_rates'])) {
            if ($this->role() !== 'admin') { $this->respond(['error' => 'Only the administrator can change check rates']); return; }
            $rates = json_decode((string)$input['check_rates'], true);
            if (!is_array($rates)) { $this->respond(['error' => 'Invalid check rates']); return; }
            $valid = [];
            foreach ($rates as $source => $value) {
                if (!in_array($source, VtCheckRateLimiter::SOURCES, true) || !is_int($value) || $value < 1 || $value > 5) {
                    $this->respond(['error' => 'Each request limit must be a whole number from 1 to 5 per minute']); return;
                }
                $valid[$source] = (int)$value;
            }
            $save['check_rates'] = array_replace(VtCheckRateLimiter::rates(), $valid);
        }

        // Avast / AVG classification: one server-wide setting of the administrator
        if (isset($input['avast_products']) && $this->role() === 'admin') {
            $ps = array_values(array_unique(array_filter(array_map('intval', explode(',', (string)$input['avast_products'])), fn($p) => isset(VtAvast::PRODUCTS[$p]))));
            sort($ps);
            if ($this->settings->getUserId() === 'admin') $save['avast_products'] = $ps; // same file: saved below
            else (new VtSettings('admin'))->set('avast_products', $ps);
        }
        // Google Safe Browsing: server-wide settings of the administrator; a new key is tested first
        $gsbMsg = null;
        if ($this->role() === 'admin' && (isset($input['gsb_enabled']) || isset($input['gsb_api_key']))) {
            $gsb = [];
            if (isset($input['gsb_enabled'])) $gsb['gsb_enabled'] = filter_var($input['gsb_enabled'], FILTER_VALIDATE_BOOLEAN);
            $newKey = trim((string)($input['gsb_api_key'] ?? ''));
            if ($newKey !== '') {
                if (!preg_match('/^[A-Za-z0-9_\-]{20,100}$/', $newKey)) { $this->respond(['error' => 'That does not look like a Google API key']); return; }
                [$ok, $gsbMsg] = VtSafeBrowsing::testKey($newKey);
                if (!$ok) { $this->respond(['error' => 'Google Safe Browsing: ' . $gsbMsg]); return; }
                $gsb['gsb_api_key'] = $newKey;
            }
            if ($this->settings->getUserId() === 'admin') $save += $gsb; // same file: saved below
            else (new VtSettings('admin'))->setAll($gsb);
        }

        // Log rotation: server-wide settings of the administrator
        if ($this->role() === 'admin') {
            $logCfg = [];
            foreach (array_keys(VtLogRotate::DEFAULTS) as $k) {
                if (isset($input[$k])) $logCfg[$k] = VtLogRotate::clamp($k, $input[$k]);
            }
            if ($logCfg) {
                if ($this->settings->getUserId() === 'admin') $save += $logCfg; // same file: saved below
                else (new VtSettings('admin'))->setAll($logCfg);
            }
        }
        if (isset($save['check_rates']) && $this->settings->getUserId() !== 'admin') {
            (new VtSettings('admin'))->set('check_rates', $save['check_rates']);
            unset($save['check_rates']);
        }
        $save['login'] = $this->settings->getUserId(); // lets the notifier find this user's address
        $this->settings->setAll($save);
        // Clear cached error results when API key changes so they get rescanned
        if (isset($save['api_key'])) {
            $this->cache->clearErrors();
        }
        $this->respond(['ok' => true, 'saved' => array_values(array_diff(array_keys($save), ['gsb_api_key'])), 'gsb_message' => $gsbMsg]);
    }

    // ── Helpers ─────────────────────────────────────────────────

    /** Send a test notification to the current user's own e-mail address */
    public function testNotifyAction(): void
    {
        if (!$this->getRequest()->isPost()) {
            $this->respond(['error' => 'POST required']); return;
        }
        $login = $this->settings->getUserId();
        $to    = trim((string)$this->getRequest()->getPost('email', ''));
        if ($to === '') $to = (string)($this->settings->get('notify_email') ?: VtNotifier::emailOfLogin($login));
        $sent  = VtNotifier::sendTest($login, $to);
        $this->respond($sent ? ['ok' => true, 'email' => $sent]
                             : ['error' => 'No valid e-mail address found for your Plesk account']);
    }

    /** Package version on disk, without depending on panel metadata caches. */
    private function installedVersion(): string
    {
        $file = dirname(__DIR__) . '/meta.xml';
        if (is_file($file)) {
            $meta = @simplexml_load_file($file);
            if ($meta && isset($meta->version)) return (string)$meta->version;
        }
        try { return (string)(pm_Context::getModuleInfo()->version ?? ''); }
        catch (\Throwable $e) { return ''; }
    }

    /** admin, reseller or client */
    private function role(): string
    {
        try {
            $c = pm_Session::getClient();
            if ($c->isAdmin()) return 'admin';
            if ($c->isReseller()) return 'reseller';
            return 'client';
        } catch (\Throwable $e) {
            return 'admin';
        }
    }

    // ── Orphan databases (related hostnames), administrator only ──

    /** Database and table prefix of every WordPress install on disk (root helper, cached for an hour) */
    private static function wpConfigs(): ?array
    {
        $cache = VtDataDir::path('vt_wpconfigs.json');
        if (is_file($cache) && time() - filemtime($cache) < 3600) {
            $d = json_decode((string)file_get_contents($cache), true);
            if (is_array($d)) return $d;
        }
        try {
            $res  = pm_ApiCli::callSbin('site-scan', ['wpconfigs'], pm_ApiCli::RESULT_FULL);
            $list = json_decode(trim((string)($res['stdout'] ?? '')), true)['configs'] ?? null;
        } catch (\Throwable $e) {
            $list = null;
        }
        if (!is_array($list)) return null;
        @file_put_contents($cache, json_encode($list));
        return $list;
    }

    /**
     * Per related hostname: the database (or tables) it comes from, their size, and whether
     * a live site still uses them. kind: 'database' = no install uses this database,
     * 'tables' = old tables inside a database a live site uses, 'inuse' = an install uses exactly these.
     */
    private static function orphanInfo(array $related): array
    {
        if (!$related) return [];
        $want    = array_flip($related);
        $configs = self::wpConfigs();
        $dbs     = PleskDomains::pleskDatabases();
        $out     = [];
        foreach (PleskDomains::wordPressSources() ?? [] as $s) {
            if (!isset($want[$s['host']])) continue;
            $usedBy = []; $same = false;
            foreach ($configs ?? [] as $c) {
                if ($c['db'] !== $s['db']) continue;
                $usedBy[] = $c['path'];
                if ($c['prefix'] === $s['prefix']) $same = true;
            }
            $p  = $dbs[$s['db']] ?? null;
            $ws = $p['webspace'] ?? 0;
            $out[$s['host']][] = [
                'db' => $s['db'], 'prefix' => $s['prefix'], 'tables' => $s['tables'], 'size' => $s['size'],
                'kind'      => $configs === null ? 'unknown' : ($same ? 'inuse' : ($usedBy ? 'tables' : 'database')),
                'used_by'   => array_values(array_unique($usedBy)),
                'webspace'  => $p['webspace_name'] ?? '',
                'in_plesk'  => (bool)$p,
                'plesk_url' => $ws ? '/admin/subscription/login/id/' . $ws . '?pageUrl=/smb/database/list/domainId/' . $ws : '',
            ];
        }
        return $out;
    }

    /** Back up an orphan database (or its old tables) to the subscription's private folder. Deletes nothing. */
    public function dumpDbAction(): void
    {
        if (!$this->getRequest()->isPost()) { $this->respond(['error' => 'POST required']); return; }
        if ($this->role() !== 'admin') { $this->respond(['error' => 'Only the administrator can back up databases']); return; }
        $host = $this->sanitize($this->_getParam('domain', ''));
        $db   = (string)$this->_getParam('db', '');
        $item = null;
        foreach (self::orphanInfo(PleskDomains::relatedForCurrentUser())[$host] ?? [] as $o) {
            if ($o['db'] === $db) $item = $o;
        }
        if (!$item || !in_array($item['kind'], ['database', 'tables'], true)) { $this->respond(['error' => 'Not an orphan database']); return; }
        // Only the old tables when a live site shares the database, never the live ones
        $args = ['dump', $item['db'], $item['kind'] === 'tables' ? $item['prefix'] : '-', $host];
        try {
            $res = pm_ApiCli::callSbin('site-scan', $args, pm_ApiCli::RESULT_FULL);
            $out = json_decode(trim((string)($res['stdout'] ?? '')), true);
        } catch (\Throwable $e) {
            $out = ['error' => 'The backup failed: ' . $e->getMessage()];
        }
        if (!is_array($out)) $out = ['error' => 'The backup failed'];
        if (!empty($out['ok'])) $this->appendLog('[db] Backup of ' . $item['db'] . ($item['kind'] === 'tables' ? ' (tables ' . $item['prefix'] . '*)' : '') . " for $host: " . $out['file']);
        $this->respond($out);
    }

    /** Which trend history this user sees: the server (admin), or their own sites (reseller, customer) */
    private function historyScope(): string
    {
        $role = $this->role();
        if ($role === 'admin') return 'admin';
        return ($role === 'reseller' ? 'r:' : 'c:') . $this->settings->getUserId();
    }

    /** Daily totals for the trend graph: the whole server (administrator) or the user's own sites */
    public function historyAction(): void
    {
        VtHistory::scope($this->historyScope());
        $this->respond(['days' => VtHistory::days(92), 'scope' => $this->role() === 'admin' ? 'server' : 'own']);
    }

    /** Recent per-domain status changes for the events feed in the Trend tab */
    public function eventsAction(): void
    {
        // Once: add the status changes from before the feed existed, found in the scan logs
        $marker = VtDataDir::path('vt_events_backfilled');
        if (!file_exists($marker) && @touch($marker)) VtEvents::backfillFromLogs();
        VtAvastStore::recordCachedObservations();
        $recent = VtSiteLog::recentChanges();
        if ($this->role() === 'admin') { // the whole server, including sites that have left the list
            $this->respond(['events' => array_slice($recent, 0, 200)]);
            return;
        }
        $allowed = array_flip(array_merge(
            PleskDomains::forCurrentUser($this->wpOnly()),
            PleskDomains::relatedForCurrentUser()
        ));
        $events = array_values(array_filter(
            $recent,
            fn($e) => isset($allowed[$e['domain'] ?? ''])
        ));
        $this->respond(['events' => array_slice($events, 0, 200)]);
    }

    /** A site of this user, or a related hostname under one of the user's domains */
    private function mayCheck(string $domain): bool
    {
        return in_array($domain, PleskDomains::forCurrentUser($this->wpOnly()), true)
            || in_array($domain, PleskDomains::relatedForCurrentUser(), true);
    }

    /** Scan WordPress sites only, instead of every hosted website */
    private function wpOnly(): bool
    {
        return (bool)$this->settings->get('wordpress_only', false);
    }

    private function userLogFile(): string
    {
        $uid = preg_replace('/[^a-z0-9_\-]/i', '_', $this->settings->getUserId());
        return VtDataDir::path('cron_' . $uid . '.log');
    }

    private function appendLog(string $msg): void
    {
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
        @file_put_contents($this->userLogFile(), $line, FILE_APPEND);
    }

    private function sanitize(string $d): string
    {
        $d = preg_replace('#^https?://(www\.)?#', '', strtolower(trim($d)));
        $d = rtrim($d, '/');
        return preg_match('/^[a-z0-9.\-]+\.[a-z]{2,}$/', $d) ? $d : '';
    }

    private function respond(array $data): void
    {
        // Disable layout and view rendering, then emit JSON directly
        // Note: use property access (->layout, ->viewRenderer) not method calls
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($data));
    }
}
