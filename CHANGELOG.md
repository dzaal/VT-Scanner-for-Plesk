# Changes

Every version of StingyScan for Plesk (called VT Site Scanner until 1.35), newest first.
Install or update: see the [latest release](https://github.com/dzaal/StingyScan-for-Plesk/releases/latest).

## 2.0.2 — 2026-11-22
- New home: [dzaal/StingyScan-for-Plesk](https://github.com/dzaal/StingyScan-for-Plesk). The old repository dzaal/VT-Scanner-for-Plesk stays, so existing installations keep finding their updates.
- An installed VT Site Scanner now updates to StingyScan **in place**: `virustotal-extension.zip` is the same StingyScan, packaged under the old extension id (`bash build.sh legacy`). Settings, keys, results, history and the panel address stay as they are. Updates then come from the new repository.
- License in plain language: StingyScan is the intellectual property of Dirk Zaal (Digizaal). Free to install and use as published; changing, copying into other software or distributing needs written permission.

## 2.0.1 — 2026-11-22
- Fix: the Plesk hooks (menu button, Home card) still had class names of the old extension id. While VT Site Scanner was installed next to StingyScan this gave "Cannot redeclare class Modules_VirustotalExtension_CustomButtons", and the Home card did not load.

## 2.0.0 — 2026-11-22
- **New name: StingyScan** (was VT Site Scanner), with a new logo. *Stingy* because it is sparing with every API call; the name no longer contains a third party's trademark.
- New extension id `stingyscan` (was `virustotal-extension`), panel address `/modules/stingyscan/`, and repository `dzaal/StingyScan-for-Plesk` (the old address redirects).
- **Coming from VT Site Scanner:** install StingyScan; it copies the settings, API keys, results, history and logs of VT Site Scanner. Then remove the old extension with `plesk bin extension --uninstall virustotal-extension`. Until then StingyScan shows a reminder, because both would run their own scans. Releases also carry the zip under its old name, so the update check of VT Site Scanner finds StingyScan.
- Documentation: Settings, Google Safe Browsing and AVG / Avast Web Shield described in full; what is sent to which service; trademark notices in the README and in the About tab.

## 1.35.3 — 2026-11-22
- License: VT Site Scanner by Digizaal, including its code, design and way of working, is the intellectual property of Digizaal. The source code may be read; installing and running the unmodified extension is allowed. Modifying the code in any way, derivative works and redistribution are not allowed without written permission. See LICENSE.

## 1.35.2 — 2026-11-22
- Google Safe Browsing has its own card in Settings, apart from the experimental AVG / Avast checks (it is Google's official API).
- The card explains how to get a key, with direct links to the Google Cloud Console: enable the Safe Browsing API, create and restrict the key, and see its quotas and usage.

## 1.35.1 — 2026-11-22
- Settings: no text lighter than rgb(32, 32, 32), for easier reading.
- The online services (VirusTotal, AVG, Avast, Google Safe Browsing) each have their own coloured block with a larger logo.
- A green check (*API key active*) next to VirusTotal and Google Safe Browsing when the key is accepted and in use; a red cross with the reason when VirusTotal or Google rejects it (for Google: the last answer from Google).

## 1.35.0 — 2026-11-22
- New: **Google Safe Browsing** (optional, administrator), Google's official list of unsafe sites that Chrome, Firefox and Safari use to warn visitors. Uses your own Google API key (Google Cloud → APIs & Services → Safe Browsing API); a new key is tested with Google's own test page before it is saved, and the key is never sent to the browser.
- Every site is checked each night (up to 250 sites per request), and live when you open a site's details or rescan it. Adds no VirusTotal API usage. Calls today and a per-minute limit are shown in Settings.
- A listed site gets a badge (for example *Google: Phishing*) and a row in the Why? table with a link to Google Search Console to request a review. Listing and delisting go into the site history and Recent status changes.
- Following Google's terms, a result is used for 30 minutes at most; after that it is shown as *last known* and checked again. Google allows this API for non-commercial use only.

## 1.34.1 — 2026-11-22
- Fix: a rescan could run twice, in the browser and by the server queue at the same time (two VirusTotal calls, budget charged twice). The open page now reports its rescans every 30 seconds; the server only takes over a rescan when no page has reported it for 3 minutes.
- Fix: after the server queue had finished rescans, every page load for 2 hours fetched those reports again from VirusTotal (spending manual calls). It now shows the result the server already saved.
- Fix: two runs of the server queue could overlap and scan the same sites; only one runs at a time now. The queue file is changed under a lock and written safely, so the browser and the server no longer overwrite each other's changes. A rescan that VirusTotal refuses is taken off the queue instead of being retried every 5 minutes.
- Security: the review address saved with a review request must be an http(s) address or an e-mail address, and the site history only makes links of http(s) addresses (a `javascript:` link could otherwise run code when the administrator clicked it).
- Removing a rescan from the server queue now checks that the site is yours.

## 1.34.0 — 2026-11-22
- Max calls / day is a number field again (default 500); the separate slider for it is gone.
- The slider for background scan / reserved for manual scans has a thin black line as its handle.
- Number fields in Settings (cache days, max calls, log rotation) are narrower: 2/12 of the width each.
- New: **Log rotation** (administrator, server-wide). A scan log larger than the limit (default 5 MB) is compressed to an old copy (`cron_<user>.log.1.gz`, …) and a new log is started; at most 5 old copies are kept and none older than 90 days. All three are adjustable. Checked every 5 minutes and before the nightly scan.

## 1.33.0 — 2026-11-22
- VirusTotal daily budget as two clear sliders, each with its name inside: **Max calls / day** (0 – 1,000) and **Reserved for manual scans (%)**. Each range has its own colour (background scan blue, manual rescans purple, outside the budget hatched) and an orange bar inside it shows what was used today. The number of VirusTotal calls made today is shown large above the sliders, split into background and manual rescans.
- AVG and Avast one below the other as **AVG Web Shield** and **Avast Web Shield**, with what they are, and the number of calls made today next to each.
- "Internal scanners" is now "Internal serverside file scanners".

## 1.32.0 — 2026-11-22
- VirusTotal daily budget as one slider from 0 to 10,000 calls: drag the bottom knob for the daily limit and the top knob for the share kept for manual rescans (the rest is for the background scan). Orange bars show the calls used today in each part. The numbers can still be typed.
- A 20px logo next to each scanner and service in Settings (VirusTotal, AVG, Avast, ClamAV, Maldet, Imunify) and next to Avast / AVG in the site details. The logos ship with the extension; nothing is loaded from outside.

## 1.31.2 — 2026-11-22
- Fix a missing request-token variable that stopped the Install button before it sent a request. Use Plesk's privileged extension helper, wait for its actual result, and verify the installed version before reloading.
- Report failed upgrades and connection errors instead of treating them as success.
- Read the installed version directly from package metadata; refresh GitHub update checks every 15 minutes instead of 24 hours.

## 1.31.1 — 2026-11-22
- Keep today's VirusTotal API usage with its API settings in a compact two-column summary, including background/manual counters and the date. Remove the separate statistics card at the bottom.

## 1.31.0 — 2026-11-22
- Redesign Settings: VirusTotal first with four columns of numeric controls; AVG/Avast directly below in a separate Experimental section; detected internal scanners next.
- Enable AVG, Avast, Imunify, Maldet and ClamAV independently, with a separate 1–5 requests/minute limit per provider/scanner (default 2).
- Enforce optional check limits server-wide across users and browser tabs. External checks wait without losing the previous result; internal scanner starts respect their rate and the existing single-scan queue. Imunify still only reads its own results.
- Keep settings usable on narrow screens, with two-column VirusTotal fields and stacked provider controls.

## 1.30.4 — 2026-11-22
- Copy the site's full HTTPS URL when opening a vendor review form or e-mail, including direct AVG/Avast false-positive requests.
- Clipboard contents are replaced, never appended; show confirmation or an explicit message if copying is blocked. Undoing or denying a request does not copy anything.

## 1.30.3 — 2026-11-22
- Show direct AVG and Avast checks and classification changes in Recent status changes, separately from VirusTotal verdicts.
- Record the first successful clear check as “No classification”; do not present it as a proven delisting. Import existing checks at their original timestamps.
- Compare with the last successful observation, so a failed request between two successful checks does not hide an improvement.

## 1.30.2 — 2026-11-22
- Trend tab, Recent status changes: a solid, coloured arrow between the two statuses (green = better, red = worse).

## 1.30.1 — 2026-11-22
- The Avast / AVG badge in the site list (for example *Avast: Botnet*) no longer wraps onto two lines.

## 1.30.0 — 2026-11-22
- Click anywhere on a site's row to open or close its details (Why?), not only on the score.
- Fix: the details of an infected site made the page too wide; long text and links now wrap within the table.

## 1.29.1 — 2026-11-22
- Avast / AVG false-positive link: explains that the form's Alert ID comes from the warning popup of the Avast / AVG app, not from this check, and what to do without one.

## 1.29.0 — 2026-11-22
- Avast and AVG are now two checkboxes in Settings: switch on either one or both. With both, each site gets a row for Avast and one for AVG in the details, and a badge for each that classifies it (two requests, one second apart).
- The earlier choice and the saved results are carried over.

## 1.28.0 — 2026-11-22
- Avast / AVG is now checked live instead of in the background: when you open the details of a site, and with every rescan. A result of the last 5 minutes is reused; *check again* asks right away. Still at most one request per second server-wide.
- AVG (or Avast) is the first row of the vendor table in the details, marked *(direct)*, with its classification, *No classification received* or *Check failed*, and the false-positive link (remembered like the other review links).
- While the check is on, every site has a *Details* button on its score, also clean sites.
- The background task (`avast_scan.php`) and its queue are gone.

## 1.27.1 — 2026-11-22
- Fix: the Avast / AVG setting jumped back to Off after saving.

## 1.27.0 — 2026-11-22
- Optional Avast / AVG classification (Settings, administrator; off by default). Asks the URL reputation service of Avast (product 146) or AVG (147) how it classifies each site, for example *URL:Botnet*. Unofficial: worked out by hand, so it may stop working; failures show as *Check failed*.
- Runs as its own background task every 5 minutes, never in a page request: at most one request per second server-wide, never two at once (shared lock, also across processes), and a pause on HTTP 429 (Retry-After, at least 60 seconds).
- Every site about once a week, classified sites every day, failed checks after an hour; *Check now* in the Why? panel puts a site first in the queue.
- Dashboard: a red *AVG: Botnet* badge under the status of a classified site. Why? panel: the checked URL, time, HTTP status, classification and detection name, or *No classification received*, with a link to the official false-positive form (choose URL in the form).
- Site history: when the classification appears, changes or goes away.

## 1.26.0 — 2026-11-22
- Review requests: mark one as **denied** (for example "added less than a day ago"), with an optional reason. The Requested note is removed from the overview, so you can ask again later, and the site's history shows "Review denied" with the reason.
- The undo link is now only for a click made by mistake.

## 1.25.0 — 2026-11-21
- History per site, at the bottom of the Why? panel, newest first, with date (time):
  - Detected: the first time the site was flagged, with the score and the vendors.
  - Flagged / Delisted: a vendor started flagging the site, or no longer flags it.
  - Status changes: coloured [status] → [status].
  - Review asked: which vendor, via website form or by e-mail (and to which address), and by whom; undone requests too.
- Clean sites that have a history get a History button on their score.
- The first detection of every site is filled in once from the scan logs.

## 1.24.0 — 2026-11-21
- Why? panel: a click on a vendor's review link (website form or e-mail) is remembered per site and per vendor: "✓ Requested 21-11-2026 (22:58) by admin", with undo.
- Saved on the server, so every admin, reseller or customer who manages the site sees it, in any browser.
- The panel header shows at how many of the flagging vendors a review was requested, and those vendors get a check mark.

## 1.23.1 — 2026-11-21
- Recent status changes: the coloured status change is now the second column, before the site name; badges have the same width so the columns line up.

## 1.23.0 — 2026-11-21
- Includes the work of 1.21 to 1.22.1, which ran on the server but was never released:
  - Rescans keep going on the server when you leave the page: a queue that is processed every 5 minutes.
  - Recent status changes are recorded at the moment a site is scanned.
- Recent status changes (bottom of the Trend tab): one line per change with date (time), site, and coloured [status] → [status]; the arrow is green when it got better, red when it got worse.
- Older status changes are filled in once from the scan logs.
- An infected or suspicious site that leaves the site list (deleted, disabled or suspended) shows as Left the list.
- A site's first result (pending → infected) no longer counts as a change.
- The administrator sees all changes on the server, including sites that have left the list.

## 1.20.0 — 2026-11-21
- Trend tab: Recent status changes at the bottom, per site with date and time, coloured from → to status (infected, suspicious, clean, or left the list).
- Older status changes are filled in once from the scan logs.
- A site that was infected or suspicious and leaves the site list (deleted, disabled or suspended) is recorded as Left the list.
- About tab: What's new, with links to the latest changes and all versions.

## 1.19.0 — 2026-11-20
- Infected files badge: when ClamAV, maldet or Imunify finds infected files, a red badge appears in the site's status column; a click opens the file scanner details.
- Click the Scan Monitor header to open or close it.
- Fix: after a failed rescan the Score and Last scan columns kept spinning.

## 1.18.0 — 2026-11-20
- File scans (ClamAV, maldet, Imunify) show in the Scan Monitor next to the VirusTotal lookups, with the infected count.
- Colour bars per job: blue = VirusTotal running, amber = file scan running, green = done and clean, red = failed or infected.
- A Completed divider separates running from finished jobs; Clear finished also clears file scans.
- Scan Monitor is a panel on the right, just below the header.

## 1.17.0 — 2026-11-19
- Scan Queue renamed to Scan Monitor, with Collapse and Expand.
- Shows several jobs at once (running, up to 5 waiting, up to 8 finished), so failures stay visible; finished jobs disappear after 30 seconds.
- A failed job marks only the step that failed in red.
- When the Plesk session has expired, the page reloads to the login.

## 1.16.0 — 2026-11-19
- Update check: a yellow banner shows when a newer version is available, with the release notes and an Install button (admin only) that upgrades the extension in one click.
- Sort by Domain, Score or Last scan by clicking the column header, in both Group and Sort mode (within each group).
- Group or Sort mode: grouped by reseller and customer, or one flat list. Mode, sort column and direction are remembered.
- Last scan shows how long ago the site was checked; the exact time on hover, VirusTotal's own analysis date below it.
- Faster first load: the site list appears right away while results load, with a progress bar.
- Fix: Rescan failed with Submit failed.
- Fix: two VT Site Scanner items in the Plesk menu; stray menu buttons are cleaned up on update.

## 1.15.6 — 2026-11-18
- Demo data for resellers and customers: `seed_demo_history.php --resellers`, `--customers` and `--remove`.
- Demo data is scaled to the number of sites on the server.

## 1.15.4 — 2026-11-18
- Trend tab for customers: their own sites, recorded daily per customer.

## 1.15.3 — 2026-11-18
- Menu item in the customer panel too (hosting panel tabs).

## 1.15.2 — 2026-11-18
- Trend tab for resellers: their own customers and sites, recorded daily per reseller.

## 1.15.1 — 2026-11-18
- A ready-made, installable zip is published as a GitHub Release for every version.
- One-command install and update:
  `plesk bin extension --upgrade-url https://github.com/dzaal/VT-Scanner-for-Plesk/releases/latest/download/virustotal-extension.zip`

## 1.15.0 — 2026-11-18
- Dashboard groups sites by reseller and customer; groups can be folded.
- Search also finds customers.

## 1.14.1 — 2026-11-18
- Orphan databases outside Plesk can be backed up too.
- Demo trend data in proportion to the number of sites.
- README: trend screenshot.

## 1.14.0 — 2026-11-17
- New Trend tab: infected, suspicious and cleaned sites per day over 1, 2 or 3 months; daily totals are saved.
- Orphan DBs has its own filter button.
- Refresh also reloads the orphan databases.

## 1.13.0 — 2026-11-17
- Orphan databases: per leftover hostname the database, its size and whether a live site still uses it.
- Back up an orphan database to a private folder, with a Delete-in-Plesk link.

## 1.12.0 — 2026-11-17
- Related hostnames: leftover WordPress hostnames without DNS are listed separately and checked only on request.
- API key comes first in Settings.

## 1.11.1 — 2026-11-17
- WordPress hostnames from leftover databases that no longer exist in DNS are skipped.

## 1.11.0 — 2026-11-17
- Left menu item for the admin, resellers, customers and the Power User view.
- Sites wait for an API key instead of showing Error.

## 1.10.3 — 2026-11-17
- Fortinet: links the Malicious URL Appeal form instead of the software dispute form.

## 1.10.2 — 2026-11-17
- Review links to the website forms of Sophos, BitDefender, Chong Lua Dao, Trustwave and Bfore.Ai.
- Tips per vendor and a pre-filled e-mail for vendors without a form.

## 1.10.1 — 2026-11-17
- Review links to the vendor forms of alphaMountain and Gridinsoft.
- Clearer rescan steps.

## 1.10.0 — 2026-11-17
- Why flagged: per site, the vendors that flag it, with review links and delisting steps.

## 1.9.1 — 2026-11-17
- Settings: scanner details wrap at words.

## 1.9.0 — 2026-11-17
- File scanners can be switched on and off in Settings, with their detection status.

## 1.8.3 — 2026-11-17
- The page breaks out of the Plesk frame, so notifications are no longer shown twice.

## 1.8.2 — 2026-11-17
- Fix: scanner bar was squashed by the score-bar style.

## 1.8.1 — 2026-11-17
- Scanner buttons are easier to see.

## 1.8.0 — 2026-11-17
- Scanner bar per site for ClamAV, maldet and Imunify, through one root helper (`sbin/site-scan`) with an adapter per scanner.
- Detects which scanners are installed; runs ClamAV or maldet on demand on the document root.
- Reads Imunify's latest result without starting a scan (Imunify may be set to clean up on detect).
- A click opens a second line under the site with progress, then the infected files.
- The extension only detects; it never changes sites.

## 1.7.4 — 2026-11-17
- Queued count includes automatic lookups.
- File-list dialog shows above the Plesk UI.

## 1.7.2 — 2026-11-17
- Scan files: on infected or suspicious rows, scans the site's document root with the running clamd (read-only, one scan at a time, later clicks queue).
- Results show on the row, with a file-list dialog.
- About tab and README explain why the tool exists: Plesk's own VirusTotal check ran out of API calls daily and left sites unscanned.

## 1.6.4 — 2026-11-17
- Disabled and suspended sites drop out of the list, also when they are WordPress subsites of a hosted domain.
- The hosted-domain list is cached 10 minutes instead of a day.

## 1.6.3 — 2026-11-17
- Coffee button: PayPal.Me link updated.

## 1.6.2 — 2026-11-17
- Buy-me-a-coffee button goes to PayPal, $3.

## 1.6.1 — 2026-11-17
- Notifications go to one e-mail address (empty = your Plesk address).
- One summary e-mail with the status changes and the full list of infected and suspicious sites: after the nightly scan, and during the day 10 minutes after a change.
- A site that flips back before the summary is sent is left out.

## 1.6.0 — 2026-11-17
- E-mail notifications when a site becomes infected, suspicious or clean again; from the nightly scan and from dashboard lookups and rescans.
- Refresh and Rescan infected buttons replaced by a menu on each status button: Rescan (capped at the manual budget) and, on All, Refresh site list.
- README: users and roles, notifications.

## 1.5.0 — 2026-11-17
- New official VT shield logo with a transparent background, in the Extensions list, left menu, tools entry, page header and README.

## 1.4.0 — 2026-11-17
- Scan queue on one line: the site being checked, or the latest result.
- Animated bar counts down the pause for VirusTotal's 4-per-minute limit.
- New About tab: the maker, who the tool is for, and a Buy-me-a-coffee button.
- README: who it's for.

## 1.3.2 — 2026-11-17
- Scan queue keeps every finished site with its result: status, score and time, or why the lookup failed.

## 1.3.1 — 2026-11-17
- Plesk's page header is hidden on the scanner page; only the branded header shows.

## 1.3.0 — 2026-11-17
- Scans every hosted website: active domains and subdomains with hosting, plus WordPress sites on hostnames under them.
- New setting Sites to scan: all hosted websites or WordPress only.
- The Scanner enabled switch is gone.
- Home card and nightly scan follow the chosen scope.

## 1.2.1 — 2026-11-17
- Page title completes Plesk's breadcrumb (Home > Extensions > VT Site Scanner).
- Branded header with logo, version and Digizaal link.
- Catalog icons; deep links work.

## 1.2.0 — 2026-11-17
- Plesk Home page card: infected, suspicious and clean counts and the worst-scoring sites; reads cached results only.
- Fix: removed a broken permissions hook that logged an error on every request.
- README: screenshots.

## 1.1.0 — 2026-11-17
- Installable by others: the nightly scan is registered in Plesk's scheduler on install and removed on uninstall.
- `build.sh` builds the zip; settings file with the API key is written with mode 0600.
- README: requirements, installation, updating, uninstalling, troubleshooting, data and privacy.

## 1.0.0 — 2026-11-17
- First version: scans the WordPress sites on a Plesk server against the VirusTotal API v3.
- Per-key rate limiter (4 per minute, 500 per day), result caching, nightly background scan and a live queue for manual rescans.
