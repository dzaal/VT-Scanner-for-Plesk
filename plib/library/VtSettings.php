<?php
require_once __DIR__ . '/VtDataDir.php';

/**
 * VtSettings — persistent settings stored in extension data dir.
 *
 * Settings:
 *   api_key          string   VirusTotal API key
 *   daily_limit      int      Max API calls per day (auto-detected or manual)
 *   auto_scan_share  float    Fraction of daily_limit reserved for background scan (default 0.8)
 *                             Remaining fraction is kept for manual Reanalyze buttons.
 *   clean_ttl_days   int      Days before re-checking a clean site (default 30)
 *   infected_ttl_days int     Days before re-checking an infected site (default 1)
 *   wordpress_only   bool     Scan WordPress sites only instead of every hosted website
 *   notify_enabled   bool     E-mail when a site's status changes
 *   notify_events    string[] Which new statuses to report: infected, suspicious, clean
 *   notify_email     string   Address for the summary e-mail (empty = the user's Plesk address)
 *   login            string   Plesk login these settings belong to (for notifications)
 *   avast_products   int[]    Avast / AVG classification: 146 = Avast, 147 = AVG, either or both (administrator, server-wide)
 */
class VtSettings
{
    private string $userId;

    /** File per user: vt_settings_admin.json, vt_settings_reseller1.json, … */
    public static function settingsFile(string $userId = 'admin'): string
    {
        return VtDataDir::path('vt_settings_' . preg_replace('/[^a-z0-9_\-]/i', '_', $userId) . '.json');
    }

    // VirusTotal free API limits
    const VT_FREE_DAILY   = 500;
    const VT_FREE_PER_MIN = 4;

    // VirusTotal premium API limits (approximate)
    const VT_PREMIUM_DAILY   = 100000;
    const VT_PREMIUM_PER_MIN = 1000;

    private static array $defaults = [
        'api_key'            => '',
        'wordpress_only'     => false, // false = every hosted website, true = WordPress sites only
        'notify_enabled'     => false,
        'notify_events'      => ['infected', 'suspicious'],
        'notify_email'       => '',
        'login'              => '',
        'daily_limit'        => 500,   // set to 0 for auto-detect via VT quota endpoint
        'auto_scan_share'    => 0.8,   // 80% for background, 20% for manual
        'clean_ttl_days'     => 30,
        'infected_ttl_days'  => 1,
        'tier'               => 'free', // 'free' | 'premium' | 'custom'
        'check_rates'        => [],     // per optional provider/scanner, 1–5 requests per minute
        'scanners_disabled'  => [],     // file scanners switched off (admin settings only, server-wide)
        'avast_product'      => 0,      // old single choice (before 1.29.0), read once for avast_products
        'log_max_mb'         => 5,      // log rotation (admin only, server-wide): rotate a log larger than this
        'log_keep'           => 5,      // old copies kept (gzipped)
        'log_max_days'       => 90,     // old copies deleted after this many days
        'gsb_enabled'        => false,  // Google Safe Browsing (admin only, server-wide)
        'gsb_api_key'        => '',     // the administrator's Google API key; never sent to the browser
        'avast_products'     => null,   // Avast / AVG classification: [] = off, [146] Avast, [147] AVG, or both (admin only, server-wide)
    ];

    private array $data;

    public function __construct(string $userId = 'admin')
    {
        $this->userId = $userId;
        VtDataDir::ensureExists();
        $this->data = array_merge(self::$defaults, $this->load());
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
        $this->save();
    }

    public function setAll(array $values): void
    {
        foreach ($values as $k => $v) {
            if (array_key_exists($k, self::$defaults)) {
                $this->data[$k] = $v;
            }
        }
        // Recompute tier label
        $limit = (int)($this->data['daily_limit'] ?? 500);
        if ($limit <= 500)        $this->data['tier'] = 'free';
        elseif ($limit >= 100000) $this->data['tier'] = 'premium';
        else                      $this->data['tier'] = 'custom';

        $this->save();
    }

    public function all(): array
    {
        return $this->data;
    }

    /** Max background scan calls per day */
    public function maxAutoPerDay(): int
    {
        $limit = (int)($this->data['daily_limit'] ?? 500);
        $share = (float)($this->data['auto_scan_share'] ?? 0.8);
        return (int)floor($limit * $share);
    }

    /** Max manual reanalyze calls per day */
    public function maxManualPerDay(): int
    {
        $limit = (int)($this->data['daily_limit'] ?? 500);
        return $limit - $this->maxAutoPerDay();
    }

    /** Try to auto-detect tier by calling VT quota endpoint */
    public function detectTierFromApi(): array
    {
        $apiKey = $this->data['api_key'] ?? '';
        if (!$apiKey) return ['error' => 'No API key configured'];

        $ch = curl_init('https://www.virustotal.com/api/v3/users/' . $apiKey . '/api_usage');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => ['x-apikey: ' . $apiKey],
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200) {
            // Try the user endpoint instead
            $ch2 = curl_init('https://www.virustotal.com/api/v3/users/self');
            curl_setopt_array($ch2, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_HTTPHEADER     => ['x-apikey: ' . $apiKey],
            ]);
            $body2 = curl_exec($ch2);
            $code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
            curl_close($ch2);

            if ($code2 === 200) {
                $user = json_decode($body2, true);
                $quota = $user['data']['attributes']['quotas']['api_requests_daily']['allowed'] ?? null;
                if ($quota) {
                    $this->data['daily_limit'] = (int)$quota;
                    $this->save();
                    return ['detected' => true, 'daily_limit' => (int)$quota];
                }
            }
            return ['error' => "API returned HTTP $code"];
        }

        $data = json_decode($body, true);
        // VT usage endpoint gives daily totals — use it to infer tier
        return ['raw' => $data];
    }

    public function getUserId(): string { return $this->userId; }

    private function load(): array
    {
        $file = self::settingsFile($this->userId);
        if (!file_exists($file)) return [];
        $d = json_decode(file_get_contents($file), true);
        return is_array($d) ? $d : [];
    }

    private function save(): void
    {
        $file = self::settingsFile($this->userId);
        file_put_contents($file, json_encode($this->data, JSON_PRETTY_PRINT));
        @chmod($file, 0600); // contains the API key — panel user only
    }
}
