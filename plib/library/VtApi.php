<?php
/**
 * VtApi — thin wrapper around VirusTotal API v3.
 *
 * Endpoints used:
 *   GET  /domains/{domain}            — primary scan (richer than URL endpoint: DNS, WHOIS, age, tags)
 *   POST /urls/{id}/analyse           — trigger re-analysis
 *   GET  /analyses/{id}               — poll analysis status (queued → inprogress → completed)
 *   POST /urls                        — submit URL for first-time scan
 *
 * Not yet implemented (extra API calls per domain, impractical on 500/day free tier):
 *   GET /domains/{domain}/communicating_files  — count of known malware files contacting this domain
 *   GET /domains/{domain}/downloaded_files     — files downloaded from domain
 *   GET /domains/{domain}/resolutions          — full IP resolution history
 *   GET /domains/{domain}/subdomains           — known subdomains
 *   GET /domains/{domain}/votes                — community harmless/malicious votes
 */
require_once __DIR__ . '/VtRateLimiter.php';

class VtApi
{
    private string $apiKey;
    private VtRateLimiter $limiter;

    public function __construct(string $apiKey)
    {
        $this->apiKey   = $apiKey;
        $this->limiter  = new VtRateLimiter($apiKey);
    }

    /** Encode URL to VT's base64url-without-padding ID */
    public static function urlId(string $domain): string
    {
        return rtrim(base64_encode('https://' . $domain), '=');
    }

    /**
     * Get domain report via /domains/{domain}.
     * Returns detection stats plus: IP, creation_date, registrar, tags, categories.
     */
    public function getReport(string $domain): array
    {
        $check = $this->limiter->check();
        if ($check !== true) return $this->limitResult($domain, $check);

        $res = $this->request('GET', 'domains/' . rawurlencode($domain));
        $this->limiter->record();

        if ($res['code'] === 404) {
            // Domain not yet in VT — submit homepage URL and return pending
            $this->submitUrl($domain);
            return ['status' => 'pending', 'domain' => $domain, 'message' => 'Submitted for first scan'];
        }
        if ($res['code'] === 429) {
            return $this->make429($domain, $res);
        }
        if ($res['code'] !== 200) {
            return ['status' => 'error', 'domain' => $domain, 'http_code' => $res['code']];
        }

        return $this->parseDomainReport($domain, $res['body']);
    }

    /**
     * Request a fresh re-analysis via POST /urls/{id}/analyse.
     * Returns analysis_id so the caller can poll GET /analyses/{id} for completion.
     */
    public function reanalyze(string $domain): array
    {
        $check = $this->limiter->check();
        if ($check !== true) return $this->limitResult($domain, $check);

        // Re-analyse the *domain* object — that is what getReport() reads.
        // (POST /urls/{id}/analyse only refreshes the homepage URL object.)
        $res = $this->request('POST', 'domains/' . rawurlencode($domain) . '/analyse');
        $this->limiter->record();
        if ($res['code'] === 404) {
            // Domain unknown to VT yet: submit the homepage URL, which creates it
            $res = $this->request('POST', 'urls', ['url' => 'https://' . $domain]);
            $this->limiter->record();
        }
        if ($res['code'] === 200) {
            $analysisId = $res['body']['data']['id'] ?? null;
            return ['status' => 'queued', 'domain' => $domain, 'analysis_id' => $analysisId];
        }
        if ($res['code'] === 429) {
            return $this->make429($domain, $res);
        }
        return ['status' => 'error', 'domain' => $domain, 'http_code' => $res['code']];
    }

    /**
     * Poll an analysis by ID.
     * VT analysis lifecycle: queued → inprogress → completed
     */
    public function getAnalysisStatus(string $analysisId): array
    {
        $res = $this->request('GET', 'analyses/' . rawurlencode($analysisId));
        if ($res['code'] !== 200) {
            return ['status' => 'unknown', 'http_code' => $res['code']];
        }
        $status = $res['body']['data']['attributes']['status'] ?? 'unknown';
        return ['status' => $status];
    }

    /** Submit a URL to VT for first-time scanning */
    public function submitUrl(string $domain): void
    {
        $this->request('POST', 'urls', ['url' => 'https://' . $domain]);
    }

