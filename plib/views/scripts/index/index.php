<style>
/* VirusTotal Dashboard — inline styles (Plesk wraps this in its layout) */
#vt-wrap *{box-sizing:border-box}
#vt-wrap{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-size:14px;color:#1a1d23}
#vt-wrap :root,#vt-wrap{
  --bg:#f4f5f7;--surface:#fff;--border:#dde1e7;
  --text:#1a1d23;--muted:#6b7280;--heading:#111827;
  --red:#dc2626;--red-bg:#fef2f2;--red-border:#fca5a5;
  --green:#16a34a;--green-bg:#f0fdf4;--green-border:#86efac;
  --yellow:#d97706;--yellow-bg:#fffbeb;--yellow-border:#fcd34d;
  --blue:#2563eb;--blue-bg:#eff6ff;--blue-border:#93c5fd;
  --gray:#6b7280;--gray-bg:#f9fafb;
  --radius:8px;
}

/* Our branded header replaces Plesk's page header (breadcrumb + title); only on this page */
body:has(#vt-wrap) .pul-page-header.page-content-header{display:none}
#vt-wrap{padding-top:8px}

/* Brand header */
.vt-brand{display:flex;align-items:center;gap:14px;padding:14px 18px;margin-bottom:14px;border-radius:var(--radius);background:linear-gradient(135deg,#eff6ff 0%,#fff 60%);border:1px solid #dbe5f5}
.vt-brand-logo{width:44px;height:44px;flex-shrink:0;border-radius:10px;background:linear-gradient(180deg,#2563eb,#1e3a8a);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(37,99,235,.3)}
.vt-brand-logo svg{width:28px;height:28px}
.vt-brand-text{flex:1;min-width:0}
.vt-brand-name{font-size:16px;font-weight:700;color:var(--heading);line-height:1.3}
.vt-brand-ver{font-size:11px;font-weight:600;color:var(--blue);background:#dbeafe;border-radius:10px;padding:1px 7px;vertical-align:2px;margin-left:4px}
.vt-brand-tag{font-size:12px;color:var(--muted);margin-top:2px}
.vt-brand-by{flex-shrink:0;font-size:12px;color:var(--muted);text-decoration:none;padding:6px 12px;border:1px solid var(--border);border-radius:20px;background:#fff}
.vt-brand-by strong{color:#b91c1c;font-weight:700;letter-spacing:.2px}
.vt-brand-by:hover{border-color:#b91c1c}
@media (max-width:640px){.vt-brand-tag{display:none}}

/* Tabs */
.vt-tabs{display:flex;border-bottom:2px solid var(--border);margin-bottom:16px}
.vt-tab{padding:10px 18px;font-size:13px;font-weight:500;color:var(--muted);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px}
.vt-tab:hover{color:var(--text)}
.vt-tab.active{color:var(--blue);border-bottom-color:var(--blue);font-weight:600}

.vt-panel{display:none}.vt-panel.active{display:block}

/* Alert */
.vt-alert{padding:12px 16px;border-radius:var(--radius);font-size:13px;margin-bottom:14px;display:flex;align-items:center;gap:8px}
.vt-alert.warn{background:var(--yellow-bg);border:1px solid var(--yellow-border);color:#92400e}
.vt-alert a{font-weight:600;color:inherit;text-decoration:underline;cursor:pointer}

/* Budget strip */
.vt-budget{display:flex;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);margin-bottom:14px;overflow:hidden}
.vt-b-cell{flex:1;padding:12px 16px;border-right:1px solid var(--border)}
.vt-b-cell:last-child{border-right:none}
.vt-b-label{font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px}
.vt-b-num{font-size:20px;font-weight:800;color:var(--heading);line-height:1}
.vt-b-sub{font-size:11px;color:var(--muted);margin-top:2px}
.vt-b-track{height:4px;background:var(--border);border-radius:2px;margin-top:7px;overflow:hidden}
.vt-b-fill{height:100%;background:var(--green);border-radius:2px;transition:width .4s}
.vt-b-fill.warn{background:var(--yellow)}.vt-b-fill.crit{background:var(--red)}

/* Toolbar */
.vt-toolbar{display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap}
.vt-search{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:6px 11px;color:var(--text);font-size:13px;width:190px;outline:none}
.vt-search:focus{border-color:var(--blue)}
.vt-filters{display:flex;gap:4px;flex-wrap:wrap}
.vt-fbtn{padding:4px 10px;border-radius:20px;border:1px solid var(--border);background:var(--surface);font-size:12px;font-weight:500;cursor:pointer;color:var(--muted);display:flex;align-items:center;gap:4px}
.vt-fbtn:hover{border-color:var(--blue);color:var(--blue)}
.vt-fbtn.active{background:var(--blue);border-color:var(--blue);color:#fff}
.vt-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;display:inline-block}
.dot-r{background:var(--red);box-shadow:0 0 0 2px #fca5a5}
.dot-y{background:#f59e0b;box-shadow:0 0 0 2px #fde68a}
.dot-g{background:var(--green);box-shadow:0 0 0 2px #86efac}
.dot-b{background:var(--blue)}.dot-gr{background:var(--gray)}
@keyframes vt-pulse{0%,100%{opacity:1}50%{opacity:.4}}
.dot-r{animation:vt-pulse 1.4s ease-in-out infinite}
.vt-spacer{flex:1}
.vt-btn{padding:6px 12px;border-radius:var(--radius);border:1px solid transparent;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:4px}
.vt-btn:disabled{opacity:.4;cursor:not-allowed}
.vt-btn.primary{background:var(--blue);color:#fff;border-color:var(--blue)}
.vt-btn.primary:hover:not(:disabled){background:#1d4ed8}
.vt-btn.ghost{background:var(--surface);color:var(--muted);border-color:var(--border)}
.vt-btn.ghost:hover:not(:disabled){color:var(--text)}
.vt-btn.danger{background:var(--red-bg);color:var(--red);border-color:var(--red-border)}
.vt-status-txt{font-size:12px;color:var(--muted)}

/* Progress */
.vt-prog-wrap{height:2px;background:var(--border);margin-bottom:1px}
.vt-prog-fill{height:100%;background:var(--blue);transition:width .4s;width:0}

/* Table */
.vt-tbl-wrap{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.vt-tbl-wrap table{width:100%;border-collapse:collapse}
.vt-tbl-wrap thead th{padding:9px 13px;text-align:left;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;background:var(--gray-bg);border-bottom:1px solid var(--border);white-space:nowrap}
.vt-tbl-wrap tbody tr{border-bottom:1px solid var(--border);transition:background .1s;border-left:3px solid transparent}
.vt-tbl-wrap tbody tr:last-child{border-bottom:none}
.vt-tbl-wrap tbody tr:hover{background:#fafbfc}
.vt-tbl-wrap tbody tr[data-status=infected]{background:#fff5f5;border-left-color:var(--red)}
.vt-tbl-wrap tbody tr[data-status=infected]:hover{background:#fee2e2}
.vt-tbl-wrap tbody tr[data-status=suspicious]{background:#fffbeb;border-left-color:#f59e0b}
.vt-tbl-wrap tbody tr[data-status=suspicious]:hover{background:#fef3c7}
.vt-tbl-wrap tbody td{padding:9px 13px;vertical-align:middle}
.vt-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap}
.vt-badge .vt-dot{width:7px;height:7px;border-radius:50%}
.vt-badge.infected  {background:var(--red-bg);   color:var(--red);   border:1px solid var(--red-border)}
.vt-badge.suspicious{background:var(--yellow-bg);color:var(--yellow);border:1px solid var(--yellow-border)}
.vt-badge.clean     {background:var(--green-bg);  color:var(--green); border:1px solid var(--green-border)}
.vt-badge.pending   {background:var(--blue-bg);   color:var(--blue);  border:1px solid var(--blue-border)}
.vt-badge.scanning,.vt-badge.waiting{background:var(--blue-bg);color:var(--blue);border:1px solid var(--blue-border)}
.vt-badge.loading,.vt-badge.error,.vt-badge.ratelimit,.vt-badge.quota_exceeded,.vt-badge.budget_exceeded{background:var(--gray-bg);color:var(--gray);border:1px solid var(--border)}
.vt-domain-name{font-weight:600;font-size:13px;color:var(--heading)}
.vt-domain-ip{font-size:11px;color:var(--muted);margin-top:1px}
.vt-domain-age{display:inline-block;font-size:10px;padding:1px 5px;border-radius:4px;font-weight:600;margin-left:4px;vertical-align:middle}
.vt-domain-age.new-30d{background:var(--red-bg);color:var(--red);border:1px solid var(--red-border)}
.vt-domain-age.new-1y{background:var(--yellow-bg);color:var(--yellow);border:1px solid var(--yellow-border)}
.vt-tag{display:inline-block;font-size:10px;padding:1px 5px;border-radius:4px;font-weight:600;margin:1px 1px 0 0;background:var(--red-bg);color:var(--red);border:1px solid var(--red-border)}
.vt-dlinks{display:inline-flex;gap:5px;margin-left:7px}
.vt-dlinks a{font-size:11px;color:var(--blue);text-decoration:none;padding:1px 5px;border:1px solid var(--blue-border);border-radius:4px;background:var(--blue-bg)}
.vt-dlinks a:hover{background:var(--blue);color:#fff}
.vt-score{display:flex;align-items:center;gap:7px}
.vt-sbar{flex:1;height:5px;background:var(--border);border-radius:3px;overflow:hidden;min-width:50px}
.vt-sbar-fill{height:100%;border-radius:3px}
.vt-sbar-fill.infected{background:var(--red)}.vt-sbar-fill.clean{background:var(--green)}
.vt-snum{font-size:12px;font-weight:600;min-width:38px;text-align:right;color:var(--muted)}
.vt-snum.infected{color:var(--red);font-size:13px}
.vt-snum.clean{color:var(--green)}
.vt-row-btn{padding:4px 9px;border-radius:5px;border:1px solid var(--border);background:var(--surface);color:var(--muted);font-size:11px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:4px}
.vt-row-btn:hover:not(:disabled){color:var(--blue);border-color:var(--blue-border);background:var(--blue-bg)}
.vt-row-btn:disabled{opacity:.5;cursor:not-allowed}
.vt-spin{display:inline-block;width:10px;height:10px;border:2px solid var(--border);border-top-color:var(--blue);border-radius:50%;animation:vtspin .6s linear infinite}
.vt-empty td{text-align:center;padding:40px;color:var(--muted)}
.vt-stale{font-size:10px;color:var(--yellow);font-weight:600;margin-left:4px}

/* Scan queue panel */
.vt-queue{background:var(--surface);border:1px solid var(--blue-border);border-radius:var(--radius);margin-bottom:12px;overflow:hidden;display:none}
.vt-q-head{display:flex;align-items:center;gap:10px;padding:9px 13px;background:var(--blue-bg);border-bottom:1px solid var(--blue-border)}
.vt-q-title{font-size:12px;font-weight:700;color:var(--blue);text-transform:uppercase;letter-spacing:.4px}
.vt-q-sum{font-size:12px;color:var(--muted)}
.vt-q-head .vt-row-btn{margin-left:4px}
.vt-q-list{max-height:320px;overflow-y:auto}
.vt-q-item{display:flex;align-items:center;gap:12px;padding:8px 13px;border-bottom:1px solid var(--border);font-size:13px}
.vt-q-item:last-child{border-bottom:none}
.vt-q-item.done,.vt-q-item.failed{background:var(--gray-bg)}
.vt-q-icon{width:26px;text-align:center;flex-shrink:0;font-weight:700;font-size:12px;color:var(--muted)}
.vt-q-icon.ok{color:var(--green)}.vt-q-icon.bad{color:var(--red)}
.vt-q-main{flex:1;min-width:0}
.vt-q-dom{font-weight:600;color:var(--heading)}
.vt-q-kind{font-size:10px;font-weight:600;padding:1px 5px;border-radius:4px;margin-left:6px;background:var(--gray-bg);border:1px solid var(--border);color:var(--muted);vertical-align:middle}
.vt-q-msg{font-size:12px;color:var(--muted);margin-top:1px}
.vt-q-steps{display:flex;gap:3px;margin-top:5px}
.vt-q-steps span{font-size:10px;padding:1px 6px;border-radius:10px;background:var(--gray-bg);border:1px solid var(--border);color:var(--muted)}
.vt-q-steps span.done{background:var(--green-bg);border-color:var(--green-border);color:var(--green)}
.vt-q-steps span.on{background:var(--blue);border-color:var(--blue);color:#fff}
.vt-q-steps span.bad{background:var(--red-bg);border-color:var(--red-border);color:var(--red)}
.vt-q-pbar{height:3px;background:var(--border);border-radius:2px;margin-top:5px;overflow:hidden;max-width:260px}
.vt-q-pbar div{height:100%;background:var(--blue);transition:width .4s}
.vt-q-right{text-align:right;font-size:12px;color:var(--muted);white-space:nowrap}
.vt-q-right .vt-badge{margin-bottom:2px}

/* Settings */
.vt-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px;max-width:600px;margin-bottom:14px}
.vt-card h3{font-size:14px;font-weight:700;color:var(--heading);margin-bottom:14px;padding-bottom:9px;border-bottom:1px solid var(--border)}
.vt-field{margin-bottom:14px}
.vt-field label{display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.4px}
.vt-field input[type=text],.vt-field input[type=number]{width:100%;padding:8px 11px;border:1px solid var(--border);border-radius:var(--radius);font-size:13px;color:var(--text);outline:none;background:var(--surface);font-family:monospace}
.vt-field input:focus{border-color:var(--blue)}
.vt-field .hint{font-size:11px;color:var(--muted);margin-top:4px}
.vt-field-row{display:flex;gap:10px}
.vt-field-row .vt-field{flex:1}
.vt-save-row{display:flex;align-items:center;gap:10px;margin-top:4px}
.vt-smsg{font-size:12px;font-weight:600;display:none}
.vt-smsg.ok{color:var(--green)}.vt-smsg.err{color:var(--red)}
.vt-key-display{margin-top:8px;padding:8px 11px;background:var(--gray-bg);border:1px solid var(--border);border-radius:var(--radius);font-family:monospace;font-size:12px;color:var(--text);word-break:break-all;display:none}
.vt-key-display .vt-key-label{font-size:10px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px}

/* Radio options */
.vt-radio{display:flex;align-items:flex-start;gap:10px;padding:8px 0;cursor:pointer;font-size:13px}
.vt-radio input{margin-top:3px}
.vt-radio .hint{font-size:12px;color:var(--muted)}

/* Toasts */
.vt-toasts{position:fixed;bottom:20px;right:20px;display:flex;flex-direction:column;gap:6px;z-index:9999;pointer-events:none}
.vt-toast{background:#fff;border:1px solid #dde1e7;border-radius:var(--radius);padding:10px 14px;font-size:13px;animation:vt-tin .2s ease;max-width:320px;box-shadow:0 4px 16px rgba(0,0,0,.18);line-height:1.4}
.vt-toast.ok  {background:#f0fdf4;border-color:#86efac;color:#166534}
.vt-toast.err {background:#fef2f2;border-color:#fca5a5;color:#991b1b}
.vt-toast.info{background:#eff6ff;border-color:#93c5fd;color:#1e40af}
.vt-toast.warn{background:#fffbeb;border-color:#fcd34d;color:#92400e}
@keyframes vtspin{to{transform:rotate(360deg)}}
@keyframes vt-tin{from{transform:translateX(16px);opacity:0}to{transform:translateX(0);opacity:1}}
</style>

<div id="vt-wrap">

  <div class="vt-brand">
    <div class="vt-brand-logo" aria-hidden="true">
      <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M8 1L2 3.5V8C2 11.5 4.5 14.5 8 15.5C11.5 14.5 14 11.5 14 8V3.5L8 1Z" fill="#fff"/>
        <path d="M5.6 8.2L7.4 10L10.6 6.6" stroke="#2563eb" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <div class="vt-brand-text">
      <div class="vt-brand-name">VT Site Scanner<?php if ($this->version): ?> <span class="vt-brand-ver">v<?= htmlspecialchars($this->version) ?></span><?php endif; ?></div>
      <div class="vt-brand-tag">Malware, phishing and spam monitoring for every hosted website on this server, powered by VirusTotal</div>
    </div>
    <a class="vt-brand-by" href="https://www.digizaal.nl" target="_blank" rel="noopener">
      <span>by</span> <strong>Digizaal</strong>
    </a>
  </div>

  <div class="vt-tabs">
    <div class="vt-tab active" id="vtab-dashboard" onclick="vtShowTab('dashboard')">Dashboard</div>
    <div class="vt-tab" id="vtab-settings" onclick="vtShowTab('settings')">Settings</div>
    <div class="vt-tab" id="vtab-log" onclick="vtShowTab('log');vtLoadLog()">Log</div>
  </div>

  <!-- Dashboard -->
  <div class="vt-panel active" id="vtpanel-dashboard">

    <div class="vt-alert warn" id="vt-api-warning" style="display:none">
      No valid API key configured or key is invalid (HTTP 401) &mdash; scans will not work.
      <a onclick="vtShowTab('settings')" style="cursor:pointer">Configure in Settings &rarr;</a>
    </div>

    <div class="vt-budget">
      <div class="vt-b-cell">
        <div class="vt-b-label">Auto scans today</div>
        <div class="vt-b-num" id="vt-auto-num">—</div>
        <div class="vt-b-sub" id="vt-auto-sub">of — calls</div>
        <div class="vt-b-track"><div class="vt-b-fill" id="vt-auto-bar" style="width:0%"></div></div>
      </div>
      <div class="vt-b-cell">
        <div class="vt-b-label">Manual remaining</div>
        <div class="vt-b-num" id="vt-manual-num">—</div>
        <div class="vt-b-sub" id="vt-manual-sub">of — available</div>
        <div class="vt-b-track"><div class="vt-b-fill" id="vt-manual-bar" style="width:0%"></div></div>
      </div>
      <div class="vt-b-cell">
        <div class="vt-b-label">VT quota used today</div>
        <div class="vt-b-num" id="vt-quota-num">—</div>
        <div class="vt-b-sub" id="vt-quota-sub">of 500 requests</div>
        <div class="vt-b-track"><div class="vt-b-fill" id="vt-quota-bar" style="width:0%"></div></div>
      </div>
      <div class="vt-b-cell">
        <div class="vt-b-label">Daily limit</div>
        <div class="vt-b-num" id="vt-daily-total">—</div>
        <div class="vt-b-sub">requests per day</div>
      </div>
      <div class="vt-b-cell">
        <div class="vt-b-label">Date</div>
        <div class="vt-b-num" id="vt-budget-date" style="font-size:14px;padding-top:3px">—</div>
        <div class="vt-b-sub">resets at midnight UTC</div>
      </div>
    </div>

    <div class="vt-toolbar">
      <input class="vt-search" type="text" id="vt-search" placeholder="Search domain..." oninput="vtApplyFilter()">
      <div class="vt-filters">
        <button class="vt-fbtn active" data-f="all"        onclick="vtSetFilter('all')">        All <strong id="vt-cnt-all">—</strong></button>
        <button class="vt-fbtn"        data-f="infected"   onclick="vtSetFilter('infected')">   <span class="vt-dot dot-r"></span> Infected <strong id="vt-cnt-infected">0</strong></button>
        <button class="vt-fbtn"        data-f="suspicious" onclick="vtSetFilter('suspicious')"> <span class="vt-dot dot-y"></span> Suspicious <strong id="vt-cnt-suspicious">0</strong></button>
        <button class="vt-fbtn"        data-f="clean"      onclick="vtSetFilter('clean')">      <span class="vt-dot dot-g"></span> Clean <strong id="vt-cnt-clean">0</strong></button>
        <button class="vt-fbtn"        data-f="pending"    onclick="vtSetFilter('pending')">    <span class="vt-dot dot-b"></span> Queued <strong id="vt-cnt-pending">0</strong></button>
        <button class="vt-fbtn"        data-f="error"      onclick="vtSetFilter('error')">      <span class="vt-dot dot-gr"></span> Error <strong id="vt-cnt-error">0</strong></button>
      </div>
      <div class="vt-spacer"></div>
      <span class="vt-status-txt" id="vt-scan-status"></span>
      <button class="vt-btn ghost" onclick="vtRefreshDomains()">Refresh</button>
      <button class="vt-btn danger" id="vt-btn-reanalyze" onclick="vtReanalyzeInfected()" disabled>Rescan infected</button>
    </div>

    <div class="vt-queue" id="vt-queue">
      <div class="vt-q-head">
        <span class="vt-q-title">Scan queue</span>
        <span class="vt-q-sum" id="vt-q-sum"></span>
        <div class="vt-spacer"></div>
        <button class="vt-row-btn" id="vt-q-cancel" onclick="vtCancelWaiting()">Cancel waiting</button>
        <button class="vt-row-btn" id="vt-q-clear" onclick="vtClearFinished()">Clear finished</button>
      </div>
      <div class="vt-q-list" id="vt-q-list"></div>
    </div>

    <div class="vt-prog-wrap"><div class="vt-prog-fill" id="vt-prog"></div></div>

    <div class="vt-tbl-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:120px">Status</th>
            <th>Domain</th>
            <th style="width:155px">Score</th>
            <th style="width:135px">Last scan</th>
            <th style="width:85px;text-align:right">Action</th>
          </tr>
        </thead>
        <tbody id="vt-tbody"><tr class="vt-empty"><td colspan="5">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>

  <!-- Log -->
  <div class="vt-panel" id="vtpanel-log">
    <div class="vt-card" style="max-width:100%">
      <h3 style="display:flex;align-items:center;gap:10px">
        Scan Log
        <span style="font-size:11px;font-weight:400;color:var(--muted)" id="vt-log-meta"></span>
        <div style="flex:1"></div>
        <button class="vt-btn ghost" onclick="vtLoadLog()">Refresh</button>
        <button class="vt-btn danger" onclick="vtClearLog()" style="margin-left:4px">Clear log</button>
      </h3>
      <pre id="vt-log-pre" style="margin:0;padding:10px;background:var(--gray-bg);border:1px solid var(--border);border-radius:var(--radius);font-size:11px;line-height:1.6;overflow-x:auto;white-space:pre-wrap;max-height:600px;overflow-y:auto;color:var(--text)">Loading...</pre>
    </div>
  </div>

  <!-- Settings -->
  <div class="vt-panel" id="vtpanel-settings">
    <div class="vt-card">
      <h3>Sites to scan</h3>
      <label class="vt-radio"><input type="radio" name="vt-s-scope" id="vt-s-scope-all" value="all" checked>
        <span><strong>All hosted websites</strong><br><span class="hint">Every active domain and subdomain with web hosting, whatever it runs</span></span></label>
      <label class="vt-radio"><input type="radio" name="vt-s-scope" id="vt-s-scope-wp" value="wp">
        <span><strong>WordPress sites only</strong><br><span class="hint">Only sites with a WordPress database on this server</span></span></label>
    </div>

    <div class="vt-card">
      <h3>VirusTotal API Settings</h3>
      <div class="vt-field">
        <label>API Key</label>
        <input type="text" id="vt-s-apikey" placeholder="Paste your VirusTotal API key here..." autocomplete="off" spellcheck="false">
        <div class="hint">Get your free key at virustotal.com &rarr; Profile &rarr; API key</div>
        <div class="vt-key-display" id="vt-key-display">
          <div class="vt-key-label">Stored key</div>
          <div id="vt-key-value">—</div>
        </div>
      </div>
      <div class="vt-field-row">
        <div class="vt-field">
          <label>Daily API limit</label>
          <input type="number" id="vt-s-daily" min="1" max="1000000" value="500">
          <div class="hint">500 = free tier &nbsp;|&nbsp; 1000+ = premium</div>
        </div>
        <div class="vt-field">
          <label>Background scan (%)</label>
          <input type="number" id="vt-s-share" min="10" max="95" value="80" step="5">
          <div class="hint">% reserved for nightly cron scan</div>
        </div>
      </div>
      <div class="vt-field-row">
        <div class="vt-field">
          <label>Cache clean sites (days)</label>
          <input type="number" id="vt-s-clean-ttl" min="1" max="365" value="30">
        </div>
        <div class="vt-field">
          <label>Cache infected sites (days)</label>
          <input type="number" id="vt-s-inf-ttl" min="1" max="30" value="1">
        </div>
      </div>
      <div class="vt-save-row">
        <button class="vt-btn primary" onclick="vtSaveSettings()">Save settings</button>
        <span class="vt-smsg" id="vt-smsg"></span>
      </div>
    </div>

    <div class="vt-card">
      <h3>API usage today</h3>
      <div style="display:flex;gap:24px;flex-wrap:wrap">
        <div>
          <div class="vt-b-label" style="margin-bottom:4px">Auto scans (cron)</div>
          <div style="font-size:18px;font-weight:800" id="vts-auto-label">—</div>
          <div class="vt-b-track" style="width:150px;margin-top:5px"><div class="vt-b-fill" id="vts-auto-bar" style="width:0%"></div></div>
        </div>
        <div>
          <div class="vt-b-label" style="margin-bottom:4px">Manual (Rescan)</div>
          <div style="font-size:18px;font-weight:800" id="vts-manual-label">—</div>
          <div class="vt-b-track" style="width:150px;margin-top:5px"><div class="vt-b-fill" id="vts-manual-bar" style="width:0%"></div></div>
        </div>
        <div>
          <div class="vt-b-label" style="margin-bottom:4px">Date</div>
          <div style="font-size:18px;font-weight:800" id="vts-date">—</div>
        </div>
      </div>
    </div>
  </div>

</div><!-- #vt-wrap -->

<div class="vt-toasts" id="vt-toasts"></div>

<script>
(function(){
const BASE = <?= json_encode($this->baseUrl ?: '') ?> || location.pathname;
let domains=[], results={}, budget={}, activeFilter='all';
let scanQueue=[], inFlight=0;
const MAX_C=1;          // one at a time to stay within VT rate limits
const PUMP_DELAY=15500; // 15.5s between requests = safe for 4 req/min free tier
const MAX_PUMP=10;      // max domains to auto-scan per page load (cron handles bulk)
const LABELS={
  infected:'Infected',suspicious:'Suspicious',clean:'Clean',
  loading:'Loading',pending:'Queued',error:'Error',
  ratelimit:'Rate limit',quota_exceeded:'Quota exceeded',budget_exceeded:'Daily limit',
  scanning:'Scanning...',waiting:'VT processing...'
};
const DOT_CLS={infected:'dot-r',suspicious:'dot-y',clean:'dot-g',pending:'dot-b'};
// Dangerous VT tags to highlight
const DANGER_TAGS=['malware','phishing','spam','ransomware','trojan','malicious','phishing-site','malware-site'];

// ── Init ──────────────────────────────────────────────────────
async function init(){
  const data=await vtFetch('?action=all').catch(()=>({}));
  domains=data.domains||[]; results=data.results||{}; budget=data.budget||{};
  vtSet('vt-cnt-all', domains.length);
  updateBudget(); renderTable(); updateCounts();

  const cfg=await vtFetch('?action=get-settings').catch(()=>({}));
  if(!cfg.has_key||cfg.api_key_invalid) document.getElementById('vt-api-warning').style.display='flex';
  loadSettingsForm(cfg);

  // Auto-pump: only scan truly unseen domains (no cache at all) + errors
  // Cron handles bulk stale refresh; web UI just fills gaps
  scanQueue=domains
    .filter(d=>!results[d]||['error','ratelimit','pending'].includes(results[d].status))
    .slice(0, MAX_PUMP);
  pump();
}

function isStale(d){
  if(!results[d]||!results[d].cached_at) return true;
  const st=results[d].status;
  if(st==='error'||st==='ratelimit') return true;
  const ttl=['infected','suspicious'].includes(st)?86400:30*86400;
  return (Date.now()/1000-results[d].cached_at)>ttl;
}

// ── Tabs ──────────────────────────────────────────────────────
window.vtShowTab=function(name){
  document.querySelectorAll('.vt-tab').forEach(t=>t.classList.remove('active'));
  document.querySelectorAll('.vt-panel').forEach(p=>p.classList.remove('active'));
  document.getElementById('vtab-'+name).classList.add('active');
  document.getElementById('vtpanel-'+name).classList.add('active');
};

// ── Scan pump ─────────────────────────────────────────────────
// Sequential with delay — stays within VT free tier (4 req/min).
// Manual rescans bypass the pump entirely (they use vtReanalyzeSingle directly).
let pumpRunning=false, pumpCurrent=null, pumpWaitUntil=0;
async function pump(){
  if(pumpRunning) return;
  pumpRunning=true;
  while(scanQueue.length){
    const d=scanQueue.shift();
    const st=results[d]?.status;
    if(activeJob(d)) continue; // being manually rescanned
    if(st&&st!=='error'&&st!=='ratelimit'&&st!=='quota_exceeded'&&st!=='pending') continue; // already has a valid result
    inFlight=1; pumpCurrent=d;
    updateProgress();
    try{
      const data=await withVtSlot(()=>vtFetch('?action=scan&domain='+encodeURIComponent(d)));
      if(data.status==='ratelimit'){
        // Per-minute limit (shared with manual rescans): wait it out and retry
        scanQueue.unshift(d);
        const wait=Math.max(5,Number(data.wait||data.retry_after||60));
        inFlight=0; pumpCurrent=null; pumpWaitUntil=Date.now()+wait*1000; updateProgress();
        await delay(wait*1000);
        pumpWaitUntil=0;
        continue;
      }
      results[d]=data; updateRow(d,data); updateCounts();
      if(data.status==='quota_exceeded'){ scanQueue=[]; break; }
      // Bump counters locally — no extra server round-trip needed
      if(data.status!=='error'){ budget.auto_used=(budget.auto_used??0)+1; budget.vt_quota_used=(budget.vt_quota_used??0)+1; updateBudget(); }
    }catch(e){
      results[d]={status:'error',domain:d}; updateRow(d,results[d]);
    }
    inFlight=0; pumpCurrent=null;
    updateProgress();
    if(scanQueue.length) await delay(PUMP_DELAY); // respect 4 req/min limit
  }
  pumpRunning=false; pumpCurrent=null;
  updateProgress();
}

function updateProgress(){
  const remaining=scanQueue.length+inFlight;
  const loaded=domains.filter(d=>results[d]&&!['loading','scanning','waiting'].includes(results[d].status)).length;
  document.getElementById('vt-prog').style.width=(domains.length?loaded/domains.length*100:0)+'%';
  document.getElementById('vt-scan-status').textContent=remaining>0?('Loading: '+remaining+' remaining...'):(domains.length?'All loaded':'');
  renderQueue();
}

// ── Budget ────────────────────────────────────────────────────
function updateBudget(){
  if(!budget.auto_max) return;
  const au=budget.auto_used??0, am=budget.auto_max;
  const mu=budget.manual_used??0, mm=budget.manual_max;
  vtSet('vt-auto-num',    au);
  vtSet('vt-auto-sub',    'of '+am+' calls');
  vtSet('vt-manual-num',  mm-mu);
  vtSet('vt-manual-sub',  'of '+mm+' available');
  vtSet('vt-daily-total', am+mm);
  vtSet('vt-budget-date', budget.date??'—');
  vtBar('vt-auto-bar',   Math.round(au/am*100));
  vtBar('vt-manual-bar', Math.round(mu/mm*100));
  const qused=budget.vt_quota_used??0;
  vtSet('vt-quota-num', qused);
  vtSet('vt-quota-sub', 'of 500 requests');
  vtBar('vt-quota-bar', Math.round(qused/500*100));
  vtSet('vts-auto-label',   au+' / '+am);
  vtSet('vts-manual-label', mu+' / '+mm);
  vtSet('vts-date', budget.date??'—');
  vtBar('vts-auto-bar',   Math.round(au/am*100));
  vtBar('vts-manual-bar', Math.round(mu/mm*100));
}

function vtBar(id,pct){
  const el=document.getElementById(id);
  if(!el) return;
  el.style.width=pct+'%';
  el.className='vt-b-fill'+(pct>85?' crit':pct>65?' warn':'');
}

// ── Table ─────────────────────────────────────────────────────
function renderTable(){
  const tbody=document.getElementById('vt-tbody');
  if(!domains.length){tbody.innerHTML='<tr class="vt-empty"><td colspan="5">No sites found. Click Refresh.</td></tr>';return;}
  tbody.innerHTML=domains.map(d=>rowHTML(d,results[d]||null)).join('');
  vtApplyFilter();
}

function updateRow(domain,data){
  const row=document.getElementById('vtr-'+rid(domain));
  if(!row){renderTable();return;}
  row.outerHTML=rowHTML(domain,data);
  vtApplyFilter();
}

function rid(d){return d.replace(/[^a-z0-9]/gi,'_');}

function rowHTML(domain,data){
  const st=data?.status??'loading';
  const isScanning=st==='scanning'||st==='waiting';
  const dc=DOT_CLS[st]??'';
  const job=activeJob(domain);
  let badge;
  if(job){
    badge=job.state==='queued'
      ?'<span class="vt-badge pending"><span class="vt-dot dot-b"></span>In queue #'+queuePos(job)+'</span>'
      :'<span class="vt-badge scanning"><span class="vt-spin"></span> '+JOB_SHORT[job.state]+'</span>';
  }else if(isScanning){
    badge='<span class="vt-badge '+st+'"><span class="vt-spin"></span> '+(LABELS[st]??st)+'</span>';
  }else{
    const dot=dc?'<span class="vt-dot '+dc+'"></span>':'';
    badge='<span class="vt-badge '+st+'">'+dot+(LABELS[st]??st)+'</span>';
  }

  // Dangerous tags
  const dangerTags=(data?.tags??[]).filter(t=>DANGER_TAGS.some(k=>t.toLowerCase().includes(k)));
  const tagHtml=dangerTags.length?'<div style="margin-top:3px">'+dangerTags.slice(0,3).map(t=>'<span class="vt-tag">'+vtEsc(t)+'</span>').join('')+'</div>':'';
  const statusCell=badge+tagHtml;

  // Domain age badge
  let ageHtml='';
  if(data?.creation_date){
    const ageInDays=(Date.now()/1000-data.creation_date)/86400;
    if(ageInDays<30) ageHtml='<span class="vt-domain-age new-30d">&lt; 30 days old</span>';
    else if(ageInDays<365) ageHtml='<span class="vt-domain-age new-1y">&lt; 1 year old</span>';
  }
  // IP address
  const ipHtml=data?.ip?'<div class="vt-domain-ip">'+vtEsc(data.ip)+'</div>':'';
  const vtId=btoa('https://'+domain).replace(/=/g,'');
  const domainCell='<span class="vt-domain-name">'+vtEsc(domain)+'</span>'+ageHtml+'<span class="vt-dlinks"><a href="https://'+vtEsc(domain)+'" target="_blank" rel="noopener">Site</a><a href="https://www.virustotal.com/gui/domain/'+vtEsc(domain)+'/detection" target="_blank" rel="noopener">VT</a></span>'+ipHtml;

  let score='<span style="color:var(--muted)">—</span>';
  if(data?.malicious!==undefined&&data?.total!==undefined){
    const pct=data.total>0?Math.round(data.malicious/data.total*100):0;
    const c=data.malicious>0?'infected':'clean';
    const scoreNum=data.malicious>0?'<strong>'+data.malicious+'</strong>/'+data.total:data.malicious+'/'+data.total;
    score='<div class="vt-score"><div class="vt-sbar"><div class="vt-sbar-fill '+c+'" style="width:'+pct+'%"></div></div><span class="vt-snum '+c+'">'+scoreNum+'</span></div>';
  }else if(isScanning){score='<span class="vt-spin"></span>';}
  else if(st==='loading'){score='<span class="vt-spin"></span>';}
  else if(st==='error'){score='<span style="color:var(--red);font-size:12px">No result</span>';}

  let scanDate='—';
  if(data?.last_scan) scanDate=vtEsc(data.last_scan)+(data?.stale?'<span class="vt-stale">stale</span>':'');
  else if(st==='loading'||isScanning) scanDate='<span class="vt-spin"></span>';
  else if(st==='pending') scanDate='Queued...';

  // Rescan button: show spinner while scanning/waiting
  let actionBtn;
  if(job){
    actionBtn='<button class="vt-row-btn" disabled>'+(job.state==='queued'?'Queued':'<span class="vt-spin"></span> Busy')+'</button>';
  }else if(isScanning){
    const lbl=st==='waiting'?'VT processing...':'Scanning...';
    actionBtn='<button class="vt-row-btn" disabled><span class="vt-spin"></span> '+lbl+'</button>';
  }else{
    actionBtn='<button class="vt-row-btn" id="vrb-'+rid(domain)+'" onclick="vtReanalyzeSingle(\''+vtEsca(domain)+'\')">Rescan</button>';
  }

  return '<tr id="vtr-'+rid(domain)+'" data-domain="'+vtEsc(domain)+'" data-status="'+st+'"'+(job?' data-job="1"':'')+'>'
    +'<td>'+statusCell+'</td>'
    +'<td>'+domainCell+'</td>'
    +'<td>'+score+'</td>'
    +'<td style="font-size:12px;color:var(--muted)">'+scanDate+'</td>'
    +'<td style="text-align:right">'+actionBtn+'</td>'
    +'</tr>';
}

// ── Filter ────────────────────────────────────────────────────
window.vtSetFilter=function(f){
  activeFilter=f;
  document.querySelectorAll('.vt-fbtn').forEach(b=>b.classList.toggle('active',b.dataset.f===f));
  vtApplyFilter();
};
window.vtApplyFilter=function(){
  const q=document.getElementById('vt-search').value.toLowerCase();
  document.querySelectorAll('#vt-tbody tr[data-domain]').forEach(row=>{
    const st=row.dataset.status;
    const match=activeFilter==='all'||st===activeFilter
      ||(activeFilter==='pending'&&(row.dataset.job==='1'||st==='scanning'||st==='waiting'));
    const show=match&&(!q||row.dataset.domain.includes(q));
    row.style.display=show?'':'none';
  });
};

function updateCounts(){
  const c={infected:0,suspicious:0,clean:0,pending:0,error:0};
  for(const d of domains){
    const s=results[d]?.status??'loading';
    if(s in c) c[s]++;
    if(s!=='pending'&&activeJob(d)) c.pending++; // in the rescan queue
  }
  vtSet('vt-cnt-infected',c.infected);
  vtSet('vt-cnt-suspicious',c.suspicious);
  vtSet('vt-cnt-clean',c.clean);
  vtSet('vt-cnt-pending',c.pending);
  vtSet('vt-cnt-error',c.error);
  document.getElementById('vt-btn-reanalyze').disabled=c.infected===0&&c.suspicious===0;
}

// ── Rescan queue ──────────────────────────────────────────────
// Every Rescan click becomes a job. VT-quota calls (submit + fetch report) run
// one at a time through a shared slot (also used by the auto pump); polling the
// analysis doesn't need the slot, so the next job can submit meanwhile.
// Per-minute rate limits are waited out instead of failing the job.
let jobs=[], jobSeq=0, vtLock=Promise.resolve(), qTicker=null;
const ACTIVE=new Set(['queued','submit','ratewait','processing','fetch']);
const JOB_SHORT={submit:'Submitting...',ratewait:'Rate limit wait',processing:'VT processing...',fetch:'Fetching...'};
const STEPS=['Queued','Submit','VT analysis','Report','Done'];
const POLL_MAX=12, POLL_MS=5000;

function activeJob(d){return jobs.find(j=>j.domain===d&&ACTIVE.has(j.state));}
function queuePos(j){return jobs.filter(x=>x.state==='queued').indexOf(j)+1;}
function withVtSlot(fn){const p=vtLock.then(fn);vtLock=p.catch(()=>{});return p;}

function setJob(j,patch){
  Object.assign(j,patch);
  renderQueue();
  // Refresh rows of all waiting jobs too (their queue position may change)
  updateRow(j.domain,results[j.domain]||null);
  jobs.filter(x=>x!==j&&x.state==='queued').forEach(x=>updateRow(x.domain,results[x.domain]||null));
  updateCounts();
}

function finishJob(j,state,msg){
  if(!ACTIVE.has(j.state)) return; // already finished (e.g. cancelled)
  setJob(j,{state,msg:msg||'',endedAt:Date.now()});
}

// Call a VT-spending endpoint; on per-minute rate limit wait and retry.
async function vtCallRetry(j,url,stepState){
  for(let attempt=0;attempt<6;attempt++){
    if(j.cancelled) return {status:'cancelled'};
    setJob(j,{state:stepState,step:stepState});
    const data=await vtFetch(url).catch(()=>({status:'error',message:'Network error'}));
    if(data.status!=='ratelimit') return data;
    const wait=Math.max(5,Number(data.wait||data.retry_after||60));
    setJob(j,{state:'ratewait',until:Date.now()+wait*1000});
    await delay(wait*1000);
  }
  return {status:'ratelimit'};
}

async function runJob(j){
  const enc=encodeURIComponent(j.domain);

  // 1. Request re-analysis (costs 1 manual call)
  const sub=await withVtSlot(()=>{ if(!j.startedAt) j.startedAt=Date.now(); return vtCallRetry(j,'?action=reanalyze&domain='+enc,'submit'); });
  if(j.cancelled) return;
  if(sub.status==='budget_exceeded') return finishJob(j,'failed','Daily manual rescan limit reached');
  if(sub.status==='quota_exceeded')  return finishJob(j,'failed','VT daily quota exhausted — resumes after midnight UTC');
  if(sub.status==='ratelimit')       return finishJob(j,'failed','Still rate limited after retries');
  if(sub.status!=='queued')          return finishJob(j,'failed','Submit failed'+(sub.http_code?' (HTTP '+sub.http_code+')':''));
  budget.manual_used=(budget.manual_used??0)+1; budget.vt_quota_used=(budget.vt_quota_used??0)+1; updateBudget();

  // 2. Wait for VT to finish the analysis (polling doesn't use the slot)
  setJob(j,{state:'processing',step:'processing',polls:0,vtState:'queued'});
  if(sub.analysis_id){
    for(let i=1;i<=POLL_MAX;i++){
      await delay(POLL_MS);
      const poll=await vtFetch('?action=poll-analysis&id='+encodeURIComponent(sub.analysis_id)).catch(()=>({status:'unknown'}));
      setJob(j,{polls:i,vtState:poll.status});
      if(poll.status==='completed'||poll.status==='unknown') break;
    }
  }else{
    await delay(15000);
  }

  // 3. Fetch the fresh report (costs 1 call)
  const fresh=await withVtSlot(()=>vtCallRetry(j,'?action=scan&force=1&domain='+enc,'fetch'));
  if(['ratelimit','quota_exceeded','error','cancelled'].includes(fresh.status)){
    return finishJob(j,'failed','Could not fetch new report ('+fresh.status+') — previous result kept');
  }
  results[j.domain]=fresh;
  budget.auto_used=(budget.auto_used??0)+1; budget.vt_quota_used=(budget.vt_quota_used??0)+1; updateBudget();
  j.result=fresh;
  finishJob(j,'done');

  if(fresh.status==='infected') vtToast('⚠ INFECTED: '+j.domain,'err');
  else if(fresh.status==='suspicious') vtToast('Suspicious: '+j.domain,'warn');
  else if(fresh.status==='clean') vtToast('Clean: '+j.domain,'ok');
}

function enqueueRescan(domain){
  if(activeJob(domain)) return false;
  scanQueue=scanQueue.filter(d=>d!==domain); // manual takes priority over auto pump
  const j={id:++jobSeq,domain,state:'queued',step:'queued',prev:results[domain]||null,createdAt:Date.now()};
  jobs.push(j);
  setJob(j,{});
  startQTicker();
  runJob(j);
  return true;
}

window.vtReanalyzeSingle=function(domain){
  if(!enqueueRescan(domain)) vtToast(domain+' is already in the scan queue','info');
};

window.vtReanalyzeInfected=function(){
  const targets=domains.filter(d=>results[d]&&['infected','suspicious'].includes(results[d].status));
  if(!targets.length){vtToast('No infected or suspicious sites','info');return;}
  const added=targets.filter(enqueueRescan).length;
  vtToast(added?added+' sites added to the scan queue':'All already in the scan queue','info');
};

window.vtCancelWaiting=function(){
  jobs.filter(j=>j.state==='queued').forEach(j=>{ j.cancelled=true; finishJob(j,'failed','Cancelled'); });
};

window.vtClearFinished=function(){
  jobs=jobs.filter(j=>ACTIVE.has(j.state));
  renderQueue();
};

function startQTicker(){
  if(qTicker) return;
  qTicker=setInterval(()=>{
    renderQueue();
    if(!jobs.some(j=>ACTIVE.has(j.state))&&!pumpRunning){clearInterval(qTicker);qTicker=null;}
  },1000);
}

function fmtSecs(ms){const s=Math.max(0,Math.round(ms/1000));return s<60?s+'s':Math.floor(s/60)+'m '+(s%60)+'s';}
function scoreTxt(r){return r&&r.malicious!==undefined&&r.total!==undefined?r.malicious+'/'+r.total:'—';}
function stepIdx(j){
  if(j.state==='done'||j.state==='failed') return 4;
  const s=j.state==='ratewait'?j.step:j.state;
  return {queued:0,submit:1,processing:2,fetch:3}[s]??0;
}

function jobMsg(j){
  const now=Date.now();
  switch(j.state){
    case 'queued':     return 'Waiting for a VT slot — #'+queuePos(j)+' in line ('+fmtSecs(now-j.createdAt)+')';
    case 'submit':     return 'Requesting re-analysis at VirusTotal...';
    case 'ratewait':   return 'VT rate limit (4/min) — retrying in '+fmtSecs(j.until-now);
    case 'processing': return 'VT is analysing ('+(j.vtState||'queued')+') — check '+(j.polls||0)+'/'+POLL_MAX;
    case 'fetch':      return 'Fetching the new report...';
    case 'done':       return 'Finished in '+fmtSecs(j.endedAt-(j.startedAt||j.createdAt));
    case 'failed':     return j.msg||'Failed';
  }
  return '';
}

function jobHTML(j){
  const fin=!ACTIVE.has(j.state);
  let icon;
  if(j.state==='done') icon='<div class="vt-q-icon ok">✓</div>';
  else if(j.state==='failed') icon='<div class="vt-q-icon bad">✕</div>';
  else if(j.state==='queued') icon='<div class="vt-q-icon">#'+queuePos(j)+'</div>';
  else icon='<div class="vt-q-icon"><span class="vt-spin"></span></div>';

  const si=stepIdx(j);
  const steps='<div class="vt-q-steps">'+STEPS.map((s,i)=>{
    let c='';
    if(i<si||(i===4&&j.state==='done')) c='done';
    else if(i===si&&!fin) c='on';
    else if(i===4&&j.state==='failed') c='bad';
    return '<span class="'+c+'">'+(i===4&&j.state==='failed'?'Failed':s)+'</span>';
  }).join('')+'</div>';
  const pbar=j.state==='processing'?'<div class="vt-q-pbar"><div style="width:'+Math.round((j.polls||0)/POLL_MAX*100)+'%"></div></div>':'';

  let right;
  if(j.state==='done'&&j.result){
    const st=j.result.status, dc=DOT_CLS[st];
    right='<span class="vt-badge '+st+'">'+(dc?'<span class="vt-dot '+dc+'"></span>':'')+(LABELS[st]??st)+'</span>'
      +'<div>'+scoreTxt(j.prev)+' → <strong>'+scoreTxt(j.result)+'</strong></div>';
  }else if(fin){
    right='<div>was '+scoreTxt(j.prev)+'</div>';
  }else{
    right='<div>'+fmtSecs(Date.now()-(j.startedAt||j.createdAt))+'</div><div>was '+scoreTxt(j.prev)+'</div>';
  }

  return '<div class="vt-q-item '+j.state+'">'+icon
    +'<div class="vt-q-main"><div class="vt-q-dom">'+vtEsc(j.domain)+'<span class="vt-q-kind">Rescan</span></div>'
    +'<div class="vt-q-msg">'+vtEsc(jobMsg(j))+'</div>'+steps+pbar+'</div>'
    +'<div class="vt-q-right">'+right+'</div></div>';
}

function renderQueue(){
  const box=document.getElementById('vt-queue');
  if(!box) return;
  const pumpBusy=pumpRunning&&(pumpCurrent||scanQueue.length);
  if(!jobs.length&&!pumpBusy){box.style.display='none';return;}
  box.style.display='block';
  if(pumpBusy) startQTicker();

  const running=jobs.filter(j=>ACTIVE.has(j.state)&&j.state!=='queued');
  const waiting=jobs.filter(j=>j.state==='queued');
  const finished=jobs.filter(j=>!ACTIVE.has(j.state)).sort((a,b)=>b.endedAt-a.endedAt);

  let html=running.concat(waiting).map(jobHTML).join('');
  if(pumpBusy){
    const msg=pumpCurrent?'Fetching cached/initial report...'
      :pumpWaitUntil?'VT rate limit — next in '+fmtSecs(pumpWaitUntil-Date.now())
      :'Pausing between requests (4/min limit)';
    html+='<div class="vt-q-item">'
      +'<div class="vt-q-icon">'+(pumpCurrent?'<span class="vt-spin"></span>':'⋯')+'</div>'
      +'<div class="vt-q-main"><div class="vt-q-dom">'+vtEsc(pumpCurrent||'Auto-load')+'<span class="vt-q-kind">Auto</span></div>'
      +'<div class="vt-q-msg">'+vtEsc(msg)+'</div></div>'
      +'<div class="vt-q-right">'+scanQueue.length+' more waiting</div></div>';
  }
  html+=finished.slice(0,25).map(jobHTML).join('');
  document.getElementById('vt-q-list').innerHTML=html;

  const parts=[];
  if(running.length) parts.push(running.length+' running');
  if(waiting.length) parts.push(waiting.length+' waiting');
  if(pumpBusy) parts.push((scanQueue.length+(pumpCurrent?1:0))+' auto');
  if(finished.length) parts.push(finished.length+' finished');
  vtSet('vt-q-sum',parts.join(' · '));
  document.getElementById('vt-q-cancel').style.display=waiting.length?'':'none';
  document.getElementById('vt-q-clear').style.display=finished.length?'':'none';
}

window.vtRefreshDomains=async function(){
  vtToast('Refreshing site list...','info');
  const data=await vtFetch('?action=refresh-domains').catch(()=>({}));
  if(data.domains){
    domains=data.domains; vtSet('vt-cnt-all',domains.length);
    renderTable(); scanQueue=domains.filter(d=>!results[d]); pump();
  }
};

// ── Settings ──────────────────────────────────────────────────
let savedWpOnly=false;
function loadSettingsForm(cfg){
  const keyInput=document.getElementById('vt-s-apikey');
  const kd=document.getElementById('vt-key-display');
  if(cfg.api_key_readable){
    keyInput.placeholder='Current key: '+(cfg.api_key_readable.substring(0,8))+'...';
    document.getElementById('vt-key-value').textContent=cfg.api_key_readable;
    kd.style.display='block';
  }
  document.getElementById(cfg.wordpress_only?'vt-s-scope-wp':'vt-s-scope-all').checked=true;
  savedWpOnly=!!cfg.wordpress_only;
  if(cfg.daily_limit)       document.getElementById('vt-s-daily').value    =cfg.daily_limit;
  if(cfg.auto_scan_share)   document.getElementById('vt-s-share').value    =Math.round(cfg.auto_scan_share*100);
  if(cfg.clean_ttl_days)    document.getElementById('vt-s-clean-ttl').value=cfg.clean_ttl_days;
  if(cfg.infected_ttl_days) document.getElementById('vt-s-inf-ttl').value  =cfg.infected_ttl_days;
}

window.vtSaveSettings=async function(){
  const key  =document.getElementById('vt-s-apikey').value.trim();
  const daily=parseInt(document.getElementById('vt-s-daily').value)||500;
  const share=(parseInt(document.getElementById('vt-s-share').value)||80)/100;
  const cttl =parseInt(document.getElementById('vt-s-clean-ttl').value)||30;
  const ittl =parseInt(document.getElementById('vt-s-inf-ttl').value)||1;
  const wpOnly=document.getElementById('vt-s-scope-wp').checked;
  const payload={wordpress_only:wpOnly?'true':'false',daily_limit:daily,auto_scan_share:share,clean_ttl_days:cttl,infected_ttl_days:ittl};
  if(key) payload.api_key=key;

  const body=new URLSearchParams(payload);
  const fpToken=document.getElementById('forgery_protection_token')?.content||'';
  const res=await fetch(BASE+'?action=save-settings',{method:'POST',headers:{'X-Forgery-Protection-Token':fpToken},body}).then(r=>r.json()).catch(()=>({error:'Network error'}));
  const msg=document.getElementById('vt-smsg');
  msg.style.display='inline';
  if(res.ok){
    msg.className='vt-smsg ok'; msg.textContent='Saved!';
    document.getElementById('vt-api-warning').style.display='none';
    document.getElementById('vt-s-apikey').value='';
    const scopeChanged=wpOnly!==savedWpOnly;
    const cfg=await vtFetch('?action=get-settings').catch(()=>({}));
    loadSettingsForm(cfg);
    if(scopeChanged) vtRefreshDomains(); // reload the site list for the new scope
  }else{msg.className='vt-smsg err'; msg.textContent=res.error??'Error saving';}
  setTimeout(()=>{msg.style.display='none';},4000);
};

// ── Log ───────────────────────────────────────────────────────
window.vtLoadLog=async function(){
  const pre=document.getElementById('vt-log-pre');
  const meta=document.getElementById('vt-log-meta');
  pre.textContent='Loading...';
  const data=await vtFetch('?action=get-log').catch(()=>({log:'',size:0}));
  if(!data.log){pre.textContent='(Log is empty — no cron runs yet)';meta.textContent='';return;}
  pre.innerHTML=data.log.split('\n').map(l=>{
    const esc=l.replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]||c));
    if(/\[!\]|\[ERR\]|INFECTED/.test(l)) return '<span style="color:#dc2626;font-weight:600">'+esc+'</span>';
    if(/\[\?]|suspicious/.test(l))       return '<span style="color:#d97706;font-weight:600">'+esc+'</span>';
    if(/\[ok\]|\[manual\]/.test(l))      return '<span style="color:#16a34a">'+esc+'</span>';
    if(/Done —/.test(l))                 return '<span style="font-weight:600">'+esc+'</span>';
    return esc;
  }).join('\n');
  const kb=Math.round(data.size/1024*10)/10;
  meta.textContent='('+kb+' KB — last 200 lines, newest first)';
};

window.vtClearLog=async function(){
  if(!confirm('Clear the scan log?')) return;
  await fetch(BASE+'?action=clear-log&confirm=1').then(r=>r.json()).catch(()=>{});
  document.getElementById('vt-log-pre').textContent='(Log cleared)';
  document.getElementById('vt-log-meta').textContent='';
  vtToast('Log cleared','ok');
};

// ── Utils ─────────────────────────────────────────────────────
function vtFetch(url){return fetch(BASE+url).then(r=>{if(!r.ok)throw new Error(r.status);return r.json();});}
function vtSet(id,v){const e=document.getElementById(id);if(e)e.textContent=v;}
function vtEsc(s){return String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]||c));}
function vtEsca(s){return s.replace(/'/g,"\\'");}
function delay(ms){return new Promise(r=>setTimeout(r,ms));}
function vtToast(msg,type){
  const c=document.getElementById('vt-toasts');
  const t=document.createElement('div');
  t.className='vt-toast '+type; t.textContent=msg; c.prepend(t);
  setTimeout(()=>t.remove(),5000);
}

init();
})();
</script>
