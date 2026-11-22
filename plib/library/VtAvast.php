<?php
require_once __DIR__ . '/VtDataDir.php';

/**
 * VtAvast — Avast / AVG URL reputation (classification) of a site.
 *
 * Unofficial: this is the URL reputation service the Avast and AVG products use themselves
 * (POST https://urlite.ff.avast.com/v1/urlinfo, Protocol Buffers, no key). It was worked out
 * by hand and may change or stop working at any time; every failure is shown as "Check failed".
 * Only the reputation service is asked; the site itself is never visited.
 *
 * Request:  Request{ 1: Query queries[], 2: bool saveUrlFeed=true }
 *           Query{ 1: string key (URL), 2: keyType=1 (URL), 3: ClientInfo, 4: bool doNotTrack=false }
 *           ClientInfo{ 2: Platform{ 1: os=1 }, 3: Product{ 1: id } }   product 146 = Avast, 147 = AVG
 * Response: Response{ 1: Answer answers[] },  Answer{ 1: string key, 12: Finding findings[] }
 *           Finding{ 1: "URL:Botnet|UR7D2795ED6D01A206-0200|urlb", 2, 3, 4: ints }
 *
 * Hard limit: at most one request per second, server-wide. One lock file is held for the whole
 * request, so there are never two requests at once (not even from different processes), and the
 * start of each request is at least 1,000 ms after the previous one. HTTP 429 pauses all requests
 * for Retry-After seconds (at least 60).
 */
class VtAvast
{
    const ENDPOINT = 'https://urlite.ff.avast.com/v1/urlinfo';
    const PRODUCTS = [146 => 'Avast', 147 => 'AVG'];
    const FP_FORMS = [146 => 'https://www.avast.com/report-false-positive', 147 => 'https://www.avg.com/en-us/report-false-positive'];
    const MIN_GAP_MS = 1000;

    /** "voorelkdier.nl" -> "https://voorelkdier.nl/"; a full URL keeps its path */
    public static function normalizeUrl(string $in): string
    {
        $in = trim($in);
        if (preg_match('#^https?://#i', $in)) {
            $p = parse_url($in);
            return strtolower($p['scheme']) . '://' . strtolower($p['host'] ?? '') . (isset($p['port']) ? ':' . $p['port'] : '')
                 . ($p['path'] ?? '/') . (isset($p['query']) ? '?' . $p['query'] : '');
        }
        return 'https://' . strtolower(rtrim($in, '/')) . '/';
    }

    /**
     * Check one URL with one product. Returns
     *   [url, product, checked_at, http, state: ok|failed, findings: [{classification, detection, raw}], error?]
     */
    public static function check(string $urlOrDomain, int $product = 146): array
    {
        $url  = self::normalizeUrl($urlOrDomain);
        $out  = ['url' => $url, 'product' => $product, 'checked_at' => time(), 'http' => 0, 'state' => 'failed', 'findings' => []];
        $body = self::send(self::request($url, $product), $code, $err, $product);
        $out['http'] = $code; $out['checked_at'] = time();
        if ($err !== '')          return $out + ['error' => $err];
        if ($code !== 200)        return $out + ['error' => 'HTTP ' . $code];
        try {
            $answers = self::decodeResponse((string)$body);
        } catch (\Throwable $e) {
            return $out + ['error' => 'Invalid response'];
        }
        foreach ($answers as $a) {
            if (rtrim($a['key'], '/') !== rtrim($url, '/')) continue;
            $out['state']    = 'ok';
            $out['findings'] = $a['findings'];
            return $out;
        }
        return $out + ['error' => 'No answer for this URL'];
    }

    // ── Protocol Buffers ───────────────────────────────────────

    public static function request(string $url, int $product): string
    {
        $client = self::len(2, self::int(1, 1)) . self::len(3, self::int(1, $product));
        $query  = self::len(1, $url) . self::int(2, 1) . self::len(3, $client) . self::int(4, 0);
        return self::len(1, $query) . self::int(2, 1);
    }

    /** [{key, findings: [{classification, detection, raw}]}] */
    public static function decodeResponse(string $b): array
    {
        $answers = [];
        foreach (self::fields($b) as [$f, $t, $v]) {
            if ($f !== 1 || $t !== 2) continue;
            $key = ''; $findings = [];
            foreach (self::fields($v) as [$af, $at, $av]) {
                if ($af === 1 && $at === 2) $key = $av;
                if ($af === 12 && $at === 2) {
                    $raw = '';
                    foreach (self::fields($av) as [$ff, $ft, $fv]) if ($ff === 1 && $ft === 2) $raw = $fv;
                    $parts = explode('|', $raw);
                    $findings[] = ['classification' => $parts[0] ?? $raw, 'detection' => $parts[1] ?? '', 'raw' => $raw];
                }
            }
            $answers[] = ['key' => $key, 'findings' => $findings];
        }
        if (!$answers) throw new \RuntimeException('no answers');
        return $answers;
    }

