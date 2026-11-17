# VT Site Scanner — Plesk Extension

A Plesk extension that automatically scans every WordPress site on your server for malware, phishing, and other threats using the VirusTotal API — without ever burning through your free API quota.

## Why this exists

Running 300+ WordPress sites on a single server means one compromised site can affect your entire hosting environment. Manual malware checks don't scale. This extension runs a silent background scan every night, flags anything suspicious, and lets you rescan individual sites on demand — all within the tight limits of VirusTotal's free tier (500 requests/day).

## How it works

Every night at 03:00, a cron job scans a batch of sites using VirusTotal's URL analysis API. It picks up exactly where it left off the previous night (round-robin queue), so every site gets checked regularly even with hundreds of domains. Infected or suspicious sites always jump to the front of the queue and are re-checked the next day.

Results are cached so the same site isn't scanned unnecessarily: clean sites are cached for 30 days, infected/suspicious sites for 1 day.

The web UI lets you see the current status of all sites at a glance, filter by status, and trigger an immediate rescan of any domain. All scans respect a daily budget split: 80% of your 500 daily API calls are reserved for the background cron, 20% for manual rescans from the UI.

## Screenshots / what you see

**Dashboard tab** — a full table of all WordPress sites with:
- Status badge (Infected / Suspicious / Clean / Queued / Error)
- Detection score bar (e.g. `3/90` vendors flagged)
- Last scan date
- Per-site Rescan button
- Filter buttons to quickly isolate infected or suspicious sites

**Settings tab** — configure your API key, daily limit, budget split, and cache TTLs. The saved API key is shown in full (not masked) so you can verify it.

**Log tab** — the raw scan log from the cron job, colour-coded: red for infected, orange for suspicious, green for clean. Useful for debugging and audit trails.

## Setup

### 1. Deploy to Plesk

```bash
VT_DEPLOY_HOST=root@your-plesk-server bash deploy.sh
```

This builds the extension zip and installs it via `plesk bin extension --install`. Requires SSH access to the server.

### 2. Add your VirusTotal API key

Open the extension in Plesk → **Settings** → paste your free API key → **Save**.

Get a key at [virustotal.com](https://www.virustotal.com) → Profile → API key (free, no credit card needed).

### 3. Set up the nightly cron

Add this to root's crontab on the server (`crontab -e`):

```
0 3 * * * php /usr/local/psa/admin/plib/modules/virustotal-extension/scripts/background_scan.php >> /usr/local/psa/var/modules/virustotal-extension/data/cron_admin.log 2>&1
```

That's it. The first run will start scanning and results appear in the dashboard immediately.

## API quota management

VirusTotal's free tier gives 500 requests/day and 4 requests/minute. This extension tracks both limits globally per API key so they're never exceeded:

| Pool | Default | Purpose |
|------|---------|---------|
| Background (cron) | 400 / day | Nightly sweep of all sites |
| Manual (UI rescans) | 100 / day | On-demand rescan buttons |

Both limits are configurable in Settings. The dashboard shows live usage so you always know how much quota remains.

## Stack

- PHP 8.4 (Plesk sw-engine), Zend Framework 1 (Plesk's built-in MVC)
- VirusTotal API v3
- Vanilla JS + CSS (zero dependencies, no build step)

## Data

- **Extension data (settings, cache, logs):** `/usr/local/psa/var/modules/virustotal-extension/data/` on the server — never committed to this repo.

## Development notes

- `VT_DEPLOY_HOST=root@your-plesk-server bash deploy.sh` — build + deploy in one step
- Plesk action routing: `?action=save-settings` → `saveSettingsAction()` (dashes → camelCase)
- Bootstrap: `pm_Context::init('virustotal-extension'); (new pm_Application())->run();`
- JSON responses: disable layout via `$this->_helper->layout->disableLayout()` then `$this->getResponse()->setBody(json_encode($data))` — never use `header()` / `exit` inside pm_Application
