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

/* About */
.vt-update-banner{display:none;align-items:center;gap:10px;padding:9px 16px;margin-bottom:12px;border-radius:var(--radius);background:#fefce8;border:1px solid #fde047;font-size:13px;color:#713f12}
.vt-update-banner a{color:#92400e;font-weight:700;text-decoration:none;border-bottom:1px solid #d97706}
.vt-update-banner a:hover{color:#78350f}
.vt-update-icon{font-size:16px;flex-shrink:0}
.vt-update-btn{margin-left:4px;padding:4px 12px;border-radius:5px;border:none;background:#d97706;color:#fff;font-size:12px;font-weight:700;cursor:pointer}
.vt-update-btn:hover{background:#b45309}
.vt-update-btn:disabled{opacity:.6;cursor:not-allowed}
.vt-about{max-width:760px}
.vt-about .vt-card{max-width:none}
.vt-about p{font-size:13px;line-height:1.6;color:var(--text);margin:10px 0}
.vt-about-hero{display:flex;gap:18px;align-items:flex-start}
.vt-about-mark{width:64px;height:64px;flex-shrink:0;border-radius:16px;background:linear-gradient(135deg,#b91c1c,#7f1d1d);color:#fff;font-weight:800;font-size:22px;display:flex;align-items:center;justify-content:center;letter-spacing:1px}
.vt-about-sub{font-size:13px;color:var(--muted)}
.vt-about-sub a{color:#b91c1c;font-weight:600;text-decoration:none}
.vt-about-list{margin:6px 0 0;padding-left:18px;font-size:13px;line-height:1.6}
.vt-about-list li{margin-bottom:6px}
.vt-coffee{display:flex;align-items:center;gap:16px;background:linear-gradient(135deg,#fffbeb,#fff 70%);border-color:#fde68a}
.vt-coffee-cup{font-size:34px;line-height:1}
.vt-paypal{flex-shrink:0;display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:24px;background:#ffc439;color:#111827;font-weight:700;font-size:13px;text-decoration:none;box-shadow:0 1px 2px rgba(0,0,0,.15)}
.vt-paypal:hover{background:#f2b52e;color:#111827}
.vt-paypal-pp{font-style:italic;font-weight:800;color:#003087}.vt-paypal-pp b{color:#009cde}
@media (max-width:640px){.vt-about-hero,.vt-coffee{flex-direction:column;align-items:flex-start}}

/* Brand header */
.vt-brand{display:flex;align-items:center;gap:14px;padding:14px 18px;margin-bottom:14px;border-radius:var(--radius);background:linear-gradient(135deg,#eff6ff 0%,#fff 60%);border:1px solid #dbe5f5}
.vt-brand-logo{width:48px;height:48px;flex-shrink:0;filter:drop-shadow(0 2px 4px rgba(37,99,235,.25))}
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
/* Category button + arrow menu */
.vt-fgroup{position:relative;display:inline-flex}
.vt-fgroup .vt-fbtn{border-top-right-radius:0;border-bottom-right-radius:0;border-right:none}
.vt-fcaret{padding:0 7px;border:1px solid var(--border);border-left:1px solid var(--border);border-radius:0 20px 20px 0;background:var(--surface);color:var(--muted);font-size:10px;cursor:pointer}
.vt-fcaret:hover{color:var(--blue);border-color:var(--blue)}
.vt-fgroup .vt-fbtn.active+.vt-fcaret{background:var(--blue);border-color:var(--blue);color:#fff;border-left-color:rgba(255,255,255,.4)}
.vt-fmenu{display:none;position:absolute;top:calc(100% + 4px);left:0;min-width:170px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:0 6px 18px rgba(0,0,0,.12);padding:4px;z-index:50}
.vt-fmenu.open{display:block}
.vt-fmenu button{display:block;width:100%;text-align:left;padding:7px 10px;border:none;background:none;border-radius:5px;font-size:13px;color:var(--text);cursor:pointer}
.vt-fmenu button:hover{background:var(--blue-bg);color:var(--blue)}
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
#vt-load-bar{padding:18px 0 12px;text-align:center}
#vt-load-bar.vt-load-done{display:none}
#vt-load-msg{font-size:13px;font-weight:600;color:var(--text);margin-bottom:8px}
.vt-load-track{height:10px;background:var(--border);border-radius:6px;overflow:hidden;margin:0 auto 6px;max-width:480px}
.vt-load-fill{height:100%;background:var(--blue);border-radius:6px;transition:width .5s ease;width:0}
#vt-load-pct{font-size:12px;color:var(--muted)}

/* Table */
.vt-tbl-wrap{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.vt-tbl-wrap table{width:100%;border-collapse:collapse}
.vt-tbl-wrap thead th{padding:9px 13px;text-align:left;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;background:var(--gray-bg);border-bottom:1px solid var(--border);white-space:nowrap}
th.vt-sortable{cursor:pointer;user-select:none}th.vt-sortable:hover{color:var(--blue)}th.vt-sortable::after{content:"";display:inline-block;width:14px;text-align:left}th.vt-sort-asc::after{content:" ↑"}th.vt-sort-desc::after{content:" ↓"}
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
.vt-badge.nokey{background:#fffbeb;color:#92400e;border:1px solid #fcd34d}
.vt-badge.scanning,.vt-badge.waiting{background:var(--blue-bg);color:var(--blue);border:1px solid var(--blue-border)}
.vt-badge.loading,.vt-badge.unchecked,.vt-badge.error,.vt-badge.ratelimit,.vt-badge.quota_exceeded,.vt-badge.budget_exceeded{background:var(--gray-bg);color:var(--gray);border:1px solid var(--border)}
.vt-fs-badge{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:2px 7px;border-radius:10px;background:var(--red-bg);color:var(--red);border:1px solid var(--red-border);cursor:pointer;margin-top:3px}
.vt-fs-badge:hover{background:var(--red);color:#fff}
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

/* Scan Monitor panel — fixed right, starts at app header height, 50% wide */
.vt-queue{position:fixed;right:0;top:var(--vt-header-h,56px);width:50vw;max-height:calc(100vh - var(--vt-header-h,56px));
  overflow-y:auto;z-index:900;background:var(--surface);border:1px solid var(--blue-border);border-right:none;
  border-radius:10px 0 0 10px;box-shadow:-4px 0 24px rgba(0,0,0,.13);display:none}
.vt-queue.vt-q-collapsed{max-height:none;overflow:visible}
.vt-queue.vt-q-collapsed .vt-q-body{display:none}
.vt-q-head{display:flex;align-items:center;gap:8px;padding:9px 13px;background:var(--blue-bg);border-bottom:1px solid var(--blue-border);position:sticky;top:0;z-index:1;cursor:pointer;user-select:none}
.vt-q-head button,.vt-q-head a{cursor:pointer}
.vt-queue.vt-q-collapsed .vt-q-head{border-bottom:none}
.vt-q-title{font-size:12px;font-weight:700;color:var(--blue);text-transform:uppercase;letter-spacing:.4px}
.vt-q-sum{font-size:12px;color:var(--muted)}
.vt-q-toggle{margin-left:2px;background:var(--blue-bg);border:1px solid var(--blue-border);cursor:pointer;padding:3px 10px;font-size:11px;font-weight:600;color:var(--blue);border-radius:4px;line-height:1.5;flex-shrink:0;white-space:nowrap}
.vt-q-toggle:hover{background:var(--blue);color:#fff;border-color:var(--blue)}
.vt-q-head .vt-row-btn{margin-left:4px}
.vt-q-body{}
.vt-q-list{}
/* Animated pause bar for VirusTotal's 4 requests/minute limit */
.vt-q-pause{display:none;align-items:center;gap:12px;padding:8px 13px;border-top:1px solid var(--border);background:#fafbff;font-size:12px;color:var(--muted)}
.vt-q-ptrack{flex:0 0 180px;height:8px;border-radius:4px;background:var(--border);overflow:hidden}
.vt-q-pfill{height:100%;width:0;border-radius:4px;transition:width 1s linear;
  background:repeating-linear-gradient(-45deg,#2563eb 0 6px,#60a5fa 6px 12px);background-size:17px 17px;animation:vt-stripes .8s linear infinite}
.vt-q-pfill.limit{background:repeating-linear-gradient(-45deg,#d97706 0 6px,#fbbf24 6px 12px);background-size:17px 17px}
@keyframes vt-stripes{to{background-position:17px 0}}
.vt-q-item{display:flex;align-items:center;gap:12px;padding:8px 13px 8px 10px;border-bottom:1px solid var(--border);font-size:13px;border-left:3px solid transparent}
.vt-q-item:last-child{border-bottom:none}
/* Active: blue left bar */
.vt-q-item.queued,.vt-q-item.submit,.vt-q-item.ratewait,.vt-q-item.processing,.vt-q-item.fetch,.vt-q-item.live{border-left-color:var(--blue);background:var(--surface)}
/* Completed: green or red tint */
.vt-q-item.done{background:var(--green-bg);border-left-color:var(--green-border)}
.vt-q-item.failed{background:var(--red-bg);border-left-color:var(--red-border)}
/* Section divider between active queue and completed */
.vt-q-divider{padding:4px 13px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);background:var(--gray-bg);border-bottom:1px solid var(--border);border-top:1px solid var(--border)}
/* File scanner jobs — amber accent */
.vt-q-item.vt-q-fs.running,.vt-q-item.vt-q-fs.queued{border-left-color:#f59e0b;background:#fffbeb}
/* Server-queue items — purple accent */
.vt-q-item.vt-q-srv{border-left-color:#7c3aed;background:#faf5ff}
.vt-q-kind.srv{background:#ede9fe;border-color:#c4b5fd;color:#5b21b6}
.vt-q-kind.fs{background:#fef3c7;border-color:#fcd34d;color:#92400e}
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

/* Notifications */
.vt-n-group{margin:10px 0 12px 26px}
.vt-n-label{font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px}
.vt-check{display:flex;align-items:center;gap:6px;padding:3px 0;font-size:13px;cursor:pointer}
.vt-check .hint{font-size:12px;color:var(--muted)}
.vt-n-email{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:7px 11px;font-size:13px;width:100%;max-width:340px;outline:none}
.vt-n-email:focus{border-color:var(--blue)}
.vt-n-group.hint{color:var(--muted)}
/* File scanners (ClamAV, maldet, …) */
.vt-scanbar{display:inline-flex;border:1px solid var(--border);border-radius:5px;overflow:hidden;vertical-align:middle;margin-right:6px}
.vt-sbtn{padding:4px 9px;border:0;border-left:1px solid var(--border);background:var(--gray-bg);color:#374151;font-size:11px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:4px;white-space:nowrap}
.vt-sbtn:first-child{border-left:0}
.vt-sbtn:hover{color:var(--blue);background:var(--blue-bg)}
.vt-sbtn.open{color:var(--blue);background:var(--blue-bg);box-shadow:inset 0 -2px 0 var(--blue)}
.vt-sbtn .n{background:var(--red);color:#fff;border-radius:8px;padding:0 5px;font-size:10px}
.vt-sbtn .ok{color:var(--green,#16a34a)}
tr.vt-fs-row>td{background:var(--gray-bg);border-top:0;padding:10px 13px 12px}
.vt-fs{font-size:12.5px;overflow-wrap:anywhere}
.vt-fs-row>td{max-width:0} /* the details never make the site table wider than the page */
.vt-why-tbl{table-layout:fixed}
.vt-why-tbl th:nth-child(1){width:22%}.vt-why-tbl th:nth-child(2){width:38%}
.vt-why-tbl td:last-child{white-space:normal;color:inherit}
.vt-why-tbl code{word-break:break-all}
.vt-tbl-wrap tbody tr[data-domain]:has(.vt-why-btn):not(.vt-fs-row){cursor:pointer}
.vt-fs-head{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.vt-fs-head strong{color:var(--heading)}
.vt-fs-head .sp{flex:1}
.vt-fs-head a{cursor:pointer;color:var(--blue);font-weight:600}
.vt-fs-bar{height:6px;border-radius:3px;margin-top:8px;background:repeating-linear-gradient(45deg,var(--blue) 0 10px,#93c5fd 10px 20px);background-size:28px 28px;animation:vtFsMove 1s linear infinite;opacity:.8}
.vt-fs-bar.wait{background:repeating-linear-gradient(45deg,#cbd5e1 0 10px,#e2e8f0 10px 20px);background-size:28px 28px}
@keyframes vtFsMove{to{background-position:28px 0}}
.vt-fs .bad{color:var(--red);font-weight:600}
.vt-fs .good{color:var(--green,#16a34a);font-weight:600}
.vt-fs-tbl{width:100%;border-collapse:collapse;margin-top:8px;font-size:12px;background:var(--surface)}
.vt-fs-tbl th,.vt-fs-tbl td{text-align:left;padding:5px 8px;border:1px solid var(--border);vertical-align:top}
.vt-fs-tbl th{font-size:11px;color:var(--muted);font-weight:600}
.vt-fs-tbl td:first-child{font-family:ui-monospace,Consolas,monospace;word-break:break-all}
.vt-fs-tbl td:last-child{color:var(--red);white-space:nowrap}
.vt-fs-tbl tr.handled td{color:var(--muted)!important;text-decoration:line-through}
.vt-sc-row{display:flex;align-items:center;gap:12px;padding:9px 0;border-top:1px solid var(--border)}
.vt-sc-row:first-child{border-top:0}
.vt-sc-row .vt-sc-name{font-weight:700;min-width:80px}
.vt-sc-row .vt-sc-info{flex:1;font-size:12px;color:var(--muted);overflow-wrap:anywhere}
.vt-sc-row.missing .vt-sc-name{color:var(--muted)}
.vt-sw{position:relative;width:36px;height:20px;flex:none}
.vt-sw input{opacity:0;width:0;height:0;position:absolute}
.vt-sw span{position:absolute;inset:0;border-radius:20px;background:#cbd5e1;cursor:pointer;transition:.15s}
.vt-sw span:before{content:"";position:absolute;width:16px;height:16px;left:2px;top:2px;border-radius:50%;background:#fff;transition:.15s}
.vt-sw input:checked+span{background:#16a34a}
.vt-sw input:checked+span:before{transform:translateX(16px)}
.vt-sw input:disabled+span{opacity:.45;cursor:not-allowed}
.vt-fs-note{color:var(--muted);font-size:11.5px;margin-top:6px;line-height:1.45}
.vt-rel{margin-top:4px}
tr.vt-grp-r>td{background:#e8edf5;font-size:13px;font-weight:700;padding:8px 12px!important;border-top:2px solid #cbd5e1;color:#1f2937}
tr.vt-grp-c>td{background:#f8fafc;font-size:12.5px;padding:6px 12px!important;cursor:pointer;color:#374151;font-weight:600}
tr.vt-grp-c:hover>td{background:#f1f5f9}
.vt-grp-badge{display:inline-block;font-size:10px;font-weight:600;padding:1px 5px;border-radius:4px;background:#dbeafe;color:#1d4ed8;vertical-align:middle;margin-left:5px}
.vt-grp-login{font-weight:400;color:var(--muted);font-size:11.5px}
.vt-grp-n{font-weight:400;color:var(--muted);font-size:11.5px;margin-left:6px}
.vt-grp-n .bad{color:#b91c1c;font-weight:600}
.vt-grp-caret{display:inline-block;transition:transform .15s;color:var(--muted)}
tr.vt-grp-c.closed .vt-grp-caret{transform:rotate(-90deg)}
.vt-seg{display:inline-flex;border:1px solid var(--border);border-radius:8px;overflow:hidden}
.vt-seg button{all:unset;cursor:pointer;font-size:12px;padding:5px 12px;color:var(--text);border-left:1px solid var(--border)}
.vt-seg button:first-child{border-left:0}
.vt-seg button.on{background:#2563eb;color:#fff}
.vt-tr-tiles{display:flex;gap:12px;flex-wrap:wrap;margin:6px 0 14px}
.vt-tr-tile{border:1px solid var(--border);border-radius:8px;padding:10px 14px;min-width:150px}
.vt-tr-tile .l{font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted)}
.vt-tr-tile .v{font-size:22px;font-weight:800;margin-top:2px}
.vt-tr-tile .d{font-size:12px;color:var(--muted)}
.vt-tr-tile .d.up{color:#b91c1c}.vt-tr-tile .d.down{color:#15803d}
.vt-tr-legend{display:flex;gap:16px;font-size:12px;color:var(--text);margin-bottom:4px}
.vt-tr-legend i{display:inline-block;width:12px;height:3px;border-radius:2px;vertical-align:middle;margin-right:6px}
.vt-tr-chart{position:relative;width:100%}
.vt-tr-chart svg{display:block;width:100%;overflow:visible}
.vt-tr-title{font-size:13px;font-weight:600;margin:16px 0 4px}
.vt-tr-tip{position:fixed;pointer-events:none;background:#fff;border:1px solid var(--border);border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.15);padding:8px 10px;font-size:12px;line-height:1.5;display:none;z-index:50;min-width:150px}
.vt-tr-tip b{display:block;margin-bottom:2px}
.vt-tr-tip .row{display:flex;justify-content:space-between;gap:14px}
.vt-tr-tip i{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:6px}
.vt-tr-table{margin-top:10px;font-size:12px}
.vt-tr-table table{border-collapse:collapse;margin-top:6px}
.vt-tr-table td,.vt-tr-table th{padding:3px 10px;border-bottom:1px solid var(--border);text-align:right}
.vt-tr-table td:first-child,.vt-tr-table th:first-child{text-align:left}
.vt-orphan{font-size:11.5px;color:var(--muted);margin-top:4px;line-height:1.45}
.vt-orphan code{font-size:11px}
.vt-orphan-kind{font-size:11px;margin-top:1px}
.vt-orphan-kind.free{color:#15803d}
.vt-orphan-kind.part{color:#b45309}
.vt-orphan-kind.live{color:#b91c1c;font-weight:600}
a.vt-row-btn{text-decoration:none;display:inline-block;margin-right:4px}
#vt-tbody-rel .vt-row-btn{margin-left:4px}
.vt-rel h3{font-size:15px;margin:0 0 6px;display:flex;align-items:center;gap:8px}
.vt-rel .vt-rel-cnt{font-size:12px;font-weight:600;background:var(--gray-bg);border:1px solid var(--border);border-radius:10px;padding:1px 8px;color:var(--muted)}
.vt-rel .hint{font-size:12.5px;line-height:1.5;margin:0 0 10px;max-width:900px}
.vt-why-btn{all:unset;cursor:pointer;display:flex;flex-direction:column;gap:2px;padding:3px 6px;margin:-3px -6px;border-radius:6px}
.vt-why-btn:hover,.vt-why-btn.open{background:var(--gray-bg)}
.vt-why-lbl{font-size:10.5px;color:#2563eb;font-weight:600}
.vt-why-note{background:#fffbeb;border:1px solid #fcd34d;color:#92400e;border-radius:6px;padding:8px 11px;font-size:12.5px;line-height:1.5;margin:8px 0}
.vt-why-steps{margin:10px 0 4px 18px;padding:0;font-size:12.5px;line-height:1.55}
.vt-why-steps li{margin-bottom:5px}
.vt-why-tbl td:first-child{font-family:inherit;font-weight:600}
.vt-sh-row{display:flex;align-items:center;gap:12px;padding:6px 2px;border-bottom:1px solid var(--border);font-size:12.5px;flex-wrap:wrap}
.vt-sh-row:last-child{border-bottom:none}
.vt-sh-time{color:var(--muted);white-space:nowrap;min-width:150px;font-variant-numeric:tabular-nums}
.vt-sh-time small{font-size:11px}
.vt-sh-what{display:flex;align-items:center;gap:6px;min-width:215px}
.vt-sh-what .vt-badge{min-width:80px;justify-content:center}
.vt-sh-arr{color:var(--muted);font-weight:700}
.vt-sh-text{flex:1;min-width:200px}
.vt-sh-pill{display:inline-flex;justify-content:center;min-width:92px;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:var(--gray-bg);color:var(--gray);border:1px solid var(--border)}
.vt-sh-pill.bad{background:var(--red-bg);color:var(--red);border-color:var(--red-border)}
.vt-sh-pill.good{background:var(--green-bg);color:var(--green);border-color:var(--green-border)}
.vt-sh-pill.warn{background:var(--yellow-bg);color:var(--yellow);border-color:var(--yellow-border)}
.vt-av-badge{all:unset;cursor:pointer;display:inline-flex;white-space:nowrap;flex-shrink:0;align-items:center;gap:5px;margin-top:3px;padding:1px 8px;border-radius:20px;font-size:10.5px;font-weight:600;background:var(--red-bg);color:var(--red);border:1px dashed var(--red-border)}
.vt-av a{cursor:pointer}
.vt-av{font-size:12.5px;line-height:1.7}
.vt-rev-deny{display:flex;align-items:center;gap:6px;margin-top:4px;font-size:11.5px}
.vt-rev-deny input{flex:1;min-width:180px;padding:3px 7px;border:1px solid var(--border);border-radius:6px;font-size:12px}
.vt-rev-deny a{color:var(--muted);cursor:pointer}
.vt-sh-pill.info{background:var(--blue-bg);color:var(--blue);border-color:var(--blue-border)}
.vt-rev-done{display:inline-flex;align-items:center;gap:6px;margin-top:3px;font-size:11px;font-weight:600;color:var(--green);background:var(--green-bg);border:1px solid var(--green-border);border-radius:20px;padding:1px 8px}
.vt-rev-done a{color:var(--muted);font-weight:400;cursor:pointer}
.vt-why-tbl tr.vt-av-row td{background:var(--gray-bg)}
.vt-why-tbl tr.asked td:first-child::after{content:' \2713';color:var(--green)}
.vt-why-tip{font-size:11px;color:var(--muted);margin-top:2px;line-height:1.35}
#vt-n-opts{margin-left:0}
#vt-n-opts>.vt-btn{margin-left:26px}
#vt-n-opts.off{opacity:.45;pointer-events:none}

/* Settings: primary service, experimental providers, then server scanners. */
#vtpanel-settings{max-width:1080px}
#vtpanel-settings>.vt-card{max-width:none;padding:24px}
#vtpanel-settings .vt-field label{font-size:12px;text-transform:none;letter-spacing:0;color:var(--text)}
#vtpanel-settings .vt-field input{font-family:inherit}
#vtpanel-settings .hint{font-size:12px;line-height:1.5;color:var(--muted)}
#vtpanel-settings .vt-save-row{flex-wrap:wrap;gap:10px;margin-top:16px}
#vtpanel-settings .vt-save-row button{white-space:nowrap;flex-shrink:0}
.vt-settings-title{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.vt-settings-title h3{margin:0}
.vt-settings-title .hint{margin-left:auto}
.vt-settings-main{border-left:3px solid var(--blue)}
.vt-settings-numbers{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:16px;margin-top:20px}
.vt-settings-numbers .vt-field{grid-column:span 2}
.vt-settings-numbers .vt-field.wide{grid-column:span 6}
.vt-settings-numbers .vt-field{min-width:0;margin:0}
.vt-settings-usage{margin-top:18px;padding-top:12px;border-top:1px solid var(--border);font-size:12px}
.vt-usage-heading,.vt-usage-line{display:flex;align-items:center;justify-content:space-between;gap:8px}
.vt-usage-heading{margin-bottom:10px}
.vt-usage-heading>span,.vt-usage-line>span{color:var(--muted)}
.vt-usage-columns{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.vt-usage-columns .vt-b-track{height:3px;width:100%;margin-top:5px}
.vt-usage-line{flex-wrap:wrap;row-gap:2px}
.vt-settings-experimental{border-color:var(--yellow-border);border-left:3px solid var(--yellow)}
.vt-experimental-tag{font-size:11px;font-weight:600;color:#92400e;background:var(--yellow-bg);border:1px solid var(--yellow-border);padding:3px 8px;border-radius:4px}
.vt-provider-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px;margin-top:18px}
.vt-provider{display:grid;grid-template-columns:1fr auto;gap:8px 16px;align-items:center;padding:14px 0;border-top:1px solid var(--border)}
.vt-provider .vt-radio{margin:0}
.vt-provider .hint{grid-column:1/-1}
.vt-rate{display:flex;gap:7px;align-items:center;font-size:12px;color:var(--muted);white-space:nowrap}
.vt-rate input{width:58px;padding:7px;border:1px solid var(--border);border-radius:5px;font:inherit;color:var(--text);background:var(--surface)}
#vtpanel-settings .vt-sc-row{flex-wrap:wrap;padding:14px 0}
#vtpanel-settings .vt-sc-info{flex:1;min-width:220px}
#vtpanel-settings input:focus-visible{outline:2px solid var(--blue);outline-offset:2px}
@media(max-width:850px){.vt-settings-numbers .vt-field{grid-column:span 4}}
@media(max-width:560px){.vt-settings-numbers .vt-field{grid-column:span 6}}
/* Settings: no text lighter than rgb(32,32,32) */
#vtpanel-settings{--muted:#202020;color:#202020}
#vtpanel-settings .hint,#vtpanel-settings label,#vtpanel-settings .vt-sl-name span,#vtpanel-settings .vt-sl-part.manual .vt-sl-name span,
#vtpanel-settings .vt-av-title small,#vtpanel-settings .vt-av-calls,#vtpanel-settings .vt-rate,#vtpanel-settings .vt-sl-scale,
#vtpanel-settings .vt-qs-today .parts,#vtpanel-settings .vt-gsb-key,#vtpanel-settings .vt-usage-heading>span{color:#202020}
/* Online services: each in its own tinted block with a large logo */
.vt-brand-ico.lg{width:32px;height:32px;border-radius:7px}
.vt-svc-head{padding:10px 14px;border-radius:9px;border:1px solid #c7d2fe;background:#eef2ff}
.vt-svc-head h3{font-size:17px}
.vt-provider-list .vt-provider.vt-svc{border:1px solid var(--border);border-radius:9px;padding:14px 16px;margin-top:12px}
.vt-svc.avg{background:#eef5fc;border-color:#bfd7f0}
.vt-svc.avast{background:#fff4ec;border-color:#fcd2b3}
.vt-svc.google{background:#f0f6ff;border-color:#c6dafc}
.vt-svc .vt-av-title strong{font-size:15px;gap:10px}
.vt-key-state{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;border-radius:20px;padding:2px 10px;white-space:nowrap}
.vt-key-state:empty{display:none}
.vt-key-state.ok{color:#14532d;background:#dcfce7;border:1px solid #86efac}
.vt-key-state.ok::before{content:'\2713';font-weight:800;color:#16a34a}
.vt-key-state.bad{color:#7f1d1d;background:#fee2e2;border:1px solid #fca5a5}
.vt-key-state.bad::before{content:'\2717';font-weight:800;color:#dc2626}
.vt-key-state.off{color:#202020;background:#f3f4f6;border:1px solid var(--border)}
/* Brand favicons next to scanner names */
.vt-brand-ico{width:20px;height:20px;flex-shrink:0;border-radius:4px;vertical-align:middle;object-fit:contain}
.vt-sc-row .vt-sc-name,.vt-provider .vt-radio strong,.vt-settings-title h3{display:inline-flex;align-items:center;gap:7px}
/* VirusTotal daily budget: two sliders, each with its name inside, coloured ranges and today's usage (orange) */
.vt-qs-today{display:flex;align-items:baseline;flex-wrap:wrap;gap:4px 14px;margin:2px 0 14px}
.vt-qs-today .big{font-size:26px;font-weight:700;color:var(--heading);font-variant-numeric:tabular-nums;line-height:1}
.vt-qs-today .lbl{font-size:13px;color:var(--text)}
.vt-qs-today .parts{font-size:12px;color:var(--muted);display:inline-flex;gap:14px;flex-wrap:wrap}
.vt-qs-today .parts>span{display:inline-flex;align-items:center;gap:5px}
.vt-qs-today .parts strong{color:var(--text);font-variant-numeric:tabular-nums}
.vt-sl{position:relative;height:44px;margin:0 10px;touch-action:none;user-select:none}
.vt-sl-track{position:absolute;inset:0;border-radius:8px;overflow:hidden;cursor:pointer;border:1px solid var(--border);
  background:repeating-linear-gradient(135deg,#f3f4f6 0 6px,#e9ebef 6px 12px)}
.vt-sl-fill,.vt-sl-part{position:absolute;top:0;bottom:0;left:0;overflow:hidden}
.vt-sl-fill{background:#dbeafe;border-right:1px solid var(--blue-border)}
.vt-sl-part.auto{background:#dbeafe}
.vt-sl-part.manual{background:#ede9fe;border-left:1px solid #c4b5fd}
.vt-sl-used{position:absolute;left:0;bottom:0;height:7px;background:#f59e0b;max-width:100%}
.vt-sl-used.on{min-width:3px}
.vt-sl-name{position:absolute;left:10px;top:5px;right:6px;display:flex;flex-direction:column;line-height:1.15;white-space:nowrap;overflow:hidden;pointer-events:none}
.vt-sl-name span{font-size:10.5px;text-transform:uppercase;letter-spacing:.4px;color:#1e40af;text-overflow:ellipsis;overflow:hidden}
.vt-sl-part.manual .vt-sl-name span{color:#5b21b6}
.vt-sl-part .vt-sl-name span{text-transform:none;letter-spacing:0;font-size:11.5px;font-weight:600}
.vt-sl-part.manual .vt-sl-name{left:10px}
.vt-sl-name strong{font-size:14px;color:var(--heading);font-variant-numeric:tabular-nums}
.vt-sl-knob{position:absolute;top:-6px;width:6px;height:56px;margin-left:-3px;border-radius:3px;background:#111827;box-shadow:0 0 0 2px #fff,0 1px 4px rgba(0,0,0,.35);cursor:ew-resize;outline:none}
.vt-sl-knob:focus-visible,.vt-sl-knob.drag{box-shadow:0 0 0 2px #fff,0 0 0 5px rgba(37,99,235,.35)}
.vt-sl-scale{position:relative;height:15px;margin:8px 10px 16px;font-size:10.5px;color:var(--muted);font-variant-numeric:tabular-nums}
.vt-sl-scale span{position:absolute;transform:translateX(-50%);white-space:nowrap}
.vt-sl-scale span:first-child{transform:none}.vt-sl-scale span:last-child{transform:translateX(-100%)}
.vt-qs-legend{display:flex;flex-wrap:wrap;align-items:center;gap:8px 22px;font-size:12px}
.vt-qs-legend>span,.vt-qs-legend>label{display:inline-flex;align-items:center;gap:6px;color:var(--muted)}
.vt-qs-legend input{width:78px;padding:5px 7px;border:1px solid var(--border);border-radius:5px;font:inherit;color:var(--text);background:var(--surface)}
.vt-qs-legend input#vt-s-manual-pct{width:56px}
.vt-qs-sw{width:11px;height:11px;border-radius:3px;display:inline-block;flex-shrink:0}
.vt-qs-sw.auto{background:#dbeafe;border:1px solid var(--blue-border)}
.vt-qs-sw.manual{background:#ede9fe;border:1px solid #c4b5fd}
.vt-qs-sw.free{background:repeating-linear-gradient(135deg,#f3f4f6 0 3px,#dde0e6 3px 6px);border:1px solid var(--border)}
.vt-qs-sw.used{background:#f59e0b}
/* AVG / Avast: one below the other */
.vt-provider-list{display:flex;flex-direction:column;margin-top:14px}
.vt-provider-list .vt-provider{grid-template-columns:1fr auto auto}
.vt-av-title{display:flex;flex-direction:column;gap:2px}
.vt-av-title small{font-weight:400;color:var(--muted);font-size:12px}
.vt-av-calls{font-size:12px;color:var(--muted);white-space:nowrap}
.vt-gsb-help summary{cursor:pointer;font-size:13px}
.vt-gsb-key{grid-column:1/-1;display:flex;align-items:center;gap:10px;flex-wrap:wrap;font-size:12px;color:var(--muted)}
.vt-gsb-key input{flex:0 1 380px;padding:7px 9px;border:1px solid var(--border);border-radius:5px;font:inherit;font-family:monospace;color:var(--text);background:var(--surface)}
.vt-av-badge.stale{opacity:.6;border-style:dotted}
.vt-av-calls strong{font-size:16px;color:var(--heading);font-variant-numeric:tabular-nums;margin-right:4px}
@media(max-width:560px){#vtpanel-settings>.vt-card{padding:16px}.vt-provider-grid{grid-template-columns:1fr}.vt-settings-numbers{gap:14px}.vt-settings-title{flex-wrap:wrap}.vt-settings-title .hint{margin-left:0}}

/* Status-change events feed */
.vt-ev-wrap{margin-top:14px}
.vt-ev-row{display:flex;align-items:center;gap:12px;padding:7px 2px;border-bottom:1px solid var(--border);font-size:13px;flex-wrap:wrap}
.vt-ev-row:last-of-type{border-bottom:none}
.vt-ev-time{color:var(--muted);white-space:nowrap;flex-shrink:0;min-width:150px;font-variant-numeric:tabular-nums}
.vt-ev-time small{font-size:11px}
.vt-ev-domain{font-weight:600;flex:1;min-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--heading)}
.vt-ev-domain a{color:inherit;text-decoration:none}
.vt-ev-domain a:hover{text-decoration:underline}
.vt-ev-change{display:flex;align-items:center;gap:6px;flex-shrink:0}
.vt-ev-change .vt-badge{min-width:92px;justify-content:center}
.vt-ev-arrow{display:inline-flex;align-items:center;flex-shrink:0}
.vt-ev-arrow svg{width:22px;height:14px;display:block}
.vt-ev-change .vt-badge.gone{background:var(--gray-bg);color:var(--gray);border:1px dashed var(--gray)}

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
    <img class="vt-brand-logo" src="<?= htmlspecialchars(($this->baseUrl ?: '') . 'images/logo-128.png') ?>" alt="" width="48" height="48">
    <div class="vt-brand-text">
      <div class="vt-brand-name">StingyScan<?php if ($this->version): ?> <span class="vt-brand-ver">v<?= htmlspecialchars($this->version) ?></span><?php endif; ?></div>
      <div class="vt-brand-tag">Malware, phishing and spam monitoring for every hosted website on this server, powered by VirusTotal</div>
    </div>
    <a class="vt-brand-by" href="#about" onclick="vtShowTab('about');return false;" title="About the maker">
      <span>by</span> <strong>Digizaal</strong>
    </a>
  </div>

  <div class="vt-update-banner" id="vt-update-banner">
    <span class="vt-update-icon">🔔</span>
    <span id="vt-update-msg"></span>
  </div>

  <!-- Scan Monitor: fixed right panel, starts below the app header -->
  <div class="vt-queue" id="vt-queue">
    <div class="vt-q-head" onclick="vtQueueToggle(event)">
      <span class="vt-q-title">Scan Monitor</span>
      <span class="vt-q-sum" id="vt-q-sum"></span>
      <div class="vt-spacer"></div>
      <button class="vt-row-btn" id="vt-q-cancel" onclick="vtCancelWaiting();event.stopPropagation()" style="display:none">Cancel waiting</button>
      <button class="vt-row-btn" onclick="vtClearFinished();event.stopPropagation()" style="margin-left:4px">Clear done</button>
      <button class="vt-q-toggle" id="vt-q-toggle" onclick="event.stopPropagation();vtQueueToggle()">▲ Collapse</button>
    </div>
    <div class="vt-q-body" id="vt-q-body">
      <div class="vt-q-list" id="vt-q-list"></div>
      <div class="vt-q-pause" id="vt-q-pause">
        <div class="vt-q-ptrack"><div class="vt-q-pfill" id="vt-q-pfill"></div></div>
        <span id="vt-q-ptext"></span>
      </div>
    </div>
  </div>

  <div class="vt-tabs">
    <div class="vt-tab active" id="vtab-dashboard" onclick="vtShowTab('dashboard')">Dashboard</div>
    <div class="vt-tab" id="vtab-trend" onclick="vtShowTab('trend');vtLoadTrend()" style="display:none">Trend</div>
    <div class="vt-tab" id="vtab-settings" onclick="vtShowTab('settings')">Settings</div>
    <div class="vt-tab" id="vtab-log" onclick="vtShowTab('log');vtLoadLog()">Log</div>
    <div class="vt-tab" id="vtab-about" onclick="vtShowTab('about')">About</div>
  </div>

  <!-- Dashboard -->
  <div class="vt-panel active" id="vtpanel-dashboard">

<?php if (!empty($this->oldVtInstalled)): ?>
    <div class="vt-alert warn">
      <strong>VT Site Scanner (the old name of StingyScan) is still installed.</strong> Its settings, keys and history have been copied to StingyScan,
      but both run their own scans and use your VirusTotal budget. Remove the old one as root:
      <code>plesk bin extension --uninstall virustotal-extension</code>
    </div>
<?php endif; ?>
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
      <input class="vt-search" type="text" id="vt-search" placeholder="Search domain or customer..." oninput="vtApplyFilter()">
      <div class="vt-filters">
        <div class="vt-fgroup" data-f="all">
          <button class="vt-fbtn active" data-f="all" onclick="vtSetFilter('all')">All <strong id="vt-cnt-all">—</strong></button>
          <button class="vt-fcaret" onclick="vtToggleMenu(event,'all')" aria-label="All options">&#9662;</button>
          <div class="vt-fmenu" id="vt-fmenu-all">
            <button onclick="vtMenuAction('rescan','all')">Rescan</button>
            <button onclick="vtMenuAction('refresh')">Refresh site list</button>
          </div>
        </div>
        <div class="vt-fgroup" data-f="infected">
          <button class="vt-fbtn" data-f="infected" onclick="vtSetFilter('infected')"><span class="vt-dot dot-r"></span> Infected <strong id="vt-cnt-infected">0</strong></button>
          <button class="vt-fcaret" onclick="vtToggleMenu(event,'infected')" aria-label="Infected options">&#9662;</button>
          <div class="vt-fmenu" id="vt-fmenu-infected">
            <button onclick="vtMenuAction('rescan','infected')">Rescan</button>
          </div>
        </div>
        <div class="vt-fgroup" data-f="suspicious">
          <button class="vt-fbtn" data-f="suspicious" onclick="vtSetFilter('suspicious')"><span class="vt-dot dot-y"></span> Suspicious <strong id="vt-cnt-suspicious">0</strong></button>
          <button class="vt-fcaret" onclick="vtToggleMenu(event,'suspicious')" aria-label="Suspicious options">&#9662;</button>
          <div class="vt-fmenu" id="vt-fmenu-suspicious">
            <button onclick="vtMenuAction('rescan','suspicious')">Rescan</button>
          </div>
        </div>
        <div class="vt-fgroup" data-f="clean">
          <button class="vt-fbtn" data-f="clean" onclick="vtSetFilter('clean')"><span class="vt-dot dot-g"></span> Clean <strong id="vt-cnt-clean">0</strong></button>
          <button class="vt-fcaret" onclick="vtToggleMenu(event,'clean')" aria-label="Clean options">&#9662;</button>
          <div class="vt-fmenu" id="vt-fmenu-clean">
            <button onclick="vtMenuAction('rescan','clean')">Rescan</button>
          </div>
        </div>
        <div class="vt-fgroup" data-f="pending">
          <button class="vt-fbtn" data-f="pending" onclick="vtSetFilter('pending')"><span class="vt-dot dot-b"></span> Queued <strong id="vt-cnt-pending">0</strong></button>
          <button class="vt-fcaret" onclick="vtToggleMenu(event,'pending')" aria-label="Queued options">&#9662;</button>
          <div class="vt-fmenu" id="vt-fmenu-pending">
            <button onclick="vtMenuAction('rescan','pending')">Rescan</button>
          </div>
        </div>
        <div class="vt-fgroup" data-f="error">
          <button class="vt-fbtn" data-f="error" onclick="vtSetFilter('error')"><span class="vt-dot dot-gr"></span> Error <strong id="vt-cnt-error">0</strong></button>
          <button class="vt-fcaret" onclick="vtToggleMenu(event,'error')" aria-label="Error options">&#9662;</button>
          <div class="vt-fmenu" id="vt-fmenu-error">
            <button onclick="vtMenuAction('rescan','error')">Rescan</button>
          </div>
        </div>
        <div class="vt-fgroup" data-f="orphan" id="vt-fgroup-orphan" style="display:none">
          <button class="vt-fbtn" data-f="orphan" onclick="vtSetFilter('orphan')" title="Leftover WordPress databases for hostnames that no longer exist"><span class="vt-dot dot-gr"></span> Orphan DBs <strong id="vt-cnt-orphan">0</strong></button>
        </div>
      </div>
      <div class="vt-spacer"></div>
      <div class="vt-seg" id="vt-view-seg" style="margin-right:4px"><button class="on" onclick="vtSetViewMode('group',this)">Group</button><button onclick="vtSetViewMode('sort',this)">Sort</button></div>
      <span class="vt-status-txt" id="vt-scan-status"></span>
    </div>

    <div id="vt-load-bar">
      <div id="vt-load-msg">Loading domain list…</div>
      <div class="vt-load-track"><div class="vt-load-fill" id="vt-load-fill"></div></div>
      <div id="vt-load-pct">0%</div>
    </div>
    <div class="vt-prog-wrap"><div class="vt-prog-fill" id="vt-prog"></div></div>

    <div class="vt-tbl-wrap">
      <table>
        <thead id="vt-thead-main">
          <tr>
            <th style="width:120px">Status</th>
            <th data-col="domain">Domain</th>
            <th style="width:155px" data-col="score">Score</th>
            <th style="width:135px" data-col="lastscan">Last scan</th>
            <th style="width:85px;text-align:right">Action</th>
          </tr>
        </thead>
        <tbody id="vt-tbody"><tr class="vt-empty"><td colspan="5">Loading...</td></tr></tbody>
      </table>
    </div>

    <div class="vt-rel" id="vt-rel" style="display:none">
      <h3>Orphan databases <span class="vt-rel-cnt" id="vt-rel-cnt"></span></h3>
      <p class="hint">WordPress databases on this server for a hostname under one of your domains that doesn't exist in DNS:
        usually old copies, test or staging sites. Nobody can visit them, but VirusTotal often still lists them, sometimes as infected,
        and an old, never-updated copy is an easy target. They are not looked up automatically; click <strong>Rescan</strong> to check one.
        If the copy is no longer needed: click <strong>Back up</strong> (a <code>.sql.gz</code> copy in the subscription's <code>private/vt-db-backups</code> folder),
        then delete it in Plesk. StingyScan never deletes anything itself.</p>
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
        <tbody id="vt-tbody-rel"></tbody>
      </table>
      </div>
    </div>
  </div>

  <!-- Log -->
  <div class="vt-panel" id="vtpanel-trend">
    <div class="vt-card" style="max-width:100%">
      <h3 style="display:flex;align-items:center;gap:10px;flex-wrap:wrap"><span id="vt-tr-h">Infections on this server</span>
        <span class="hint" style="font-weight:400" id="vt-tr-sub"></span>
        <span style="flex:1"></span>
        <span class="vt-seg" id="vt-tr-range">
          <button data-d="30" onclick="vtTrendRange(30)">1 month</button><button data-d="61" onclick="vtTrendRange(61)">2 months</button><button data-d="92" onclick="vtTrendRange(92)">3 months</button>
        </span>
      </h3>
      <div class="vt-tr-tiles" id="vt-tr-tiles"></div>
      <div class="vt-tr-legend"><span><i style="background:#d03b3b"></i>Infected</span><span><i style="background:#fab219"></i>Suspicious</span></div>
      <div class="vt-tr-chart" id="vt-tr-lines"></div>
      <div class="vt-tr-title">Sites cleaned per day <span class="hint">(infected or suspicious, then clean again)</span></div>
      <div class="vt-tr-chart" id="vt-tr-bars"></div>
      <div class="vt-tr-tip" id="vt-tr-tip"></div>
      <div class="vt-fs-note" id="vt-tr-note"></div>
      <details class="vt-tr-table"><summary>Show as table</summary><div id="vt-tr-table"></div></details>
      <div class="vt-ev-wrap">
        <div class="vt-tr-title">Recent status changes <span class="hint">(per site, newest first)</span></div>
        <div id="vt-ev-list"><span class="hint">Loading…</span></div>
      </div>
    </div>
  </div>

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
    <div class="vt-card vt-settings-main">
      <div class="vt-settings-title vt-svc-head"><h3><img class="vt-brand-ico lg" src="<?= htmlspecialchars(($this->baseUrl ?: '') . 'images/brands/virustotal.png') ?>" alt="" width="32" height="32">VirusTotal</h3><span class="vt-key-state" id="vt-vt-keystate"></span><span class="hint">Primary website reputation service</span></div>
      <div class="vt-field">
        <label for="vt-s-apikey">API Key</label>
        <input type="text" id="vt-s-apikey" placeholder="Paste your VirusTotal API key here..." autocomplete="off" spellcheck="false">
        <div class="hint">Get your free key at virustotal.com &rarr; Profile &rarr; API key</div>
        <div class="vt-key-display" id="vt-key-display">
          <div class="vt-key-label">Stored key</div>
          <div id="vt-key-value">—</div>
        </div>
      </div>
      <section class="vt-settings-usage" aria-label="VirusTotal daily API budget and usage today">
        <div class="vt-usage-heading"><strong>Daily API budget</strong><span>today: <span id="vts-date">—</span></span></div>
        <div class="vt-qs-today">
          <span class="big" id="vts-total">—</span><span class="lbl">VirusTotal calls made today</span>
          <span class="parts"><span><i class="vt-qs-sw auto"></i> background <strong id="vts-auto-label">—</strong></span>
            <span><i class="vt-qs-sw manual"></i> manual rescans <strong id="vts-manual-label">—</strong></span></span>
        </div>
        <div class="vt-settings-numbers" style="margin:0 0 16px">
          <div class="vt-field">
            <label for="vt-s-daily">Max calls / day</label>
            <input type="number" id="vt-s-daily" min="1" max="1000000" step="10" value="500">
          </div>
          <div class="vt-field wide"><label>&nbsp;</label><div class="hint" style="padding-top:8px">500 = free tier &nbsp;|&nbsp; more needs a premium key</div></div>
        </div>
        <div class="vt-sl" id="vt-sl-split">
          <div class="vt-sl-track" id="vt-sl-split-track">
            <div class="vt-sl-part auto" id="vt-sl-auto"><div class="vt-sl-used" id="vt-sl-auto-used" title="Background calls made today"></div>
              <div class="vt-sl-name"><span>Background scan</span><strong id="vt-sl-auto-val">—</strong></div></div>
            <div class="vt-sl-part manual" id="vt-sl-manual"><div class="vt-sl-used" id="vt-sl-manual-used" title="Manual rescans made today"></div>
              <div class="vt-sl-name" title="Reserved for manual scans (%)"><span>Reserved for manual scans</span><strong id="vt-sl-manual-val">—</strong></div></div>
          </div>
          <div class="vt-sl-knob split" id="vt-sl-split-knob" role="slider" tabindex="0" aria-label="Reserved for manual scans (%)" title="Drag: reserved for manual scans (%)"></div>
        </div>
        <div class="vt-sl-scale"><span style="left:0">0%</span><span style="left:25%">25%</span><span style="left:50%">50%</span><span style="left:75%">75%</span><span style="left:100%">100%</span></div>
        <div class="vt-qs-legend">
          <label for="vt-s-manual-pct">Reserved for manual scans <input type="number" id="vt-s-manual-pct" min="5" max="90" step="1" value="20"> %</label>
          <span><i class="vt-qs-sw used"></i>used today</span>
        </div>
        <input type="hidden" id="vt-s-share" value="80">
        <div class="hint" style="margin-top:6px">Drag the black line: what is left of it is for the nightly background scan, what is right of it is kept for manual rescans.</div>
      </section>
      <div class="vt-settings-numbers">
        <div class="vt-field">
          <label for="vt-s-clean-ttl">Cache clean sites (days)</label>
          <input type="number" id="vt-s-clean-ttl" min="1" max="365" value="30">
        </div>
        <div class="vt-field">
          <label for="vt-s-inf-ttl">Cache infected sites (days)</label>
          <input type="number" id="vt-s-inf-ttl" min="1" max="30" value="1">
        </div>
      </div>
      <div class="vt-save-row">
        <button class="vt-btn primary" onclick="vtSaveSettings()">Save settings</button>
        <span class="vt-smsg" id="vt-smsg"></span>
      </div>
    </div>

    <div class="vt-card" id="vt-gsb-card" style="display:none">
      <div class="vt-settings-title"><h3>Google Safe Browsing</h3><span class="hint">Official Google API &middot; optional</span></div>
      <div class="vt-provider-list" style="margin-top:0">
        <div class="vt-provider vt-svc google" style="margin-top:0">
          <label class="vt-radio"><input type="checkbox" id="vt-gsb-on"><span class="vt-av-title"><strong><img class="vt-brand-ico lg" src="<?= htmlspecialchars(($this->baseUrl ?: '') . 'images/brands/google.png') ?>" alt="" width="32" height="32">Google Safe Browsing <span class="vt-key-state" id="vt-gsb-keystate"></span></strong><small>Google's list of unsafe sites, used by Chrome, Firefox and Safari to warn visitors</small></span></label>
          <span class="vt-av-calls"><strong id="vt-gsb-calls">0</strong>calls today</span>
          <label class="vt-rate" for="vt-rate-google"><input type="number" id="vt-rate-google" min="1" max="5" step="1" value="2" data-check-rate="google" aria-label="Google requests per minute"> / minute</label>
          <div class="vt-gsb-key"><label for="vt-gsb-key">API key</label><input type="password" id="vt-gsb-key" placeholder="Paste a new Google API key to replace the stored one" autocomplete="off" spellcheck="false"><span id="vt-gsb-stored">no key stored</span></div>
          <span class="hint">Every site is checked each night (up to 250 per request), and live when you open its details or rescan it. Adds no VirusTotal API usage.</span>
        </div>
      </div>
      <details class="vt-gsb-help" style="margin-top:12px">
        <summary><strong>How to get an API key</strong> (Google Cloud Console, free)</summary>
        <ol class="vt-why-steps">
          <li>Open or create a project in the <a href="https://console.cloud.google.com/projectselector2/home/dashboard" target="_blank" rel="noopener">Google Cloud Console &#8599;</a>.</li>
          <li>Enable the API: <a href="https://console.cloud.google.com/apis/library/safebrowsing.googleapis.com" target="_blank" rel="noopener">Safe Browsing API &rarr; Enable &#8599;</a>.</li>
          <li>Create the key: <a href="https://console.cloud.google.com/apis/credentials" target="_blank" rel="noopener">APIs &amp; Services &rarr; Credentials &#8599;</a> &rarr; <em>+ Create credentials</em> &rarr; <em>API key</em>.</li>
          <li>Restrict it (Edit API key): <em>API restrictions</em> &rarr; only <strong>Safe Browsing API</strong>; <em>Application restrictions</em> &rarr; <strong>IP addresses</strong> &rarr; this server's outgoing IP address.</li>
          <li>Paste the key above and click <strong>Save settings</strong>. It is tested with Google's own test page first; the green check shows it works.</li>
          <li>Usage and limits: <a href="https://console.cloud.google.com/apis/api/safebrowsing.googleapis.com/quotas" target="_blank" rel="noopener">Quotas &#8599;</a> and <a href="https://console.cloud.google.com/apis/api/safebrowsing.googleapis.com/metrics" target="_blank" rel="noopener">Metrics &#8599;</a> of the Safe Browsing API.</li>
        </ol>
        <div class="hint">Google allows the Safe Browsing API for <strong>non-commercial use only</strong>. Only the site addresses are sent; Google may use them to improve its services. A result is used for 30 minutes at most, then shown as <em>last known</em> and checked again.</div>
      </details>
      <div class="vt-save-row">
        <button class="vt-btn primary" onclick="vtSaveSettings()">Save settings</button>
      </div>
    </div>

    <div class="vt-card vt-settings-experimental" id="vt-av-card" style="display:none">
      <div class="vt-settings-title"><h3>External reputation checks</h3><span class="vt-experimental-tag">Experimental</span></div>
      <p class="hint">Optional direct checks with AVG and Avast. These unofficial services may change or become unavailable. Checks run when opening site details and after a rescan.</p>
      <div class="vt-provider-list">
        <div class="vt-provider vt-svc avg">
          <label class="vt-radio"><input type="checkbox" name="vt-av" value="147"><span class="vt-av-title"><strong><img class="vt-brand-ico lg" src="<?= htmlspecialchars(($this->baseUrl ?: '') . 'images/brands/avg.png') ?>" alt="" width="32" height="32">AVG Web Shield</strong><small>URL reputation of AVG AntiVirus and the AVG Online Security browser extension</small></span></label>
          <span class="vt-av-calls"><strong id="vt-av-calls-147">0</strong>calls today</span>
          <label class="vt-rate" for="vt-rate-avg"><input type="number" id="vt-rate-avg" min="1" max="5" step="1" value="2" data-check-rate="avg" aria-label="AVG requests per minute"> / minute</label>
          <span class="hint">The verdict an AVG user gets when opening the site, asked directly from AVG's reputation service. Adds no VirusTotal API usage.</span>
        </div>
        <div class="vt-provider vt-svc avast">
          <label class="vt-radio"><input type="checkbox" name="vt-av" value="146"><span class="vt-av-title"><strong><img class="vt-brand-ico lg" src="<?= htmlspecialchars(($this->baseUrl ?: '') . 'images/brands/avast.png') ?>" alt="" width="32" height="32">Avast Web Shield</strong><small>URL reputation of Avast Antivirus and the Avast Online Security &amp; Privacy browser extension</small></span></label>
          <span class="vt-av-calls"><strong id="vt-av-calls-146">0</strong>calls today</span>
          <label class="vt-rate" for="vt-rate-avast"><input type="number" id="vt-rate-avast" min="1" max="5" step="1" value="2" data-check-rate="avast" aria-label="Avast requests per minute"> / minute</label>
          <span class="hint">The verdict an Avast user gets when opening the site, asked directly from Avast's reputation service. Can be switched on separately from AVG.</span>
        </div>
      </div>
      <div class="vt-save-row">
        <button class="vt-btn primary" onclick="vtSaveSettings()">Save settings</button>
      </div>
    </div>

    <div class="vt-card">
      <h3 style="display:flex;align-items:center;gap:10px">Internal serverside file scanners
        <div style="flex:1"></div>
        <button class="vt-btn ghost" type="button" onclick="vtDetectScanners()" id="vt-sc-detect">Detect again</button></h3>
      <div class="hint" style="margin-bottom:10px">Enable each detected scanner separately. The rate limits how often a scan can start; file scans still run one at a time. Imunify only reads existing results.</div>
      <div id="vt-sc-list"><span class="hint">Loading…</span></div>
      <span class="vt-smsg" id="vt-sc-msg"></span>
      <div class="vt-save-row"><button class="vt-btn primary" onclick="vtSaveSettings()">Save settings</button><span class="hint">Scanner switches save immediately. Save settings to apply request limits.</span></div>
    </div>

    <div class="vt-card">
      <h3>Sites to scan</h3>
      <label class="vt-radio"><input type="radio" name="vt-s-scope" id="vt-s-scope-all" value="all" checked>
        <span><strong>All hosted websites</strong><br><span class="hint">Every active domain and subdomain with web hosting, whatever it runs</span></span></label>
      <label class="vt-radio"><input type="radio" name="vt-s-scope" id="vt-s-scope-wp" value="wp">
        <span><strong>WordPress sites only</strong><br><span class="hint">Only sites with a WordPress database on this server</span></span></label>
    </div>

    <div class="vt-card">
      <h3>Notifications</h3>
      <label class="vt-radio"><input type="checkbox" id="vt-n-enabled" onchange="vtNotifyUi()">
        <span><strong>E-mail me when a site's status changes</strong><br><span class="hint">One summary with the changes and all infected sites</span></span></label>
      <div id="vt-n-opts">
        <div class="vt-n-group">
          <div class="vt-n-label">When a site becomes</div>
          <label class="vt-check"><input type="checkbox" name="vt-n-ev" value="infected"> <span class="vt-dot dot-r"></span> Infected</label>
          <label class="vt-check"><input type="checkbox" name="vt-n-ev" value="suspicious"> <span class="vt-dot dot-y"></span> Suspicious</label>
          <label class="vt-check"><input type="checkbox" name="vt-n-ev" value="clean"> <span class="vt-dot dot-g"></span> Clean again <span class="hint">(after being infected or suspicious)</span></label>
        </div>
        <div class="vt-n-group">
          <div class="vt-n-label">Send to</div>
          <input type="email" id="vt-n-email" class="vt-n-email" placeholder="" autocomplete="email">
          <div class="hint" style="margin-top:4px">One address. Leave empty to use the address of your Plesk account.</div>
        </div>
        <div class="vt-n-group hint" style="font-size:12px;line-height:1.5">
          You get <strong>one summary e-mail</strong> with the status changes and the full list of your infected sites:
          after the nightly scan, and during the day 10 minutes after a change (so several changes share one e-mail).
        </div>
        <button class="vt-btn ghost" type="button" onclick="vtTestNotify()">Send test e-mail</button>
        <span class="vt-smsg" id="vt-n-msg"></span>
      </div>
      <div class="vt-save-row">
        <button class="vt-btn primary" onclick="vtSaveSettings()">Save settings</button>
        <span class="vt-smsg" id="vt-smsg2"></span>
      </div>
    </div>

    <div class="vt-card" id="vt-lr-card" style="display:none">
      <h3>Log rotation</h3>
      <div class="hint">Keeps the scan logs small. A log larger than the limit is compressed to an old copy and a new log is started. Server-wide; checked every 5 minutes.</div>
      <div class="vt-settings-numbers">
        <div class="vt-field">
          <label for="vt-s-log-mb">Rotate above (MB)</label>
          <input type="number" id="vt-s-log-mb" min="1" max="100" value="5">
        </div>
        <div class="vt-field">
          <label for="vt-s-log-keep">Old copies to keep</label>
          <input type="number" id="vt-s-log-keep" min="1" max="30" value="5">
        </div>
        <div class="vt-field">
          <label for="vt-s-log-days">Delete copies after (days)</label>
          <input type="number" id="vt-s-log-days" min="1" max="365" value="90">
        </div>
      </div>
      <div class="hint" id="vt-lr-info" style="margin-top:10px"></div>
      <div class="vt-save-row">
        <button class="vt-btn primary" onclick="vtSaveSettings()">Save settings</button>
      </div>
    </div>


  </div>

  <!-- About -->
  <div class="vt-panel" id="vtpanel-about">
    <div class="vt-about">
      <div class="vt-card vt-about-hero">
        <div class="vt-about-mark">DZ</div>
        <div>
          <h3 style="margin:0 0 4px">Made by Dirk Zaal</h3>
          <div class="vt-about-sub"><a href="https://www.digizaal.nl" target="_blank" rel="noopener">Digizaal</a> · web development and hosting, Amsterdam</div>
          <p>Digizaal builds and hosts websites for its customers, about 600 of them WordPress sites on one Plesk server.
          I built StingyScan because I needed it myself: one hacked site can hurt its visitors, get the server's
          IP address blacklisted and drag every other site on the server down with it.</p>
          <p>I used the VirusTotal scanner that comes with Plesk, but every day it told me the API calls were exhausted:
          not all sites had been scanned, and the rest stayed unscanned. So I wrote a scanner that spends the free
          daily quota carefully, remembers results, and gets round to every site.</p>
          <a class="vt-btn ghost" href="https://www.digizaal.nl" target="_blank" rel="noopener">Visit digizaal.nl &rarr;</a>
        </div>
      </div>

      <div class="vt-card">
        <h3>Who this tool is for</h3>
        <p>For hosting companies, agencies and server admins who look after <strong>more sites than VirusTotal's free API can check in a day</strong>.
        The free key allows 500 requests per day and 4 per minute. With 500 sites or more you can't check them all daily,
        and a simple scanner burns the whole quota before you get to the site you just cleaned up.</p>
        <ul class="vt-about-list">
          <li><strong>Every site gets its turn.</strong> The nightly scan picks up where it stopped, so all sites are checked in rotation, however many there are.</li>
          <li><strong>Quota left for you.</strong> Part of the daily budget is always kept for manual rescans, for the moment you need one.</li>
          <li><strong>No wasted calls.</strong> Results are cached: clean sites are re-checked after 30 days, infected or suspicious sites after 1 day.</li>
          <li><strong>One overview.</strong> All sites with their status on one page and on the Plesk Home page, so a flagged site stands out right away.</li>
        </ul>
      </div>

      <div class="vt-card">
        <h3>Cleaned, but still red?</h3>
        <p>The status in this extension comes from <strong>VirusTotal</strong>, not from the files on your server. VirusTotal shows what dozens of
        security vendors think of the site, and each vendor keeps its own blocklist. After you clean a site, it can stay
        <strong>infected</strong> or <strong>suspicious</strong> here for days or even weeks, until those vendors check it again and take it off their list.</p>
        <ul class="vt-about-list">
          <li><strong>Click the score</strong> (for example <em>8/92 · Why?</em>) to see which vendors flag the site, with a link per vendor to ask for a review.</li>
          <li><strong>Click Rescan</strong> after cleaning. VirusTotal then asks all vendors again; the ones that find nothing turn clean right away.</li>
          <li><strong>Ask the rest for a review</strong>, and for Chrome warnings use Google Search Console. Then rescan again after a few days.</li>
        </ul>
        <p class="hint">Before asking for a review, make sure the site is really clean and safe: updates installed, unused plugins removed and all passwords changed. Otherwise it is soon hacked again and listed again.</p>
      </div>

      <div class="vt-card">
        <h3>What's new</h3>
        <p>You are using version <strong><?= htmlspecialchars($this->version ?: '?') ?></strong>. See what changed in this version and in every version before it.</p>
        <a class="vt-btn ghost" href="https://github.com/dzaal/StingyScan-for-Plesk/releases/latest" target="_blank" rel="noopener">Latest changes &rarr;</a>
        <a class="vt-btn ghost" href="https://github.com/dzaal/StingyScan-for-Plesk/blob/main/CHANGELOG.md" target="_blank" rel="noopener">All versions &rarr;</a>
      </div>

      <div class="vt-card">
        <h3>License and trademarks</h3>
        <p>&copy; 2026 Dirk Zaal, trading as Digizaal. All rights reserved. StingyScan, its code, design and the way it works, is the intellectual property of Dirk Zaal. You may install and use it exactly as published, free of charge; changing or distributing it needs written permission. <a href="https://github.com/dzaal/StingyScan-for-Plesk/blob/main/LICENSE" target="_blank" rel="noopener">License &rarr;</a></p>
        <p class="hint">StingyScan is an independent product. It is not affiliated with, endorsed or sponsored by VirusTotal, Google, Gen Digital (AVG, Avast), WebPros (Plesk) or the makers of the file scanners. VirusTotal, Google Safe Browsing and Chrome are trademarks of Google LLC; AVG and Avast of Gen Digital Inc.; Plesk of WebPros International GmbH; WordPress of the WordPress Foundation; ClamAV of Cisco Systems, Inc.; Imunify360 and ImunifyAV of CloudLinux Software, Inc. All other names and logos belong to their respective owners and are used only to identify their services. <a href="https://github.com/dzaal/StingyScan-for-Plesk#trademarks" target="_blank" rel="noopener">All trademarks &rarr;</a></p>
      </div>

      <div class="vt-card vt-coffee">
        <div class="vt-coffee-cup" aria-hidden="true">&#9749;</div>
        <div style="flex:1">
          <h3 style="margin:0 0 4px">Buy me a cup of coffee</h3>
          <p style="margin:0">StingyScan is free. If it saves you time or catches a hacked site, a coffee is a nice way to say thanks.</p>
        </div>
        <?php if ($this->paypalUrl): ?>
          <a class="vt-paypal" href="<?= htmlspecialchars($this->paypalUrl) ?>" target="_blank" rel="noopener">
            <span class="vt-paypal-pp">Pay<b>Pal</b></span> Buy me a coffee &middot; $3
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div><!-- #vt-wrap -->

<div class="vt-toasts" id="vt-toasts"></div>


<script>
// Opened inside a Plesk frame (e.g. a custom button with "Open URL in Plesk"):
// that nests a second Plesk UI, so notifications appear twice. Break out.
try{ if(window.top!==window && top.location.origin===location.origin) top.location.replace(location.href); }catch(e){}
(function(){
const BASE = <?= json_encode($this->baseUrl ?: '') ?> || location.pathname;
const BRAND_IMG = <?= json_encode(($this->baseUrl ?: '') . 'images/brands/') ?>;
const BRAND_ICO = {clamav:1,maldet:1,imunify:1,virustotal:1,avast:1,avg:1,google:1,146:'avast',147:'avg'};
/** 20px favicon of a scanner or reputation service, or '' when there is none */
function vtBrandIco(id){const k=BRAND_ICO[id]; if(!k) return ''; return '<img class="vt-brand-ico" src="'+BRAND_IMG+(k===1?id:k)+'.png" alt="" width="20" height="20">';}

// Keep --vt-header-h in sync so the fixed Scan Monitor panel starts right below the app header
function vtSyncHeaderH(){
  const brand=document.querySelector('.vt-brand');
  const banner=document.getElementById('vt-update-banner');
  const h=(brand?brand.offsetHeight:56)+((banner&&banner.offsetHeight>0)?banner.offsetHeight:0);
  document.documentElement.style.setProperty('--vt-header-h',h+'px');
}
window.addEventListener('resize',vtSyncHeaderH);
document.addEventListener('DOMContentLoaded',vtSyncHeaderH);

let domains=[], results={}, budget={}, activeFilter='all', gsb={enabled:false};
let scanners=[], scans={}, fsOpen={}, fsFull={}, reviews={}, histSites=new Set(), siteHist={}, avast={products:[]}; // file scanners: installed list, results per site, open panel per site
let scanQueue=[], inFlight=0;
const MAX_C=1;          // one at a time to stay within VT rate limits
const PUMP_DELAY=15500; // 15.5s between requests = safe for 4 req/min free tier
const MAX_PUMP=10;      // max domains to auto-scan per page load (cron handles bulk)
const LABELS={
  infected:'Infected',suspicious:'Suspicious',clean:'Clean',
  loading:'Loading',pending:'Queued',error:'Error',
  ratelimit:'Rate limit',quota_exceeded:'Quota exceeded',budget_exceeded:'Daily limit',
  scanning:'Scanning...',waiting:'VT processing...',nokey:'Needs API key',unchecked:'Not checked'
};
let related=[], relSet=new Set(), orphans={}, owners={}, grpClosed=new Set(); // hostnames from WordPress databases that are not in DNS, and their databases
let noKey=false; // no (valid) VirusTotal API key: don't look anything up yet
const DOT_CLS={infected:'dot-r',suspicious:'dot-y',clean:'dot-g',pending:'dot-b'};
// Dangerous VT tags to highlight
const DANGER_TAGS=['malware','phishing','spam','ransomware','trojan','malicious','phishing-site','malware-site'];

// ── Init ──────────────────────────────────────────────────────
function vtLoadProgress(pct,msg){
  const bar=document.getElementById('vt-load-bar');
  if(!bar) return;
  if(msg) document.getElementById('vt-load-msg').textContent=msg;
  document.getElementById('vt-load-fill').style.width=Math.min(pct,100)+'%';
  document.getElementById('vt-load-pct').textContent=Math.min(pct,100)+'%';
  if(pct>=100) setTimeout(()=>bar.classList.add('vt-load-done'),500);
}

async function init(){
  // Phase 1 — fast: get domain list only, render table skeleton immediately
  vtLoadProgress(5,'Loading domain list…');
  const quick=await vtFetch('?action=domains-quick').catch(()=>({}));
  domains=quick.domains||[];
  budget=quick.budget||{};
  vtSet('vt-cnt-all',domains.length);
  updateBudget();
  for(const d of domains) if(!results[d]) results[d]={status:'loading',domain:d};
  document.querySelectorAll('#vt-view-seg button').forEach(b=>b.classList.toggle('on',b.textContent.toLowerCase()===viewMode));
  renderTable(); updateCounts();
  vtLoadProgress(30,`Found ${domains.length} domain${domains.length===1?'':'s'} — loading scan results…`);

  // Phase 2 — full data: results, owners, scanners, history
  const data=await vtFetch('?action=all').catch(()=>({}));
  domains=data.domains||domains; related=data.related||[]; relSet=new Set(related); orphans=data.orphans||{}; owners=data.owners||{}; results=data.results||{};
  for(const d of related) if(!results[d]) results[d]={status:'unchecked',domain:d}; budget=data.budget||budget; scanners=data.scanners||[]; scans=data.scans||{}; reviews=data.reviews||{}; histSites=new Set(data.history||[]); avast=data.avast||{products:[]}; gsb=data.gsb||{enabled:false};
  for(const d in scans) for(const id in scans[d]) if(fsBusy(d,id)) vtFsPoll(d,id);
  vtSet('vt-cnt-all',domains.length);
  document.querySelectorAll('#vt-view-seg button').forEach(b=>b.classList.toggle('on',b.textContent.toLowerCase()===viewMode));
  updateBudget(); renderTable(); updateCounts();
  vtLoadProgress(100,'Ready');

  const cfg=await vtFetch('?action=get-settings').catch(()=>({}));
  if(!cfg.has_key||cfg.api_key_invalid) document.getElementById('vt-api-warning').style.display='flex';
  loadSettingsForm(cfg);
  document.getElementById('vtab-trend').style.display=''; // admin: the server; reseller and customer: their own sites
  if(!cfg.has_key||cfg.api_key_invalid){
    // Without a working key every lookup fails: show the sites as waiting for a key, not as errors
    noKey=true;
    for(const d of domains) if(!results[d]||results[d].status==='error') results[d]={status:'nokey',domain:d};
    scanQueue=[]; renderTable(); updateCounts();
    return;
  }

  // Auto-pump: only scan truly unseen domains (no cache at all) + errors
  // Cron handles bulk stale refresh; web UI just fills gaps
  scanQueue=domains
    .filter(d=>!results[d]||['error','ratelimit','pending'].includes(results[d].status))
    .slice(0, MAX_PUMP);
  renderTable(); updateCounts();
  pump();

  // Check server queue — show any domains queued from a previous browser session
  vtPollServerQueue().then(()=>{ if(serverQueue.length) startServerQueuePoll(); });
}
async function vtCheckUpdate(){
  try{
    const r=await vtFetch('?action=check-update');
    if(!r.update_available) return;
    const releaseLink='<a href="'+vtEsc(r.url)+'" target="_blank" rel="noopener">release notes</a>';
    let installBtn='';
    if(r.zip) installBtn='<button class="vt-update-btn" id="vt-update-install-btn" data-zip="'+vtEsc(r.zip)+'" data-ver="'+vtEsc(r.latest)+'" onclick="vtInstallUpdate(this.dataset.zip,this.dataset.ver)">Install v'+vtEsc(r.latest)+'</button>';
    document.getElementById('vt-update-msg').innerHTML=
      'Version <strong>v'+vtEsc(r.latest)+'</strong> is available (you have v'+vtEsc(r.current)+') — '+releaseLink+installBtn;
    document.getElementById('vt-update-banner').style.display='flex'; vtSyncHeaderH();
  }catch(e){}
}
window.vtInstallUpdate=async function(zip,ver){
  const fpToken=document.getElementById('forgery_protection_token')?.content||'';
  const btn=document.getElementById('vt-update-install-btn');
  btn.disabled=true; btn.innerHTML='<span class="vt-spin"></span> Installing…';
  const fail=(msg)=>{document.getElementById('vt-update-msg').textContent='Update failed: '+msg;};
  try{
    const res=await fetch(BASE+'?action=install-update',{method:'POST',headers:{'X-Forgery-Protection-Token':fpToken},body:new URLSearchParams({version:ver})});
    const j=await res.json();
    if(!res.ok||!j.ok) throw new Error(j.error||'Could not start updater');
    document.getElementById('vt-update-msg').textContent='Downloading and installing v'+ver+'…';
    for(let i=0;i<150;i++){
      await new Promise(resolve=>setTimeout(resolve,2000));
      let status;
      try{status=await vtFetch('?action=update-status');}catch(e){continue;}
      if(status.state==='failed'||status.error) throw new Error(status.error||status.message||'Plesk rejected the update');
      if(status.state==='done'){
        const installed=await vtFetch('?action=check-update');
        if(installed.current!==ver) throw new Error('Plesk finished, but the installed version is '+installed.current+' instead of '+ver);
        location.reload(); return;
      }
    }
    throw new Error('Update has not finished. Reload to check the installed version.');
  }catch(e){fail(e.message||'Connection lost. Reload to check the installed version.');}
};

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
let pumpRunning=false, pumpCurrent=null, pumpPauseFrom=0, pumpPauseUntil=0, pumpPauseLimit=false;
async function pump(){
  if(pumpRunning) return;
  pumpRunning=true;
  while(scanQueue.length){
    const d=scanQueue.shift();
    const st=results[d]?.status;
    if(activeJob(d)) continue; // being manually rescanned
    if(st&&st!=='error'&&st!=='ratelimit'&&st!=='quota_exceeded'&&st!=='pending') continue; // already has a valid result
    inFlight=1; pumpCurrent=d;
    const prev=results[d]||null, t0=Date.now();
    updateProgress();
    try{
      const data=await withVtSlot(()=>vtFetch('?action=scan&domain='+encodeURIComponent(d)));
      if(data.status==='ratelimit'){
        // Per-minute limit (shared with manual rescans): wait it out and retry
        scanQueue.unshift(d);
        const wait=Math.max(5,Number(data.wait||data.retry_after||60));
        inFlight=0; pumpCurrent=null; pumpPauseFrom=Date.now(); pumpPauseUntil=pumpPauseFrom+wait*1000; pumpPauseLimit=true; updateProgress();
        await delay(wait*1000);
        pumpPauseUntil=0; pumpPauseLimit=false;
        continue;
      }
      results[d]=data; updateRow(d,data); updateCounts();
      logAutoJob(d,prev,data,t0);
      if(data.status==='quota_exceeded'){ scanQueue=[]; inFlight=0; pumpCurrent=null; renderTable(); updateCounts(); break; }
      // Bump counters locally — no extra server round-trip needed
      if(data.status!=='error'){ budget.auto_used=(budget.auto_used??0)+1; budget.vt_quota_used=(budget.vt_quota_used??0)+1; updateBudget(); }
    }catch(e){
      results[d]={status:'error',domain:d}; updateRow(d,results[d]);
      logAutoJob(d,prev,results[d],t0);
    }
    inFlight=0; pumpCurrent=null;
    updateProgress(); updateCounts();
    if(scanQueue.length){ // respect 4 req/min limit
      pumpPauseFrom=Date.now(); pumpPauseUntil=pumpPauseFrom+PUMP_DELAY; pumpPauseLimit=false; renderQueue();
      await delay(PUMP_DELAY);
      pumpPauseUntil=0;
    }
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
  vtQsRender();
}

// ── VirusTotal daily budget (Settings) ─────────────────────────
// Max calls per day is a number field. The slider sets the share reserved for manual rescans; the rest
// is for the background scan. Each coloured range shows in orange how much of it was used today.
function vtQsVals(){
  const lim=Math.max(1,parseInt(document.getElementById('vt-s-daily').value)||500);
  const share=Math.min(95,Math.max(10,parseInt(document.getElementById('vt-s-share').value)||80));
  return {lim,share};
}
function vtQsRender(){
  if(!document.getElementById('vt-sl-split')) return;
  const {lim,share}=vtQsVals(), man=100-share, clamp=v=>Math.min(100,Math.max(0,v));
  const am=Math.floor(lim*share/100), mm=lim-am, au=budget.auto_used??0, mu=budget.manual_used??0;
  const st=(id,o)=>Object.assign(document.getElementById(id).style,o), on=(id,b)=>document.getElementById(id).classList.toggle('on',b);
  const n=x=>Number(x).toLocaleString('en-US');
  st('vt-sl-auto',{width:share+'%'}); st('vt-sl-manual',{left:share+'%',width:man+'%'});
  st('vt-sl-auto-used',{width:clamp(am?au/am*100:0)+'%'}); on('vt-sl-auto-used',au>0);
  st('vt-sl-manual-used',{width:clamp(mm?mu/mm*100:0)+'%'}); on('vt-sl-manual-used',mu>0);
  st('vt-sl-split-knob',{left:share+'%'});
  vtSet('vt-sl-auto-val',n(au)+' / '+n(am)+' used');
  vtSet('vt-sl-manual-val',man+'% · '+n(mu)+' / '+n(mm)+' used');
  vtSet('vts-total',n(au+mu)); vtSet('vts-auto-label',n(au)+' of '+n(am)); vtSet('vts-manual-label',n(mu)+' of '+n(mm));
  const mp=document.getElementById('vt-s-manual-pct'); if(document.activeElement!==mp) mp.value=man;
  const ks=document.getElementById('vt-sl-split-knob');
  ks.setAttribute('aria-valuemin',5); ks.setAttribute('aria-valuemax',90); ks.setAttribute('aria-valuenow',man);
}
(function vtQsInit(){
  if(!document.getElementById('vt-sl-split')) return;
  const share=document.getElementById('vt-s-share');
  const frac=x=>{const r=document.getElementById('vt-sl-split').getBoundingClientRect(); return Math.max(0,Math.min(1,(x-r.left)/r.width));};
  const setManual=p=>{share.value=100-Math.max(5,Math.min(90,Math.round(p))); vtQsRender();};
  const splitAt=x=>setManual(100-frac(x)*100), knob=document.getElementById('vt-sl-split-knob');
  const drag=el=>el.addEventListener('pointerdown',e=>{
    e.preventDefault(); e.stopPropagation();
    knob.focus(); knob.classList.add('drag'); el.setPointerCapture(e.pointerId); splitAt(e.clientX);
    const mv=ev=>splitAt(ev.clientX), up=()=>{knob.classList.remove('drag');el.removeEventListener('pointermove',mv);el.removeEventListener('pointerup',up);el.removeEventListener('pointercancel',up);};
    el.addEventListener('pointermove',mv); el.addEventListener('pointerup',up); el.addEventListener('pointercancel',up);
  });
  // Drag the line, or press anywhere on the bar and drag from there
  drag(knob); drag(document.getElementById('vt-sl-split-track'));
  knob.addEventListener('keydown',e=>{const d={ArrowLeft:-1,ArrowDown:-1,ArrowRight:1,ArrowUp:1}[e.key]; if(!d) return; e.preventDefault(); setManual(100-vtQsVals().share-d*(e.shiftKey?5:1));});
  document.getElementById('vt-s-daily').addEventListener('input',vtQsRender);
  const mp=document.getElementById('vt-s-manual-pct');
  mp.addEventListener('input',()=>{const v=parseInt(mp.value); if(v>=5&&v<=90){share.value=100-v; vtQsRender();}});
  mp.addEventListener('change',()=>setManual(parseInt(mp.value)||20));
  vtQsRender();
})();

function vtBar(id,pct){
  const el=document.getElementById(id);
  if(!el) return;
  el.style.width=pct+'%';
  el.className='vt-b-fill'+(pct>85?' crit':pct>65?' warn':'');
}

// ── Table ─────────────────────────────────────────────────────
function vtBytes(n){n=+n||0;const u=['B','KB','MB','GB'];let i=0;while(n>=1024&&i<3){n/=1024;i++;}return (i?n.toFixed(1):n)+' '+u[i];}
const ORPHAN_KIND={
  database:['free','No WordPress install on this server uses this database'],
  tables:['part','Old tables inside a database a live site uses: delete only these tables, not the database'],
  inuse:['live','A WordPress install on disk uses these tables: check before deleting'],
  unknown:['live','Could not check whether a site uses it']
};
function orphanInfoHTML(domain){
  return (orphans[domain]||[]).map(o=>{
    const k=ORPHAN_KIND[o.kind]||ORPHAN_KIND.unknown;
    return '<div class="vt-orphan"><code>'+vtEsc(o.db)+'</code>'+(o.kind==='tables'?' · tables <code>'+vtEsc(o.prefix)+'*</code>':'')
      +' · '+o.tables+' table'+(o.tables===1?'':'s')+' · <strong>'+vtBytes(o.size)+'</strong>'
      +(o.webspace?' · '+vtEsc(o.webspace):'')
      +(o.in_plesk===false?' · <span title="Created outside Plesk, so Plesk cannot show or delete it">not registered in Plesk: remove it with phpMyAdmin or the mysql command</span>':'')
      +'<div class="vt-orphan-kind '+k[0]+'">'+vtEsc(k[1])+(o.used_by&&o.used_by.length&&o.kind!=='database'?' ('+o.used_by.map(vtEsc).join(', ')+')':'')+'</div></div>';
  }).join('');
}
function orphanBtns(domain){
  const e=vtEsca(domain);
  return (orphans[domain]||[]).map(o=>{
    let h='';
    if(o.kind==='database'||o.kind==='tables') h+='<button class="vt-row-btn" title="Make a .sql.gz copy in the private folder of the subscription" onclick="vtDumpDb(\''+e+'\',\''+vtEsca(o.db)+'\',this)">Back up</button>';
    if(o.plesk_url) h+='<a class="vt-row-btn" href="'+vtEsc(o.plesk_url)+'" target="_top" title="Open the Databases page of '+vtEsc(o.webspace)+' in Plesk">Delete in Plesk ↗</a>';
    return h;
  }).join('');
}
window.vtDumpDb=async function(domain,db,btn){
  btn.disabled=true; btn.innerHTML='<span class="vt-spin"></span> Backing up';
  const fpToken=document.getElementById('forgery_protection_token')?.content||'';
  const res=await fetch(BASE+'?action=dump-db',{method:'POST',headers:{'X-Forgery-Protection-Token':fpToken},body:new URLSearchParams({domain,db})})
    .then(r=>r.json()).catch(()=>({error:'Network error'}));
  btn.disabled=false; btn.textContent='Back up';
  if(res.ok) vtToast('Backup saved: '+res.file+' ('+vtBytes(res.size)+')','ok');
  else vtToast(res.error||'The backup failed','err');
};
function renderTable(){
  const tbody=document.getElementById('vt-tbody');
  if(!domains.length){tbody.innerHTML='<tr class="vt-empty"><td colspan="5">No sites found. Click Refresh.</td></tr>';return;}
  updateSortHeaders();
  tbody.innerHTML=viewMode==='sort'?sortedDomains().map(d=>rowHTML(d,results[d]||null)).join(''):groupedRowsHTML();
  vtSet('vt-rel-cnt',related.length); vtSet('vt-cnt-orphan',related.length);
  document.getElementById('vt-fgroup-orphan').style.display=related.length?'':'none';
  if(!related.length&&activeFilter==='orphan') activeFilter='all';
  document.getElementById('vt-tbody-rel').innerHTML=related.map(d=>rowHTML(d,results[d]||null)).join('');
  vtApplyFilter();
}

// ── Grouping by reseller and customer ─────────────────────────
function grpKey(o){return (o.rl||'-')+'|'+o.cl;}
let viewMode=localStorage.getItem('vt_view_mode')||'group';
let sortCol=localStorage.getItem('vt_sort_col')||'domain';
let sortDir=localStorage.getItem('vt_sort_dir')||'asc';
window.vtSetViewMode=function(m,btn){
  viewMode=m;
  localStorage.setItem('vt_view_mode',m);
  document.querySelectorAll('#vt-view-seg button').forEach(b=>b.classList.toggle('on',b===btn));
  renderTable();
};
window.vtSortBy=function(col){
  if(sortCol===col) sortDir=sortDir==='asc'?'desc':'asc';
  else{sortCol=col;sortDir=col==='lastscan'?'desc':'asc';}
  localStorage.setItem('vt_sort_col',sortCol);
  localStorage.setItem('vt_sort_dir',sortDir);
  renderTable();
};
function sortedDomains(list){
  return [...(list||domains)].sort((a,b)=>{
    if(sortCol==='domain') return sortDir==='asc'?a.localeCompare(b,undefined,{sensitivity:'base'}):b.localeCompare(a,undefined,{sensitivity:'base'});
    if(sortCol==='score'){
      const va=results[a]?.malicious??-1, vb=results[b]?.malicious??-1;
      return sortDir==='asc'?va-vb:vb-va;
    }
    if(sortCol==='lastscan'){
      const va=results[a]?.cached_at??0, vb=results[b]?.cached_at??0;
      return sortDir==='asc'?va-vb:vb-va;
    }
    return 0;
  });
}
function updateSortHeaders(){
  const thead=document.getElementById('vt-thead-main');
  if(!thead) return;
  thead.querySelectorAll('th[data-col]').forEach(th=>{
    const col=th.dataset.col;
    th.classList.add('vt-sortable');
    th.classList.toggle('vt-sort-asc',sortCol===col&&sortDir==='asc');
    th.classList.toggle('vt-sort-desc',sortCol===col&&sortDir==='desc');
    th.onclick=()=>vtSortBy(col);
  });
}
function groupedRowsHTML(){
  const withOwner=domains.filter(d=>owners[d]);
  const custs=new Set(withOwner.map(d=>grpKey(owners[d])));
  if(custs.size<2) return domains.map(d=>rowHTML(d,results[d]||null)).join(''); // one customer: no headers
  const resellers=new Set(withOwner.map(d=>owners[d].rl).filter(Boolean));
  const byRes={};
  for(const d of domains){
    const o=owners[d]||{c:'Unknown owner',cl:'?',ct:'client',r:'',rl:''};
    const rk=o.rl||'-';
    (byRes[rk]=byRes[rk]||{name:o.r,cust:{}});
    const ck=grpKey(o);
    (byRes[rk].cust[ck]=byRes[rk].cust[ck]||{o,list:[]}).list.push(d);
  }
  const cmp=(a,b)=>a.localeCompare(b,undefined,{sensitivity:'base'});
  const resKeys=Object.keys(byRes).sort((a,b)=>a==='-'?-1:b==='-'?1:cmp(byRes[a].name,byRes[b].name));
  const bad=list=>list.filter(d=>['infected','suspicious'].includes(results[d]?.status)).length;
  let h='';
  for(const rk of resKeys){
    const R=byRes[rk], all=Object.values(R.cust).flatMap(c=>c.list);
    if(resellers.size){
      const b=bad(all);
      h+='<tr class="vt-grp-r" data-res="'+vtEsc(rk)+'"><td colspan="5">'
        +(rk==='-'?'Customers of the administrator':'<strong>'+vtEsc(R.name||rk)+'</strong><span class="vt-grp-badge">reseller</span> <span class="vt-grp-login">'+vtEsc(rk)+'</span>')
        +' <span class="vt-grp-n">'+all.length+' site'+(all.length===1?'':'s')+(b?' · <span class="bad">'+b+' flagged</span>':'')+'</span></td></tr>';
    }
    const custKeys=Object.keys(R.cust).sort((a,b)=>{const A=R.cust[a].o,B=R.cust[b].o;
      const w=o=>o.ct==='admin'||o.ct==='reseller'?0:1; return w(A)-w(B)||cmp(A.c,B.c);});
    for(const ck of custKeys){
      const C=R.cust[ck], b=bad(C.list), closed=grpClosed.has(ck);
      const label=C.o.ct==='reseller'?'Own sites of the reseller':C.o.ct==='admin'?'Administrator':vtEsc(C.o.c);
      h+='<tr class="vt-grp-c'+(closed?' closed':'')+'" data-res="'+vtEsc(rk)+'" data-gk="'+vtEsc(ck)+'" onclick="vtGrpToggle(this)"><td colspan="5">'
        +'<span class="vt-grp-caret">▾</span> '+label+(C.o.ct==='client'?' <span class="vt-grp-login">'+vtEsc(C.o.cl)+'</span>':'')
        +' <span class="vt-grp-n">'+C.list.length+' site'+(C.list.length===1?'':'s')+(b?' · <span class="bad">'+b+' flagged</span>':'')+'</span></td></tr>';
      h+=sortedDomains(C.list).map(d=>rowHTML(d,results[d]||null)).join('');
    }
  }
  return h;
}
window.vtGrpToggle=function(tr){
  const k=tr.dataset.gk;
  if(grpClosed.has(k)) grpClosed.delete(k); else grpClosed.add(k);
  tr.classList.toggle('closed',grpClosed.has(k));
  vtApplyFilter();
};
function updateRow(domain,data){
  const row=document.getElementById('vtr-'+rid(domain));
  if(!row){renderTable();return;}
  document.getElementById('vtfs-'+rid(domain))?.remove();
  row.outerHTML=rowHTML(domain,data);
  vtApplyFilter();
}

function rid(d){return d.replace(/[^a-z0-9]/gi,'_');}
function vtAgo(ts){
  if(!ts) return null;
  const s=Math.floor(Date.now()/1000-ts);
  if(s<60) return 'just now';
  if(s<3600) return Math.floor(s/60)+'m ago';
  if(s<86400) return Math.floor(s/3600)+'h ago';
  if(s<86400*30) return Math.floor(s/86400)+'d ago';
  if(s<86400*365) return Math.floor(s/86400/30)+'mo ago';
  return Math.floor(s/86400/365)+'y ago';
}

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
  // File scanner infected-files badge: show when any scanner has found infections
  let fsBadgeHtml='';
  if(!relSet.has(domain)&&scans[domain]){
    for(const id in scans[domain]){
      const r=scans[domain][id];
      if(r?.status==='done'&&r.infected>0){
        const e=vtEsca(domain);
        fsBadgeHtml+='<div><button type="button" class="vt-fs-badge" onclick="vtFsClick(\''+e+'\',\''+vtEsc(id)+'\')" title="Click to see the infected files found by '+vtEsc(fsName(id))+'">'
          +'<span class="vt-dot dot-r"></span>'+r.infected+' file'+(r.infected===1?'':'s')+' infected — '+vtEsc(fsName(id))+'</button></div>';
      }
    }
  }
  const avHtml=(avast.products||[]).map(pr=>{const avr=avast.results?.[domain]?.[pr.id];
  return avr&&avr.state==='ok'&&avr.findings?.length
    ?'<div><button type="button" class="vt-av-badge" onclick="vtWhyClick(\''+vtEsca(domain)+'\')" title="'+vtEsc(pr.name)+' classification: '+vtEsc(avr.findings.map(f=>f.raw).join(', '))+'"><span class="vt-dot dot-r"></span>'
      +vtEsc(pr.name)+': '+vtEsc(avr.findings.map(f=>f.classification.replace(/^URL:/,'')).join(', '))+'</button></div>':'';}).join('');
  const gr=gsb.enabled?gsb.results?.[domain]:null, gStale=gr&&Date.now()/1000-gr.checked_at>(gsb.fresh||1800);
  const gsbHtml=gr&&gr.state==='ok'&&gr.threats?.length
    ?'<div><button type="button" class="vt-av-badge'+(gStale?' stale':'')+'" onclick="vtWhyClick(\''+vtEsca(domain)+'\')" title="Google Safe Browsing'+(gStale?' (last known, checked again when you open the details)':'')+'"><span class="vt-dot dot-r"></span>Google: '+vtEsc(gr.threats.join(', '))+'</button></div>':'';
  const statusCell=badge+tagHtml+fsBadgeHtml+avHtml+gsbHtml;

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
  let domainCell='<span class="vt-domain-name">'+vtEsc(domain)+'</span>'+ageHtml+'<span class="vt-dlinks"><a href="https://'+vtEsc(domain)+'" target="_blank" rel="noopener">Site</a><a href="https://www.virustotal.com/gui/domain/'+vtEsc(domain)+'/detection" target="_blank" rel="noopener">VT</a></span>'+ipHtml;

  if(relSet.has(domain)) domainCell+=orphanInfoHTML(domain);

  let score='<span style="color:var(--muted)">—</span>';
  if(data?.malicious!==undefined&&data?.total!==undefined){
    const pct=data.total>0?Math.round(data.malicious/data.total*100):0;
    const c=data.malicious>0?'infected':'clean';
    const scoreNum=data.malicious>0?'<strong>'+data.malicious+'</strong>/'+data.total:data.malicious+'/'+data.total;
    score='<div class="vt-score"><div class="vt-sbar"><div class="vt-sbar-fill '+c+'" style="width:'+pct+'%"></div></div><span class="vt-snum '+c+'">'+scoreNum+'</span></div>';
    const avFlag=(avast.products||[]).some(pr=>avast.results?.[domain]?.[pr.id]?.findings?.length)||!!(gsb.enabled&&gsb.results?.[domain]?.threats?.length);
    const flagged=data.malicious>0||st==='suspicious'||(data.flagged_by||[]).length||avFlag;
    if(flagged||histSites.has(domain)||avast.products?.length||gsb.enabled){
      score='<button type="button" class="vt-why-btn'+(fsOpen[domain]==='vt'?' open':'')+'" title="'+(flagged?'Which vendors flag this site, how to get it reviewed after cleaning, and its history':'History of this site')+'" onclick="vtWhyClick(\''+vtEsca(domain)+'\')">'+score+'<span class="vt-why-lbl">'+(flagged?'Why?':histSites.has(domain)?'History':'Details')+' ▾</span></button>';
    }
  }else if(isScanning){score='<span class="vt-spin"></span>';}
  else if(st==='loading'){score='<span class="vt-spin"></span>';}
  else if(st==='error'){score='<span style="color:var(--red);font-size:12px">No result</span>';}

  let scanDate='—';
  if(data?.cached_at){
    const _ago=vtAgo(data.cached_at);
    const _full=new Date(data.cached_at*1000).toLocaleString('nl-NL',{day:'2-digit',month:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'});
    scanDate='<span title="Checked: '+_full+'" style="cursor:default">'+_ago+'</span>';
    if(data?.stale) scanDate+=' <span class="vt-stale">stale</span>';
    if(data?.last_scan) scanDate+='<div style="font-size:10px;color:var(--muted);margin-top:2px">VT: '+vtEsc(data.last_scan)+'</div>';
  }else if(data?.last_scan){
    scanDate=vtEsc(data.last_scan)+(data?.stale?'<span class="vt-stale">stale</span>':'');
  }else if(st==='loading'||isScanning) scanDate='<span class="vt-spin"></span>';
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
  actionBtn=(relSet.has(domain)?orphanBtns(domain):fsBarHTML(domain))+actionBtn; // orphan databases have no files to scan

  const own=owners[domain];
  return '<tr id="vtr-'+rid(domain)+'" data-domain="'+vtEsc(domain)+'" data-status="'+st+'"'+(inQueue(domain)?' data-job="1"':'')
    +(own?' data-grp="'+vtEsc(grpKey(own))+'" data-own="'+vtEsc((own.c+' '+own.cl+' '+own.r).toLowerCase())+'"':'')+'>'
    +'<td>'+statusCell+'</td>'
    +'<td>'+domainCell+'</td>'
    +'<td>'+score+'</td>'
    +'<td style="font-size:12px;color:var(--muted)">'+scanDate+'</td>'
    +'<td style="text-align:right;white-space:nowrap">'+actionBtn+'</td>'
    +'</tr>'+fsRowHTML(domain,st);
}

// ── Trend (server totals per day, administrator) ──────────────
const TR_COL={infected:'#d03b3b',suspicious:'#fab219',cleaned:'#0ca30c',grid:'#e1e0d9',axis:'#c3c2b7',muted:'#898781',surface:'#fcfcfb'};
let trDays=[], trRange=92;
window.vtLoadTrend=async function(){
  const [r,ev]=await Promise.all([
    vtFetch('?action=history').catch(()=>null),
    vtFetch('?action=events').catch(()=>null),
  ]);
  trDays=r?.days||[];
  document.getElementById('vt-tr-h').textContent=r?.scope==='own'?'Infections on your sites':'Infections on this server';
  vtTrendRange(trRange);
  vtRenderEvents(ev?.events||[]);
};
let evAll=[], evShowAll=false;
function vtRenderEvents(events){
  if(events) evAll=events;
  const el=document.getElementById('vt-ev-list');
  if(!el) return;
  if(!evAll.length){
    el.innerHTML='<span class="hint">No status changes recorded yet. The list fills as sites are scanned.</span>';
    return;
  }
  const LBL={infected:'Infected',suspicious:'Suspicious',clean:'Clean',gone:'Left the list'};
  const RANK={clean:0,suspicious:1,infected:2};
  const badge=st=>'<span class="vt-badge '+(LBL[st]?st:'unchecked')+'">'
    +(st==='gone'?'':'<span class="vt-dot" style="background:currentColor"></span>')+vtEsc(LBL[st]||st||'unknown')+'</span>';
  const pad=n=>String(n).padStart(2,'0');
  const shown=evShowAll?evAll:evAll.slice(0,50);
  el.innerHTML=shown.map(e=>{
    const d=new Date(e.ts*1000);
    const when=pad(d.getDate())+'-'+pad(d.getMonth()+1)+'-'+d.getFullYear()
      +' <small>('+(e.approx?'last seen ':'')+pad(d.getHours())+':'+pad(d.getMinutes())+')</small>';
    const direct=e.type==='av';
    const better=direct?(e.observed?null:!e.to):e.to==='gone'?null:(RANK[e.to]??0)<(RANK[e.from]??0);
    const avBadge=(value,first)=>'<span class="vt-badge '+(first?'unchecked':value?'infected':'clean')+'">'
      +vtEsc(e.product)+': '+(first?'First check':value?vtEsc(value.replace(/^URL:/,'')):'No classification')+'</span>';
    const change=direct?avBadge(e.from,!!e.observed):badge(e.from);
    const arrow='<span class="vt-ev-arrow" style="color:'+(better===null?'var(--muted)':better?'var(--green)':'var(--red)')+'" aria-label="'+(better===null?'to':better?'better: to':'worse: to')+'">'
      +'<svg viewBox="0 0 22 14" aria-hidden="true"><path d="M1 5h12V1l8 6-8 6V9H1z" fill="currentColor"/></svg></span>';
    return '<div class="vt-ev-row">'
      +'<div class="vt-ev-time">'+when+'</div>'
      +'<div class="vt-ev-change">'+change+arrow+(direct?avBadge(e.to,false):badge(e.to))+'</div>'
      +'<div class="vt-ev-domain"><a href="https://www.virustotal.com/gui/domain/'+vtEsc(e.domain)+'/detection" target="_blank" rel="noopener" title="Open on VirusTotal">'+vtEsc(e.domain)+'</a></div>'
      +'</div>';
  }).join('')
  +(evAll.length>shown.length?'<button class="vt-btn ghost" style="margin-top:8px" onclick="evShowAll=true;vtRenderEvents()">Show all '+evAll.length+'</button>':'');
}

window.vtTrendRange=function(n){
  trRange=n;
  document.querySelectorAll('#vt-tr-range button').forEach(b=>b.classList.toggle('on',+b.dataset.d===n));
  vtTrendDraw();
};
function trNice(max){for(const s of [1,2,5,10,20,25,50,100,200,250,500,1000]){if(max/s<=5)return s;}return Math.ceil(max/5);}
function trDate(iso,long){const p=iso.split('-');return long?p[2]+'-'+p[1]+'-'+p[0]:p[2]+'-'+p[1];}
function vtTrendDraw(){
  const days=trDays.slice(-trRange);
  const tiles=document.getElementById('vt-tr-tiles');
  if(!days.length){
    tiles.innerHTML='<span class="hint">No history yet. Totals are saved once a day from now on.</span>';
    ['vt-tr-lines','vt-tr-bars','vt-tr-table'].forEach(id=>document.getElementById(id).innerHTML='');
    return;
  }
  const first=days[0], last=days[days.length-1];
  const sum=k=>days.reduce((a,d)=>a+(+d[k]||0),0);
  const delta=k=>{const v=(+last[k]||0)-(+first[k]||0);return '<div class="d '+(v>0?'up':v<0?'down':'')+'">'+(v>0?'+':'')+v+' since '+trDate(first.date)+'</div>';};
  tiles.innerHTML=
    '<div class="vt-tr-tile"><div class="l">Infected now</div><div class="v">'+last.infected+'</div>'+delta('infected')+'</div>'
   +'<div class="vt-tr-tile"><div class="l">Suspicious now</div><div class="v">'+last.suspicious+'</div>'+delta('suspicious')+'</div>'
   +'<div class="vt-tr-tile"><div class="l">Cleaned in this period</div><div class="v">'+sum('cleaned')+'</div><div class="d">sites</div></div>'
   +'<div class="vt-tr-tile"><div class="l">Clean sites now</div><div class="v">'+last.clean+'</div><div class="d">of '+last.total+'</div></div>';
  document.getElementById('vt-tr-sub').textContent=trDate(first.date,1)+' to '+trDate(last.date,1);
  const demo=days.filter(d=>d.demo);
  document.getElementById('vt-tr-note').innerHTML=demo.length
    ?'<strong>Demo data:</strong> the days up to '+trDate(demo[demo.length-1].date,1)+' (shaded) are made-up example figures. Real totals replace them day by day.'
    :'Totals are saved once a day, by the nightly scan and when you open the dashboard.';

  const W=Math.max(320,document.getElementById('vt-tr-lines').clientWidth||900), L=36, R=14, T=10;
  const n=days.length, step=(W-L-R)/Math.max(1,n-1), x=i=>L+(n<=1?0:i*step), bw=Math.max(2,Math.min(24,(W-L-R)/n-2));
  const demoEnd=demo.length?days.findIndex(d=>!d.demo):-1;
  const shadeW=demo.length?(demoEnd<0?x(n-1)-L:x(demoEnd)-step/2-L):0;
  const shade=B=>shadeW>0?'<rect x="'+L+'" y="'+T+'" width="'+shadeW+'" height="'+(B-T)+'" fill="#f1f0ec"/>':'';
  const xticks=B=>{let h='';const st=Math.max(1,Math.round(n/6));for(let i=0;i<n;i+=st)h+='<text x="'+x(i)+'" y="'+(B+16)+'" font-size="11" fill="'+TR_COL.muted+'" text-anchor="middle">'+trDate(days[i].date)+'</text>';return h;};
  const yaxis=(max,B)=>{const st=trNice(max),top=Math.max(st,Math.ceil(max/st)*st);let h='';
    for(let v=0;v<=top;v+=st){const y=B-(v/top)*(B-T);
      h+='<line x1="'+L+'" x2="'+(W-R)+'" y1="'+y+'" y2="'+y+'" stroke="'+(v?TR_COL.grid:TR_COL.axis)+'" stroke-width="1"/>'
        +'<text x="'+(L-6)+'" y="'+(y+4)+'" font-size="11" fill="'+TR_COL.muted+'" text-anchor="end">'+v+'</text>';}
    return {h,top};};

  // Lines: infected and suspicious (one scale)
  const H1=260, B1=H1-24, max1=Math.max(5,...days.map(d=>Math.max(+d.infected||0,+d.suspicious||0)));
  const ya=yaxis(max1,B1), y1=v=>B1-(v/ya.top)*(B1-T);
  const path=k=>days.map((d,i)=>(i?'L':'M')+x(i).toFixed(1)+' '+y1(+d[k]||0).toFixed(1)).join(' ');
  const area=k=>path(k)+' L'+x(n-1).toFixed(1)+' '+B1+' L'+x(0).toFixed(1)+' '+B1+' Z';
  const endLbl=(k,name)=>'<circle cx="'+x(n-1)+'" cy="'+y1(+last[k]||0)+'" r="4" fill="'+TR_COL[k]+'" stroke="'+TR_COL.surface+'" stroke-width="2"/>'
     +'<text x="'+(x(n-1)-8)+'" y="'+(y1(+last[k]||0)-9)+'" font-size="11" fill="#333" text-anchor="end">'+name+' '+last[k]+'</text>';
  document.getElementById('vt-tr-lines').innerHTML='<svg viewBox="0 0 '+W+' '+H1+'" height="'+H1+'" role="img" aria-label="Infected and suspicious sites per day">'
    +shade(B1)+ya.h+xticks(B1)
    +'<path d="'+area('infected')+'" fill="'+TR_COL.infected+'" opacity=".1"/>'
    +'<path d="'+path('suspicious')+'" fill="none" stroke="'+TR_COL.suspicious+'" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>'
    +'<path d="'+path('infected')+'" fill="none" stroke="'+TR_COL.infected+'" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>'
    +endLbl('suspicious','Suspicious')+endLbl('infected','Infected')
    +'<line id="vt-tr-x1" y1="'+T+'" y2="'+B1+'" stroke="#9ca3af" stroke-width="1" style="display:none"/>'
    +'<rect x="'+L+'" y="'+T+'" width="'+(W-L-R)+'" height="'+(B1-T)+'" fill="transparent"/></svg>';

  // Bars: sites cleaned per day
  const H2=130, B2=H2-24, max2=Math.max(3,...days.map(d=>+d.cleaned||0));
  const yb=yaxis(max2,B2), y2=v=>B2-(v/yb.top)*(B2-T);
  const bar=(i,v)=>{if(!v)return '';const h=B2-y2(v),r=Math.min(4,h/2,bw/2),x0=x(i)-bw/2,y0=y2(v);
    return '<path d="M'+x0+' '+B2+' V'+(y0+r)+' Q'+x0+' '+y0+' '+(x0+r)+' '+y0+' H'+(x0+bw-r)+' Q'+(x0+bw)+' '+y0+' '+(x0+bw)+' '+(y0+r)+' V'+B2+' Z" fill="'+TR_COL.cleaned+'"/>';};
  document.getElementById('vt-tr-bars').innerHTML='<svg viewBox="0 0 '+W+' '+H2+'" height="'+H2+'" role="img" aria-label="Sites cleaned per day">'
    +shade(B2)+yb.h+xticks(B2)+days.map((d,i)=>bar(i,+d.cleaned||0)).join('')
    +'<line id="vt-tr-x2" y1="'+T+'" y2="'+B2+'" stroke="#9ca3af" stroke-width="1" style="display:none"/>'
    +'<rect x="'+L+'" y="'+T+'" width="'+(W-L-R)+'" height="'+(B2-T)+'" fill="transparent"/></svg>';

  // Hover: one crosshair over both charts, with a tooltip
  const tip=document.getElementById('vt-tr-tip');
  document.querySelectorAll('#vt-tr-lines svg,#vt-tr-bars svg').forEach(svg=>{
    svg.onmousemove=ev=>{
      const rc=svg.getBoundingClientRect(), px=(ev.clientX-rc.left)*W/rc.width;
      const i=Math.max(0,Math.min(n-1,Math.round((px-L)/step)));
      const d=days[i];
      ['vt-tr-x1','vt-tr-x2'].forEach(id=>{const l=document.getElementById(id);l.setAttribute('x1',x(i));l.setAttribute('x2',x(i));l.style.display='';});
      tip.innerHTML='<b>'+trDate(d.date,1)+(d.demo?' (demo)':'')+'</b>'
        +'<div class="row"><span><i style="background:'+TR_COL.infected+'"></i>Infected</span><span>'+d.infected+'</span></div>'
        +'<div class="row"><span><i style="background:'+TR_COL.suspicious+'"></i>Suspicious</span><span>'+d.suspicious+'</span></div>'
        +'<div class="row"><span><i style="background:'+TR_COL.cleaned+'"></i>Cleaned</span><span>'+(d.cleaned||0)+'</span></div>'
        +'<div class="row"><span>Clean sites</span><span>'+d.clean+' of '+d.total+'</span></div>';
      tip.style.display='block';
      const tx=ev.clientX+14+tip.offsetWidth>innerWidth?ev.clientX-tip.offsetWidth-14:ev.clientX+14;
      tip.style.left=tx+'px'; tip.style.top=(ev.clientY+12)+'px';
    };
    svg.onmouseleave=()=>{tip.style.display='none';['vt-tr-x1','vt-tr-x2'].forEach(id=>document.getElementById(id).style.display='none');};
  });

  document.getElementById('vt-tr-table').innerHTML='<table><thead><tr><th>Date</th><th>Infected</th><th>Suspicious</th><th>Clean</th><th>Cleaned</th></tr></thead><tbody>'
    +days.slice().reverse().map(d=>'<tr><td>'+trDate(d.date,1)+(d.demo?' (demo)':'')+'</td><td>'+d.infected+'</td><td>'+d.suspicious+'</td><td>'+d.clean+'</td><td>'+(d.cleaned||0)+'</td></tr>').join('')+'</tbody></table>';
}
window.addEventListener('resize',()=>{if(document.getElementById('vtpanel-trend').classList.contains('active'))vtTrendDraw();});

// ── Filter ────────────────────────────────────────────────────
window.vtSetFilter=function(f){
  activeFilter=f;
  document.querySelectorAll('.vt-fbtn').forEach(b=>b.classList.toggle('active',b.dataset.f===f));
  vtApplyFilter();
};
window.vtApplyFilter=function(){
  const q=document.getElementById('vt-search').value.toLowerCase();
  // Orphan databases are their own view: shown only with the Orphan DBs button, instead of the site list
  const orphanView=activeFilter==='orphan';
  document.getElementById('vt-rel').style.display=orphanView&&related.length?'':'none';
  document.getElementById('vt-tbody').closest('.vt-tbl-wrap').style.display=orphanView?'none':'';
  document.querySelectorAll('#vt-tbody-rel tr[data-domain]').forEach(row=>{row.style.display=(!q||row.dataset.domain.includes(q))?'':'none';});
  document.querySelectorAll('#vt-tbody tr[data-domain]').forEach(row=>{
    const st=row.dataset.status;
    const match=activeFilter==='all'||st===activeFilter
      ||(activeFilter==='pending'&&(row.dataset.job==='1'||st==='scanning'||st==='waiting'));
    const show=match&&(!q||row.dataset.domain.includes(q)||(row.dataset.own||'').includes(q));
    row.dataset.vis=show?'1':'';
    row.style.display=show&&!grpClosed.has(row.dataset.grp)?'':'none';
  });
  // Group headers: only when the group has a site that matches the filter
  const tb=document.getElementById('vt-tbody'), resVis={};
  tb.querySelectorAll('tr.vt-grp-c').forEach(hd=>{
    const any=[...tb.querySelectorAll('tr[data-domain]')].some(r=>r.dataset.grp===hd.dataset.gk&&r.dataset.vis==='1');
    hd.style.display=any?'':'none';
    if(any) resVis[hd.dataset.res]=1;
  });
  tb.querySelectorAll('tr.vt-grp-r').forEach(hd=>{hd.style.display=resVis[hd.dataset.res]?'':'none';});
};

function updateCounts(){
  const c={infected:0,suspicious:0,clean:0,pending:0,error:0};
  for(const d of domains){
    const s=results[d]?.status??'loading';
    if(s in c) c[s]++;
    if(s!=='pending'&&inQueue(d)) c.pending++; // in the rescan queue or waiting for an automatic lookup
  }
  vtSet('vt-cnt-infected',c.infected);
  vtSet('vt-cnt-suspicious',c.suspicious);
  vtSet('vt-cnt-clean',c.clean);
  vtSet('vt-cnt-pending',c.pending);
  vtSet('vt-cnt-error',c.error);
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

function inQueue(d){return !!activeJob(d)||d===pumpCurrent||scanQueue.includes(d);}
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
  // For failed jobs that never reached the fetch step, results[domain] may still be
  // the initial {status:'loading'} placeholder — that keeps spinners in the row forever.
  // Restore the previous result (or fall back to 'error') so the row clears correctly.
  if(state==='failed'){
    const cur=results[j.domain];
    if(!cur||['loading','scanning','waiting'].includes(cur.status)){
      const prev=j.prev&&!['loading','scanning','waiting'].includes(j.prev?.status)
        ?j.prev:{status:'error',domain:j.domain,message:msg||'Failed'};
      results[j.domain]=prev;
    }
  }
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
    setJob(j,{state:'ratewait',waitFrom:Date.now(),until:Date.now()+wait*1000});
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
  vtAvastLive(j.domain,1); vtGsbLive(j.domain,1); // a rescan also asks Avast / AVG and Google

  if(fresh.status==='infected') vtToast('⚠ INFECTED: '+j.domain,'err');
  else if(fresh.status==='suspicious') vtToast('Suspicious: '+j.domain,'warn');
  else if(fresh.status==='clean') vtToast('Clean: '+j.domain,'ok');
}

// Finished automatic lookups stay in the scan queue with their result
function logAutoJob(domain,prev,data,t0){
  const ok=['clean','suspicious','infected','pending'].includes(data.status);
  const msgs={quota_exceeded:'VT daily quota exhausted — resumes after midnight UTC',
              budget_exceeded:'Daily background budget used up — the nightly scan continues',
              error:data.message||'Lookup failed'};
  jobs.push({id:++jobSeq,domain,kind:'auto',state:ok?'done':'failed',prev,result:data,
             msg:ok?'':(msgs[data.status]||data.message||data.status),
             createdAt:t0,startedAt:t0,endedAt:Date.now()});
  renderQueue();
}

function enqueueRescan(domain){
  if(activeJob(domain)) return false;
  scanQueue=scanQueue.filter(d=>d!==domain); // manual takes priority over auto pump
  const j={id:++jobSeq,domain,state:'queued',step:'queued',prev:results[domain]||null,createdAt:Date.now()};
  jobs.push(j);
  setJob(j,{});
  startQTicker();
  vtServerPost('enqueue-rescan',domain); // persist on server so cron can finish it if browser leaves
  startServerQueuePoll();
  runJob(j).finally(()=>vtServerPost('dequeue-rescan',domain)); // browser finished it: remove from server queue
  return true;
}

window.vtReanalyzeSingle=function(domain){
  if(noKey){vtToast('Add your VirusTotal API key in Settings first','warn');vtShowTab('settings');return;}
  if(!enqueueRescan(domain)) vtToast(domain+' is already in the scan queue','info');
};

// ── Category menus (arrow next to each filter button) ──────────
window.vtToggleMenu=function(ev,f){
  ev.stopPropagation();
  const m=document.getElementById('vt-fmenu-'+f), open=m.classList.contains('open');
  document.querySelectorAll('.vt-fmenu.open').forEach(x=>x.classList.remove('open'));
  if(!open) m.classList.add('open');
};
document.addEventListener('click',()=>document.querySelectorAll('.vt-fmenu.open').forEach(x=>x.classList.remove('open')));

function inCategory(d,f){
  const st=results[d]?.status;
  if(f==='all') return true;
  if(f==='error') return ['error','ratelimit','quota_exceeded','budget_exceeded'].includes(st);
  return st===f;
}

window.vtMenuAction=function(action,f){
  document.querySelectorAll('.vt-fmenu.open').forEach(x=>x.classList.remove('open'));
  if(action==='refresh') return vtRefreshDomains();
  const targets=domains.filter(d=>inCategory(d,f)&&!activeJob(d));
  if(!targets.length){vtToast('No sites to rescan in this category','info');return;}
  // Every rescan costs one manual call: never queue more than today's manual budget allows
  const left=Math.max(0,(budget.manual_max??0)-(budget.manual_used??0)-jobs.filter(j=>ACTIVE.has(j.state)).length);
  if(!left){vtToast('Daily manual rescan limit reached — resets at midnight UTC','warn');return;}
  // Worst first: infected, then suspicious, then the rest
  const rank={infected:0,suspicious:1};
  targets.sort((a,b)=>(rank[results[a]?.status]??2)-(rank[results[b]?.status]??2));
  const pick=targets.slice(0,left);
  pick.forEach(enqueueRescan);
  vtToast(pick.length+' site'+(pick.length===1?'':'s')+' added to the scan queue'
    +(pick.length<targets.length?' ('+(targets.length-pick.length)+' skipped: daily manual limit)':''),'info');
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
  fsJobs=fsJobs.filter(j=>j.state==='running'||j.state==='queued');
  renderQueue();
};

function startQTicker(){
  if(qTicker) return;
  qTicker=setInterval(()=>{
    // Auto-prune finished/failed jobs older than 30s
    const cut=Date.now()-30000;
    jobs=jobs.filter(j=>ACTIVE.has(j.state)||!j.endedAt||(j.endedAt>cut));
    fsJobs=fsJobs.filter(j=>!j.endedAt||(j.endedAt>cut));
    renderQueue();
    const fsIdle=!fsJobs.some(j=>!j.endedAt);
    if(!jobs.some(j=>ACTIVE.has(j.state))&&!pumpRunning&&fsIdle&&!serverQueue.length){clearInterval(qTicker);qTicker=null;}
  },1000);
}

function fmtSecs(ms){const s=Math.max(0,Math.round(ms/1000));return s<60?s+'s':Math.floor(s/60)+'m '+(s%60)+'s';}
function scoreTxt(r){return r&&r.malicious!==undefined&&r.total!==undefined?r.malicious+'/'+r.total:'—';}
function stepIdx(j){
  if(j.state==='done') return 4;
  // For failed: return index of the step that failed so only prior steps show green
  if(j.state==='failed'){
    const idx={submit:1,processing:2,fetch:3}[j.step];
    return idx!==undefined?idx:4; // 4 = only the Done/Failed pill is marked bad
  }
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
    case 'done':
      if(j.kind==='cron') return 'Processed by cron at '+new Date(j.endedAt).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
      if(j.kind==='auto') return j.result&&j.result.status==='pending'
        ?'New to VirusTotal — submitted for a first scan, result follows later'
        :'Report fetched at '+new Date(j.endedAt).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
      return 'Finished at '+new Date(j.endedAt).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})+' in '+fmtSecs(j.endedAt-(j.startedAt||j.createdAt));
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
    else if((i===4||(i===si&&si<4))&&j.state==='failed') c='bad';
    return '<span class="'+c+'">'+(i===4&&j.state==='failed'?'Failed':s)+'</span>';
  }).join('')+'</div>';
  const auto=j.kind==='auto', cron=j.kind==='cron';
  const kindLabel=cron?'Cron':auto?'Auto':'Rescan';
  const pbar=j.state==='processing'?'<div class="vt-q-pbar"><div style="width:'+Math.round((j.polls||0)/POLL_MAX*100)+'%"></div></div>':'';

  let right;
  if(j.state==='done'&&j.result){
    const st=j.result.status, dc=DOT_CLS[st];
    right='<span class="vt-badge '+st+'">'+(dc?'<span class="vt-dot '+dc+'"></span>':'')+(LABELS[st]??st)+'</span>'
      +'<div>'+(j.prev&&j.prev.malicious!==undefined?scoreTxt(j.prev)+' → ':'')+'<strong>'+scoreTxt(j.result)+'</strong></div>';
  }else if(fin){
    right='<div>was '+scoreTxt(j.prev)+'</div>';
  }else{
    right='<div>'+fmtSecs(Date.now()-(j.startedAt||j.createdAt))+'</div><div>was '+scoreTxt(j.prev)+'</div>';
  }

  return '<div class="vt-q-item '+j.state+'">'+icon
    +'<div class="vt-q-main"><div class="vt-q-dom">'+vtEsc(j.domain)+'<span class="vt-q-kind">'+kindLabel+'</span></div>'
    +'<div class="vt-q-msg">'+vtEsc(jobMsg(j))+'</div>'+(auto||cron?'':steps)+pbar+'</div>'
    +'<div class="vt-q-right">'+right+'</div></div>';
}

function liveHTML(d){
  return '<div class="vt-q-item live"><div class="vt-q-icon"><span class="vt-spin"></span></div>'
    +'<div class="vt-q-main"><div class="vt-q-dom">'+vtEsc(d)+'<span class="vt-q-kind">Auto</span></div>'
    +'<div class="vt-q-msg">Fetching the report from VirusTotal...</div></div>'
    +'<div class="vt-q-right"></div></div>';
}

// One line only: what is being checked right now, otherwise the latest result.
// Below it, an animated bar while waiting for VirusTotal's 4 requests/minute limit.
let queueCollapsed=localStorage.getItem('vt_q_collapsed')==='1';
window.vtQueueToggle=function(e){
  if(e&&e.target&&e.target.tagName==='BUTTON') return; // let button handle its own click
  queueCollapsed=!queueCollapsed;
  localStorage.setItem('vt_q_collapsed',queueCollapsed?'1':'0');
  applyQueueCollapse();
};
function applyQueueCollapse(){
  const box=document.getElementById('vt-queue');
  if(!box) return;
  box.classList.toggle('vt-q-collapsed',queueCollapsed);
  const t=document.getElementById('vt-q-toggle');
  if(t){t.textContent=queueCollapsed?'▼ Expand':'▲ Collapse';t.title=queueCollapsed?'Show Scan Monitor':'Hide Scan Monitor';}
}
// ── Server-side manual queue ───────────────────────────────────
// Domains pushed to the server on Rescan, so the cron can finish them if the
// browser navigates away before the in-browser job completes.
let serverQueue=[], serverQueueAt={}, serverQueuePoll=null;

function vtServerPost(action,domain){
  const fpToken=document.getElementById('forgery_protection_token')?.content||'';
  fetch(BASE+'?action='+action,{method:'POST',headers:{'X-Forgery-Protection-Token':fpToken},
    body:new URLSearchParams({domain})}).catch(()=>{});
}

async function vtPollServerQueue(){
  try{
    // Heartbeat: rescans this page still runs or has waiting stay with this page, not with cron
    const mine=[...new Set(jobs.filter(j=>j.kind!=='auto'&&j.kind!=='cron'&&ACTIVE.has(j.state)).map(j=>j.domain))];
    if(mine.length){
      const fpToken=document.getElementById('forgery_protection_token')?.content||'';
      fetch(BASE+'?action=rescan-alive',{method:'POST',headers:{'X-Forgery-Protection-Token':fpToken},body:new URLSearchParams({domains:mine.join(',')})}).catch(()=>{});
    }
    const data=await vtFetch('?action=queue-status');
    const inBrowser=new Set(jobs.filter(j=>ACTIVE.has(j.state)).map(j=>j.domain));
    const prev=serverQueue.slice();
    serverQueue=(data.pending||[]).filter(d=>!inBrowser.has(d));
    serverQueueAt=data.queued_at||{};

    // Domains that cron processed: show them in the monitor as done jobs
    const cronDone=new Set(jobs.filter(j=>j.kind==='cron').map(j=>j.domain));
    for(const d of (data.completed||[])){
      if(!cronDone.has(d)&&!inBrowser.has(d)){
        const doneAt=(data.completed_at?.[d]||0)*1000||Date.now();
        jobs.push({id:++jobSeq,domain:d,kind:'cron',state:'done',
          msg:'',result:null,prev:null,createdAt:doneAt,startedAt:doneAt,endedAt:doneAt});
        startQTicker();
        // Fetch fresh result to update the table row
        vtFetch('?action=scan&domain='+encodeURIComponent(d)) // cron already saved the fresh result: no new VirusTotal call
          .then(r=>{results[d]=r;updateRow(d,r);updateCounts();})
          .catch(()=>{});
      }
    }
    // Also handle domains that left the pending list without appearing in completed
    // (e.g. dequeued by browser from another tab)
    for(const d of prev){
      if(!data.pending?.includes(d)&&!inBrowser.has(d)&&!cronDone.has(d)){
        vtFetch('?action=scan&domain='+encodeURIComponent(d)) // cron already saved the fresh result: no new VirusTotal call
          .then(r=>{results[d]=r;updateRow(d,r);updateCounts();})
          .catch(()=>{});
      }
    }
    renderQueue();
  }catch(e){}
}

function startServerQueuePoll(){
  if(serverQueuePoll) return;
  serverQueuePoll=setInterval(vtPollServerQueue,30000);
}

function serverJobHTML(domain){
  const qAt=serverQueueAt[domain]||0;
  const ago=qAt?fmtSecs(Date.now()-qAt*1000)+' ago':'';
  return '<div class="vt-q-item vt-q-srv queued">'
    +'<div class="vt-q-icon">⏳</div>'
    +'<div class="vt-q-main"><div class="vt-q-dom">'+vtEsc(domain)
    +'<span class="vt-q-kind srv">Server</span></div>'
    +'<div class="vt-q-msg">Queued on server'+(ago?' — '+ago:'')
    +' — cron will process this within 5 minutes</div></div>'
    +'<div class="vt-q-right"></div></div>';
}

// ── File-scanner jobs in the Scan Monitor ──────────────────────
let fsJobs=[];
function getFsJob(domain,id){return fsJobs.find(j=>j.domain===domain&&j.scanner===id&&!j.endedAt);}
function addFsJob(domain,id){
  fsJobs=fsJobs.filter(j=>!(j.domain===domain&&j.scanner===id)); // drop any old entry
  const j={id:'fs-'+(++jobSeq),domain,scanner:id,state:'running',startedAt:Date.now(),endedAt:null,result:null,msg:''};
  fsJobs.push(j); startQTicker(); return j;
}
function updateFsJob(domain,id,state,result,msg){
  const j=getFsJob(domain,id); if(!j) return;
  j.state=state; if(result) j.result=result; if(msg) j.msg=msg;
  if(state==='done'||state==='failed') j.endedAt=Date.now();
  renderQueue();
}
function fsJobHTML(j){
  let icon;
  if(j.state==='done')   icon='<div class="vt-q-icon ok">✓</div>';
  else if(j.state==='failed') icon='<div class="vt-q-icon bad">✕</div>';
  else if(j.state==='queued') icon='<div class="vt-q-icon">…</div>';
  else icon='<div class="vt-q-icon"><span class="vt-spin"></span></div>';
  let msg=j.msg;
  if(!msg){
    if(j.state==='running')      msg='Scanning files…';
    else if(j.state==='queued')  msg='Waiting — another scan is running on the server';
    else if(j.state==='done')    msg=j.result?(j.result.infected?j.result.infected+' infected file(s) found in '+(j.result.files||0).toLocaleString()+' files':'No infected files in '+(j.result.files||0).toLocaleString()+' files'):'Scan complete';
    else                         msg='Scan failed';
  }
  let right='';
  if(j.state==='done'&&j.result){
    right=j.result.infected
      ?'<span class="vt-badge infected"><span class="vt-dot dot-r"></span>'+j.result.infected+' infected</span>'
      :'<span class="vt-badge clean"><span class="vt-dot dot-g"></span>Clean</span>';
  }else if(j.state==='running'){
    right='<div>'+fmtSecs(Date.now()-j.startedAt)+'</div>';
  }
  return '<div class="vt-q-item vt-q-fs '+j.state+'">'+icon
    +'<div class="vt-q-main"><div class="vt-q-dom">'+vtEsc(j.domain)
    +'<span class="vt-q-kind fs">'+vtEsc(fsName(j.scanner))+'</span></div>'
    +'<div class="vt-q-msg">'+vtEsc(msg)+'</div></div>'
    +'<div class="vt-q-right">'+right+'</div></div>';
}

function renderQueue(){
  const box=document.getElementById('vt-queue');
  if(!box) return;
  const pumpBusy=pumpRunning&&(pumpCurrent||scanQueue.length);
  if(!jobs.length&&!fsJobs.length&&!pumpBusy&&!serverQueue.length){box.style.display='none';return;}
  box.style.display='block';
  applyQueueCollapse();
  if(pumpBusy) startQTicker();

  const running=jobs.filter(j=>ACTIVE.has(j.state)&&j.state!=='queued');
  const waiting=jobs.filter(j=>j.state==='queued');
  const finished=jobs.filter(j=>!ACTIVE.has(j.state)).sort((a,b)=>b.endedAt-a.endedAt);
  const fsActive=fsJobs.filter(j=>j.state==='running'||j.state==='queued');
  const fsDone=fsJobs.filter(j=>j.state==='done'||j.state==='failed').sort((a,b)=>b.endedAt-a.endedAt);
  const allFinished=[...finished,...fsDone].sort((a,b)=>b.endedAt-a.endedAt).slice(0,8);
  // Server-queue: domains pushed to server but not in any active in-browser job
  const inBrowserDomains=new Set([...jobs.filter(j=>ACTIVE.has(j.state)).map(j=>j.domain),...(pumpCurrent?[pumpCurrent]:[])]);
  const srvItems=serverQueue.filter(d=>!inBrowserDomains.has(d));
  const hasActive=pumpCurrent||running.length||waiting.length||fsActive.length||srvItems.length;

  // Active queue first, then a divider, then recently completed
  let html='';
  if(pumpCurrent) html+=liveHTML(pumpCurrent);
  running.forEach(j=>{ html+=jobHTML(j); });
  fsActive.forEach(j=>{ html+=fsJobHTML(j); });
  waiting.slice(0,5).forEach(j=>{ html+=jobHTML(j); });
  srvItems.forEach(d=>{ html+=serverJobHTML(d); });
  if(allFinished.length&&hasActive) html+='<div class="vt-q-divider">Completed</div>';
  allFinished.forEach(j=>{ html+=(j.scanner?fsJobHTML(j):jobHTML(j)); });
  document.getElementById('vt-q-list').innerHTML=html;

  // Pause bar
  const now=Date.now(), rw=running.find(j=>j.state==='ratewait');
  let from=0, until=0, limit=false;
  if(rw){ from=rw.waitFrom||now; until=rw.until; limit=true; }
  else if(pumpPauseUntil>now){ from=pumpPauseFrom; until=pumpPauseUntil; limit=pumpPauseLimit; }
  const bar=document.getElementById('vt-q-pause');
  if(until>now){
    bar.style.display='flex';
    const fill=document.getElementById('vt-q-pfill');
    fill.classList.toggle('limit',limit);
    fill.style.width=Math.min(100,Math.round((now-from)/Math.max(1,until-from)*100))+'%';
    vtSet('vt-q-ptext',(limit?'VirusTotal rate limit reached, waiting':'Pausing for VirusTotal\'s limit of 4 requests per minute')
      +' — next check in '+fmtSecs(until-now));
  }else{
    bar.style.display='none';
  }

  const left=waiting.length+scanQueue.length;
  const parts=[];
  if(running.length||pumpCurrent||fsActive.length) parts.push('checking '+(running.length+fsActive.length+(pumpCurrent?1:0)));
  if(left) parts.push(left+' waiting');
  if(srvItems.length) parts.push(srvItems.length+' on server');
  if(allFinished.length) parts.push(allFinished.length+' done');
  vtSet('vt-q-sum',parts.join(' · '));
  document.getElementById('vt-q-cancel').style.display=waiting.length?'':'none';
}

window.vtRefreshDomains=async function(){
  vtToast('Refreshing site list...','info');
  const data=await vtFetch('?action=refresh-domains').catch(()=>({}));
  if(data.domains){
    domains=data.domains; vtSet('vt-cnt-all',domains.length);
    // Orphan databases too (the server cache was just cleared, so a deleted database drops out)
    const all=await vtFetch('?action=all').catch(()=>null);
    if(all){
      related=all.related||[]; relSet=new Set(related); orphans=all.orphans||{}; owners=all.owners||{};
      Object.assign(results,all.results||{});
      for(const d of related) if(!results[d]) results[d]={status:'unchecked',domain:d};
    }
    scanQueue=domains.filter(d=>!results[d]); renderTable(); updateCounts(); pump();
  }
};

// ── Settings ──────────────────────────────────────────────────
window.vtNotifyUi=function(){
  document.getElementById('vt-n-opts').classList.toggle('off',!document.getElementById('vt-n-enabled').checked);
};
window.vtTestNotify=async function(){
  const fpToken=document.getElementById('forgery_protection_token')?.content||'';
  const msg=document.getElementById('vt-n-msg'); msg.style.display='inline'; msg.className='vt-smsg'; msg.textContent='Sending...';
  const body=new URLSearchParams({email:document.getElementById('vt-n-email').value.trim()});
  const res=await fetch(BASE+'?action=test-notify',{method:'POST',headers:{'X-Forgery-Protection-Token':fpToken},body}).then(r=>r.json()).catch(()=>({error:'Network error'}));
  msg.className='vt-smsg '+(res.ok?'ok':'err');
  msg.textContent=res.ok?'Test e-mail sent to '+res.email:(res.error||'Could not send');
  setTimeout(()=>{msg.style.display='none';},6000);
};
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
  // Notifications: only offer the recipients this role may use
  document.getElementById('vt-n-enabled').checked=!!cfg.notify_enabled;
  const evs=cfg.notify_events||['infected','suspicious'];
  document.querySelectorAll('input[name=vt-n-ev]').forEach(c=>c.checked=evs.includes(c.value));
  const em=document.getElementById('vt-n-email');
  em.value=cfg.notify_email||'';
  em.placeholder=cfg.my_email||'name@example.com';
  vtNotifyUi();
  if(cfg.daily_limit)       document.getElementById('vt-s-daily').value    =cfg.daily_limit;
  if(cfg.auto_scan_share)   document.getElementById('vt-s-share').value    =Math.round(cfg.auto_scan_share*100);
  vtQsRender();
  if(cfg.clean_ttl_days)    document.getElementById('vt-s-clean-ttl').value=cfg.clean_ttl_days;
  if(cfg.infected_ttl_days) document.getElementById('vt-s-inf-ttl').value  =cfg.infected_ttl_days;
  checkRates=cfg.check_rates||{};
  vtRenderScanners(cfg.scanners||[],cfg.role==='admin');
  document.getElementById('vt-av-card').style.display=cfg.role==='admin'?'':'none';
  document.getElementById('vt-gsb-card').style.display=cfg.role==='admin'?'':'none';
  document.querySelectorAll('input[name=vt-av]').forEach(r=>r.checked=(cfg.avast_products||[]).includes(+r.value));
  for(const id of [146,147]) vtSet('vt-av-calls-'+id,(cfg.avast_calls||{})[id]??0);
  // Green check when a key is accepted and in use, red cross with the reason when not
  const ks=(id,cls,text,title)=>{const e=document.getElementById(id); if(!e) return; e.className='vt-key-state'+(cls?' '+cls:''); e.textContent=text; e.title=title||'';};
  if(cfg.api_key_invalid) ks('vt-vt-keystate','bad','API key not accepted','VirusTotal rejected the stored key (HTTP 401)');
  else if(cfg.has_key) ks('vt-vt-keystate','ok','API key active','VirusTotal accepted the key');
  else ks('vt-vt-keystate','off','no API key');
  if(cfg.gsb){
    const g=cfg.gsb, last=g.last||{};
    if(!g.key) ks('vt-gsb-keystate','off','no API key');
    else if(last.ok===false) ks('vt-gsb-keystate','bad','API key problem',last.error||'');
    else if(!g.enabled) ks('vt-gsb-keystate','off','key stored, switched off');
    else ks('vt-gsb-keystate','ok','API key active',last.at?'Last answer from Google: '+new Date(last.at*1000).toLocaleString():'Key tested when it was saved');
    document.getElementById('vt-gsb-on').checked=!!cfg.gsb.enabled;
    vtSet('vt-gsb-calls',cfg.gsb.calls??0);
    vtSet('vt-gsb-stored',cfg.gsb.key?'stored key '+cfg.gsb.key:'no key stored');
    document.getElementById('vt-gsb-key').value='';
  }
  const lr=cfg.log_rotation;
  document.getElementById('vt-lr-card').style.display=lr?'':'none';
  if(lr){
    document.getElementById('vt-s-log-mb').value=lr.log_max_mb; document.getElementById('vt-s-log-keep').value=lr.log_keep; document.getElementById('vt-s-log-days').value=lr.log_max_days;
    const mb=b=>(b/1048576).toFixed(b<104858?2:1)+' MB';
    vtSet('vt-lr-info','Now: '+lr.logs+' log'+(lr.logs===1?'':'s')+' ('+mb(lr.logs_bytes)+'), '+lr.old+' old cop'+(lr.old===1?'y':'ies')+' ('+mb(lr.old_bytes)+').');
  }
  document.querySelectorAll('[data-check-rate]').forEach(input=>{input.value=cfg.check_rates?.[input.dataset.checkRate]||2;input.disabled=cfg.role!=='admin';});
}

// ── File scanners on/off (server-wide, administrator) ─────────
let scIsAdmin=false, checkRates={};
function vtRenderScanners(list,isAdmin){
  scIsAdmin=isAdmin;
  const el=document.getElementById('vt-sc-list');
  document.getElementById('vt-sc-detect').style.display=isAdmin?'':'none';
  if(!list.length){el.innerHTML='<span class="hint">Could not ask the server which scanners are installed.</span>';return;}
  const found=list.filter(s=>s.installed);
  if(!found.length){el.innerHTML='<span class="hint">No supported internal scanners found. Install ClamAV, Maldet or Imunify, then choose Detect again.</span>';return;}
  el.innerHTML=found.map(s=>{
    const info=s.installed
      ?'Installed: '+vtEsc(s.detail||'')+(s.instant?' · shows the results of Imunify scans, starts no scan':'')
      :'Not installed on this server';
    return '<div class="vt-sc-row'+(s.installed?'':' missing')+'">'
      +'<label class="vt-sw" title="'+(isAdmin?'Switch on or off':'Set by the administrator')+'"><input type="checkbox" '+(s.installed&&s.enabled?'checked ':'')
      +((!s.installed||!isAdmin)?'disabled ':'')+'onchange="vtToggleScanner(\''+s.id+'\',this.checked)"><span></span></label>'
      +'<span class="vt-sc-name">'+vtBrandIco(s.id)+vtEsc(s.name)+'</span>'
      +'<span class="vt-sc-info">'+info+(s.installed&&!s.enabled?' · <strong>switched off</strong>':'')+'</span>'
      +'<label class="vt-rate" for="vt-rate-'+s.id+'"><input type="number" id="vt-rate-'+s.id+'" data-check-rate="'+s.id+'" min="1" max="5" step="1" value="'+(checkRates[s.id]||2)+'" '+(!isAdmin?'disabled ':'')+'aria-label="'+vtEsc(s.name)+' requests per minute"> / minute</label></div>';
  }).join('')+(isAdmin?'':'<div class="hint" style="margin-top:6px">Only the administrator can switch scanners on or off.</div>');
}
function vtScMsg(ok,text){
  const m=document.getElementById('vt-sc-msg');
  m.style.display='inline';m.className='vt-smsg '+(ok?'ok':'err');m.textContent=text;
  setTimeout(()=>{m.style.display='none';},4000);
}
function vtApplyScanners(list){
  document.querySelectorAll('[data-check-rate]').forEach(input=>{checkRates[input.dataset.checkRate]=input.value;});
  scanners=list.filter(s=>s.installed&&s.enabled).map(s=>({id:s.id,name:s.name,instant:s.instant}));
  vtRenderScanners(list,scIsAdmin);
  renderTable();
}
window.vtToggleScanner=async function(id,on){
  const fpToken=document.getElementById('forgery_protection_token')?.content||'';
  const res=await fetch(BASE+'?action=save-scanner',{method:'POST',headers:{'X-Forgery-Protection-Token':fpToken},
    body:new URLSearchParams({scanner:id,enabled:on?'true':'false'})}).then(r=>r.json()).catch(()=>({error:'Network error'}));
  if(res.ok){vtApplyScanners(res.scanners);vtScMsg(true,'Saved');}
  else{vtScMsg(false,res.error||'Could not save');const cfg=await vtFetch('?action=get-settings').catch(()=>({}));vtRenderScanners(cfg.scanners||[],cfg.role==='admin');}
};
window.vtDetectScanners=async function(){
  const b=document.getElementById('vt-sc-detect');b.disabled=true;b.textContent='Detecting…';
  const fpToken=document.getElementById('forgery_protection_token')?.content||'';
  const res=await fetch(BASE+'?action=detect-scanners',{method:'POST',headers:{'X-Forgery-Protection-Token':fpToken}}).then(r=>r.json()).catch(()=>({error:'Network error'}));
  b.disabled=false;b.textContent='Detect again';
  if(res.scanners){vtApplyScanners(res.scanners);vtScMsg(true,res.scanners.filter(s=>s.installed).length+' scanner(s) found');}
  else vtScMsg(false,res.error||'Could not detect');
};

window.vtSaveSettings=async function(){
  const key  =document.getElementById('vt-s-apikey').value.trim();
  const daily=parseInt(document.getElementById('vt-s-daily').value)||500;
  const share=(parseInt(document.getElementById('vt-s-share').value)||80)/100;
  const cttl =parseInt(document.getElementById('vt-s-clean-ttl').value)||30;
  const ittl =parseInt(document.getElementById('vt-s-inf-ttl').value)||1;
  const wpOnly=document.getElementById('vt-s-scope-wp').checked;
  const checked=n=>[...document.querySelectorAll('input[name='+n+']:checked')].map(c=>c.value).join(',');
  const payload={wordpress_only:wpOnly?'true':'false',
    notify_enabled:document.getElementById('vt-n-enabled').checked?'true':'false',
    notify_events:checked('vt-n-ev'),notify_email:document.getElementById('vt-n-email').value.trim(),daily_limit:daily,auto_scan_share:share,clean_ttl_days:cttl,infected_ttl_days:ittl};
  if(scIsAdmin){
    const rates={};
    for(const input of document.querySelectorAll('[data-check-rate]')){
      const n=Number(input.value);
      if(!Number.isInteger(n)||n<1||n>5){input.reportValidity();vtToast('Request limits must be whole numbers from 1 to 5 per minute','error');return;}
      rates[input.dataset.checkRate]=n;
    }
    payload.check_rates=JSON.stringify(rates);
    checkRates=rates;
  }
  if(key) payload.api_key=key;
  if(document.getElementById('vt-lr-card').style.display!=='none'){
    payload.log_max_mb=parseInt(document.getElementById('vt-s-log-mb').value)||5;
    payload.log_keep=parseInt(document.getElementById('vt-s-log-keep').value)||5;
    payload.log_max_days=parseInt(document.getElementById('vt-s-log-days').value)||90;
  }
  if(document.getElementById('vt-gsb-card').style.display!=='none'){
    payload.gsb_enabled=document.getElementById('vt-gsb-on').checked?'true':'false';
    const gk=document.getElementById('vt-gsb-key').value.trim(); if(gk) payload.gsb_api_key=gk;
  }
  if(document.getElementById('vt-av-card').style.display!=='none') payload.avast_products=[...document.querySelectorAll('input[name=vt-av]:checked')].map(c=>c.value).join(',')||'0';

  const body=new URLSearchParams(payload);
  const fpToken=document.getElementById('forgery_protection_token')?.content||'';
  const res=await fetch(BASE+'?action=save-settings',{method:'POST',headers:{'X-Forgery-Protection-Token':fpToken},body}).then(r=>r.json()).catch(()=>({error:'Network error'}));
  // Two save buttons (top and bottom of Settings): show the message next to both
  const msgs=['vt-smsg','vt-smsg2'].map(id=>document.getElementById(id)).filter(Boolean);
  const msg={set className(v){msgs.forEach(m=>m.className=v);},set textContent(v){msgs.forEach(m=>m.textContent=v);},
             style:{set display(v){msgs.forEach(m=>m.style.display=v);}}};
  msg.style.display='inline';
  if(res.ok){
    msg.className='vt-smsg ok'; msg.textContent='Saved!'+(res.gsb_message?' '+res.gsb_message:'');
    document.getElementById('vt-api-warning').style.display='none';
    document.getElementById('vt-s-apikey').value='';
    if(noKey&&key){setTimeout(()=>location.reload(),800);} // key added: start looking sites up
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
// ── File scanners (ClamAV, maldet, …) ─────────────────────────
// Every site gets a bar with one button per scanner found on the server.
// A click opens a second line under the site: scan progress, then the result
// and the list of infected files. Scans run on demand, one at a time on the server.
function vtAgo(ts){
  if(!ts) return '';
  const s=Date.now()/1000-ts;
  if(s<90) return 'just now';
  if(s<3600) return Math.round(s/60)+' min ago';
  if(s<86400) return Math.round(s/3600)+' h ago';
  return new Date(ts*1000).toLocaleDateString();
}
function fsGet(d,id){return scans[d]?.[id]||null;}
function fsBusy(d,id){return ['running','queued'].includes(fsGet(d,id)?.status);}
function fsName(id){return scanners.find(x=>x.id===id)?.name||id;}
function fsBarHTML(domain){
  if(!scanners.length) return '';
  const e=vtEsca(domain);
  return '<span class="vt-scanbar">'+scanners.map(sc=>{
    const r=fsGet(domain,sc.id);
    let mark='';
    if(fsBusy(domain,sc.id)) mark='<span class="vt-spin"></span>';
    else if(r?.status==='done') mark=r.infected?'<span class="n">'+r.infected+'</span>':'<span class="ok">✓</span>';
    else if(r) mark='<span style="color:var(--red)">!</span>';
    const open=fsOpen[domain]===sc.id;
    return '<button type="button" class="vt-sbtn'+(open?' open':'')+'" title="Scan the files of this site with '+vtEsc(sc.name)+'" onclick="vtFsClick(\''+e+'\',\''+sc.id+'\')">'+vtEsc(sc.name)+mark+'</button>';
  }).join('')+'</span>';
}
// ── Why flagged, and how to get delisted after cleaning ─────────
// Review / false-positive contacts per vendor, from VirusTotal's list:
// https://docs.virustotal.com/docs/false-positive-contacts
// Where a vendor has its own form for websites, that form is used instead (alphaMountain, Gridinsoft).
const VT_FP_LIST='https://docs.virustotal.com/docs/false-positive-contacts';
const VT_FP={
  abusix:'https://lookup.abusix.com/', adminus:'samples@adminus.net', rising:'fp@rising.com.cn', acronis:'virustotal-falsepositive@acronis.com', ailabs:'aicc@monitorapp.com',
  alibaba:'virustotal@list.alibaba-inc.com', alienvault:'otx-support@alienvault.com', alphamountain:'https://www.alphamountain.ai/false-positive/',
  alphasoc:'virustotal@alphasoc.com', antiy:'avlsdk_support@antiy.cn', arcsight:'virustotalsupport@opentext.com',
  avira:'https://www.avira.com/en/analysis/submit-url', antivir:'https://www.avira.com/en/analysis/submit-url',
  avast:'DL-Virus@gendigital.com', avg:'http://www.avg.com/us-en/whitelist', bitdefender:'https://www.bitdefender.com/submit',
  bfore:'https://portal.bfore.ai/knowledge-base/kb-tickets/new', blueliv:'klabs_services@outpost24.com', certego:'fp@certego.net', chainpatrol:'dispute@chainpatrol.io',
  chongluadao:'https://chongluadao.vn/en/report/reportmistake', cins:'http://cinsscore.com/#contact', cleanmx:'abuse@clean-mx.de', cluster25:'threatintel@cluster25.io',
  crdf:'https://threatcenter.crdf.fr/false_positive.html', criminalip:'support@aispera.com', crowdstrike:'VTscanner@crowdstrike.com',
  csis:'abuse-reporting@csis.com', cyble:'cyblevt_patnership@cyble.com', cyradar:'https://cyradar.com/reportfp/',
  desenmascara:'vt-falsepositives@desenmascara.me', dns8:'dns8@layer8.pt', drweb:'vms@drweb.com', elastic:'https://discuss.elastic.co/t/submitting-false-positives/232322',
  emsisoft:'fp@emsisoft.com', estsecurity:'esrc@estsecurity.com', alyac:'esrc@estsecurity.com', emergingthreats:'support@emergingthreats.net', ermes:'support-vt@ermes.company',
  eset:'https://support.eset.com/kb141/?page=content&id=SOLN141', forcepoint:'reviewmysite@forcepoint.com',
  fortinet:'https://www.fortiguard.com/faq/malurl', gdata:'https://www.gdata.de/help/en/general/GeneralInformation/submitFileAppURL/',
  googlesafebrowsing:'https://safebrowsing.google.com/safebrowsing/report_error/?hl=en', greensnow:'https://greensnow.co/contact',
  gridinsoft:'https://portal.gridinsoft.com/false-positive', guardpot:'virustotal@guardpot.com', heimdal:'report-vt@heimdalsecurity.com', ikarus:'fp@ikarus.at',
  ipsum:'https://github.com/stamparm/ipsum', k7:'reportfp@labs.k7computing.com', kaspersky:'newvirus@kaspersky.com',
  lionic:'https://www.lionic.com/supports/report-false-positive/', lumu:'vt@lumu.io', malbeacon:'vtreport@malbeacon.com',
  malwarebytes:'https://forums.malwarebytes.com/forum/122-false-positives/', malwarepatrol:'fp@malwarepatrol.net', malwareurl:'team@malwareurl.com',
  mcafee:'https://www.mcafee.com/support/s/article/000001921', microsoft:'https://www.microsoft.com/en-us/wdsi/filesubmission',
  mimecast:'bep-soc@mimecast.com', netcraft:'https://report.netcraft.com/report/mistake', openphish:'support@openphish.com',
  paloalto:'vt-pan-false-positive@paloaltonetworks.com', panda:'falsepositives@pandasecurity.com',
  phishingdatabase:'https://github.com/phishing-Database/Phishing.Database/?tab=readme-ov-file#requests--support', phishlabs:'info@phishlabs.com',
  phishtank:'https://www.phishtank.com/contact.php', prebytes:'https://www.support.prebytes.com/helpcenter/removals/', quickheal:'viruslab@quickheal.com',
  quttera:'support@quttera.com', safetoopen:'virustotal@safetoopen.com', sansec:'virustotal@sansec.io', scantitan:'fp@scantitan.com',
  scumware:'https://www.scumware.org/removals.php', seclookup:'info@seclookup.com', securolytics:'https://tickets.securolytics.io/portal/en/kb/securolytics-1',
  sentinelone:'report@sentinelone.com', skyhigh:'gatewayantimalwarefpsubmission@skyhighsecurity.com', socradar:'vt@socradar.io',
  sophos:'https://support.sophos.com/support/s/filesubmission', spam404:'https://www.spam404.com/revision-request-domain.html', spamhaus:'https://www.spamhaus.org/dbl/removal/form/',
  stopforumspam:'https://www.stopforumspam.com/removal', sucuri:'soc@sucuri.net', symantec:'https://symsubmit.symantec.com',
  tencent:'TAVfp@tencent.com', trellix:'datasubmission@trellix.com', trendmicro:'https://www.trendmicro.com/en_us/about/legal/detection-reevaluation.html',
  trustwave:'https://support.trustwave.com/virustotal-detection-review/', levelblue:'LevelBlueVirusTotalEscalations@levelblue.com', urlhaus:'vt-fp@abuse.ch',
  urlquery:'contact@urlquery.net', viettel:'cyberthreat@viettel.com.vn', vipre:'https://helpdesk.vipre.com/hc/en-us/requests/new',
  virusdie:'partners@virusdie.com', webroot:'https://support.threatintel.opentext.com/tools/change-request.php', xcitium:'support@xcitium.com',
  comodo:'support@xcitium.com', yandexsafebrowsing:'sbapi@support.yandex.ru', yandex:'yandex-antivir@support.yandex.ru', zerofox:'ask@zerofox.com',
  zillya:'support@zillya.com', zonealarm:'zonealarm_VT_reports@checkpoint.com', zoner:'false@zonerantivirus.com'
};
// Short tips per vendor, from their own help pages and what site owners report
const VT_FP_TIP={
  alphamountain:'Ticket form; you have to log in or sign up first. Takes up to 24 h to apply after approval.',
  gridinsoft:'Website form. Give the full address and proof the site was cleaned; no passwords or tokens in the URL.',
  sophos:'Choose "Web Address (URL)". No Sophos account needed; usually 24–72 h.',
  bitdefender:'Choose "False positive" and "URL". Mention you own the site and add the VirusTotal link.',
  fortinet:'Malicious URL Appeal form: the URL, your name, e-mail and company, and a short comment.',
  chongluadao:'Complaint form for mislabelled sites.',
  trustwave:'Form for VirusTotal detections.',
  bfore:'Ticket form.',
  googlesafebrowsing:'Site owners can also ask for a review in Google Search Console.',
  socradar:'No form; e-mail with the VirusTotal link. Site owners report this works.',
  cyradar:'Form at cyradar.com/reportfp — no separate domain field, put the domain in the Message box.',
  malwareurl:'No form; e-mail with the VirusTotal link.',
  spamhaus:'Check the domain first; removal needs the cause fixed.'
};
function vtFpKey(vendor){
  const n=String(vendor).toLowerCase().replace(/[^a-z0-9]/g,'');
  let best='';
  for(const k in VT_FP) if(n.startsWith(k)&&k.length>best.length) best=k;
  return best;
}
function vtFpContact(vendor){const k=vtFpKey(vendor);return k?VT_FP[k]:'';}
function vtRevDone(domain,vendor){
  const r=reviews[domain]?.[vendor]; if(!r) return '';
  const d=new Date(r.ts*1000), p=n=>String(n).padStart(2,'0');
  return '<div class="vt-rev-done" title="Saved for this site, for everyone who manages it">&#10003; Requested '+p(d.getDate())+'-'+p(d.getMonth()+1)+'-'+d.getFullYear()
    +' ('+p(d.getHours())+':'+p(d.getMinutes())+')'+(r.by?' by '+vtEsc(r.by):'')
    +' · <a onclick="vtReviewDeny(this,\''+vtEsca(domain)+'\',\''+vtEsca(vendor)+'\')" title="The vendor turned the request down: remove this note and log it in the history">denied</a>'
    +' · <a onclick="vtReviewMark(\''+vtEsca(domain)+'\',\''+vtEsca(vendor)+'\',1)" title="Clicked by mistake: forget this request">undo</a></div>';
}
window.vtReviewDeny=function(a,domain,vendor){
  const box=a.closest('.vt-rev-done');
  box.outerHTML='<div class="vt-rev-deny"><input type="text" maxlength="300" placeholder="Reason (optional), e.g. added less than a day ago">'
    +'<button class="vt-btn sm" onclick="vtReviewMark(\''+vtEsca(domain)+'\',\''+vtEsca(vendor)+'\',1,\'\',\'\',this.previousElementSibling.value)">Save</button>'
    +'<a onclick="vtFsRefresh(\''+vtEsca(domain)+'\')">cancel</a></div>';
  const inp=document.querySelector('.vt-rev-deny input'); if(inp){inp.focus(); inp.onkeydown=ev=>{if(ev.key==='Enter')inp.nextElementSibling.click(); if(ev.key==='Escape')vtFsRefresh(domain);};}
};
window.vtCopyReviewUrl=function(url){
  const fallback=()=>{
    const input=document.createElement('textarea');
    input.value=url;
    input.setAttribute('readonly','');
    input.style.cssText='position:fixed;top:0;left:-9999px';
    const previous=document.activeElement;
    document.body.appendChild(input);
    input.select();
    let copied=false;
    try{copied=document.execCommand('copy');}catch(e){}
    input.remove();
    if(previous&&previous.focus) previous.focus({preventScroll:true});
    return copied;
  };
  const done=()=>vtToast('Site URL copied: '+url,'ok');
  const failed=()=>vtToast('Could not copy automatically. Site URL: '+url,'info');
  if(navigator.clipboard&&navigator.clipboard.writeText){
    try{
      navigator.clipboard.writeText(url).then(done,()=>{fallback()?done():failed();});
    }catch(e){fallback()?done():failed();}
  }else{fallback()?done():failed();}
};
window.vtReviewMark=async function(domain,vendor,remove,via,to,deniedReason){
  const denied=typeof deniedReason==='string';
  if(!remove&&!denied) vtCopyReviewUrl('https://'+domain+'/');
  const fpToken=document.getElementById('forgery_protection_token')?.content||'';
  if(!remove){ // show it at once; the server copy follows
    (reviews[domain]=reviews[domain]||{})[vendor]={ts:Math.floor(Date.now()/1000),by:''};
    setTimeout(()=>vtFsRefresh(domain),60); // after the browser has followed the link
  }
  try{
    const r=await fetch(BASE+'?action=review-mark',{method:'POST',headers:{'X-Forgery-Protection-Token':fpToken},
      body:new URLSearchParams({domain,vendor,remove:remove?'1':'',via:via||'',to:to||'',denied:denied?'1':'',reason:denied?deniedReason:''})}).then(x=>x.json());
    if(r&&r.ok){reviews[domain]=r.reviews; if(Array.isArray(r.reviews)) reviews[domain]={};}
    else vtToast('Could not save the review request','error');
  }catch(e){vtToast('Could not save the review request','error');}
  vtFsRefresh(domain); vtLoadSiteHist(domain);
};
function vtFpLink(vendor,domain){
  const k=vtFpKey(vendor), c=k?VT_FP[k]:'', tip=k&&VT_FP_TIP[k]?'<div class="vt-why-tip">'+vtEsc(VT_FP_TIP[k])+'</div>':'';
  const via=c.includes('@')?'email':'form';
  const mark=' onclick="vtReviewMark(\''+vtEsca(domain)+'\',\''+vtEsca(vendor)+'\',0,\''+via+'\',\''+vtEsca(c)+'\')"', done=vtRevDone(domain,vendor);
  if(!c) return '<a data-site-url="'+vtEsc('https://'+domain+'/')+'" onclick="vtCopyReviewUrl(this.dataset.siteUrl)" href="'+VT_FP_LIST+'" target="_blank" rel="noopener">find the contact</a>';
  if(c.includes('@')) return '<a'+mark+' href="mailto:'+vtEsc(c)+'?subject='+encodeURIComponent('Review request: '+domain+' has been cleaned')
    +'&body='+encodeURIComponent('Hello,\n\nThe website '+domain+' is flagged by your service on VirusTotal:\nhttps://www.virustotal.com/gui/domain/'+domain+'/detection\n\nThe site has been cleaned and secured. Could you review it and remove it from your list?\n\nThank you.')+'">'+vtEsc(c)+'</a>'+done+tip;
  return '<a'+mark+' href="'+vtEsc(c)+'" target="_blank" rel="noopener">request a review ↗</a>'+done+tip;
}
function vtWhyRowHTML(domain,st){
  const d=results[domain]||{}, e=vtEsca(domain);
  const list=(d.flagged_by||[]).map(s=>{const i=String(s).indexOf(': ');return i>0?[s.slice(0,i),s.slice(i+2)]:[s,''];});
  const close='<a onclick="vtFsClose(\''+e+'\')">Close</a>';
  let h='<div class="vt-fs-head"><strong>VirusTotal</strong>'
    +(list.length?'<span class="bad">flagged by '+list.length+' vendor'+(list.length===1?'':'s')+'</span>':'<span>'+vtEsc(LABELS[st]||st)+'</span>')
    +(d.last_scan?'<span>last checked '+vtEsc(d.last_scan)+'</span>':'')
    +(list.length?'<span>review requested at '+list.filter(([v])=>reviews[domain]?.[v]).length+' of '+list.length+'</span>':'')
    +'<span class="sp"></span><a href="https://www.virustotal.com/gui/domain/'+vtEsc(domain)+'/detection" target="_blank" rel="noopener">Full report ↗</a> · '+close+'</div>';
  h+='<div class="vt-why-note"><strong>Already cleaned the site, and it is still red or orange?</strong> That is normal for a while. '
    +'This status comes from VirusTotal, not from the files on the server. Each vendor below keeps its own blocklist and updates it on its own schedule: '
    +'some within hours, others only after days or weeks, and some only when you ask.</div>';
  if(list.length||avast.products?.length||gsb.enabled){
    h+='<table class="vt-fs-tbl vt-why-tbl"><thead><tr><th>Vendor</th><th>Verdict</th><th>Ask for a review</th></tr></thead><tbody>'+vtGsbRow(domain)+vtAvastRow(domain)
      +list.map(([v,r])=>'<tr'+(reviews[domain]?.[v]?' class="asked"':'')+'><td>'+vtEsc(v)+'</td><td>'+vtEsc(r)+'</td><td>'+vtFpLink(v,domain)+'</td></tr>').join('')+'</tbody></table>';
  }
  h+='<div class="vt-sh"><div class="vt-tr-title" style="margin-top:12px">History of this site <span class="hint">(newest first)</span></div><div id="vtsh-'+rid(domain)+'">'+vtSiteHistHTML(domain)+'</div></div>';
  if(!list.length) return '<tr class="vt-fs-row" id="vtfs-'+rid(domain)+'" data-domain="'+vtEsc(domain)+'" data-status="'+st+'"><td colspan="5"><div class="vt-fs">'+h+'</div></td></tr>';
  h+='<ol class="vt-why-steps">'
    +'<li><strong>Clean the site first.</strong> Use the scanner buttons to find infected files. Update WordPress, plugins and themes, remove plugins you don\'t use, change all passwords (admin, FTP/SFTP, database, hosting) and remove admin users you don\'t know. Otherwise the site is infected again soon and the vendors list it again.</li>'
    +'<li><strong>Click Rescan.</strong> VirusTotal then checks the site address again with all vendors (this uses one manual call). The score shown here comes from the VirusTotal report for the whole domain, which follows when the vendors update their own lists, so it does not always drop right away.</li>'
    +'<li><strong>Ask the vendors that still flag it for a review.</strong> Opening a review link copies the site URL to your clipboard. Paste it into the form and mention that it has been cleaned and what you did. Most answer within a few days.</li>'
    +'<li><strong>Google:</strong> if Chrome shows a red warning, the site owner can ask Google for a review in <a href="https://search.google.com/search-console/security-issues" target="_blank" rel="noopener">Search Console → Security issues</a>. Google usually handles this within a few days.</li>'
    +'<li><strong>Click Rescan again after a few days</strong> to see which vendors have removed the site from their list. Results of infected sites are refreshed every day by the background scan anyway. '
    +'When a vendor says it has removed the site but VirusTotal still shows it: VirusTotal keeps separate reports for <code>www.</code> and <code>http://</code> versions of the address; open them on virustotal.com and click <em>Reanalyze</em> on each.</li>'
    +'</ol><div class="vt-fs-note">Contacts from <a href="'+VT_FP_LIST+'" target="_blank" rel="noopener">VirusTotal\'s list of vendor contacts</a>. StingyScan never changes anything on the site; cleaning is up to you.</div>';
  return '<tr class="vt-fs-row" id="vtfs-'+rid(domain)+'" data-domain="'+vtEsc(domain)+'" data-status="'+st+'"'+(inQueue(domain)?' data-job="1"':'')+'><td colspan="5"><div class="vt-fs">'+h+'</div></td></tr>';
}
// A click anywhere on a site's row (not on a button, link or field) opens or closes its details
document.addEventListener('click',ev=>{
  const tr=ev.target.closest('#vt-tbody tr[data-domain]');
  if(!tr||tr.classList.contains('vt-fs-row')) return;
  if(ev.target.closest('a,button,input,select,textarea,label,summary')) return;
  if(window.getSelection&&String(window.getSelection())) return; // selecting text, not clicking
  if(tr.querySelector('.vt-why-btn')) vtWhyClick(tr.dataset.domain);
});
window.vtWhyClick=function(domain){
  if(fsOpen[domain]==='vt'){vtFsClose(domain);return;}
  fsOpen[domain]='vt'; vtFsRefresh(domain); vtLoadSiteHist(domain); vtAvastLive(domain); vtGsbLive(domain);
};
window.vtLoadSiteHist=async function(domain){
  const r=await vtFetch('?action=site-history&domain='+encodeURIComponent(domain)).catch(()=>null);
  if(r&&Array.isArray(r.history)){siteHist[domain]=r.history; if(r.history.length) histSites.add(domain);}
  const el=document.getElementById('vtsh-'+rid(domain));
  if(el) el.innerHTML=vtSiteHistHTML(domain);
};
let avChecking={};
/** Google Safe Browsing as its own vendor row in the details: official API, checked live and every night */
let gsbChecking={};
function vtGsbRow(domain){
  if(!gsb.enabled) return '';
  const r=gsb.results?.[domain], p=x=>String(x).padStart(2,'0'), name='Google Safe Browsing';
  let verdict, action='—';
  if(gsbChecking[domain]) verdict='<span class="vt-spin"></span> '+(gsbChecking[domain]==='waiting'?'queued for request limit':'checking Google…');
  else if(!r) verdict='<span class="hint">not checked yet</span>';
  else{
    const d=new Date(r.checked_at*1000), stale=Date.now()/1000-r.checked_at>(gsb.fresh||1800);
    const meta='<div class="vt-why-tip">'+(stale?'<strong>last known</strong> · ':'')+p(d.getDate())+'-'+p(d.getMonth()+1)+'-'+d.getFullYear()+' ('+p(d.getHours())+':'+p(d.getMinutes())+')'
      +' · <a onclick="vtGsbLive(\''+vtEsca(domain)+'\',1)">check again</a></div>';
    if(r.state!=='ok') verdict='<span class="vt-sh-pill warn">Check failed</span> '+vtEsc(r.error||'')+meta;
    else if(!r.threats?.length) verdict='<span class="vt-sh-pill good">Not listed</span>'+meta;
    else{
      verdict=r.threats.map(t=>'<span class="vt-sh-pill bad">'+vtEsc(t)+'</span>').join(' ')+meta;
      const url=gsb.review||'https://search.google.com/search-console/security-issues';
      action='<a onclick="vtReviewMark(\''+vtEsca(domain)+'\',\''+name+'\',0,\'form\',\''+vtEsca(url)+'\')" href="'+vtEsc(url)+'" target="_blank" rel="noopener">request a review in Search Console ↗</a>'
        +vtRevDone(domain,name)+'<div class="vt-why-tip">The site owner adds the site to Google Search Console, fixes what is listed under <strong>Security issues</strong>, ticks <em>I have fixed these issues</em> and clicks <strong>Request review</strong>. Phishing reviews usually take a day, malware reviews a few days.</div>';
    }
  }
  return '<tr class="vt-av-row'+(reviews[domain]?.[name]?' asked':'')+'"><td>'+vtBrandIco('google')+' '+name+' <span class="hint" title="Asked directly from Google\'s official Safe Browsing API, not via VirusTotal.">(direct)</span></td><td>'+verdict+'</td><td>'+action+'</td></tr>';
}
/** Ask Google now (force = also when a result of the last 30 minutes exists) */
window.vtGsbLive=async function(domain,force){
  if(!gsb.enabled||gsbChecking[domain]) return;
  gsbChecking[domain]=true; if(fsOpen[domain]==='vt') vtFsRefresh(domain);
  let r;
  for(;;){
    r=await vtFetch('?action=gsb-live&domain='+encodeURIComponent(domain)+(force?'&force=1':'')).catch(()=>null);
    if(r?.result?.state!=='rate_limited') break;
    gsbChecking[domain]='waiting'; if(fsOpen[domain]==='vt') vtFsRefresh(domain);
    await delay(Math.max(1,r.result.retry_after||60)*1000);
    if(fsOpen[domain]!=='vt'){delete gsbChecking[domain];return;}
    gsbChecking[domain]=true;
  }
  delete gsbChecking[domain];
  gsb.results=gsb.results||{};
  if(r?.result) gsb.results[domain]=r.result;
  else gsb.results[domain]={checked_at:Math.floor(Date.now()/1000),state:'failed',threats:[],error:r?.error||'Network error'};
  vtFsRefresh(domain); if(typeof updateRow==='function'&&results[domain]) updateRow(domain,results[domain]);
  if(fsOpen[domain]==='vt') vtLoadSiteHist(domain);
};
/** Avast / AVG as their own vendor rows in the details: checked live, not via VirusTotal */
function vtAvastRow(domain){
  return (avast.products||[]).map(pr=>vtAvastRow1(domain,pr)).join('');
}
function vtAvastRow1(domain,pr){
  const r=avast.results?.[domain]?.[pr.id], n=vtEsc(pr.name), p=x=>String(x).padStart(2,'0');
  let verdict, action='—';
  if(avChecking[domain+'|'+pr.id]) verdict='<span class="vt-spin"></span> '+(avChecking[domain+'|'+pr.id]==='waiting'?'queued for request limit':('checking '+n+'…'));
  else if(!r) verdict='<span class="hint">not checked yet</span>';
  else{
    const d=new Date(r.checked_at*1000);
    const meta='<div class="vt-why-tip">'+vtEsc(r.url)+' · '+p(d.getDate())+'-'+p(d.getMonth()+1)+'-'+d.getFullYear()+' ('+p(d.getHours())+':'+p(d.getMinutes())+') · HTTP '+(r.http||'–')
      +' · <a onclick="vtAvastLive(\''+vtEsca(domain)+'\',1)">check again</a></div>';
    if(r.state!=='ok') verdict='<span class="vt-sh-pill warn">Check failed</span> '+vtEsc(r.error||'')+meta;
    else if(!r.findings?.length) verdict='<span class="vt-sh-pill good">No classification received</span>'+meta;
    else{
      verdict=r.findings.map(f=>'<span class="vt-sh-pill bad">'+vtEsc(f.classification)+'</span> <code>'+vtEsc(f.detection||f.raw)+'</code>').join('<br>')+meta;
      action='<a onclick="vtReviewMark(\''+vtEsca(domain)+'\',\''+vtEsca(pr.name)+'\',0,\'form\',\''+vtEsca(pr.form)+'\')" href="'+vtEsc(pr.form)+'" target="_blank" rel="noopener">report a false positive ↗</a>'
        +vtRevDone(domain,pr.name)+'<div class="vt-why-tip">Official '+n+' form; choose <strong>URL</strong> and enter the site address. '
        +'The form asks for an <strong>Alert ID</strong>: that is not the code above, it only appears in the warning popup of the '+n+' app on a computer that blocks the site. '
        +'Leave it empty if the form allows it, or open the site on a PC with '+n+' installed and copy the Alert ID from the popup. '
        +'Mention the classification (<code>'+vtEsc(r.findings.map(f=>f.classification).join(', '))+'</code>) in the description.</div>';
    }
  }
  return '<tr class="vt-av-row'+(reviews[domain]?.[pr.name]?' asked':'')+'"><td>'+vtBrandIco(pr.id)+' '+n+' <span class="hint" title="Asked directly from '+n+'\'s URL reputation service, not via VirusTotal. Unofficial, may stop working.">(direct)</span></td><td>'+verdict+'</td><td>'+action+'</td></tr>';
}
/** Ask Avast and/or AVG now, one after the other (force = also when a result of the last 5 minutes exists) */
window.vtAvastLive=async function(domain,force){
  for(const pr of (avast.products||[])){
    const k=domain+'|'+pr.id;
    if(avChecking[k]) continue;
    avChecking[k]=true; if(fsOpen[domain]==='vt') vtFsRefresh(domain);
    let r;
    for(;;){
      r=await vtFetch('?action=avast-live&domain='+encodeURIComponent(domain)+'&product='+pr.id+(force?'&force=1':'')).catch(()=>null);
      if(r?.result?.state!=='rate_limited') break;
      avChecking[k]='waiting'; if(fsOpen[domain]==='vt') vtFsRefresh(domain);
      await delay(Math.max(1,r.result.retry_after||60)*1000);
      if(fsOpen[domain]!=='vt'){delete avChecking[k];return;}
      avChecking[k]=true;
    }
    delete avChecking[k];
    const res=avast.results=avast.results||{}; res[domain]=res[domain]||{};
    if(r?.result){res[domain][pr.id]=r.result; if(r.result.state==='ok') histSites.add(domain);}
    else res[domain][pr.id]={url:'https://'+domain+'/',checked_at:Math.floor(Date.now()/1000),http:0,state:'failed',findings:[],error:r?.error||'Network error'};
    vtFsRefresh(domain);
  }
  if(fsOpen[domain]==='vt') vtLoadSiteHist(domain);
  if(document.getElementById('vtpanel-trend').classList.contains('active')) vtLoadTrend();
};
function vtSiteHistHTML(domain){
  const list=siteHist[domain];
  if(!list) return '<span class="hint">Loading history…</span>';
  if(!list.length) return '<span class="hint">Nothing recorded yet for this site.</span>';
  const p=n=>String(n).padStart(2,'0');
  const LBL={infected:'Infected',suspicious:'Suspicious',clean:'Clean',gone:'Left the list'};
  const badge=st=>'<span class="vt-badge '+(LBL[st]?st:'unchecked')+'">'+vtEsc(LBL[st]||st||'unknown')+'</span>';
  const pill=(cls,t)=>'<span class="vt-sh-pill '+cls+'">'+t+'</span>';
  return list.map(e=>{
    const d=new Date(e.ts*1000);
    const when=p(d.getDate())+'-'+p(d.getMonth()+1)+'-'+d.getFullYear()+' <small>('+(e.approx?'last seen ':'')+p(d.getHours())+':'+p(d.getMinutes())+')</small>';
    let what='', text='';
    if(e.type==='status'){what=badge(e.from)+'<span class="vt-sh-arr">&rarr;</span>'+badge(e.to);}
    else if(e.type==='detected'){what=pill('bad','Detected');text='<strong>'+vtEsc(e.score||'')+'</strong>'+(e.vendors?.length?' by '+vtEsc(e.vendors.join(', '))+(e.partial?' and others':''):'');}
    else if(e.type==='flag'){what=pill('bad','Flagged');text='<strong>'+vtEsc(e.vendor)+'</strong>'+(e.verdict?': '+vtEsc(e.verdict):'');}
    else if(e.type==='unflag'){what=pill('good','Delisted');text='<strong>'+vtEsc(e.vendor)+'</strong> no longer flags the site';}
    else if(e.type==='review'){what=pill('info','Review asked');text='<strong>'+vtEsc(e.vendor)+'</strong> '+(e.via==='email'?'by e-mail'+(e.to?' to '+vtEsc(e.to):''):'via website form'+(e.to?' ('+(/^https?:\/\//i.test(e.to)?'<a href="'+vtEsc(e.to)+'" target="_blank" rel="noopener">'+vtEsc(e.to.replace(/^https?:\/\//i,'').slice(0,50))+'</a>':vtEsc(e.to.slice(0,50)))+')':''))+(e.by?' · by '+vtEsc(e.by):'');}
    else if(e.type==='denied'){what=pill('warn','Review denied');text='<strong>'+vtEsc(e.vendor)+'</strong>'+(e.reason?': '+vtEsc(e.reason):'')+(e.by?' · by '+vtEsc(e.by):'');}
    else if(e.type==='av'){what=e.to?pill('bad',vtEsc(e.product)+': '+vtEsc(e.to.replace(/^URL:/,''))):pill('good',vtEsc(e.product)+': none');
      text=e.to?vtEsc(e.product)+' classifies the site as <strong>'+vtEsc(e.to)+'</strong>'+(e.detection?' (<code>'+vtEsc(e.detection)+'</code>)':'')+(e.from?', was '+vtEsc(e.from):'')
               :vtEsc(e.product)+(e.observed?' check returned no classification':' no longer classifies the site')+(e.from?' (was '+vtEsc(e.from)+')':'');}
    else if(e.type==='unreview'){what=pill('','Undone');text='review request for <strong>'+vtEsc(e.vendor)+'</strong> undone'+(e.by?' · by '+vtEsc(e.by):'');}
    return '<div class="vt-sh-row"><div class="vt-sh-time">'+when+'</div><div class="vt-sh-what">'+what+'</div><div class="vt-sh-text">'+text+'</div></div>';
  }).join('');
}
function fsRowHTML(domain,st){
  const id=fsOpen[domain]; if(!id) return '';
  if(id==='vt') return vtWhyRowHTML(domain,st);
  const r=fsGet(domain,id), full=fsFull[domain+'|'+id], name=vtEsc(fsName(id)), e=vtEsca(domain);
  const close='<a onclick="vtFsClose(\''+e+'\')">Close</a>';
  let h;
  if(!r){
    h='<div class="vt-fs-head"><span class="vt-spin"></span> <strong>'+name+'</strong> starting…<span class="sp"></span>'+close+'</div><div class="vt-fs-bar"></div>';
  }else if(r.status==='queued'){
    h='<div class="vt-fs-head"><strong>'+name+'</strong> waiting: another scan is running on the server; this one starts right after it.<span class="sp"></span>'+close+'</div><div class="vt-fs-bar wait"></div>';
  }else if(r.status==='running'){
    h='<div class="vt-fs-head"><span class="vt-spin"></span> <strong>'+name+'</strong> is scanning the files in <code>'+vtEsc(r.root||'')+'</code> · started '+vtAgo(r.started)+'<span class="sp"></span>'+close+'</div>'
     +'<div class="vt-fs-bar"></div><div class="vt-fs-note">A large site can take several minutes. You can leave this page; the scan keeps running.</div>';
  }else if(r.status==='done'){
    const inst=scanners.find(x=>x.id===id)?.instant;
    const again='<a onclick="vtFsScan(\''+e+'\',\''+id+'\')">'+(inst?'Refresh':'Scan again')+'</a>';
    h='<div class="vt-fs-head"><strong>'+name+'</strong>'
     +(r.infected?'<span class="bad">'+r.infected+' infected file'+(r.infected===1?'':'s')+'</span>':'<span class="good">no infected files</span>')
     +'<span>in '+(r.files||0).toLocaleString()+' files · '+vtAgo(r.finished)+(r.errors?' · '+r.errors+' file(s) could not be read':'')+'</span>'
     +'<span class="sp"></span>'+again+' · '+close+'</div>';
    const found=full?.found;
    if(r.note) h+='<div class="vt-fs-note">'+vtEsc(r.note)+'</div>';
    if((r.infected||r.listed)&&!found) h+='<div class="vt-fs-note"><span class="vt-spin"></span> Loading the file list…</div>';
    if(found&&found.length){
      h+='<table class="vt-fs-tbl"><thead><tr><th>File (in <code>'+vtEsc(r.root||'')+'</code>)</th><th>Signature</th></tr></thead><tbody>'
        +found.map(f=>'<tr'+(f.handled?' class="handled"':'')+'><td>'+vtEsc(f.file)+'</td><td>'+vtEsc(f.signature)+'</td></tr>').join('')+'</tbody></table>';
      if((r.listed||r.infected)>found.length) h+='<div class="vt-fs-note">Showing the first '+found.length+' of '+(r.listed||r.infected)+'.</div>';
      h+='<div class="vt-fs-note">'+(inst?'':'Nothing was changed or removed. ')+'Check each file, clean or delete it (or restore a backup), then scan again. '
        +'Signatures starting with <code>PUA.</code> or <code>Heuristics.</code> are often false alarms.</div>';
    }
  }else{
    h='<div class="vt-fs-head"><strong>'+name+'</strong><span class="bad">'+vtEsc(r.message||r.status)+'</span><span class="sp"></span>'
     +'<a onclick="vtFsScan(\''+e+'\',\''+id+'\')">Try again</a> · '+close+'</div>';
  }
  return '<tr class="vt-fs-row" id="vtfs-'+rid(domain)+'" data-domain="'+vtEsc(domain)+'" data-status="'+st+'"'+(inQueue(domain)?' data-job="1"':'')+'><td colspan="5"><div class="vt-fs">'+h+'</div></td></tr>';
}
function vtFsRefresh(domain){updateRow(domain,results[domain]||null);}
window.vtFsClose=function(domain){delete fsOpen[domain];vtFsRefresh(domain);};
window.vtFsClick=function(domain,id){
  if(fsOpen[domain]===id){vtFsClose(domain);return;}   // click again: fold away
  fsOpen[domain]=id;
  const r=fsGet(domain,id);
  if(!r) return vtFsScan(domain,id);                   // never scanned: start
  vtFsRefresh(domain);
  if(fsBusy(domain,id)) vtFsPoll(domain,id);
  else if(r.status==='done'&&(r.infected||r.listed)) vtFsLoad(domain,id);
};
async function vtFsLoad(domain,id){
  const r=await vtFetch('?action=file-result&scanner='+id+'&domain='+encodeURIComponent(domain)).catch(()=>null);
  if(!r?.result) return;
  (scans[domain]=scans[domain]||{})[id]=r.result; fsFull[domain+'|'+id]=r.result; vtFsRefresh(domain);
}
window.vtFsScan=async function(domain,id){
  fsOpen[domain]=id; delete fsFull[domain+'|'+id];
  (scans[domain]=scans[domain]||{})[id]={status:'running',started:Date.now()/1000};
  addFsJob(domain,id);
  vtFsRefresh(domain);
  const fpToken=document.getElementById('forgery_protection_token')?.content||'';
  const res=await fetch(BASE+'?action=file-scan',{method:'POST',headers:{'X-Forgery-Protection-Token':fpToken},body:new URLSearchParams({domain,scanner:id})})
    .then(r=>r.json()).catch(()=>({error:'Network error'}));
  if(res.error){
    vtToast(res.error,'error');
    updateFsJob(domain,id,'failed',null,res.error);
    if(res.result&&!['running','queued'].includes(res.result.status)) scans[domain][id]=res.result; else delete scans[domain][id];
    vtFsRefresh(domain); return;
  }
  if(res.result){
    scans[domain][id]=res.result;
    if(res.result.status==='queued') updateFsJob(domain,id,'queued',null,'');
  }
  vtFsRefresh(domain); vtFsPoll(domain,id);
};
const fsPolling=new Set();
async function vtFsPoll(domain,id){
  const key=domain+'|'+id;
  if(fsPolling.has(key)) return;
  fsPolling.add(key);
  try{
    for(;;){
      await delay(5000);
      const r=await vtFetch('?action=file-result&scanner='+id+'&domain='+encodeURIComponent(domain)).catch(()=>null);
      if(!r?.result) continue;
      const prev=scans[domain]?.[id]?.status;
      (scans[domain]=scans[domain]||{})[id]=r.result;
      if(r.result.status==='running') updateFsJob(domain,id,'running',null,'');
      else if(r.result.status==='queued') updateFsJob(domain,id,'queued',null,'');
      else if(r.result.status==='done') updateFsJob(domain,id,'done',r.result,'');
      else updateFsJob(domain,id,'failed',null,r.result.message||r.result.status||'Error');
      if(['running','queued'].includes(r.result.status)){ if(prev!==r.result.status) vtFsRefresh(domain); continue; }
      fsFull[key]=r.result; vtFsRefresh(domain);
      if(fsOpen[domain]!==id){ // panel closed meanwhile: tell the user
        if(r.result.status==='done') vtToast(fsName(id)+' '+domain+': '+(r.result.infected?r.result.infected+' infected file(s) found':'no infected files'),r.result.infected?'error':'success');
        else vtToast(fsName(id)+' '+domain+': '+(r.result.message||r.result.status),'error');
      }
      break;
    }
  }finally{fsPolling.delete(key);}
}

function vtFetch(url){
  return fetch(BASE+url).then(r=>{
    // Plesk redirects to its login page when the session expires
    if(r.redirected&&!r.url.startsWith(location.origin+BASE)){
      vtToast('Session expired — reloading...','info');
      setTimeout(()=>window.location.reload(),1200);
      throw new Error('session');
    }
    if(!r.ok)throw new Error(r.status);
    return r.json();
  });
}
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

init().then(()=>vtCheckUpdate());
})();
</script>