    /** Parse one message level: [[field, wiretype, value], ...]; groups are skipped */
    private static function fields(string $b): array
    {
        $out = []; $i = 0; $n = strlen($b); $depth = 0;
        while ($i < $n) {
            $key = self::readVarint($b, $i); $f = $key >> 3; $t = $key & 7;
            switch ($t) {
                case 0: $v = self::readVarint($b, $i); break;
                case 1: if ($i + 8 > $n) throw new \RuntimeException('truncated'); $v = substr($b, $i, 8); $i += 8; break;
                case 2: $l = self::readVarint($b, $i); if ($l < 0 || $i + $l > $n) throw new \RuntimeException('truncated');
                        $v = substr($b, $i, $l); $i += $l; break;
                case 5: if ($i + 4 > $n) throw new \RuntimeException('truncated'); $v = substr($b, $i, 4); $i += 4; break;
                case 3: $depth++; continue 2;   // start group: its fields are skipped below
                case 4: $depth--; continue 2;   // end group
                default: throw new \RuntimeException('wire type ' . $t);
            }
            if ($depth === 0) $out[] = [$f, $t, $v];
        }
        return $out;
    }

    private static function readVarint(string $b, int &$i): int
    {
        $v = 0; $s = 0;
        do {
            if ($i >= strlen($b) || $s > 63) throw new \RuntimeException('bad varint');
            $c = ord($b[$i++]); $v |= ($c & 0x7f) << $s; $s += 7;
        } while ($c & 0x80);
        return $v;
    }

    private static function varint(int $v): string
    {
        $o = '';
        do { $b = $v & 0x7f; $v >>= 7; if ($v) $b |= 0x80; $o .= chr($b); } while ($v);
        return $o;
    }
    private static function int(int $f, int $v): string    { return self::varint($f << 3) . self::varint($v); }
    private static function len(int $f, string $v): string { return self::varint(($f << 3) | 2) . self::varint(strlen($v)) . $v; }

    /** Requests sent today per product: [146 => n, 147 => n] */
    public static function callsToday(): array
    {
        $st  = json_decode((string)@file_get_contents(VtDataDir::path('vt_avast_rate.json')), true) ?: [];
        $out = array_fill_keys(array_keys(self::PRODUCTS), 0);
        if (($st['day'] ?? '') !== date('Y-m-d')) return $out;
        foreach ($st['calls'] ?? [] as $p => $n) if (isset($out[(int)$p])) $out[(int)$p] = (int)$n;
        return $out;
    }

    // ── HTTP with the shared limiter ───────────────────────────

    private static function send(string $payload, ?int &$code, ?string &$err, int $product = 0)
    {
        $code = 0; $err = '';
        $fp = fopen(VtDataDir::path('vt_avast_rate.lock'), 'c');
        if (!$fp || !flock($fp, LOCK_EX)) { $err = 'Rate limiter unavailable'; return false; }
        try {
            $stateFile = VtDataDir::path('vt_avast_rate.json');
            $st = json_decode((string)@file_get_contents($stateFile), true) ?: [];
            $now = microtime(true) * 1000;
            $startAt = max((float)($st['last_start_ms'] ?? 0) + self::MIN_GAP_MS, (float)($st['not_before_ms'] ?? 0));
            if ($startAt - $now > 120000) { $err = 'Paused by the service (HTTP 429), retry later'; return false; }
            if ($startAt > $now) usleep((int)(($startAt - $now) * 1000));
            $st['last_start_ms'] = microtime(true) * 1000;
            // Calls today per product (server time), shown next to AVG / Avast in Settings
            if (($st['day'] ?? '') !== date('Y-m-d')) { $st['day'] = date('Y-m-d'); $st['calls'] = []; }
            $st['calls'][$product] = (int)($st['calls'][$product] ?? 0) + 1;
            @file_put_contents($stateFile, json_encode($st));

            $retryAfter = 0;
            $ch = curl_init(self::ENDPOINT);
            curl_setopt_array($ch, [
                CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload, CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/octet-stream'],
                CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_TIMEOUT => 15,
                CURLOPT_HEADERFUNCTION => function ($c, $h) use (&$retryAfter) {
                    if (stripos($h, 'Retry-After:') === 0) $retryAfter = (int)trim(substr($h, 12));
                    return strlen($h);
                },
            ]);
            $body = curl_exec($ch);
            $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($body === false) $err = curl_errno($ch) === CURLE_OPERATION_TIMEDOUT ? 'Timed out' : 'Connection failed: ' . curl_error($ch);
            curl_close($ch);
            if ($code === 429) {
                $st['not_before_ms'] = microtime(true) * 1000 + max(60, $retryAfter) * 1000;
                @file_put_contents($stateFile, json_encode($st));
            }
            return $body;
        } finally {
            flock($fp, LOCK_UN); fclose($fp);
        }
    }
}
