<?php
// Load our non-autoloaded library classes
$_vtLib = '/usr/local/psa/admin/plib/modules/virustotal-extension/library';
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

/**
 * IndexController — Plesk extension controller (extends pm_Controller_Action for layout).
 */
class IndexController extends pm_Controller_Action
{
    /**
     * PayPal.Me username for the "Buy me a coffee" button on the About tab.
     * Leave empty to hide the button.
     */
    private const PAYPAL_ME = 'digizaal';
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
        $this->view->pageTitle = 'VT Site Scanner';
        $this->view->baseUrl   = pm_Context::getBaseUrl();
        $version = '';
        try {
            $info = pm_Context::getModuleInfo();
            $version = (string)($info->version ?? '');
        } catch (\Throwable $e) {
        }
        $this->view->version   = $version;
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
        if (!in_array($domain, PleskDomains::forCurrentUser($this->wpOnly()), true)) {
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
        if (!in_array($domain, PleskDomains::forCurrentUser($this->wpOnly()), true)) {
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

    public function allAction(): void
    {
        $domains    = PleskDomains::forCurrentUser($this->wpOnly());
        $allResults = $this->cache->all();
        // Filter cached results to only domains this user has access to
        $results = array_intersect_key($allResults, array_flip($domains));
        $budgetInfo = $this->cache->budgetInfo();
        $budgetInfo['vt_quota_used'] = VtRateLimiter::todayCountForKey($this->effectiveApiKey);
        $this->respond([
            'domains' => $domains,
            'results' => $results,
            'budget'  => $budgetInfo,
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

        $save['login'] = $this->settings->getUserId(); // lets the notifier find this user's address
        $this->settings->setAll($save);
        // Clear cached error results when API key changes so they get rescanned
        if (isset($save['api_key'])) {
            $this->cache->clearErrors();
        }
        $this->respond(['ok' => true, 'saved' => array_keys($save)]);
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