    // ── Private ─────────────────────────────────────────────────

    private function parseDomainReport(string $domain, ?array $body): array
    {
        $attrs      = $body['data']['attributes'] ?? [];
        $stats      = $attrs['last_analysis_stats'] ?? [];
        $malicious  = (int)($stats['malicious']  ?? 0);
        $suspicious = (int)($stats['suspicious'] ?? 0);
        $total      = array_sum($stats);
        $date       = $attrs['last_analysis_date'] ?? 0;

        // Domain-specific enrichment (not available from URL endpoint)
        $creationDate = isset($attrs['creation_date']) ? (int)$attrs['creation_date'] : null;
        $registrar    = $attrs['registrar'] ?? null;
        $tags         = $attrs['tags'] ?? [];

        // Extract first A record IP from last DNS records
        $ip = null;
        foreach ($attrs['last_dns_records'] ?? [] as $rec) {
            if (($rec['type'] ?? '') === 'A') {
                $ip = $rec['value'];
                break;
            }
        }

        // Collect which vendors flagged it
        $flagged = [];
        foreach ($attrs['last_analysis_results'] ?? [] as $vendor => $r) {
            if (in_array($r['category'] ?? '', ['malicious', 'suspicious'])) {
                $flagged[] = $vendor . ': ' . ($r['result'] ?? '?');
            }
        }

        return [
            'status'        => $malicious > 0 ? 'infected' : ($suspicious > 0 ? 'suspicious' : 'clean'),
            'domain'        => $domain,
            'malicious'     => $malicious,
            'suspicious'    => $suspicious,
            'undetected'    => (int)($stats['undetected'] ?? 0),
            'total'         => $total,
            'last_scan'     => $date ? date('d-m-Y H:i', $date) : null,
            'creation_date' => $creationDate,
            'registrar'     => $registrar,
            'ip'            => $ip,
            'tags'          => $tags,
            'categories'    => $attrs['categories'] ?? [],
            'flagged_by'    => $flagged,
        ];
    }

    /** How many requests remain today for this API key (across all users) */
    public function remainingToday(): int
    {
        return $this->limiter->remaining();
    }

    /** Convert a limiter check result into an API-style response array */
    private function limitResult(string $domain, array $check): array
    {
        if (!empty($check['quota_exceeded'])) {
            return ['status' => 'quota_exceeded', 'domain' => $domain, 'retry_after' => $check['retry_after']];
        }
        return ['status' => 'ratelimit', 'domain' => $domain, 'wait' => $check['wait'] ?? 60];
    }

    /**
     * Build a structured 429 response.
     * VT uses two distinct 429 error codes:
     *   QuotaExceededError    — daily quota (500/day free tier) exhausted; Retry-After = seconds to midnight UTC
     *   TooManyRequestsError  — per-minute limit (4/min free tier) hit;   Retry-After ≈ 60s
     */
    private function make429(string $domain, array $res): array
    {
        $errCode    = $res['body']['error']['code'] ?? 'TooManyRequestsError';
        $retryAfter = $res['retry_after'] ?? null;
        $isQuota    = $errCode === 'QuotaExceededError';
        return [
            'status'      => $isQuota ? 'quota_exceeded' : 'ratelimit',
            'domain'      => $domain,
            'retry_after' => $retryAfter,
        ];
    }

    private function request(string $method, string $endpoint, ?array $data = null): array
    {
        $ch = curl_init('https://www.virustotal.com/api/v3/' . ltrim($endpoint, '/'));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => [
                'x-apikey: ' . $this->apiKey,
                'Accept: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            // Always set POSTFIELDS (even empty) so curl sends Content-Length: 0
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data ? http_build_query($data) : '');
        }
        $raw      = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $hsize    = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $rawHeaders = substr($raw, 0, $hsize);
        $body       = substr($raw, $hsize);
        $retryAfter = null;
        if (preg_match('/retry-after:\s*(\d+)/i', $rawHeaders, $m)) {
            $retryAfter = (int)$m[1];
        }
        return ['code' => $httpCode, 'body' => json_decode($body, true), 'retry_after' => $retryAfter];
    }
}
