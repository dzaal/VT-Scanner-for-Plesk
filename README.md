<img src="docs/logo.png" alt="" width="72" align="right">

# VT Site Scanner for Plesk

A Plesk extension that checks every website hosted on your Plesk server against [VirusTotal](https://www.virustotal.com) for malware, phishing and spam, while carefully managing the limited number of API calls in VirusTotal's free tier.

Made by **Dirk Zaal** of **[Digizaal](https://www.digizaal.nl)**, a web development and hosting company in Amsterdam.

> **Detection only.** VT Site Scanner never changes anything on your sites. It does not edit, clean, move, quarantine or delete files, and it does not touch databases. It only looks and reports, so you decide what to do.

## Who it's for

Hosting companies, agencies and server admins who look after **more sites than VirusTotal's free API can check in a day**. The free key allows 500 requests per day and 4 per minute. With 500 sites or more you can't check them all every day, and a simple scanner burns the whole quota before you get to the one site you just cleaned up.

VT Site Scanner makes that quota go a long way:

- **Every site gets its turn.** The nightly scan picks up where it stopped the night before, so all sites are checked in rotation, however many there are.
- **Quota left for you.** Part of the daily budget is always kept for manual rescans.
- **No wasted calls.** Clean sites are re-checked after 30 days, infected or suspicious sites after 1 day.
- **One overview.** All sites with their status on one page and on the Plesk Home page, so a flagged site stands out right away.

## Screenshots

**Scanner dashboard:** every hosted website with its status, detection score and a Rescan button. The scan queue at the top shows what is being checked right now.

![VT Site Scanner dashboard in Plesk](docs/screenshots/scanner-dashboard.png)

**Why is a site flagged, and how do you fix it?** Click the score of a flagged site: you see which security vendors flag it, a review link per vendor, and the steps from cleaning the site to getting it delisted.

![Vendors that flag a site, with review links and the steps to get delisted](docs/screenshots/why-flagged.png)

**Card on the Plesk Home page:** counts per status and the sites flagged by the most engines, straight on the admin's Home page.

<p>
  <img src="docs/screenshots/home-widget.png" alt="VT Site Scanner card on the Plesk Home page" width="346">
</p>

<details>
<summary>The card in context on the Plesk Home page</summary>

![Plesk Home page with the VT Site Scanner card](docs/screenshots/home-dashboard.png)

</details>

*Domain names and IP addresses in these screenshots are replaced with placeholders.*

---

## Why this extension exists

Digizaal hosts about 600 WordPress sites on a single Plesk server. A single compromised site can harm its visitors, get the server's IP address blacklisted, and damage the reputation of every other site on it, so we need to know early when a site has been flagged.

With that many sites, VirusTotal's free API quota (500 requests per day) runs out every single day: checking every site even once takes more than a day, and rescans of infected sites come on top of that. Plesk ships with its own VirusTotal integration (*VirusTotal Website Check*), but every day it reported that the API calls were exhausted and not all sites had been scanned, leaving the rest unscanned, with no control over which sites were checked or when. That left no room to rescan a site after cleaning it up, which is the moment you need it most.

VT Site Scanner was built to fix that:

- **Careful with API calls.** Every call goes through a rate limiter, results are cached, and the daily budget is split between background scans and manual rescans.
- **Interactive.** A live dashboard shows all sites, lets you filter on infected or suspicious ones, and rescans a site on demand, with a visible queue that shows each step of each scan.
- **Hands-off.** A nightly background scan works through all sites in turn and picks up where it left off the night before.

## Features

- Dashboard with every website hosted on the server (WordPress or not): status (Infected / Suspicious / Clean / Queued / Error), detection score (e.g. `3/93` engines), IP address, domain age and danger tags (malware, phishing, …), with direct links to the site and to its VirusTotal page.
- **Home page card** for the Plesk admin (Plesk 18.0.60 or newer): infected / suspicious / clean counts and the worst-scoring sites, styled like Plesk's own cards. It reads cached results only and never uses API calls. Show or hide it via **Home → Customize → Security → VT Site Scanner**.
- **Live scan queue**: one line showing the site being checked right now, or the latest result with its score. An animated bar shows the pause for VirusTotal's 4-requests-per-minute limit, which is waited out automatically instead of failing.
- **Nightly background scan** (03:00, server time), scheduled automatically through Plesk's own scheduler.
- **Helps you solve it, not just report it.** Click the score of an infected or suspicious site to see which vendors flag it, with a link per vendor to ask for a review (see [Cleaned, but still red?](#cleaned-but-still-red)).
- **E-mail notifications:** one summary e-mail with status changes and the full list of infected sites (see [Notifications](#notifications)).
- **Works for every Plesk user level:** administrator, reseller and client (see [Users and roles](#users-and-roles)).
- **Category menus:** the small arrow next to each status button (All, Infected, Suspicious, …) opens a menu with **Rescan**, which rescans every site in that category, worst first and never more than today's manual budget allows. The menu next to **All** also has **Refresh site list**.
- **File scanners on demand:** a button bar on every site for the malware scanners found on the server (ClamAV, maldet, Imunify). Results and infected files appear right under the site (see [File scanners](#file-scanners-clamav-maldet-imunify)).
- **Log tab** with a colour-coded scan log.
- **About tab** about the maker, with a *Buy me a coffee* button.

## File scanners (ClamAV, maldet, Imunify)

VirusTotal judges a site from the outside (its URL and domain). To look inside a site, every row has a small **scanner bar** with one button per malware scanner found on the server. The extension detects what is installed and only shows those buttons:

| Button | Scanner | What a click does |
|---|---|---|
| **ClamAV** | [ClamAV](https://www.clamav.net) | Scans the site's document root. Uses a running `clamd` (for example the one for mail scanning) via `clamdscan --fdpass`, so signatures are not loaded again; otherwise `clamscan`. |
| **Maldet** | [Linux Malware Detect](https://www.rfxn.com/projects/linux-malware-detect/) | Runs `maldet -a` on the document root only (the temp folders maldet normally adds are skipped). Uses ClamAV's engine when it is installed. |
| **Imunify** | ImunifyAV / Imunify360 | **Starts no scan.** Shows the result of Imunify's own latest scan of the site, including files Imunify has already cleaned. |

Clicking a button opens a second line under the site. It shows the scan while it runs, then the result and the list of infected files with the signature that matched. Click the button again (or **Close**) to fold it away; the button keeps a mark: ✓ for clean, a red number for infected files. **Scan again** runs a new scan.

- **On demand only.** A scan runs when you click, never by itself. One scan runs at a time on the server; a click while another scan is busy waits its turn and starts by itself. You can leave the page; the scan keeps running.
- **Detection only.** The extension asks every scanner to report, never to clean, quarantine or delete. maldet runs with its own quarantine setting, which is off by default; leave `quarantine_hits=0` in `/usr/local/maldetect/conf.maldet`.
- **Why Imunify is read-only:** Imunify handles what it finds according to its own settings (*Default action on detect*, for example *cleanup*). Starting an Imunify scan from here could therefore change files, so the extension only reads its results.
- Scans run at low CPU and disk priority. A large site takes a few minutes (about 17,000 files took 5–6 minutes with ClamAV in testing).
- Scans from this extension run one at a time. maldet's own daily cron scan (installed with maldet) runs outside the extension and can overlap with a scan you start here.
- Scanners don't always agree, and VirusTotal can flag a site for spam or phishing content in its database while its files are clean. Signatures starting with `PUA.` or `Heuristics.` are often false alarms.

### Switching scanners on and off

**Settings → File scanners** lists every scanner the extension knows (ClamAV, Maldet, Imunify), with what was found on the server:

- **Installed**, with how it will be used: for ClamAV a running `clamd` (and its config file) or `clamscan`; for maldet and Imunify the program path. Imunify is marked *results only*.
- **Not installed on this server**: the switch is greyed out and there is no button for it on the sites.

Each installed scanner has an on/off switch. A scanner that is switched off gets no button on any site, its earlier results are hidden, and it cannot be started. The setting is saved right away and applies to the whole server, so it is the same for every user; only the administrator can change it. Resellers and customers see the list, but the switches are locked for them.

The extension checks which scanners are installed once an hour. After you install or remove a scanner, click **Detect again** to update the list immediately. A newly found scanner is switched on.

How it works: website files belong to the subscription users, so the scans run through a small root helper (`sbin/site-scan`) that Plesk installs with the extension and calls through its own `callSbin` mechanism. The helper only accepts a scanner name and a hostname, and looks up the folder in Plesk's database. Each scanner is a small adapter in that file (detect, scan, read the result), so another scanner can be added the same way.

## Users and roles

VT Site Scanner works at all three Plesk user levels: **administrator**, **reseller** and **client**. Each user opens it from their own Plesk panel and only ever sees their own sites.

| Level | Sees |
|---|---|
| **Administrator** | Every hosted site on the server |
| **Reseller** | Their own sites and those of their customers |
| **Client** | Only their own sites |

Every user has their own settings: API key, budget, sites to scan and notifications. Users without an API key of their own automatically use the administrator's key.

## Notifications

In **Settings → Notifications**, each user can switch on e-mail for when one of their sites changes status:

- **When a site becomes:** Infected, Suspicious, and/or Clean again (only after it was infected or suspicious, so a first clean result doesn't count).
- **Send to:** one e-mail address. Leave it empty to use the address of your Plesk account.

You don't get an e-mail per site. Changes are collected and sent as **one summary e-mail** with:

1. the status changes (old → new status, score, and the vendors that flag the site), and
2. the full list of your sites that are **infected right now**, plus the suspicious ones.

The summary goes out at the end of the nightly scan, and during the day 10 minutes after a change, so several changes share one e-mail. A site that flips back to its old status before the summary is sent is left out. **Send test e-mail** sends a sample summary with your current infected list.

Mail is sent with the server's own mail system (PHP `mail()` via sendmail), from the administrator's e-mail address. Keep in mind that a single vendor flagging a site can be a false alarm, so check a site before alarming its owner.

## Cleaned, but still red?

A red or orange site raises two questions for the webmaster: *who* says the site is unsafe, and *how* do I get that changed once the site is clean? VT Site Scanner answers both on the same screen, so you don't have to search for each vendor's support page yourself.

![Vendors that flag a site, with review links and the steps to get delisted](docs/screenshots/why-flagged.png)

The status in VT Site Scanner comes from **VirusTotal**, not from the files on your server. VirusTotal shows what dozens of security vendors think of a site, and **each vendor keeps its own blocklist** and updates it on its own schedule. So after you clean a site, it can stay *infected* or *suspicious* in the dashboard for a while: sometimes hours, often days, sometimes weeks, and some vendors only remove a site when you ask.

Click the **score** of a flagged site (for example *8/92 · Why?*). A second line opens with:

- **The vendors that flag the site** and what they call it (for example *Fortinet: malware*).
- **A review link per vendor**: a form or e-mail address where you can report that the site is clean. The contacts come from [VirusTotal's list of vendor contacts](https://docs.virustotal.com/docs/false-positive-contacts); where a vendor has its own form for websites, that form is used instead (for example [alphaMountain](https://www.alphamountain.ai/false-positive/) and [Gridinsoft](https://portal.gridinsoft.com/false-positive)). For a vendor that is not in the list, the link goes to VirusTotal's list.
- **The steps to get delisted:**
  1. **Clean the site first.** Use the file scanner buttons to find infected files. Update WordPress, plugins and themes, remove unused plugins, change all passwords (admin, FTP/SFTP, database, hosting) and remove unknown admin users. Otherwise the site is soon hacked and listed again.
  2. **Click Rescan.** VirusTotal checks the site address again with all vendors (one manual API call). The score in the dashboard comes from VirusTotal's report for the whole domain, which follows when the vendors update their own lists, so it does not always drop right away.
  3. **Ask the vendors that still flag it for a review**, using the links. Mention the site address, that it has been cleaned and what you did.
  4. **Google:** if Chrome shows a red warning, the site owner can request a review in [Google Search Console → Security issues](https://search.google.com/search-console/security-issues).
  5. **Rescan again after a few days** to see who has removed it. Infected and suspicious sites are also re-checked daily by the background scan. If a vendor says it has removed the site but VirusTotal still shows it, open the `www.` and `http://` versions of the address on virustotal.com and click **Reanalyze** on each: VirusTotal keeps a separate report for each version.

**Vendors that often flag websites**, and where to ask for a review (checked November 2026):

| Vendor | Where | Notes |
|---|---|---|
| alphaMountain.ai | [false-positive page](https://www.alphamountain.ai/false-positive/) | Ticket form; you have to log in or sign up. After approval it takes up to 24 hours to apply; then reanalyze on VirusTotal. Many site owners complain about slow answers. |
| Gridinsoft | [portal.gridinsoft.com/false-positive](https://portal.gridinsoft.com/false-positive) | Gridinsoft's own form for websites (not their support desk). Give the full address and proof the site was cleaned. Gridinsoft also looks at other vendors' verdicts, so ask the others first. |
| Fortinet | [Malicious URL Appeal](https://www.fortiguard.com/faq/malurl) | Web form: the URL, your name, e-mail and company, and a comment. (Not the "classification dispute" form, which is for software.) |
| Sophos | [Submit a sample](https://support.sophos.com/support/s/filesubmission) | Choose *Web Address (URL)*; no Sophos account needed. Usually 24–72 hours; you may not get a reply. |
| BitDefender | [bitdefender.com/submit](https://www.bitdefender.com/submit) | Choose *False positive* and *URL*; mention you own the site and add the VirusTotal link. |
| CRDF | [false-positive form](https://threatcenter.crdf.fr/false_positive.html) | Web form. |
| G-Data | [submit a URL](https://www.gdata.de/help/en/general/GeneralInformation/submitFileAppURL/) | Web form. |
| Lionic | [report a false positive](https://www.lionic.com/supports/report-false-positive/) | Web form. |
| Chong Lua Dao | [report a mistake](https://chongluadao.vn/en/report/reportmistake) | Complaint form for mislabelled sites. |
| Trustwave | [VirusTotal detection review](https://support.trustwave.com/virustotal-detection-review/) | Web form. |
| Bfore.Ai PreCrime | [ticket form](https://portal.bfore.ai/knowledge-base/kb-tickets/new) | Ticket form. |
| Webroot | [change request](https://support.threatintel.opentext.com/tools/change-request.php) | Web form. |
| SOCRadar | vt@socradar.io | No form; e-mail with the VirusTotal link. Site owners report that it works. |
| CyRadar | virustotal@cyradar.com | No form; e-mail with the VirusTotal link. |
| MalwareURL | team@malwareurl.com | No form; e-mail with the VirusTotal link. |
| Google Safe Browsing | [Search Console → Security issues](https://search.google.com/search-console/security-issues) | For the site owner; otherwise the [report form](https://safebrowsing.google.com/safebrowsing/report_error/). |

For vendors with only an e-mail address, the link in the extension opens an e-mail with the subject and a short message already filled in, including the VirusTotal link. If a vendor has confirmed the removal but VirusTotal still shows it after reanalyzing, you can send that confirmation to VirusTotal through its [false positive page](https://docs.virustotal.com/docs/false-positive).

The extension does not send review requests itself: each vendor has its own form or address, and a request should come from someone who can vouch that the site is clean.

## How it saves API calls

VirusTotal's free API allows **4 requests per minute** and **500 requests per day**.

| Mechanism | What it does |
|---|---|
| Global rate limiter | Counts every request per API key across all users and never sends more than 4/min or 500/day. |
| Result cache | Clean sites are re-checked after 30 days, infected or suspicious sites after 1 day (both configurable). Opening the dashboard reads from the cache and costs no API calls. |
| Daily budget split | By default 80% of the daily limit is reserved for the nightly scan and 20% for manual rescans. |
| Round-robin queue | The nightly scan continues where it stopped the night before, so even with hundreds of sites every site is still checked in turn. |
| One request at a time | Manual rescans go through a single queue, together with the dashboard's automatic first-time lookups. |

Every API call is a domain report lookup (`GET /domains/{domain}`), a re-analysis request (`POST /domains/{domain}/analyse`) or an analysis status check (`GET /analyses/{id}`). Only domain names are sent to VirusTotal, never site content or customer data.

---

## Requirements

- **Plesk Obsidian 18.0 or newer** on Linux, with root SSH access to the server
- Websites hosted in Plesk. Every active domain and subdomain with web hosting is checked, whatever it runs (WordPress, Joomla, plain HTML, …), plus WordPress sites on a hostname under a hosted domain. Forwarding-only domains, domains without hosting and suspended domains are skipped. The list is read from Plesk's own database. To check WordPress sites only, choose **Settings → Sites to scan → WordPress sites only**.
- A **VirusTotal API key**. The free key is enough: create an account at [virustotal.com](https://www.virustotal.com), then open your profile menu → **API key**.
- Optional: **ClamAV**, **maldet** and/or **ImunifyAV / Imunify360** for the file scans. The extension uses whichever are installed; none of them is required. You can switch each one off in **Settings → File scanners**.

The extension runs on Plesk's own bundled PHP (8.x), so the PHP version of your websites doesn't matter.

---

## Installation

### Step 1: Build the extension package

On any machine with `bash` and either `zip` or Python:

```bash
git clone https://github.com/dzaal/VT-Scanner-for-Plesk.git
cd VT-Scanner-for-Plesk
bash build.sh
```

This creates `dist/virustotal-extension-<version>.zip`.

> GitHub's green **Code → Download ZIP** button gives you the *source*, not an installable package. Always use `build.sh`, or a ready-made zip from the repository's **Releases** page if one is published.

### Step 2: Install it in Plesk

Choose **one** of the two options.

**Option A: from the command line (recommended)**

Copy the zip to the server and install it as root:

```bash
scp dist/virustotal-extension-*.zip root@your-plesk-server:/tmp/virustotal-extension.zip
```

```bash
ssh root@your-plesk-server "plesk bin extension --install /tmp/virustotal-extension.zip && rm /tmp/virustotal-extension.zip"
```

**Option B: through the Plesk interface**

Plesk only shows an upload button for your own extensions after you enable it once:

1. On the server, add these lines to `/usr/local/psa/admin/conf/panel.ini` (create the file if it doesn't exist):
   ```ini
   [ext-catalog]
   extensionUpload = true
   ```
2. In Plesk, go to **Extensions → My Extensions** and click **Upload Extension**.
3. Select the zip from step 1.

Installing registers the nightly background scan automatically. You don't need to set up a cron job.

### Step 3: Add your VirusTotal API key

1. In Plesk, open **VT Site Scanner** (in the left navigation, or under **Extensions → My Extensions**).
2. Go to the **Settings** tab.
3. Paste your API key and click **Save settings**.
4. Optional: choose **Sites to scan** (all hosted websites, or WordPress sites only), and adjust the daily limit (500 for the free tier, higher if you have a premium key), the background/manual split, and how long results are cached.

### Step 4: Check that it works

1. Open the **Dashboard** tab. Your hosted websites appear within a few seconds. Sites without a cached result are looked up gradually (at most 10 per page load, 4 per minute); the nightly scan handles the rest.
2. Click **Rescan** on any site. The **Scan queue** panel shows the scan moving through its steps.
3. Optional: switch on the summary e-mail in **Settings → Notifications** and click **Send test e-mail**.
4. Check that the nightly scan is scheduled:
   ```bash
   crontab -l -u psaadm | grep virustotal-extension
   ```
   You should see a line ending in `virustotal-extension/scripts/background_scan.php`.

To start a background scan straight away instead of waiting for 03:00:

```bash
sudo -u psaadm /usr/local/psa/admin/bin/php /usr/local/psa/admin/plib/modules/virustotal-extension/scripts/background_scan.php
```

> Always run it as `psaadm` with Plesk's PHP (`/usr/local/psa/admin/bin/php`). Running it as root leaves files the panel can't write to, and the operating system's own `php` is often too old for this code.

### Step 5 (recommended): Avoid two scanners sharing one quota

If Plesk's built-in **VirusTotal Website Check** is also active with the same API key, both draw from the same 500 requests per day, and the built-in one checks every hour. Turn it off in its own settings, or remove it:

```bash
plesk bin extension --uninstall website-virus-check
```

---

## Updating

Build the new version (step 1) and install it again (step 2). Plesk keeps your settings, API key and scan results, and the nightly task is re-registered automatically.

**Upgrading from 1.0.x?** Earlier versions asked you to add a line to root's crontab by hand. Remove that line (`crontab -e` as root, delete the line with `background_scan.php`); the extension now schedules itself.

## Uninstalling

```bash
plesk bin extension --uninstall virustotal-extension
```

This removes the extension and its scheduled task. To also delete all stored settings, results and logs (including the API key), remove the data directory:

```bash
rm -rf /usr/local/psa/var/modules/virustotal-extension
```

---

## Troubleshooting

| Problem | What to check |
|---|---|
| "No sites found" | Open the arrow menu next to **All** and click **Refresh site list** (the site list is cached for a day). Only active domains with web hosting are listed; forwarding-only and suspended domains are skipped. |
| "Invalid API key (401)" | Re-paste the key in **Settings**; make sure there are no spaces around it. |
| Many sites stay "Queued" | VirusTotal has never seen these domains; the first lookup submits them for a scan. They resolve on a later lookup or the next nightly run. |
| Rescans say "Daily manual rescan limit reached" | The manual share of today's budget is used up. It resets at midnight UTC, or raise the percentage in **Settings**. |
| The nightly scan doesn't seem to run | Run `crontab -l -u psaadm \| grep virustotal`, then run the scan by hand (step 4) to see its output. The **Log** tab shows every run. |

## Data and privacy

All data stays on your server in `/usr/local/psa/var/modules/virustotal-extension/data/`: settings (including the API key, readable only by the panel user), cached results, budget counters and logs. Nothing is stored in this repository. The only information sent to VirusTotal is the domain names of the sites being checked.

---

## Development

```
META_INF/meta.xml            Extension metadata (id, name, version)
htdocs/index.php             Entry point (Plesk MVC bootstrap)
plib/controllers/            IndexController: page + JSON actions
plib/views/scripts/index/    Dashboard UI (vanilla JS + CSS, no build step)
plib/library/                VirusTotal client, rate limiter, cache, settings, domain discovery
plib/library/Hook/           Home page card (HomeBlock)
plib/scripts/                Nightly scan + install/uninstall hooks (scheduler registration)
plib/hooks/Home.php          Registers the Home page card (Plesk SDK hook, returns an anonymous class)
_meta/icons/                 Extension icons shown in Plesk's Extensions list
docs/                        Logo and screenshots used in this README
build.sh                     Builds dist/virustotal-extension-<version>.zip
deploy.sh                    Build + install on a server over SSH
```

Build and install in one step:

```bash
VT_DEPLOY_HOST=root@your-plesk-server bash deploy.sh
```

Notes:
- Plesk routes `?action=save-settings` to `saveSettingsAction()` (dashes become camelCase).
- For JSON responses, disable the layout with `$this->_helper->layout->disableLayout()` and set the response body. Never use `header()` or `exit` inside `pm_Application`.

## Stack

- PHP 8 (Plesk's bundled sw-engine), Zend Framework 1 (Plesk's built-in MVC)
- VirusTotal API v3
- Vanilla JavaScript and CSS, no dependencies

## Author

**Dirk Zaal**, [Digizaal](https://www.digizaal.nl), Amsterdam

VT Site Scanner is free. If it saves you time or catches a hacked site, you can [buy me a cup of coffee ($3)](https://www.paypal.com/paypalme/zirk020/3USD) ☕
