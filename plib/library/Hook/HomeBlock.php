<?php
/**
 * Home dashboard card: scan status at a glance, styled like Plesk's own cards
 * (SSL/TLS Certificates). Reads cached results only, never calls the VirusTotal API.
 *
 * Autoloaded as plib/library/Hook/HomeBlock.php → Modules_Stingyscan_Hook_HomeBlock
 */
require_once dirname(__DIR__) . '/VtDataDir.php';
require_once dirname(__DIR__) . '/VtSettings.php';
require_once dirname(__DIR__) . '/VtCache.php';
require_once dirname(__DIR__) . '/PleskDomains.php';

class Modules_Stingyscan_Hook_HomeBlock extends \Plesk\SDK\Hook\Home\Block
{
    private const MAX_LISTED = 4;

    public function getId(): string      { return 'vt-site-scanner'; }
    public function getName(): string    { return 'StingyScan'; }
    public function getTitle(): string   { return 'StingyScan'; }
    public function getSection(): string { return self::SECTION_SECURITY; }
    public function getColumn(): int     { return 1; }
    public function getOrder(): int      { return 2; } // right below SSL/TLS Certificates
    public function getIcon(): string    { return 'shield'; } // Plesk UI icon name, like the built-in cards
    public function getSkeletonSize(): int { return 3; }

    public function getContent(): string
    {
        $s = $this->stats();
        if ($s['total'] === 0) {
            return '<div style="padding:8px 0;color:#737373">No scan results yet. Open the scanner to add a VirusTotal API key.</div>';
        }

        $html = $this->row('Infected', $s['infected'], 'danger', 'infected')
              . $this->row('Suspicious', $s['suspicious'], 'warning', 'suspicious')
              . $this->row('Clean', $s['clean'], 'success', 'clean')
              . ($s['other'] > 0 ? $this->row('Not checked yet', $s['other'], 'inactive', 'pending') : '');

        if ($s['flagged']) {
            $html .= '<div style="margin-top:12px;padding-top:10px;border-top:1px solid #ececec">';
            foreach (array_slice($s['flagged'], 0, self::MAX_LISTED) as $f) {
                $dot = $f['status'] === 'infected' ? '#d02d4b' : '#f28d00';
                $html .= '<div style="display:flex;justify-content:space-between;align-items:center;padding:3px 16px 3px 0;font-size:13px">'
                       . '<span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">'
                       . '<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:' . $dot . ';margin-right:8px"></span>'
                       . htmlspecialchars($f['domain']) . '</span>'
                       . '<span style="color:#737373;margin-left:8px;white-space:nowrap">' . (int)$f['malicious'] . '/' . (int)$f['total'] . '</span>'
                       . '</div>';
            }
            if (count($s['flagged']) > self::MAX_LISTED) {
                $html .= '<div style="padding:3px 0;font-size:12px;color:#737373">+ ' . (count($s['flagged']) - self::MAX_LISTED) . ' more</div>';
            }
            $html .= '</div>';
        }

        if ($s['last']) {
            $html .= '<div style="margin-top:10px;font-size:12px;color:#737373">Last check: ' . date('M j, Y H:i', $s['last']) . '</div>';
        }
        return $html;
    }

    public function getFooter(): string
    {
        $s = $this->stats();
        $label = ($s['infected'] + $s['suspicious']) > 0 ? 'Review flagged sites' : 'Open StingyScan';
        return '<a href="' . htmlspecialchars(pm_Context::getBaseUrl()) . '">' . $label . '</a>';
    }

    // ── Helpers ─────────────────────────────────────────────────

    private function row(string $label, int $count, string $intent, string $filter): string
    {
        return '<div style="display:flex;justify-content:space-between;align-items:center;padding:8px 16px 0 0">'
             . '<span>' . $label . '</span>'
             . '<span class="pul-label pul-label--' . $intent . ' pul-label--caps"><span class="pul-label__content">' . $count . '</span></span>'
             . '</div>';
    }

    private ?array $stats = null;

    private function stats(): array
    {
        if ($this->stats !== null) return $this->stats;
        $s = ['total' => 0, 'infected' => 0, 'suspicious' => 0, 'clean' => 0, 'other' => 0, 'last' => 0, 'flagged' => []];
        try {
            $settings = new VtSettings('admin');
            $results  = (new VtCache($settings))->all();
            // Cached site list only: never run discovery while rendering the Home page
            $domains = PleskDomains::cachedDomains((bool)$settings->get('wordpress_only', false));
            if (is_array($domains) && $domains) {
                $results = array_intersect_key($results, array_flip($domains));
                $neverScanned = count(array_diff($domains, array_keys($results)));
                $s['other'] += $neverScanned;
                $s['total'] += $neverScanned;
            }
            foreach ($results as $domain => $r) {
                $st = $r['status'] ?? '';
                $s['total']++;
                if (in_array($st, ['infected', 'suspicious', 'clean'], true)) $s[$st]++; else $s['other']++;
                $s['last'] = max($s['last'], (int)($r['cached_at'] ?? 0));
                if ($st === 'infected' || $st === 'suspicious') {
                    $s['flagged'][] = ['domain' => $domain, 'status' => $st,
                                       'malicious' => $r['malicious'] ?? 0, 'total' => $r['total'] ?? 0];
                }
            }
            // Infected first, then by number of engines flagging it
            usort($s['flagged'], fn($a, $b) => [$b['status'] === 'infected', $b['malicious']] <=> [$a['status'] === 'infected', $a['malicious']]);
        } catch (\Throwable $e) {
            // Never break the Plesk Home page because of this card
        }
        return $this->stats = $s;
    }
}
