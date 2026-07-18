<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $panelName }}</title>
<link rel="icon" id="favicon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="/panel-design.css">
<style>
:root{--bg:#0a0a0f;--bg2:#0f0f16;--surface:#13131c;--surface2:#191923;--text:#e8e6ed;--text2:#9b98a8;--text3:#5c5a6a;--border:rgba(255,255,255,.04);--shadow:rgba(0,0,0,.5);--accent:#5b8af5;--accent2:#7c5cfc;--green:#34d399;--blue:#5b8af5;--sidebar-bg:#0c0c14;--nav-bg:rgba(10,10,15,.92);--card-bg:#13131c;--input-bg:rgba(255,255,255,.03);--hover-bg:rgba(255,255,255,.035);--glow:rgba(91,138,245,.15)}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif;background:url('/background.png') center/cover no-repeat fixed!important;color:var(--text);height:100vh;display:flex;overflow:hidden}
.sidebar{background:rgba(12,12,20,.4)!important;-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px)}
.topbar{background:rgba(10,10,15,.85)!important;-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px)}
.content{background:rgba(10,10,15,.45)!important}
::selection{background:var(--accent);color:#fff}
@keyframes popIn{from{opacity:0;transform:scale(.92) translateY(16px)}to{opacity:1;transform:scale(1) translateY(0)}}
@keyframes slideUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
@keyframes breathe{0%,100%{opacity:.5;transform:scale(1)}50%{opacity:1;transform:scale(1.08)}}
@keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}
@keyframes sweep{0%{transform:translateX(-100%)}100%{transform:translateX(100%)}}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-24px)}}
.anim-pop{animation:popIn .5s cubic-bezier(.22,1,.36,1) both}
.anim-slide{animation:slideUp .4s cubic-bezier(.22,1,.36,1) both}
.stagger-1{animation-delay:.04s}.stagger-2{animation-delay:.08s}.stagger-3{animation-delay:.14s}.stagger-4{animation-delay:.2s}.stagger-5{animation-delay:.26s}.stagger-6{animation-delay:.32s}
.sidebar{width:240px;background:var(--sidebar-bg);border-right:1px solid var(--border);display:flex;flex-direction:column;flex-shrink:0;height:100vh}
.sidebar-header{display:flex;align-items:center;gap:10px;padding:20px 20px 16px;border-bottom:1px solid var(--border);margin-bottom:4px}
.sidebar-logo{width:34px;height:34px;background:linear-gradient(135deg,var(--accent),var(--accent2));border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:700;color:#fff;box-shadow:0 4px 16px var(--glow)}
.sidebar-brand{font-size:16px;font-weight:600;color:var(--text);display:flex;align-items:center;gap:8px;letter-spacing:-.01em}
.sidebar-brand i{color:var(--accent)}
.sidebar-nav{padding:6px 10px;flex:1;overflow-y:auto}
.nav-section{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--text3);padding:14px 12px 5px}
.nav-item{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;cursor:pointer;font-size:13px;color:var(--text2);transition:all .2s;text-decoration:none;border:none;background:none;width:100%;text-align:left;font-family:inherit}
.nav-item i{width:18px;font-size:14px}
.nav-item:hover{background:var(--hover-bg);color:var(--text)}
.nav-item.active{background:var(--glow);color:var(--accent);font-weight:500}
.sidebar-user{margin:10px 12px;padding:10px 12px;border-radius:10px;background:var(--hover-bg);display:flex;align-items:center;gap:10px;cursor:pointer;border:1px solid var(--border)}
.sidebar-user .avatar{width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:600;color:#fff}
.sidebar-user .user-info{flex:1;min-width:0}
.sidebar-user .user-name{font-size:12px;font-weight:600;color:var(--text)}
.sidebar-user .user-email{font-size:10px;color:var(--text3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.main{flex:1;display:flex;flex-direction:column;overflow:hidden}
.content{padding:24px 28px;overflow-y:auto;flex:1;background:var(--bg)}
.topbar{display:flex;align-items:center;justify-content:space-between;padding:14px 28px;border-bottom:1px solid var(--border);background:var(--nav-bg);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);flex-shrink:0}
.topbar h2{font-size:17px;font-weight:600;letter-spacing:-.01em}
.topbar-actions{display:flex;align-items:center;gap:12px}
.topbar-btn{width:34px;height:34px;border-radius:8px;border:1px solid var(--border);background:var(--input-bg);color:var(--text2);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;font-size:12px;font-weight:600}
.topbar-btn:hover{background:var(--hover-bg);color:var(--text)}
.view{display:none}
.view.active{display:block}
.dash-greeting{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px}
.greet-text{display:flex;align-items:center;gap:10px}
.greet-head{font-size:18px;font-weight:500;color:var(--text2)}
.greet-name{font-size:18px;font-weight:600;color:var(--text)}
.greet-badge{display:flex;align-items:center;gap:8px;padding:6px 16px;border-radius:100px;background:var(--glow);border:1px solid rgba(91,138,245,.08);color:var(--text2);font-size:12px}
.pulse-dot{width:7px;height:7px;border-radius:50%;background:var(--green);animation:breathe 2s ease-in-out infinite;box-shadow:0 0 10px rgba(52,211,153,.3)}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;margin-bottom:20px}
.stat-card{background:#111;border:1px solid #222;border-radius:14px;padding:16px 18px;transition:all .25s cubic-bezier(.22,1,.36,1)}
.stat-card:hover{transform:translateY(-3px);box-shadow:0 8px 32px var(--shadow);border-color:var(--glow)}
.stat-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:15px;margin-bottom:12px}
.stat-icon.green{background:rgba(52,211,153,.08);color:var(--green)}
.stat-icon.accent{background:var(--glow);color:var(--accent)}
.stat-icon.amber{background:rgba(124,92,252,.08);color:var(--accent2)}
.stat-icon.blue{background:rgba(91,138,245,.08);color:var(--blue)}
.stat-label{font-size:11px;color:var(--text3);margin-bottom:3px;text-transform:uppercase;letter-spacing:.04em}
.stat-value{font-size:26px;font-weight:700;line-height:1.1;letter-spacing:-.03em}
.stat-sub{font-size:11px;color:var(--text2);margin-top:2px}
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.section-header h3{font-size:15px;font-weight:600}
.badge{padding:3px 10px;border-radius:100px;background:var(--hover-bg);color:var(--text2);font-size:11px;font-weight:500}
.server-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;margin-bottom:24px}
.server-card{background:#111;border:1px solid #222;border-radius:14px;padding:14px 18px;cursor:pointer;transition:all .25s cubic-bezier(.22,1,.36,1);position:relative;overflow:hidden}
.server-card:hover{transform:translateY(-3px);box-shadow:0 8px 32px var(--shadow)}
.server-card:active{transform:translateY(-1px)}
.server-card-strip{position:absolute;top:0;left:0;width:3px;height:100%;border-radius:3px 0 0 3px}
.server-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.server-name{font-size:13px;font-weight:600;display:flex;align-items:center}
.server-status{font-size:11px;display:flex;align-items:center;gap:5px;color:var(--text2);text-transform:capitalize}
.status-dot{width:7px;height:7px;border-radius:50%;display:inline-block;flex-shrink:0}
.status-dot.online{background:var(--green);box-shadow:0 0 8px rgba(52,211,153,.4);animation:breathe 2s ease-in-out infinite}
.status-dot.offline{background:#6b7280}
.status-dot.starting,.status-dot.stopping{background:#fbbf24;animation:breathe 1s ease-in-out infinite}
.server-meta{display:flex;gap:12px;font-size:11px;color:var(--text3);margin-bottom:8px;flex-wrap:wrap}
.server-meta span{display:flex;align-items:center;gap:3px}
.server-progress{height:3px;background:var(--border);border-radius:100px;overflow:hidden}
.server-progress .fill{height:100%;border-radius:100px;background:linear-gradient(90deg,var(--accent2),var(--accent));transition:width 1s ease}
.resource-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:24px}
.resource-card{background:#111;border:1px solid #222;border-radius:14px;padding:14px 18px}
.resource-card h3{font-size:12px;font-weight:600;margin-bottom:10px;display:flex;align-items:center;gap:6px;text-transform:uppercase;letter-spacing:.03em;color:var(--text2)}
.resource-bar-wrap{height:6px;background:var(--border);border-radius:100px;overflow:hidden;margin-bottom:6px}
.resource-bar{height:100%;border-radius:100px;transition:width 1.2s cubic-bezier(.22,1,.36,1)}
.resource-bar.orange{background:linear-gradient(90deg,var(--accent2),var(--accent))}
.resource-bar.green{background:linear-gradient(90deg,var(--green),#6ee7b7)}
.resource-stats{display:flex;justify-content:space-between;font-size:11px;color:var(--text2)}
.sd-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px}
.sd-header .si{display:flex;align-items:center;gap:14px}
.sd-header .si .big-dot{width:12px;height:12px;border-radius:50%;box-shadow:0 0 12px var(--glow)}
.sd-header .si h2{font-size:20px;font-weight:600}
.sd-header .si .addr{font-size:12px;color:var(--text3)}
.power-actions{display:flex;gap:6px;flex-wrap:wrap}
.power-btn{padding:7px 14px;border-radius:8px;border:1px solid var(--border);font-size:12px;font-weight:500;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:5px;font-family:inherit}
.power-btn,.power-btn.start,.power-btn.stop,.power-btn.restart,.admin-sub-tab,.server-tab,.topbar-btn,.btn-icon,.btn-sm,.btn-cancel,.btn-save,.btn-danger{background:#111!important;color:var(--text)!important;border-color:#222!important}
.power-btn:hover,.power-btn.start:hover,.power-btn.stop:hover,.power-btn.restart:hover,.admin-sub-tab:hover,.server-tab:hover,.topbar-btn:hover,.btn-icon:hover,.btn-sm:hover{background:#1a1a1a!important;color:var(--text)!important}
.admin-sub-tab.active,.server-tab.active{background:#1a1a1a!important;color:var(--accent)!important}
.server-tabs{display:flex;gap:3px;background:transparent!important;border-radius:10px;padding:3px;margin-bottom:14px;overflow-x:auto}
.server-tab{padding:8px 14px;border-radius:8px;cursor:pointer;font-size:12px;font-weight:500;color:var(--text2);transition:all .2s;border:none;background:none;display:flex;align-items:center;gap:5px;white-space:nowrap;font-family:inherit}
.server-tab:hover{color:var(--text);background:var(--hover-bg)}
.server-tab.active{background:var(--card-bg);color:var(--text);box-shadow:0 2px 8px var(--shadow)}
.server-tab-content{display:none}
.server-tab-content.active{display:block}
.console-wrap{background:#111;border:1px solid #222;border-radius:14px;overflow:hidden}
.console-output{padding:18px;height:340px;overflow-y:auto;font-family:'JetBrains Mono','Cascadia Code','Fira Code',monospace;font-size:12.5px;line-height:1.6;color:var(--text2)}
.console-output .line{padding:1px 0}
.console-output .line .time{color:var(--text3);margin-right:8px;font-size:11px}
.console-output .line .info{color:var(--accent)}
.console-output .line .warn{color:#fbbf24}
.console-output .line .err{color:#ef4444}
.console-output .line .ok{color:var(--green)}
.console-input-wrap{display:flex;align-items:center;gap:8px;padding:10px 14px;border-top:1px solid var(--border);background:var(--surface2)}
.console-input-wrap .prompt{color:var(--accent);font-family:'JetBrains Mono','Cascadia Code',monospace;font-size:13px;font-weight:700}
.console-input-wrap input{flex:1;background:none;border:none;outline:none;color:var(--text);font-family:'JetBrains Mono','Cascadia Code','Fira Code',monospace;font-size:12.5px}
.console-input-wrap input::placeholder{color:var(--text3)}
.file-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:6px;margin-top:10px}
.file-item{background:var(--card-bg);border:1px solid var(--border);border-radius:10px;padding:10px 12px;display:flex;align-items:center;gap:8px;cursor:pointer;transition:all .2s}
.file-item:hover{border-color:var(--accent);background:var(--hover-bg)}
.file-item .fi-name{flex:1;font-size:12px;color:var(--text);word-break:break-all}
.file-item .fi-size{font-size:10px;color:var(--text3);white-space:nowrap}
.file-actions{display:flex;gap:6px;margin-bottom:10px;flex-wrap:wrap}
.file-actions button{padding:6px 12px;border-radius:7px;border:1px solid var(--border);font-size:11px;cursor:pointer;background:var(--surface2);color:var(--text2);transition:all .2s;display:flex;align-items:center;gap:5px;font-family:inherit}
.file-actions button:hover{background:var(--hover-bg);color:var(--text)}
.file-path-bar{display:flex;align-items:center;gap:5px;padding:6px 10px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;font-size:12px;font-family:'JetBrains Mono',monospace;color:var(--text2);margin-bottom:10px;overflow-x:auto}
.file-path-bar span{cursor:pointer;color:var(--accent)}
.file-path-bar span:hover{text-decoration:underline}
.debug-btn{width:34px;height:34px;border-radius:8px;border:1px solid var(--border);background:var(--input-bg);color:var(--text3);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;font-size:11px;font-weight:700;font-family:monospace}.debug-btn:hover{background:var(--hover-bg);color:var(--text)}.debug-btn.active{border-color:rgba(251,191,36,.3);color:#fbbf24;background:rgba(251,191,36,.08);box-shadow:0 0 12px rgba(251,191,36,.1)}.debug-panel{position:fixed;top:56px;right:0;width:480px;max-height:calc(100vh - 64px);background:#111;border:1px solid #222;border-radius:0 0 0 14px;box-shadow:-8px 8px 40px var(--shadow);z-index:99999;display:none;flex-direction:column;overflow:hidden;backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px)}.debug-panel.open{display:flex}.debug-panel-header{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid var(--border);background:var(--surface2);flex-shrink:0}.debug-panel-header span{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--text2)}.debug-panel-header button{padding:3px 8px;border-radius:5px;border:1px solid var(--border);font-size:9px;cursor:pointer;background:var(--hover-bg);color:var(--text2);transition:all .2s;font-family:inherit}.debug-panel-header button:hover{background:rgba(239,68,68,.08);color:#ef4444}.debug-panel-body{flex:1;overflow-y:auto;padding:8px 0;font-family:'JetBrains Mono','Cascadia Code',monospace;font-size:11px;line-height:1.5}.debug-section{border-bottom:1px solid var(--border);padding:8px 14px}.debug-section:last-child{border-bottom:none}.debug-section-title{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--accent);margin-bottom:4px;cursor:pointer;display:flex;align-items:center;gap:4px}.debug-section-title:hover{opacity:.8}.debug-section-content{color:var(--text2);word-break:break-all;white-space:pre-wrap;max-height:280px;overflow-y:auto;display:none}.debug-section-content.open{display:block}.debug-key{color:var(--accent2)}.debug-string{color:var(--green)}.debug-number{color:#fbbf24}.debug-bool{color:var(--accent)}.debug-null{color:var(--text3)}.debug-req-row{padding:3px 0;border-bottom:1px solid var(--border);font-size:10px;display:flex;gap:6px;align-items:baseline}.debug-req-row .method{font-weight:700;padding:1px 5px;border-radius:3px;background:var(--glow);color:var(--accent);font-size:9px}.debug-req-row .path{color:var(--text);flex:1}.debug-req-row .status{padding:1px 5px;border-radius:3px;font-size:9px;font-weight:600}.debug-req-row .status.ok{background:rgba(52,211,153,.1);color:var(--green)}.debug-req-row .status.err{background:rgba(239,68,68,.1);color:#ef4444}.data-table{width:100%;border-collapse:separate;border-spacing:0;background:var(--card-bg);border:1px solid var(--border);border-radius:12px;overflow:hidden}
.node-card{background:#111;border:1px solid #222;border-radius:12px;padding:14px 18px;transition:all .2s}.node-card .node-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:6px}.node-card .node-name{font-size:14px;font-weight:600;display:flex;align-items:center;gap:6px}.node-card .node-meta{display:flex;gap:12px;font-size:11px;color:var(--text3);flex-wrap:wrap}.node-card .node-actions{display:flex;gap:5px;margin-top:8px}.node-card .node-actions button{padding:4px 10px;border-radius:6px;border:1px solid var(--border);font-size:10px;cursor:pointer;background:var(--surface2);color:var(--text2);transition:all .2s;font-family:inherit}.node-card .node-actions button:hover{background:var(--hover-bg);color:var(--text)}
.data-table{background:#111}.data-table th,.data-table td{padding:10px 14px;text-align:left;font-size:12px;border-bottom:1px solid #222}
.data-table th{background:#1a1a1a;font-weight:600;color:var(--text2);font-size:11px;text-transform:uppercase;letter-spacing:.03em}
.data-table tr:last-child td{border-bottom:none}
.data-table tr:hover td{background:#1a1a1a}
.data-table .actions{display:flex;gap:5px}
.data-table .actions button{padding:3px 8px;border-radius:5px;border:1px solid var(--border);font-size:10px;cursor:pointer;transition:all .2s}
.btn-icon{width:28px;height:28px;border-radius:7px;border:1px solid var(--border);background:var(--surface2);color:var(--text2);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .2s;font-size:12px}
.btn-icon:hover{background:var(--hover-bg);color:var(--text)}
.btn-sm{padding:4px 10px;border-radius:6px;border:1px solid var(--border);font-size:10px;cursor:pointer;background:var(--surface2);color:var(--text2);transition:all .2s;font-family:inherit}
.btn-sm:hover{background:var(--hover-bg);color:var(--text)}
.btn-sm.danger{border-color:rgba(239,68,68,.15);color:#ef4444}
.btn-sm.danger:hover{background:rgba(239,68,68,.08)}
.btn-sm.primary{border-color:rgba(91,138,245,.15);color:var(--accent)}
.btn-sm.primary:hover{background:var(--glow)}
.empty-state{padding:50px 32px;text-align:center;color:var(--text3);border:1px dashed var(--border);border-radius:16px;font-size:13px;margin-top:10px}
.plugin-card{display:flex;align-items:center;gap:14px;background:#111;border:1px solid #222;border-radius:12px;padding:14px 16px;margin-bottom:8px;transition:all .2s}
.plugin-card:hover{border-color:var(--accent);box-shadow:0 2px 12px rgba(91,138,245,.08)}
.plugin-card.disabled{opacity:.5}
.plugin-icon{width:40px;height:40px;border-radius:10px;background:var(--surface2);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--accent);flex-shrink:0}
.plugin-info{flex:1;min-width:0}
.plugin-info strong{font-size:13px;color:var(--text)}
.plugin-ver{font-size:11px;color:var(--text3);margin-left:6px}
.plugin-desc{font-size:12px;color:var(--text2);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.plugin-author{font-size:11px;color:var(--text3);margin-top:1px}
.plugin-actions{display:flex;gap:6px;flex-shrink:0}
.dev-docs{max-width:860px;padding-bottom:40px}
.dev-docs h2{font-size:18px;font-weight:700;margin:28px 0 8px;color:var(--text);display:flex;align-items:center;gap:8px}
.dev-docs p{font-size:13px;color:var(--text2);line-height:1.7;margin:6px 0}
.dev-docs code{background:var(--surface2);padding:2px 6px;border-radius:4px;font-size:12px;color:var(--accent)}
.dev-docs pre{background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:14px 16px;overflow-x:auto;margin:10px 0;font-size:12px;line-height:1.6;color:var(--text);white-space:pre}
.dev-docs .hook-card{background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:14px 16px;margin:10px 0}
.dev-docs .hook-card h4{font-size:13px;font-weight:600;margin-bottom:4px}
.dev-docs .hook-card p{font-size:12px;margin:0}
.dev-docs .example-plugin{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:16px;margin:14px 0}
.dev-docs .example-plugin .file-label{font-size:11px;color:var(--text3);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px}
.dev-docs .pill{display:inline-block;padding:2px 10px;border-radius:100px;font-size:10px;font-weight:600;margin:2px 4px 2px 0}
.dev-docs .pill.required{background:rgba(239,68,68,.15);color:#ef4444}
.dev-docs .pill.optional{background:rgba(59,130,246,.15);color:#3b82f6}
.admin-sub-tabs{display:flex;gap:3px;background:transparent!important;border-radius:10px;padding:3px;margin-bottom:20px;overflow-x:auto}
.admin-sub-tab{padding:8px 16px;border-radius:8px;cursor:pointer;font-size:12px;font-weight:500;color:var(--text2);transition:all .2s;border:none;background:none;display:flex;align-items:center;gap:5px;white-space:nowrap;font-family:inherit}
.admin-sub-tab:hover{color:var(--text);background:var(--hover-bg)}
.admin-sub-tab.active{background:var(--glow);color:var(--accent);font-weight:500}
.admin-sub-content{display:none}
.admin-sub-content.active{display:block}
.node-token-box{background:var(--surface2);border:1px solid var(--accent);border-radius:8px;padding:8px 12px;margin-top:8px;font-family:'JetBrains Mono',monospace;font-size:11px;word-break:break-all;color:var(--accent)}
.toast{position:fixed;bottom:24px;right:24px;padding:10px 20px;background:var(--surface);border:1px solid var(--border);border-radius:10px;color:var(--text);font-size:13px;font-weight:500;z-index:99999;backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);box-shadow:0 8px 32px var(--shadow);animation:slideUp .3s ease}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:99998;display:none;align-items:center;justify-content:center;backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px)}
.modal-overlay.active{display:flex}
.modal-box{background:#111;border:1px solid #222;border-radius:16px;padding:24px;width:100%;max-width:460px;box-shadow:0 24px 80px var(--shadow);animation:popIn .3s cubic-bezier(.22,1,.36,1) both;max-height:90vh;overflow-y:auto}
.modal-box h3{font-size:16px;font-weight:600;margin-bottom:14px}
.modal-box label{display:block;font-size:11px;color:var(--text2);margin-bottom:3px;margin-top:10px;text-transform:uppercase;letter-spacing:.03em}
.modal-box label:first-of-type{margin-top:0}
.modal-box input,.modal-box select,.modal-box textarea{width:100%;padding:9px 11px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;outline:none;font-family:inherit;transition:border-color .2s}
.modal-box input:focus,.modal-box select:focus,.modal-box textarea:focus{border-color:var(--accent);box-shadow:0 0 0 2px var(--glow)}
.modal-box textarea{min-height:70px;resize:vertical}
.modal-box .modal-actions{display:flex;gap:8px;margin-top:14px;justify-content:flex-end}
.modal-box .modal-actions button{padding:8px 16px;border-radius:8px;font-size:12px;font-weight:500;cursor:pointer;border:none;transition:all .2s;font-family:inherit}
.modal-box .modal-actions .btn-cancel{background:var(--surface2);color:var(--text2)}
.modal-box .modal-actions .btn-cancel:hover{background:var(--hover-bg)}
.modal-box .modal-actions .btn-save{background:var(--glow);color:var(--accent)}
.modal-box .modal-actions .btn-save:hover{background:rgba(91,138,245,.2)}
.modal-box .modal-actions .btn-danger{background:rgba(239,68,68,.08);color:#ef4444}
.modal-box .modal-actions .btn-danger:hover{background:rgba(239,68,68,.15)}
::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--border);border-radius:100px}
::-webkit-scrollbar-thumb:hover{background:var(--text3)}
@media(max-width:768px){.sidebar{width:200px}.content{padding:18px 14px}.topbar{padding:10px 14px}.stats-grid{grid-template-columns:1fr 1fr}.server-grid{grid-template-columns:1fr}.resource-row{grid-template-columns:1fr}.file-grid{grid-template-columns:1fr 1fr}}
</style>
</head>
<body>
<div class="mouse-glow" id="mouseGlow"></div>

<div class="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo">@if($panelLogo)<img src="{{ $panelLogo }}" style="width:34px;height:34px;border-radius:10px;object-fit:cover">@else P @endif</div>
    <span class="sidebar-brand"><i class="fas fa-cog"></i> {{ $panelName }}</span>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">Main</div>
    <button class="nav-item active" data-view="dashboard"><i class="fas fa-th-large"></i> Dashboard</button>
    <button class="nav-item" data-view="server"><i class="fas fa-server"></i> Server</button>
    <button class="nav-item" data-view="nodedetail" style="display:none" id="nodeDetailNav"><i class="fas fa-server"></i> <span id="nodeDetailNavLabel">Node</span></button>
    <div class="nav-section" id="adminSection">Administration</div>
    <button class="nav-item" id="adminNavBtn" data-view="admin"><i class="fas fa-shield-halved"></i> Admin</button>
    <div class="nav-section">Account</div>
    <button class="nav-item" id="profileBtn" data-view="profile"><i class="fas fa-user"></i> Profile</button>
    <button class="nav-item" id="logoutBtn"><i class="fas fa-right-from-bracket"></i> Logout</button>
  </nav>
  <div class="sidebar-user" onclick="showProfile()">
    <div class="avatar" id="userAvatar">AU</div>
    <div class="user-info">
      <div class="user-name" id="userName">Admin User</div>
      <div class="user-email" id="userEmail">Loading...</div>
    </div>
  </div>
</div>

<div class="main">
  <div class="topbar">
    <h2 id="pageTitle">{{ $panelName }}</h2>
  </div>

  <div class="content">
    <!-- ── Dashboard ── -->
    <div class="view active" id="view-dashboard">
      <div class="dash-greeting anim-pop">
        <div class="greet-text">
          <span class="greet-head">good morning,</span>
          <span class="greet-name" id="greetUser">Admin</span>
        </div>
        <div class="greet-badge"><span class="pulse-dot"></span> all systems nominal</div>
      </div>
      <div class="stats-grid" id="dashStats"></div>
      <div class="section-header"><h3>Your Servers</h3><span class="badge" id="serverCount">0 total</span></div>
      <div class="server-grid" id="serverGrid"></div>
    </div>

    <!-- ── Server Detail ── -->
    <div class="view" id="view-server">
      <div class="sd-header">
        <div class="si">
          <span class="big-dot" id="serverStatusDot" style="background:#34d399"></span>
          <div><h2 id="serverName">Server Name</h2><span class="addr" id="serverAddr">-</span></div>
        </div>
        <div class="power-actions" id="powerActions">
          <button class="power-btn start" onclick="sendPowerAction('start')"><i class="fas fa-play"></i> Start</button>
          <button class="power-btn stop" onclick="sendPowerAction('stop')"><i class="fas fa-stop"></i> Stop</button>
          <button class="power-btn restart" onclick="sendPowerAction('restart')"><i class="fas fa-rotate"></i> Restart</button>
        </div>
      </div>
      <div class="server-tabs" id="serverTabs">
        <button class="server-tab active" data-stab="console"><i class="fas fa-terminal"></i> Console</button>
        <button class="server-tab" data-stab="files"><i class="fas fa-folder-tree"></i> Files</button>
        <button class="server-tab" data-stab="databases"><i class="fas fa-database"></i> Databases</button>
        <button class="server-tab" data-stab="schedules"><i class="fas fa-clock"></i> Schedules</button>
        <button class="server-tab" data-stab="backups"><i class="fas fa-cloud-arrow-up"></i> Backups</button>
        <button class="server-tab" data-stab="subusers"><i class="fas fa-users"></i> Subusers</button>
        <button class="server-tab" data-stab="activity"><i class="fas fa-list"></i> Activity</button>
      </div>

      <div class="server-tab-content active" id="stab-console">
        <div class="resource-row">
          <div class="resource-card"><h3><i class="fas fa-microchip" style="color:var(--accent2)"></i> CPU</h3><div class="resource-bar-wrap"><div class="resource-bar orange" id="cpuBar" style="width:15%"></div></div><div class="resource-stats"><span id="cpuText">15%</span><span>of 4 cores</span></div></div>
          <div class="resource-card"><h3><i class="fas fa-memory" style="color:var(--green)"></i> Memory</h3><div class="resource-bar-wrap"><div class="resource-bar green" id="memBar" style="width:30%"></div></div><div class="resource-stats"><span id="memText">614 / 2048 MB</span><span>30%</span></div></div>
        </div>
        <div class="console-wrap">
          <div class="console-output" id="consoleOutput"></div>
          <div class="console-input-wrap">
            <span class="prompt">&gt;</span>
            <input type="text" placeholder="Type a command..." id="consoleInput" onkeydown="if(event.key==='Enter')sendServerCommand()">
          </div>
        </div>
      </div>

      <div class="server-tab-content" id="stab-files">
        <div class="file-actions">
          <button onclick="filePathUp()"><i class="fas fa-arrow-up"></i> Up</button>
          <button onclick="showFileCreateDirModal()"><i class="fas fa-folder-plus"></i> New Folder</button>
          <button onclick="showFileCreateModal()"><i class="fas fa-file-plus"></i> New File</button>
          <button onclick="document.getElementById('fileUploadInput').click()"><i class="fas fa-upload"></i> Upload</button>
          <input type="file" id="fileUploadInput" style="display:none" multiple onchange="fileUpload(this.files)">
        </div>
        <div class="file-path-bar" id="filePathBar"><span onclick="fileList('/')">/</span></div>
        <div class="file-grid" id="fileGrid"><div class="empty-state">Loading files...</div></div>
      </div>

      <div class="server-tab-content" id="stab-databases">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
          <span style="font-size:13px;font-weight:500">Server Databases</span>
          <button class="power-btn start" onclick="showDbCreateModal()"><i class="fas fa-plus"></i> New Database</button>
        </div>
        <div id="dbList"></div>
      </div>

      <div class="server-tab-content" id="stab-schedules">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
          <span style="font-size:13px;font-weight:500">Task Schedules</span>
          <button class="power-btn start" onclick="showScheduleCreateModal()"><i class="fas fa-plus"></i> New Schedule</button>
        </div>
        <div id="scheduleList"></div>
      </div>

      <div class="server-tab-content" id="stab-backups">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
          <span style="font-size:13px;font-weight:500">Backups</span>
          <button class="power-btn start" onclick="createBackup()"><i class="fas fa-plus"></i> New Backup</button>
        </div>
        <div id="backupList"></div>
      </div>

      <div class="server-tab-content" id="stab-subusers">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
          <span style="font-size:13px;font-weight:500">Subusers</span>
          <button class="power-btn start" onclick="showSubuserModal()"><i class="fas fa-user-plus"></i> Add Subuser</button>
        </div>
        <div id="subuserList"></div>
      </div>

      <div class="server-tab-content" id="stab-activity">
        <span style="font-size:13px;font-weight:500">Activity Log</span>
        <div id="serverActivityList" style="margin-top:12px"></div>
      </div>
    </div>

    <!-- ── Admin ── -->
    <div class="view" id="view-admin">
      <div class="admin-sub-tabs">
        <button class="admin-sub-tab active" data-atab="overview"><i class="fas fa-eye"></i> Overview</button>
        <button class="admin-sub-tab" data-atab="users"><i class="fas fa-users"></i> Users</button>
        <button class="admin-sub-tab" data-atab="adminservers"><i class="fas fa-cubes"></i> Servers</button>
        <button class="admin-sub-tab" data-atab="nodes"><i class="fas fa-server"></i> Nodes</button>
        <button class="admin-sub-tab" data-atab="locations"><i class="fas fa-location-dot"></i> Locations</button>
        <button class="admin-sub-tab" data-atab="allocations"><i class="fas fa-network-wired"></i> Allocations</button>
        <button class="admin-sub-tab" data-atab="settings">Settings</button>
        <button class="admin-sub-tab" data-atab="plugins"><i class="fas fa-puzzle-piece"></i> Plugins</button>
        <button class="admin-sub-tab" data-atab="developers"><i class="fas fa-code"></i> Developers</button>
        <button class="admin-sub-tab" data-atab="designer"><i class="fas fa-paint-brush"></i> Designer</button>
        <button class="admin-sub-tab" data-atab="webserver"><i class="fas fa-globe"></i> Web Server</button>
        <button class="admin-sub-tab" data-atab="activity"><i class="fas fa-list"></i> Activity</button>
      </div>

      <div class="admin-sub-content active" id="atab-overview">
        <div class="stats-grid" id="adminStats"></div>
        <div style="margin-top:14px"><span style="font-size:13px;font-weight:500">Recent Activity</span><div id="adminRecentActivity" style="margin-top:10px"></div></div>
      </div>

      <div class="admin-sub-content" id="atab-users">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
          <span style="font-size:13px;font-weight:500">All Users</span>
          <button class="power-btn start" onclick="showUserCreateModal()"><i class="fas fa-plus"></i> Add User</button>
        </div>
        <div id="userAdminList"></div>
      </div>

      <div class="admin-sub-content" id="atab-adminservers">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
          <span style="font-size:13px;font-weight:500">All Servers</span>
          <button class="power-btn start" onclick="showAdminServerCreateModal()"><i class="fas fa-plus"></i> Create Server</button>
        </div>
        <div id="adminServerList"><div style="padding:32px;text-align:center;color:var(--text3)">Loading servers...</div></div>
      </div>

      <div class="admin-sub-content" id="atab-nodes">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
          <span style="font-size:13px;font-weight:500">Node Agents</span>
          <button class="power-btn start" onclick="showCreateNodeModal()"><i class="fas fa-plus"></i> Add Node</button>
        </div>
        <div id="nodeList" style="display:grid;gap:10px"><div style="padding:32px;text-align:center;color:var(--text3)">Loading nodes...</div></div>
      </div>

      <div class="admin-sub-content" id="atab-locations">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
          <span style="font-size:13px;font-weight:500">Locations</span>
          <button class="power-btn start" onclick="showLocationCreateModal()"><i class="fas fa-plus"></i> Add Location</button>
        </div>
        <div id="locationList"></div>
      </div>

      <div class="admin-sub-content" id="atab-allocations">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
          <span style="font-size:13px;font-weight:500">Allocations</span>
          <button class="power-btn start" onclick="showAllocationCreateModal()"><i class="fas fa-plus"></i> Add Allocation</button>
        </div>
        <div id="allocationList"></div>
      </div>

      <div class="admin-sub-content" id="atab-settings">
        <span style="font-size:13px;font-weight:500">Panel Settings</span>
        <div style="margin-top:14px;max-width:500px" id="settingsForm"></div>
      </div>

      <div class="admin-sub-content" id="atab-activity">
        <span style="font-size:13px;font-weight:500">System Activity Log</span>
        <div id="adminActivityList" style="margin-top:12px"></div>
      </div>

      <div class="admin-sub-content" id="atab-plugins">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
          <span style="font-size:13px;font-weight:500">Plugin Manager</span>
          <div style="display:flex;gap:8px">
            <label for="pluginUploadInput" class="power-btn start" style="cursor:pointer"><i class="fas fa-upload"></i> Upload Plugin</label>
            <input type="file" id="pluginUploadInput" accept=".zip" style="display:none" onchange="uploadPlugin(this)">
          </div>
        </div>
        <div id="pluginList"></div>
      </div>

      <div class="admin-sub-content" id="atab-developers">
        <span style="font-size:13px;font-weight:500;display:block;margin-bottom:14px">Developer Documentation</span>
        <div id="developerDocs"><div style="padding:32px;text-align:center;color:var(--text3)">Loading...</div></div>
      </div>

      <div class="admin-sub-content" id="atab-designer">
        <span style="font-size:13px;font-weight:500;display:block;margin-bottom:14px">Panel Designer</span>
        <div id="designerContent"><div style="padding:32px;text-align:center;color:var(--text3)">Loading...</div></div>
      </div>

      <div class="admin-sub-content" id="atab-webserver">
        <span style="font-size:13px;font-weight:500">Web Server Configuration</span>
        <div id="webserverStatus" style="margin-top:10px"></div>
        <div id="webserverForm" style="margin-top:16px;max-width:600px;display:none">
          <label style="font-size:11px;text-transform:uppercase;letter-spacing:.03em;color:var(--text2);display:block;margin-bottom:3px">Config Type</label>
          <select id="wsConfigType" style="width:100%;padding:9px 11px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;outline:none;margin-bottom:10px"></select>
          <label style="font-size:11px;text-transform:uppercase;letter-spacing:.03em;color:var(--text2);display:block;margin-bottom:3px">Domain</label>
          <input type="text" id="wsDomain" value="example.com" style="width:100%;padding:9px 11px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;outline:none;margin-bottom:10px">
          <div id="wsSslFields" style="display:none">
            <label style="font-size:11px;text-transform:uppercase;letter-spacing:.03em;color:var(--text2);display:block;margin-bottom:3px">SSL Certificate Path</label>
            <input type="text" id="wsSslCert" value="/etc/ssl/certs/example.com.pem" style="width:100%;padding:9px 11px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;outline:none;margin-bottom:10px">
            <label style="font-size:11px;text-transform:uppercase;letter-spacing:.03em;color:var(--text2);display:block;margin-bottom:3px">SSL Key Path</label>
            <input type="text" id="wsSslKey" value="/etc/ssl/private/example.com-key.pem" style="width:100%;padding:9px 11px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;outline:none;margin-bottom:10px">
          </div>
          <div id="wsCaddySslField" style="display:none">
            <label style="font-size:11px;text-transform:uppercase;letter-spacing:.03em;color:var(--text2);display:block;margin-bottom:3px">SSL Email (for Let's Encrypt)</label>
            <input type="email" id="wsSslEmail" value="admin@example.com" style="width:100%;padding:9px 11px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;outline:none;margin-bottom:10px">
          </div>
          <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button class="power-btn start" onclick="previewWebServerConfig()"><i class="fas fa-eye"></i> Preview Config</button>
            <button class="power-btn" onclick="downloadWebServerConfig()"><i class="fas fa-download"></i> Download</button>
            <button class="power-btn" id="wsInstallBtn" onclick="installWebServer()"><i class="fas fa-download"></i> Install</button>
          </div>
        </div>
        <div id="wsPreview" style="margin-top:14px;display:none">
          <span style="font-size:13px;font-weight:500">Config Preview</span>
          <pre id="wsPreviewContent" style="margin-top:6px;padding:12px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;font-size:12px;overflow:auto;max-height:500px;white-space:pre-wrap;word-break:break-all"></pre>
          <button class="power-btn start" style="margin-top:8px" onclick="copyWebServerConfig()"><i class="fas fa-copy"></i> Copy to Clipboard</button>
        </div>
      </div>
    </div>

    <!-- ── Node Detail ── -->
    <div class="view" id="view-nodedetail">
      <div class="sd-header">
        <div class="si">
          <span class="big-dot" id="ndStatusDot" style="background:#6b7280"></span>
          <div><h2 id="ndName">Node Name</h2><span class="addr" id="ndAddr">-</span></div>
        </div>
        <div>
          <button class="power-btn start" onclick="ndRefresh()"><i class="fas fa-rotate"></i> Refresh</button>
          <button class="power-btn restart" onclick="ndShowToken()"><i class="fas fa-key"></i> Token</button>
        </div>
      </div>
      <div class="resource-row" id="ndResources" style="margin-bottom:12px">
        <div class="resource-card"><h3><i class="fas fa-microchip" style="color:var(--accent2)"></i> CPU</h3><div class="resource-bar-wrap"><div class="resource-bar orange" id="ndCpuBar" style="width:0%"></div></div><div class="resource-stats"><span id="ndCpuText">-</span><span id="ndCpuDesc">-</span></div></div>
        <div class="resource-card"><h3><i class="fas fa-memory" style="color:var(--green)"></i> Memory</h3><div class="resource-bar-wrap"><div class="resource-bar green" id="ndMemBar" style="width:0%"></div></div><div class="resource-stats"><span id="ndMemText">-</span><span id="ndMemDesc">-</span></div></div>
        <div class="resource-card"><h3><i class="fas fa-hard-drive" style="color:#fbbf24"></i> Disk</h3><div class="resource-bar-wrap"><div class="resource-bar orange" id="ndDiskBar" style="width:0%"></div></div><div class="resource-stats"><span id="ndDiskText">-</span><span id="ndDiskDesc">-</span></div></div>
      </div>
      <div class="server-tabs" id="ndTabs">
        <button class="server-tab active" data-ndtab="overview"><i class="fas fa-server"></i> Overview</button>
        <button class="server-tab" data-ndtab="servers"><i class="fas fa-cubes"></i> Servers</button>
        <button class="server-tab" data-ndtab="logs"><i class="fas fa-terminal"></i> Live Logs</button>
        <button class="server-tab" data-ndtab="files"><i class="fas fa-folder-tree"></i> Files</button>
      </div>

      <div class="server-tab-content active" id="ndtab-overview">
        <div class="stats-grid" style="grid-template-columns:1fr 1fr">
          <div class="stat-card"><div class="stat-icon accent"><i class="fas fa-server"></i></div><div class="stat-label">Status</div><div class="stat-value" id="ndOvStatus">-</div></div>
          <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-cubes"></i></div><div class="stat-label">Servers</div><div class="stat-value" id="ndOvServers">0</div><div class="stat-sub" id="ndOvServersSub">0 running</div></div>
          <div class="stat-card"><div class="stat-icon green"><i class="fas fa-location-dot"></i></div><div class="stat-label">Location</div><div class="stat-value" id="ndOvLocation">-</div></div>
          <div class="stat-card"><div class="stat-icon amber"><i class="fas fa-network-wired"></i></div><div class="stat-label">Connection</div><div class="stat-value" id="ndOvConn">-</div></div>
        </div>
        <div style="margin-top:14px"><span style="font-size:13px;font-weight:500">Last Seen</span><span style="float:right;font-size:12px;color:var(--text2)" id="ndOvLastSeen">-</span></div>
      </div>

      <div class="server-tab-content" id="ndtab-servers">
        <div id="ndServerList"><div class="empty-state">Loading servers...</div></div>
      </div>

      <div class="server-tab-content" id="ndtab-logs">
        <div class="console-wrap">
          <div class="console-output" id="ndConsoleOutput" style="height:400px"><div class="empty-state" style="border:none">Connecting to agent...</div></div>
        </div>
      </div>

      <div class="server-tab-content" id="ndtab-files">
        <div style="margin-bottom:10px">
          <label style="font-size:11px;color:var(--text2);display:block;margin-bottom:3px">Server</label>
          <select id="ndFileServer" style="width:100%;max-width:300px;padding:9px 11px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;outline:none" onchange="ndFileList()">
            <option value="">Select a server...</option>
          </select>
        </div>
        <div class="file-actions">
          <button onclick="ndFileUp()"><i class="fas fa-arrow-up"></i> Up</button>
          <button onclick="ndNewFolder()"><i class="fas fa-folder-plus"></i> New Folder</button>
          <button onclick="document.getElementById('ndFileInput').click()"><i class="fas fa-upload"></i> Upload</button>
          <input type="file" id="ndFileInput" style="display:none" multiple onchange="ndFileUpload(this.files)">
        </div>
        <div class="file-path-bar" id="ndFilePathBar"><span>/</span></div>
        <div class="file-grid" id="ndFileGrid"><div class="empty-state">Select a server above.</div></div>
      </div>
    </div>

    <!-- ── Profile ── -->
    <div class="view" id="view-profile">
      <span style="font-size:15px;font-weight:600">Profile Settings</span>
      <div style="max-width:460px;margin-top:18px">
        <label style="font-size:11px;color:var(--text2);display:block;margin-bottom:3px;text-transform:uppercase;letter-spacing:.03em">First Name</label>
        <input type="text" id="profileFirstName" style="width:100%;padding:9px 11px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;outline:none;margin-bottom:10px">
        <label style="font-size:11px;color:var(--text2);display:block;margin-bottom:3px;text-transform:uppercase;letter-spacing:.03em">Last Name</label>
        <input type="text" id="profileLastName" style="width:100%;padding:9px 11px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;outline:none;margin-bottom:10px">
        <label style="font-size:11px;color:var(--text2);display:block;margin-bottom:3px;text-transform:uppercase;letter-spacing:.03em">Language</label>
        <select id="profileLang" style="width:100%;padding:9px 11px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;outline:none;margin-bottom:10px"><option value="en">English</option><option value="es">Spanish</option><option value="fr">French</option><option value="de">German</option></select>
        <label style="font-size:11px;color:var(--text2);display:block;margin-bottom:3px;text-transform:uppercase;letter-spacing:.03em">Timezone</label>
        <select id="profileTz" style="width:100%;padding:9px 11px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;outline:none;margin-bottom:14px"><option value="UTC">UTC</option><option value="America/New_York">America/New_York</option><option value="America/Chicago">America/Chicago</option><option value="America/Denver">America/Denver</option><option value="America/Los_Angeles">America/Los_Angeles</option><option value="Europe/London">Europe/London</option><option value="Europe/Berlin">Europe/Berlin</option><option value="Asia/Tokyo">Asia/Tokyo</option></select>
        <button class="power-btn start" onclick="saveProfile()"><i class="fas fa-save"></i> Save</button>
        <div style="margin-top:20px;padding-top:18px;border-top:1px solid var(--border)">
          <span style="font-size:13px;font-weight:500">API Tokens</span>
          <div id="apiTokenList" style="margin-top:10px"></div>
          <button class="power-btn restart" onclick="showTokenCreateModal()" style="margin-top:10px"><i class="fas fa-plus"></i> New Token</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── Modals ── -->

<div class="modal-overlay" id="nodeModal"><div class="modal-box" style="max-width:500px"><h3 id="nodeModalTitle">Add Node Agent</h3><label>Node Name</label><input type="text" id="nodeName" placeholder="us-east-1"><label>FQDN</label><input type="text" id="nodeFqdn" placeholder="node1.example.com"><label>IP Address</label><input type="text" id="nodeIp" placeholder="192.168.1.100"><label>Port</label><input type="number" id="nodePort" placeholder="8055"><label>Location</label><select id="nodeLocation"></select><label>Memory (MB)</label><input type="number" id="nodeMemory" placeholder="4096" value="4096"><label>Storage (MB)</label><input type="number" id="nodeStorage" placeholder="51200" value="51200"><div class="modal-actions"><button class="btn-cancel" onclick="closeModal('nodeModal')">Cancel</button><button class="btn-save" onclick="createNode()">Create</button></div></div></div>
<div class="modal-overlay" id="tokenModal"><div class="modal-box"><h3>Node Token</h3><p style="font-size:12px;color:var(--text2);margin-bottom:10px">Save this token. It will not be shown again.</p><div class="node-token-box" id="tokenDisplay"></div><div class="modal-actions"><button class="btn-cancel" onclick="closeModal('tokenModal')">Close</button><button class="btn-save" onclick="copyToken()">Copy</button></div></div></div>
<div class="modal-overlay" id="adminServerModal"><div class="modal-box" style="max-width:500px"><h3 id="adminServerModalTitle">Create Server</h3><label>Name</label><input type="text" id="adminServerName" placeholder="My Server"><label>User</label><select id="adminServerUser"></select><label>Node</label><select id="adminServerNode"><option value="">None</option></select><label>Type</label><select id="adminServerType"><option value="minecraft">Minecraft</option><option value="paper">Paper</option><option value="velocity">Velocity</option></select><label>Version</label><input type="text" id="adminServerVersion" placeholder="1.21.4" value="1.21.4"><label>Memory (MB)</label><input type="number" id="adminServerMemory" placeholder="1024" value="1024"><label>Storage (MB)</label><input type="number" id="adminServerStorage" placeholder="5120" value="5120"><label>Port</label><input type="number" id="adminServerPort" placeholder="25565"><div class="modal-actions"><button class="btn-cancel" onclick="closeModal('adminServerModal')">Cancel</button><button class="btn-save" id="adminServerSaveBtn">Create</button></div></div></div>
<div class="modal-overlay" id="userModal"><div class="modal-box" id="userModalBody"></div></div>
<div class="modal-overlay" id="locationModal"><div class="modal-box"><h3 id="locModalTitle">Add Location</h3><label>Short Code</label><input type="text" id="locShortCode" placeholder="US-EAST"><label>Long Name</label><input type="text" id="locLongName" placeholder="US East Coast"><label>Description</label><textarea id="locDesc" placeholder="Primary data center"></textarea><div class="modal-actions"><button class="btn-cancel" onclick="closeModal('locationModal')">Cancel</button><button class="btn-save" onclick="saveLocation()">Save</button></div></div></div>
<div class="modal-overlay" id="allocationModal"><div class="modal-box"><h3>Add Allocation</h3><label>Node</label><select id="allocNodeId"></select><label>Location</label><select id="allocLocationId"></select><label>IP Address</label><input type="text" id="allocIp" placeholder="192.168.1.100"><label>Port</label><input type="number" id="allocPort" placeholder="25565"><div class="modal-actions"><button class="btn-cancel" onclick="closeModal('allocationModal')">Cancel</button><button class="btn-save" onclick="saveAllocation()">Save</button></div></div></div>
<div class="modal-overlay" id="settingsModal"><div class="modal-box"><h3>Panel Settings</h3><div id="settingsModalBody"></div><div class="modal-actions"><button class="btn-cancel" onclick="closeModal('settingsModal')">Cancel</button><button class="btn-save" onclick="saveSettings()">Save</button></div></div></div>
<div class="modal-overlay" id="fileDirModal"><div class="modal-box"><h3 id="fileDirModalTitle">New Folder</h3><label>Name</label><input type="text" id="fileDirName" placeholder="my-folder"><div class="modal-actions"><button class="btn-cancel" onclick="closeModal('fileDirModal')">Cancel</button><button class="btn-save" onclick="confirmFileDir()">Create</button></div></div></div>
<div class="modal-overlay" id="fileEditModal"><div class="modal-box" style="max-width:700px"><h3 id="fileEditTitle">Edit File</h3><textarea id="fileEditContent" style="min-height:280px;font-family:JetBrains Mono,Consolas,monospace;font-size:13px"></textarea><div class="modal-actions"><button class="btn-cancel" onclick="closeModal('fileEditModal')">Cancel</button><button class="btn-save" onclick="saveFileEdit()">Save</button></div></div></div>
<div class="modal-overlay" id="dbModal"><div class="modal-box"><h3>New Database</h3><label>Database Name</label><input type="text" id="dbName" placeholder="minecraft"><label>Password</label><input type="text" id="dbPass" placeholder="min 8 chars"><label>Remote Host</label><input type="text" id="dbHost" placeholder="%" value="%"><div class="modal-actions"><button class="btn-cancel" onclick="closeModal('dbModal')">Cancel</button><button class="btn-save" onclick="confirmCreateDb()">Create</button></div></div></div>
<div class="modal-overlay" id="dbPassModal"><div class="modal-box"><h3>Database Password</h3><div class="node-token-box" id="dbNewPass"></div><div class="modal-actions"><button class="btn-cancel" onclick="closeModal('dbPassModal')">Close</button><button class="btn-save" onclick="copyText(document.getElementById('dbNewPass').textContent)">Copy</button></div></div></div>
<div class="modal-overlay" id="scheduleModal"><div class="modal-box" style="max-width:500px"><h3 id="schedModalTitle">New Schedule</h3><label>Name</label><input type="text" id="schedName" placeholder="Daily restart"><label>Cron Minute</label><input type="text" id="schedMin" placeholder="0" value="0"><label>Cron Hour</label><input type="text" id="schedHour" placeholder="3" value="3"><label>Cron Day of Month</label><input type="text" id="schedDom" placeholder="*" value="*"><label>Cron Day of Week</label><input type="text" id="schedDow" placeholder="*" value="*"><div class="modal-actions"><button class="btn-cancel" onclick="closeModal('scheduleModal')">Cancel</button><button class="btn-save" onclick="confirmSaveSchedule()">Save</button></div></div></div>
<div class="modal-overlay" id="subuserModal"><div class="modal-box"><h3>Add Subuser</h3><label>User Email</label><input type="email" id="subuserEmail" placeholder="user@example.com"><label>Permissions</label><div id="subuserPerms" style="display:grid;grid-template-columns:1fr 1fr;gap:5px;margin-top:5px"><label style="font-size:12px;display:flex;align-items:center;gap:5px;font-weight:400"><input type="checkbox" checked value="console"> Console</label><label style="font-size:12px;display:flex;align-items:center;gap:5px;font-weight:400"><input type="checkbox" checked value="files"> Files</label><label style="font-size:12px;display:flex;align-items:center;gap:5px;font-weight:400"><input type="checkbox" checked value="databases"> Databases</label><label style="font-size:12px;display:flex;align-items:center;gap:5px;font-weight:400"><input type="checkbox" checked value="schedules"> Schedules</label><label style="font-size:12px;display:flex;align-items:center;gap:5px;font-weight:400"><input type="checkbox" checked value="backups"> Backups</label><label style="font-size:12px;display:flex;align-items:center;gap:5px;font-weight:400"><input type="checkbox" value="subusers"> Subusers</label></div><div class="modal-actions"><button class="btn-cancel" onclick="closeModal('subuserModal')">Cancel</button><button class="btn-save" onclick="confirmAddSubuser()">Add</button></div></div></div>
<div class="modal-overlay" id="tokenCreateModal"><div class="modal-box"><h3>New API Token</h3><label>Name</label><input type="text" id="newTokenName" placeholder="My token"><label>Expires At (optional)</label><input type="date" id="newTokenExpires"><div class="modal-actions"><button class="btn-cancel" onclick="closeModal('tokenCreateModal')">Cancel</button><button class="btn-save" onclick="confirmCreateToken()">Create</button></div></div></div>
<div class="modal-overlay" id="rawTokenModal"><div class="modal-box"><h3>API Token Created</h3><p style="font-size:12px;color:var(--text2);margin-bottom:10px">Save this token. It will not be shown again.</p><div class="node-token-box" id="rawTokenDisplay"></div><div class="modal-actions"><button class="btn-cancel" onclick="closeModal('rawTokenModal')">Close</button><button class="btn-save" onclick="copyText(document.getElementById('rawTokenDisplay').textContent)">Copy</button></div></div></div>

<div class="debug-panel" id="debugPanel">
  <div class="debug-panel-header">
    <span><i class="fas fa-bug"></i> Debug Console</span>
    <div style="display:flex;gap:4px">
      <button onclick="debugDumpAll()"><i class="fas fa-rotate"></i> Refresh</button>
      <button onclick="debugClearReqs()"><i class="fas fa-trash"></i> Clear</button>
      <button onclick="document.getElementById('debugPanel').classList.remove('open')"><i class="fas fa-xmark"></i></button>
    </div>
  </div>
  <div class="debug-panel-body" id="debugPanelBody"></div>
</div>

<script src="/panel-ext.js"></script>
<script>
const TOKEN_KEY = 'hostit_token';
const API_BASE = '/api';
let currentServerId = null;
let currentServerData = null;
let currentFilePath = '/';
let userData = null;

async function api(path, opts = {}) {
  const token = localStorage.getItem(TOKEN_KEY);
  const headers = { ...opts.headers };
  if (token) headers['Authorization'] = 'Bearer ' + token;
  if (opts.body) headers['Content-Type'] = 'application/json';
  try {
    const r = await fetch(API_BASE + path, { ...opts, headers });
    const data = await r.json();
    if (!r.ok) {
      const err = data.error || data.message || 'HTTP ' + r.status;
      console.error('API ERROR', path, r.status, err);
      return { success: false, error: err };
    }
    return data || { success: false, error: 'Empty response' };
  } catch (e) {
    console.error('API FAIL', path, e.message);
    return { success: false, error: e.message };
  }
}

function showToast(msg, isError) {
  const existing = document.querySelector('.toast');
  if (existing) existing.remove();
  const t = document.createElement('div');
  t.className = 'toast';
  if (isError) t.style.borderColor = 'rgba(239,68,68,.2)';
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => { t.style.transition = 'opacity .3s'; t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 3000);
}

function closeModal(id) { document.getElementById(id).classList.remove('active'); }
function openModal(id) { document.getElementById(id).classList.add('active'); }

function copyText(t) { navigator.clipboard.writeText(t); showToast('Copied!'); }

// ── Init ──

async function init() {
  const token = localStorage.getItem(TOKEN_KEY);
  if (!token) { window.location.href = '/auth/login'; return; }
  const set = await api('/panel/admin/settings');
  if (set.success && set.settings && set.settings['panel:maintenance'] === '1') { window.location.href = '/maintenance'; return; }
  const me = await api('/auth?action=me');
  if (!me.success) { localStorage.removeItem(TOKEN_KEY); window.location.href = '/auth/login'; return; }
  userData = me.user;
  const u = me.user;
  document.getElementById('userName').textContent = u.first_name + ' ' + u.last_name;
  document.getElementById('userEmail').textContent = u.email;
  document.getElementById('userAvatar').textContent = (u.first_name[0] + u.last_name[0]).toUpperCase();
  document.getElementById('greetUser').textContent = u.first_name + '.';
  document.getElementById('profileFirstName').value = u.first_name;
  document.getElementById('profileLastName').value = u.last_name;
  document.getElementById('profileLang').value = u.language || 'en';
  document.getElementById('profileTz').value = u.timezone || 'UTC';

  if (u.role !== 'admin') {
    const as = document.getElementById('adminSection');
    const ab = document.getElementById('adminNavBtn');
    if (as) as.style.display = 'none';
    if (ab) ab.style.display = 'none';
  }
  handleRoute(true);
  loadProfileTokens();
  const set = await api('/panel/admin/settings');
  if (set.success) {
    const ss = set.settings || {};
    applyPanelBranding(ss['panel:name'] || 'DragoraPanel', ss['panel:logo'] || '');
  }
  loadActiveHooks();
}

// ── Error Page ──

function goToError(code, back) {
  if (!back) back = window.location.pathname;
  window.location.href = '/error/' + (code || '404') + '?back=' + encodeURIComponent(back);
}

// ── Navigation ──

// ── URL Router ──

const ROUTES = {
  dashboard: { view: 'dashboard', load: 'loadDashboard' },
  admin:     { view: 'admin',     load: 'loadAdminOverview' },
  server:    { view: 'server',    load: null },
  nodedetail:{ view: 'nodedetail',load: null },
  profile:   { view: 'profile',   load: 'loadProfileTokens' },
};

function navigate(viewName, sub, param, replace) {
  stopNodePoll();
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  const nav = document.querySelector('[data-view="' + viewName + '"]');
  if (nav) nav.classList.add('active');

  document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
  const v = document.getElementById('view-' + viewName);
  if (v) v.classList.add('active');

  document.getElementById('pageTitle').textContent = nav ? nav.textContent.trim() : document.title;

  // Build and update URL
  let url = '/panel';
  if (viewName === 'dashboard') url += '/dashboard';
  else if (viewName === 'admin') url += sub ? '/admin/' + sub : '/admin';
  else if (viewName === 'server') url += param ? '/servers/' + param : '/servers';
  else if (viewName === 'nodedetail') url += param ? '/nodes/' + param : '/nodes';
  else if (viewName === 'profile') url += '/profile';
  if (replace) history.replaceState({ viewName, sub, param }, '', url);
  else history.pushState({ viewName, sub, param }, '', url);

  // Load data after view switch
  setTimeout(() => {
    if (viewName === 'dashboard') loadDashboard();
    if (viewName === 'profile') loadProfileTokens();

    // Admin sub-tab
    if (viewName === 'admin' && sub) {
      const tab = document.querySelector('.admin-sub-tab[data-atab="' + sub + '"]');
      if (tab) {
        document.querySelectorAll('.admin-sub-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        document.querySelectorAll('.admin-sub-content').forEach(c => c.classList.remove('active'));
        const content = document.getElementById('atab-' + sub);
        if (content) content.classList.add('active');
        const loaders = { overview:'loadAdminOverview', users:'loadAdminUsers', adminservers:'loadAdminServers', nodes:'loadNodes', locations:'loadAdminLocations', allocations:'loadAdminAllocations', settings:'loadAdminSettings', plugins:'loadPlugins', developers:'loadDevelopersDocs', designer:'loadDesigner', webserver:'loadWebServerTab', activity:'loadAdminActivity' };
        if (loaders[sub] && window[loaders[sub]]) window[loaders[sub]]();
      }
    }

    // Server detail — fetch from API if not cached
    if (viewName === 'server' && param) {
      const cached = window.__lastServers && window.__lastServers.find(s => s.id == param);
      if (cached) openServer(cached.name, parseInt(param));
      else {
        api('/servers/' + param).then(r => {
          if (r.success) openServer(r.server.name, parseInt(param));
        });
      }
    }
  }, 30);
}

function handleRoute(replace) {
  const path = window.location.pathname.replace(/^\/panel\/?/, '') || 'dashboard';
  const parts = path.split('/').filter(Boolean);

  if (parts[0] === 'dashboard' || parts[0] === '') {
    navigate('dashboard', null, null, replace);
  } else if (parts[0] === 'admin') {
    navigate('admin', parts[1] || 'overview', null, replace);
  } else if (parts[0] === 'servers') {
    navigate(parts[1] ? 'server' : 'dashboard', null, parts[1] || null, replace);
  } else if (parts[0] === 'nodes') {
    if (parts[1]) { setTimeout(() => openNodeDetail(parseInt(parts[1])), 30); }
    else navigate('admin', 'nodes', null, replace);
  } else if (parts[0] === 'profile') {
    navigate('profile', null, null, replace);
  } else {
    navigate('dashboard', null, null, replace);
  }
}

// Nav clicks with History API
document.querySelectorAll('.nav-item[data-view]').forEach(item => {
  item.addEventListener('click', () => {
    const v = item.dataset.view;
    if (v === 'dashboard') navigate('dashboard');
    else if (v === 'admin') navigate('admin', 'overview');
    else if (v === 'server') navigate('dashboard');
    else if (v === 'profile') navigate('profile');
    // nodedetail is handled separately via openNodeDetail
  });
});

// Handle back/forward — switch view without pushing new state
window.addEventListener('popstate', () => handleRoute(true));

document.querySelectorAll('.server-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.server-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    document.querySelectorAll('.server-tab-content').forEach(c => c.classList.remove('active'));
    const s = document.getElementById('stab-' + tab.dataset.stab);
    if (s) s.classList.add('active');
    if (tab.dataset.stab === 'files' && currentServerId) fileList(currentFilePath);
    if (tab.dataset.stab === 'databases' && currentServerId) loadDatabases();
    if (tab.dataset.stab === 'schedules' && currentServerId) loadSchedules();
    if (tab.dataset.stab === 'backups' && currentServerId) loadBackups();
    if (tab.dataset.stab === 'subusers' && currentServerId) loadSubusers();
    if (tab.dataset.stab === 'activity' && currentServerId) loadServerActivity();
  });
});

document.querySelectorAll('.admin-sub-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.admin-sub-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    document.querySelectorAll('.admin-sub-content').forEach(c => c.classList.remove('active'));
    document.getElementById('atab-' + tab.dataset.atab).classList.add('active');
    if (tab.dataset.atab === 'overview') loadAdminOverview();
    if (tab.dataset.atab === 'users') loadAdminUsers();
    if (tab.dataset.atab === 'adminservers') loadAdminServers();
    if (tab.dataset.atab === 'nodes') loadNodes();
    if (tab.dataset.atab === 'locations') loadAdminLocations();
    if (tab.dataset.atab === 'allocations') { loadAdminAllocations(); loadNodeLocationSelects(); }
    if (tab.dataset.atab === 'settings') loadAdminSettings();
    if (tab.dataset.atab === 'plugins') loadPlugins();
    if (tab.dataset.atab === 'developers') loadDevelopersDocs();
    if (tab.dataset.atab === 'designer') loadDesigner();
    if (tab.dataset.atab === 'activity') loadAdminActivity();
    document.getElementById('nodeDetailNav').style.display = 'none';
    // Update URL
    history.pushState({ viewName: 'admin', sub: tab.dataset.atab }, '', '/panel/admin/' + tab.dataset.atab);
    document.getElementById('pageTitle').textContent = 'Admin - ' + tab.dataset.atab;
  });
});

// ── Dashboard ──

async function loadDashboard() {
  const sv = await api('/servers');
  if (!sv.success) { goToError('503'); return; }
  renderServers(sv.servers);
  renderStats(sv.servers);
}

function renderStats(servers) {
  const online = servers.filter(s => s.status === 'online').length;
  const offline = servers.filter(s => s.status === 'offline').length;
  const ips = servers.filter(s => s.ip_address).length;
  document.getElementById('dashStats').innerHTML = `
    <div class="stat-card anim-pop stagger-1"><div class="stat-icon green"><i class="fas fa-cubes"></i></div><div class="stat-label">Online</div><div class="stat-value">${online}</div><div class="stat-sub">${servers.length} total servers</div></div>
    <div class="stat-card anim-pop stagger-2"><div class="stat-icon accent"><i class="fas fa-power-off"></i></div><div class="stat-label">Offline</div><div class="stat-value">${offline}</div><div class="stat-sub">${offline > 0 ? servers.filter(s=>s.status==='offline').map(s=>s.name).join(', ') : 'All running'}</div></div>
    <div class="stat-card anim-pop stagger-3"><div class="stat-icon amber"><i class="fas fa-network-wired"></i></div><div class="stat-label">IPs Assigned</div><div class="stat-value">${ips} / ${servers.length}</div><div class="stat-sub">${servers.length - ips} unassigned</div></div>
  `;
}

function renderServers(servers) {
  const grid = document.getElementById('serverGrid');
  document.getElementById('serverCount').textContent = servers.length + ' total';
  if (servers.length === 0) {
    grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1">No servers yet.</div>';
    return;
  }
  grid.innerHTML = servers.map((s, i) => {
    const col = { online: '#34d399', offline: '#6b7280', starting: '#fbbf24', stopping: '#fbbf24' }[s.status] || '#888';
    const icon = { online: 'fa-cube', offline: 'fa-power-off', starting: 'fa-spinner', stopping: 'fa-stop' }[s.status] || 'fa-power-off';
    const isOn = s.status === 'online';
    return `<div class="server-card anim-pop stagger-${(i%4)+1}" onclick="openServer('${s.name.replace(/'/g,"\\'")}',${s.id})">
      <div class="server-card-strip" style="background:linear-gradient(135deg,${col},transparent)"></div>
      <div class="server-top"><span class="server-name"><i class="fas ${icon}" style="color:${col};margin-right:8px"></i>${s.name}</span><span class="server-status"><span class="status-dot ${isOn?'online':'offline'}"></span>${s.status}</span></div>
      <div class="server-meta"><span><i class="far fa-clock"></i> ${s.version}</span><span><i class="fas fa-microchip"></i> ${s.memory_mb}MB</span><span><i class="fas fa-map-pin"></i> ${s.ip_address || '-'}</span></div>
      <div class="server-progress"><div class="fill" style="width:${isOn?Math.floor(Math.random()*40)+30:0}%"></div></div>
    </div>`;
  }).join('');
}

// ── Server Detail ──

function openServer(name, id) {
  currentServerId = id;
  currentAgentUrl = null;
  currentAgentToken = null;
  document.getElementById('view-server').classList.add('active');
  document.getElementById('view-dashboard').classList.remove('active');
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.querySelector('[data-view="server"]').classList.add('active');
  document.getElementById('pageTitle').textContent = name;
  document.getElementById('serverName').textContent = name;
  document.getElementById('serverAddr').textContent = 'Loading...';
  loadServerDetail(id);
  // Switch to console tab
  document.querySelectorAll('.server-tab').forEach(t => t.classList.remove('active'));
  document.querySelector('[data-stab="console"]').classList.add('active');
  document.querySelectorAll('.server-tab-content').forEach(c => c.classList.remove('active'));
  document.getElementById('stab-console').classList.add('active');
}

async function loadServerDetail(id) {
  const r = await api('/servers/' + id);
  if (!r.success) { goToError('404', '/panel'); return; }
  const s = r.server;
  // Store agent connection info
  if (s.agent_url && s.agent_token) {
    currentAgentUrl = s.agent_url.replace(/\/+$/, '');
    currentAgentToken = s.agent_token;
  }
  const col = { online: '#34d399', offline: '#6b7280', starting: '#fbbf24', stopping: '#fbbf24' }[s.status] || '#888';
  document.getElementById('serverStatusDot').style.background = col;
  document.getElementById('serverAddr').textContent = (s.ip_address ? s.ip_address + ':' + (s.port || '25565') : 'No address assigned');
  currentServerData = s;
  // Update resource bars
  const memPct = s.memory_used_mb ? Math.round((s.memory_used_mb / s.memory_mb) * 100) : 0;
  document.getElementById('memBar').style.width = memPct + '%';
  document.getElementById('memText').textContent = (s.memory_used_mb || 0) + ' / ' + s.memory_mb + ' MB';
  document.getElementById('cpuBar').style.width = (s.cpu_percent || 0) + '%';
  document.getElementById('cpuText').textContent = (s.cpu_percent || 0) + '%';
}

// ── Agent helper ──
let currentAgentUrl = null;
let currentAgentToken = null;

async function agentApi(path, opts = {}) {
  if (!currentAgentUrl) return { success: false, error: 'No agent connected' };
  const headers = { 'Authorization': 'Bearer ' + (currentAgentToken), ...opts.headers };
  if (opts.body && typeof opts.body === 'object' && !(opts.body instanceof FormData)) {
    headers['Content-Type'] = 'application/json';
    opts.body = JSON.stringify(opts.body);
  }
  try {
    const r = await fetch(currentAgentUrl + '/api' + path, { ...opts, headers });
    return r.json();
  } catch (e) {
    return { success: false, error: e.message };
  }
}

async function sendPowerAction(action) {
  document.getElementById('consoleOutput').innerHTML = '';
  if (currentAgentUrl) {
    const r = await agentApi('/servers/' + currentServerId + '/' + action, { method: 'POST' });
    if (r.success) {
      showToast('Server ' + action + ' command sent.');
      document.getElementById('serverStatusDot').style.background = '#fbbf24';
    } else {
      showToast(r.error || 'Failed', 'error');
    }
  } else {
    const r = await api('/servers/' + currentServerId + '/power', { method: 'POST', body: JSON.stringify({ action }) });
    if (r.success) {
      showToast('Power action queued: ' + action);
      document.getElementById('serverStatusDot').style.background = '#fbbf24';
    } else {
      showToast(r.error || 'Failed', 'error');
    }
  }
}

// ── Console ──

async function sendServerCommand() {
  const input = document.getElementById('consoleInput');
  if (!input.value.trim()) return;
  const cmd = input.value;
  const out = document.getElementById('consoleOutput');
  const time = new Date().toLocaleTimeString('en-GB', {hour12:false});
  out.innerHTML += '<div class="line"><span class="time">[' + time + ']</span><span class="info">[CMD]</span> ' + cmd + '</div>';
  input.value = '';
  out.scrollTop = out.scrollHeight;
  if (currentAgentUrl) {
    await agentApi('/servers/' + currentServerId + '/command', { method: 'POST', body: { command: cmd } });
  } else {
    await api('/servers/' + currentServerId + '/console', { method: 'POST', body: JSON.stringify({ command: cmd }) });
  }
}

// ── File Manager ──

async function fileList(path) {
  currentFilePath = path || '/';
  document.getElementById('filePathBar').innerHTML = '<span onclick="fileList(\'/\')">/</span>' + path.split('/').filter(Boolean).map((p, i, a) => {
    const full = '/' + a.slice(0, i+1).join('/');
    return '<span onclick="fileList(\'' + full + '\')">' + p + '/</span>';
  }).join('');
  if (currentAgentUrl) {
    const r = await agentApi('/servers/' + currentServerId + '/files?path=' + encodeURIComponent(path));
    if (!r.success) { document.getElementById('fileGrid').innerHTML = '<div class="empty-state">Error loading files.</div>'; return; }
    const files = r.files || [];
    if (files.length === 0) { document.getElementById('fileGrid').innerHTML = '<div class="empty-state">Empty directory.</div>'; return; }
    document.getElementById('fileGrid').innerHTML = files.map(f => {
      if (f.isDir) return `<div class="file-item" onclick="fileList('${path.replace(/\/$/,'')}/${f.name}')"><i class="fas fa-folder" style="color:var(--accent2);font-size:16px"></i><span class="fi-name">${f.name}</span><span class="fi-size">-</span><button class="btn-icon" onclick="event.stopPropagation();fileDelete('${path.replace(/\/$/,'')}/${f.name}')"><i class="fas fa-trash"></i></button></div>`;
      const sz = f.size > 1048576 ? (f.size/1048576).toFixed(1)+'MB' : f.size > 1024 ? (f.size/1024).toFixed(1)+'KB' : f.size+'B';
      return `<div class="file-item" onclick="fileEdit('${path.replace(/\/$/,'')}/${f.name}')"><i class="fas fa-file" style="color:var(--text3);font-size:16px"></i><span class="fi-name">${f.name}</span><span class="fi-size">${sz}</span><button class="btn-icon" onclick="event.stopPropagation();fileDelete('${path.replace(/\/$/,'')}/${f.name}')"><i class="fas fa-trash"></i></button></div>`;
    }).join('');
  } else {
    const r = await api('/servers/' + currentServerId + '/files?path=' + encodeURIComponent(path));
    if (!r.success) { document.getElementById('fileGrid').innerHTML = '<div class="empty-state">Error loading files.</div>'; return; }
    if (r.items.length === 0) { document.getElementById('fileGrid').innerHTML = '<div class="empty-state">Empty directory.</div>'; return; }
    document.getElementById('fileGrid').innerHTML = r.items.map(f => {
      if (f.type === 'dir') return `<div class="file-item" onclick="fileList('${path.replace(/\/$/,'')}/${f.name}')"><i class="fas fa-folder" style="color:var(--accent2);font-size:16px"></i><span class="fi-name">${f.name}</span><span class="fi-size">-</span><button class="btn-icon" onclick="event.stopPropagation();fileDelete('${path.replace(/\/$/,'')}/${f.name}')"><i class="fas fa-trash"></i></button></div>`;
      const sz = f.size > 1048576 ? (f.size/1048576).toFixed(1)+'MB' : f.size > 1024 ? (f.size/1024).toFixed(1)+'KB' : f.size+'B';
      return `<div class="file-item" onclick="fileEdit('${path.replace(/\/$/,'')}/${f.name}')"><i class="fas fa-file" style="color:var(--text3);font-size:16px"></i><span class="fi-name">${f.name}</span><span class="fi-size">${sz}</span><button class="btn-icon" onclick="event.stopPropagation();fileDelete('${path.replace(/\/$/,'')}/${f.name}')"><i class="fas fa-trash"></i></button></div>`;
    }).join('');
  }
}

function filePathUp() {
  const p = currentFilePath.replace(/\/$/,'').split('/');
  p.pop();
  fileList(p.join('/') || '/');
}

function showFileCreateDirModal() {
  document.getElementById('fileDirModalTitle').textContent = 'New Folder';
  document.getElementById('fileDirName').value = '';
  document.getElementById('fileDirName').dataset.mode = 'dir';
  openModal('fileDirModal');
}

function showFileCreateModal() {
  document.getElementById('fileDirModalTitle').textContent = 'New File';
  document.getElementById('fileDirName').value = '';
  document.getElementById('fileDirName').dataset.mode = 'file';
  openModal('fileDirModal');
}

async function confirmFileDir() {
  const name = document.getElementById('fileDirName').value.trim();
  const mode = document.getElementById('fileDirName').dataset.mode;
  if (!name) return;
  if (currentAgentUrl) {
    if (mode === 'dir') {
      await agentApi('/servers/' + currentServerId + '/files/dir', { method: 'POST', body: { path: (currentFilePath + '/' + name).replace(/\/+/g, '/') } });
    } else {
      await agentApi('/servers/' + currentServerId + '/files/write', { method: 'PUT', body: { path: (currentFilePath + '/' + name).replace(/\/+/g, '/'), content: '' } });
    }
  } else {
    if (mode === 'dir') {
      await api('/servers/' + currentServerId + '/files/dir', { method: 'POST', body: JSON.stringify({ path: currentFilePath, name }) });
    } else {
      await api('/servers/' + currentServerId + '/files/file', { method: 'POST', body: JSON.stringify({ path: currentFilePath, name }) });
    }
  }
  closeModal('fileDirModal');
  fileList(currentFilePath);
}

async function fileEdit(path) {
  if (currentAgentUrl) {
    const r = await agentApi('/servers/' + currentServerId + '/files/read?path=' + encodeURIComponent(path));
    if (!r.success) return;
    document.getElementById('fileEditTitle').textContent = 'Edit: ' + path.split('/').pop();
    document.getElementById('fileEditContent').value = r.content;
    document.getElementById('fileEditContent').dataset.path = path;
    openModal('fileEditModal');
  } else {
    const r = await api('/servers/' + currentServerId + '/files/read?path=' + encodeURIComponent(path));
    if (!r.success) return;
    document.getElementById('fileEditTitle').textContent = 'Edit: ' + path.split('/').pop();
    document.getElementById('fileEditContent').value = r.content;
    document.getElementById('fileEditContent').dataset.path = path;
    openModal('fileEditModal');
  }
}

async function saveFileEdit() {
  const path = document.getElementById('fileEditContent').dataset.path;
  const content = document.getElementById('fileEditContent').value;
  if (currentAgentUrl) {
    await agentApi('/servers/' + currentServerId + '/files/write', { method: 'PUT', body: { path, content } });
  } else {
    await api('/servers/' + currentServerId + '/files/write', { method: 'PUT', body: JSON.stringify({ path, content }) });
  }
  closeModal('fileEditModal');
  showToast('File saved.');
}

async function fileDelete(path) {
  if (!confirm('Delete ' + path.split('/').pop() + '?')) return;
  if (currentAgentUrl) {
    await agentApi('/servers/' + currentServerId + '/files/delete', { method: 'DELETE', body: { path } });
  } else {
    await api('/servers/' + currentServerId + '/files/delete', { method: 'POST', body: JSON.stringify({ path }) });
  }
  fileList(currentFilePath);
}

async function fileUpload(files) {
  const serverId = currentServerId;
  if (currentAgentUrl) {
    for (const file of files) {
      const fd = new FormData();
      fd.append('file', file);
      fd.append('path', currentFilePath + '/' + file.name);
      try {
        await fetch(currentAgentUrl + '/api/servers/' + serverId + '/files/upload', {
          method: 'POST', headers: { 'Authorization': 'Bearer ' + currentAgentToken }, body: fd
        });
      } catch {}
    }
  } else {
    for (const file of files) {
      const fd = new FormData();
      fd.append('file', file);
      fd.append('path', currentFilePath);
      const token = localStorage.getItem(TOKEN_KEY);
      await fetch(API_BASE + '/servers/' + serverId + '/files/upload', {
        method: 'POST', headers: { 'Authorization': 'Bearer ' + token }, body: fd
      });
    }
  }
  showToast('Upload complete.');
  fileList(currentFilePath);
}

// ── Databases ──

async function loadDatabases() {
  const r = await api('/servers/' + currentServerId + '/databases');
  if (!r.success) return;
  const list = document.getElementById('dbList');
  if (r.databases.length === 0) { list.innerHTML = '<div class="empty-state">No databases configured.</div>'; return; }
  list.innerHTML = '<table class="data-table"><tr><th>Name</th><th>Username</th><th>Host</th><th>Actions</th></tr>' + r.databases.map(d =>
    '<tr><td>' + d.database_name + '</td><td>' + d.username + '</td><td>' + d.remote_host + '</td><td class="actions"><button class="btn-sm" onclick="resetDbPass(' + d.id + ')">Reset PW</button><button class="btn-sm danger" onclick="deleteDb(' + d.id + ')">Delete</button></td></tr>'
  ).join('') + '</table>';
}

function showDbCreateModal() { openModal('dbModal'); document.getElementById('dbPass').value = Math.random().toString(36).slice(2,18); }

async function confirmCreateDb() {
  const r = await api('/servers/' + currentServerId + '/databases', {
    method: 'POST', body: JSON.stringify({ database_name: document.getElementById('dbName').value, password: document.getElementById('dbPass').value, remote_host: document.getElementById('dbHost').value })
  });
  closeModal('dbModal'); if (r.success) { loadDatabases(); showToast('Database created.'); }
}

async function deleteDb(id) {
  if (!confirm('Delete this database?')) return;
  await api('/servers/' + currentServerId + '/databases/' + id, { method: 'DELETE' });
  loadDatabases();
}

async function resetDbPass(id) {
  const r = await api('/servers/' + currentServerId + '/databases/' + id + '/reset-password', { method: 'POST' });
  if (r.success) { document.getElementById('dbNewPass').textContent = r.password; openModal('dbPassModal'); }
}

// ── Schedules ──

async function loadSchedules() {
  const r = await api('/servers/' + currentServerId + '/schedules');
  if (!r.success) return;
  const list = document.getElementById('scheduleList');
  if (r.schedules.length === 0) { list.innerHTML = '<div class="empty-state">No schedules configured.</div>'; return; }
  list.innerHTML = r.schedules.map(s =>
    '<div class="node-card" style="margin-bottom:8px"><div class="node-top"><span class="node-name">' + s.name + '</span><span>' + (s.is_active ? '<span class="status-dot online"></span> Active' : '<span class="status-dot offline"></span> Inactive') + '</span></div><div class="node-meta">' +
    s.cron_minute + ' ' + s.cron_hour + ' ' + s.cron_day_of_month + ' ' + s.cron_day_of_week +
    ' | Tasks: ' + (s.tasks ? s.tasks.length : 0) +
    ' | Last: ' + (s.last_run_at ? new Date(s.last_run_at).toLocaleString() : 'never') +
    '</div><div class="node-actions"><button class="btn-sm danger" onclick="deleteSchedule(' + s.id + ')">Delete</button></div></div>'
  ).join('');
}

function showScheduleCreateModal() {
  document.getElementById('schedModalTitle').textContent = 'New Schedule';
  document.getElementById('schedName').value = '';
  document.getElementById('schedMin').value = '0';
  document.getElementById('schedHour').value = '3';
  document.getElementById('schedDom').value = '*';
  document.getElementById('schedDow').value = '*';
  document.getElementById('scheduleModal').dataset.editId = '';
  openModal('scheduleModal');
}

async function confirmSaveSchedule() {
  const body = { name: document.getElementById('schedName').value, cron_minute: document.getElementById('schedMin').value, cron_hour: document.getElementById('schedHour').value, cron_day_of_month: document.getElementById('schedDom').value, cron_day_of_week: document.getElementById('schedDow').value };
  await api('/servers/' + currentServerId + '/schedules', { method: 'POST', body: JSON.stringify(body) });
  closeModal('scheduleModal'); loadSchedules(); showToast('Schedule created.');
}

async function deleteSchedule(id) {
  if (!confirm('Delete this schedule?')) return;
  await api('/servers/' + currentServerId + '/schedules/' + id, { method: 'DELETE' });
  loadSchedules();
}

// ── Backups ──

async function loadBackups() {
  const r = await api('/servers/' + currentServerId + '/backups');
  if (!r.success) return;
  const list = document.getElementById('backupList');
  if (r.backups.length === 0) { list.innerHTML = '<div class="empty-state">No backups yet.</div>'; return; }
  list.innerHTML = r.backups.map(b =>
    '<div class="node-card" style="margin-bottom:8px"><div class="node-top"><span class="node-name">' + b.name + '</span><span>' + ('<span class="status-dot ' + (b.status === 'completed' ? 'online' : b.status === 'failed' ? 'offline' : 'starting') + '"></span> ' + b.status) + '</span></div><div class="node-meta">Size: ' + (b.size_bytes > 1048576 ? (b.size_bytes/1048576).toFixed(1)+'MB' : b.size_bytes+'B') + ' | Locked: ' + (b.is_locked ? 'Yes' : 'No') + '</div><div class="node-actions">' +
    (b.is_locked ? '<button class="btn-sm" onclick="toggleBackupLock(' + b.id + ')">Unlock</button>' : '<button class="btn-sm" onclick="toggleBackupLock(' + b.id + ')">Lock</button>') +
    '<button class="btn-sm danger" onclick="deleteBackup(' + b.id + ')">Delete</button></div></div>'
  ).join('');
}

async function createBackup() {
  const name = prompt('Backup name:');
  if (!name) return;
  await api('/servers/' + currentServerId + '/backups', { method: 'POST', body: JSON.stringify({ name }) });
  loadBackups(); showToast('Backup created.');
}

async function deleteBackup(id) {
  if (!confirm('Delete this backup?')) return;
  await api('/servers/' + currentServerId + '/backups/' + id, { method: 'DELETE' });
  loadBackups();
}

async function toggleBackupLock(id) {
  await api('/servers/' + currentServerId + '/backups/' + id + '/lock', { method: 'POST' });
  loadBackups();
}

// ── Subusers ──

async function loadSubusers() {
  const r = await api('/servers/' + currentServerId + '/subusers');
  if (!r.success) return;
  const list = document.getElementById('subuserList');
  if (r.subusers.length === 0) { list.innerHTML = '<div class="empty-state">No subusers added.</div>'; return; }
  list.innerHTML = r.subusers.map(su =>
    '<div class="node-card" style="margin-bottom:8px"><div class="node-top"><span class="node-name">' + (su.user ? su.user.email : 'Unknown') + '</span></div><div class="node-meta">Permissions: ' + (JSON.parse(su.permissions) || []).join(', ') + '</div><div class="node-actions"><button class="btn-sm danger" onclick="deleteSubuser(' + su.id + ')">Remove</button></div></div>'
  ).join('');
}

function showSubuserModal() { openModal('subuserModal'); }

async function confirmAddSubuser() {
  const perms = Array.from(document.querySelectorAll('#subuserPerms input:checked')).map(c => c.value);
  const r = await api('/servers/' + currentServerId + '/subusers', {
    method: 'POST', body: JSON.stringify({ email: document.getElementById('subuserEmail').value, permissions: perms })
  });
  closeModal('subuserModal'); if (r.success) { loadSubusers(); showToast('Subuser added.'); } else showToast(r.error || 'Failed.', true);
}

async function deleteSubuser(id) {
  if (!confirm('Remove this subuser?')) return;
  await api('/servers/' + currentServerId + '/subusers/' + id, { method: 'DELETE' });
  loadSubusers();
}

// ── Server Activity ──

async function loadServerActivity() {
  const r = await api('/servers/' + currentServerId + '/activity');
  if (!r.success) return;
  const list = document.getElementById('serverActivityList');
  if (r.logs.length === 0) { list.innerHTML = '<div class="empty-state">No activity yet.</div>'; return; }
  list.innerHTML = '<table class="data-table"><tr><th>Action</th><th>User</th><th>Time</th></tr>' + r.logs.map(l =>
    '<tr><td>' + l.action + '</td><td>' + (l.user ? l.user.email : '-') + '</td><td>' + (l.created_at ? new Date(l.created_at).toLocaleString() : '-') + '</td></tr>'
  ).join('') + '</table>';
}

// ── Profile ──

async function saveProfile() {
  const r = await api('/user/profile', {
    method: 'PUT', body: JSON.stringify({ first_name: document.getElementById('profileFirstName').value, last_name: document.getElementById('profileLastName').value, language: document.getElementById('profileLang').value, timezone: document.getElementById('profileTz').value })
  });
  if (r.success) { showToast('Profile saved.'); userData = r.user; }
}

function showProfile() { document.querySelector('[data-view="profile"]').click(); }

async function loadProfileTokens() {
  const r = await api('/user/tokens');
  if (!r.success) return;
  const list = document.getElementById('apiTokenList');
  if (r.tokens.length === 0) { list.innerHTML = '<div class="empty-state" style="padding:16px">No API tokens.</div>'; return; }
  list.innerHTML = '<table class="data-table"><tr><th>Name</th><th>Last Used</th><th>Expires</th><th></th></tr>' + r.tokens.map(t =>
    '<tr><td>' + t.name + '</td><td>' + (t.last_used_at ? new Date(t.last_used_at).toLocaleDateString() : 'Never') + '</td><td>' + (t.expires_at ? new Date(t.expires_at).toLocaleDateString() : 'Never') + '</td><td><button class="btn-sm danger" onclick="deleteToken(' + t.id + ')">Revoke</button></td></tr>'
  ).join('') + '</table>';
}

function showTokenCreateModal() { openModal('tokenCreateModal'); }

async function confirmCreateToken() {
  const r = await api('/user/tokens', {
    method: 'POST', body: JSON.stringify({ name: document.getElementById('newTokenName').value, expires_at: document.getElementById('newTokenExpires').value || null })
  });
  if (r.success) {
    closeModal('tokenCreateModal');
    document.getElementById('rawTokenDisplay').textContent = r.raw_token;
    openModal('rawTokenModal');
    loadProfileTokens();
  }
}

async function deleteToken(id) {
  if (!confirm('Revoke this token?')) return;
  await api('/user/tokens/' + id, { method: 'DELETE' });
  loadProfileTokens();
}

// ── Admin: Overview ──

async function loadAdminOverview() {
  const r = await api('/panel/admin/stats');
  if (!r.success) return;
  const s = r.stats;
  document.getElementById('adminStats').innerHTML = `
    <div class="stat-card anim-pop stagger-1"><div class="stat-icon blue"><i class="fas fa-users"></i></div><div class="stat-label">Users</div><div class="stat-value">${s.users}</div></div>
    <div class="stat-card anim-pop stagger-2"><div class="stat-icon green"><i class="fas fa-cubes"></i></div><div class="stat-label">Servers</div><div class="stat-value">${s.servers}</div></div>
    <div class="stat-card anim-pop stagger-3"><div class="stat-icon accent"><i class="fas fa-server"></i></div><div class="stat-label">Nodes</div><div class="stat-value">${s.nodes}</div><div class="stat-sub">${s.nodes_online} online</div></div>
    <div class="stat-card anim-pop stagger-4"><div class="stat-icon amber"><i class="fas fa-location-dot"></i></div><div class="stat-label">Locations</div><div class="stat-value">${s.locations}</div></div>
    <div class="stat-card anim-pop stagger-5"><div class="stat-icon green"><i class="fas fa-cloud-arrow-up"></i></div><div class="stat-label">Backups</div><div class="stat-value">${s.backups}</div></div>
  `;
  const act = await api('/panel/admin/activity');
  if (act.success) {
    document.getElementById('adminRecentActivity').innerHTML = act.logs.length === 0
      ? '<div class="empty-state" style="padding:16px">No activity yet.</div>'
      : '<table class="data-table"><tr><th>Action</th><th>User</th><th>Time</th></tr>' + act.logs.slice(0,10).map(l =>
          '<tr><td>' + l.action + '</td><td>' + (l.user ? l.user.email : '-') + '</td><td>' + (l.created_at ? new Date(l.created_at).toLocaleString() : '-') + '</td></tr>'
        ).join('') + '</table>';
  }
}

// ── Admin: Users ──

async function loadAdminUsers() {
  const r = await api('/panel/admin/users');
  if (!r.success) return;
  const list = document.getElementById('userAdminList');
  list.innerHTML = '<table class="data-table"><tr><th>ID</th><th>Email</th><th>Name</th><th>Role</th><th>Servers</th><th>Actions</th></tr>' + r.users.map(u =>
    '<tr><td>' + u.id + '</td><td>' + u.email + '</td><td>' + u.first_name + ' ' + u.last_name + '</td><td>' + u.role + '</td><td>' + (u.servers_count || 0) + '</td><td class="actions"><button class="btn-sm" onclick="editUser(' + u.id + ',\'' + u.email + '\',\'' + u.first_name + '\',\'' + u.last_name + '\',\'' + u.role + '\')">Edit</button><button class="btn-sm danger" onclick="deleteUser(' + u.id + ')">Delete</button></td></tr>'
  ).join('') + '</table>';
}

function showUserCreateModal() {
  document.getElementById('userModalBody').innerHTML = '<h3>Create User</h3><label>Email</label><input type="email" id="userEmailInput"><label>First Name</label><input type="text" id="userFirst"><label>Last Name</label><input type="text" id="userLast"><label>Password</label><input type="password" id="userPass" minlength="6"><label>Role</label><select id="userRole"><option value="member">Member</option><option value="admin">Admin</option></select><div class="modal-actions"><button class="btn-cancel" onclick="closeModal(\'userModal\')">Cancel</button><button class="btn-save" onclick="confirmCreateUser()">Create</button></div>';
  openModal('userModal');
}

async function confirmCreateUser() {
  const r = await api('/panel/admin/users', {
    method: 'POST', body: JSON.stringify({
      email: document.getElementById('userEmailInput').value,
      first_name: document.getElementById('userFirst').value,
      last_name: document.getElementById('userLast').value,
      password: document.getElementById('userPass').value,
      role: document.getElementById('userRole').value
    })
  });
  closeModal('userModal'); if (r.success) { loadAdminUsers(); showToast('User created.'); }
}

function editUser(id, email, first, last, role) {
  document.getElementById('userModalBody').innerHTML = '<h3>Edit User</h3><label>Email</label><input type="email" id="userEmailInput" value="' + email + '"><label>First Name</label><input type="text" id="userFirst" value="' + first + '"><label>Last Name</label><input type="text" id="userLast" value="' + last + '"><label>New Password (leave blank to keep)</label><input type="password" id="userPass"><label>Role</label><select id="userRole"><option value="member"' + (role === 'member' ? ' selected' : '') + '>Member</option><option value="admin"' + (role === 'admin' ? ' selected' : '') + '>Admin</option></select><div class="modal-actions"><button class="btn-cancel" onclick="closeModal(\'userModal\')">Cancel</button><button class="btn-save" onclick="confirmEditUser(' + id + ')">Save</button></div>';
  openModal('userModal');
}

async function confirmEditUser(id) {
  const body = { email: document.getElementById('userEmailInput').value, first_name: document.getElementById('userFirst').value, last_name: document.getElementById('userLast').value, role: document.getElementById('userRole').value };
  const pw = document.getElementById('userPass').value;
  if (pw) body.password = pw;
  const r = await api('/panel/admin/users/' + id, { method: 'PUT', body: JSON.stringify(body) });
  closeModal('userModal'); if (r.success) { loadAdminUsers(); showToast('User updated.'); }
}

async function deleteUser(id) {
  if (!confirm('Delete user ' + id + '? This will delete all their servers.')) return;
  const r = await api('/panel/admin/users/' + id, { method: 'DELETE' });
  if (r.success) { loadAdminUsers(); showToast('User deleted.'); }
}

// ── Admin: Nodes ──

let lastToken = '';
let nodeEditId = null;

function showCreateNodeModal() {
  nodeEditId = null;
  document.getElementById('nodeModalTitle').textContent = 'Add Node Agent';
  document.getElementById('nodeName').value = '';
  document.getElementById('nodeFqdn').value = '';
  document.getElementById('nodeIp').value = '';
  document.getElementById('nodePort').value = '';
  document.getElementById('nodeMemory').value = '4096';
  document.getElementById('nodeStorage').value = '51200';
  document.querySelector('#nodeModal .btn-save').textContent = 'Create';
  document.querySelector('#nodeModal .btn-save').onclick = createNode;
  api('/panel/admin/locations').then(r => {
    const sel = document.getElementById('nodeLocation');
    sel.innerHTML = '<option value="">Select location</option>' + (r.locations?.map(l => `<option value="${l.id}">${l.short_code} - ${l.long_name}</option>`).join('') || '');
  });
  openModal('nodeModal');
}

async function createNode() {
  const body = {
    name: document.getElementById('nodeName').value,
    fqdn: document.getElementById('nodeFqdn').value || null,
    ip_address: document.getElementById('nodeIp').value || null,
    port: document.getElementById('nodePort').value ? parseInt(document.getElementById('nodePort').value) : null,
    location_id: parseInt(document.getElementById('nodeLocation').value) || null,
    memory_mb: parseInt(document.getElementById('nodeMemory').value) || null,
    storage_mb: parseInt(document.getElementById('nodeStorage').value) || null,
  };
  const r = await api('/panel/nodes', { method: 'POST', body: JSON.stringify(body) });
  if (r.success) { closeModal('nodeModal'); lastToken = r.raw_token; document.getElementById('tokenDisplay').textContent = lastToken; openModal('tokenModal'); loadNodes(); } else showToast(r.error || 'Failed.', true);
}

async function editNode(id) {
  const r = await api('/panel/nodes');
  if (!r.success) return;
  const node = r.nodes.find(n => n.id === id);
  if (!node) return;
  nodeEditId = id;
  document.getElementById('nodeModalTitle').textContent = 'Edit Node';
  document.getElementById('nodeName').value = node.name;
  document.getElementById('nodeFqdn').value = node.fqdn || '';
  document.getElementById('nodeIp').value = node.ip_address || '';
  document.getElementById('nodePort').value = node.port || '';
  document.getElementById('nodeMemory').value = node.memory_mb || '';
  document.getElementById('nodeStorage').value = node.storage_mb || '';
  const locRes = await api('/panel/admin/locations');
  const sel = document.getElementById('nodeLocation');
  sel.innerHTML = '<option value="">Select location</option>' + (locRes.locations?.map(l => `<option value="${l.id}" ${l.id === node.location_id ? 'selected' : ''}>${l.short_code} - ${l.long_name}</option>`).join('') || '');
  document.querySelector('#nodeModal .btn-save').textContent = 'Save';
  document.querySelector('#nodeModal .btn-save').onclick = saveNodeEdit;
  openModal('nodeModal');
}

async function saveNodeEdit() {
  const body = {
    name: document.getElementById('nodeName').value,
    fqdn: document.getElementById('nodeFqdn').value || null,
    ip_address: document.getElementById('nodeIp').value || null,
    port: document.getElementById('nodePort').value ? parseInt(document.getElementById('nodePort').value) : null,
    location_id: parseInt(document.getElementById('nodeLocation').value) || null,
    memory_mb: parseInt(document.getElementById('nodeMemory').value) || null,
    storage_mb: parseInt(document.getElementById('nodeStorage').value) || null,
  };
  const r = await api('/panel/nodes/' + nodeEditId, { method: 'PUT', body: JSON.stringify(body) });
  if (r.success) { closeModal('nodeModal'); loadNodes(); showToast('Node updated.'); } else showToast(r.error || 'Failed.', true);
}

function copyToken() { copyText(lastToken); }

async function deleteNode(id) {
  if (!confirm('Delete this node?')) return;
  const r = await api('/panel/nodes/' + id, { method: 'DELETE' });
  if (r.success) { loadNodes(); showToast('Node deleted.'); } else showToast(r.error || 'Failed.', true);
}

async function regenerateNode(id) {
  const r = await api('/panel/nodes/' + id + '/regenerate', { method: 'POST' });
  if (r.success) { lastToken = r.raw_token; document.getElementById('tokenDisplay').textContent = lastToken; openModal('tokenModal'); }
}

async function loadNodes() {
  const list = document.getElementById('nodeList');
  const r = await api('/panel/nodes');
  if (!r.success) { list.innerHTML = '<div class="empty-state">Failed to load.</div>'; return; }
  if (r.nodes.length === 0) { list.innerHTML = '<div class="empty-state">No nodes registered.</div>'; return; }
  list.innerHTML = r.nodes.map(n => {
    const on = n.status === 'online';
    return `<div class="node-card"><div class="node-top"><span class="node-name"><span class="status-dot ${on?'online':'offline'}"></span> ${n.name}</span><span style="font-size:11px;color:var(--text2)">${on ? 'ONLINE' : 'OFFLINE'}</span></div><div class="node-meta"><span>FQDN: ${n.fqdn || '-'}</span><span>IP: ${n.ip_address || '-'}</span><span>Port: ${n.port || '-'}</span><span>Mem: ${n.memory_mb || '?'}MB</span><span>Storage: ${n.storage_mb || '?'}MB</span><span>Last: ${n.last_seen_at ? new Date(n.last_seen_at).toLocaleString() : 'never'}</span><span>Servers: ${n.servers_count || 0}</span></div><div class="node-actions"><button onclick="openNodeDetail(${n.id})">Manage</button><button onclick="editNode(${n.id})">Edit</button><button onclick="regenerateNode(${n.id})">Regenerate</button><button onclick="deleteNode(${n.id})" style="border-color:rgba(239,68,68,.15);color:#ef4444">Delete</button></div></div>`;
  }).join('');
  startNodePoll();
}

// ── Node Detail ──

let currentNdNode = null;
let currentNdAgentUrl = null;
let ndWs = null;
let nodePollTimer = null;
function stopNodePoll() { if (nodePollTimer) { clearInterval(nodePollTimer); nodePollTimer = null; } }
function startNodePoll() {
  stopNodePoll();
  nodePollTimer = setInterval(async () => {
    const activeView = document.querySelector('.view.active')?.id;
    if (activeView !== 'view-nodedetail' && activeView !== 'view-admin') { stopNodePoll(); return; }
    const r = await api('/panel/nodes');
    if (!r.success) return;
    // Update node list if admin nodes tab is active
    if (activeView === 'view-admin') {
      const nodesTab = document.querySelector('.admin-sub-tab[data-atab="nodes"]');
      if (nodesTab && nodesTab.classList.contains('active')) {
        const list = document.getElementById('nodeList');
        if (list) {
          list.innerHTML = r.nodes.map(n => {
            const on = n.status === 'online';
            return `<div class="node-card"><div class="node-top"><span class="node-name"><span class="status-dot ${on?'online':'offline'}"></span> ${n.name}</span><span style="font-size:11px;color:var(--text2)">${on ? 'ONLINE' : 'OFFLINE'}</span></div><div class="node-meta"><span>FQDN: ${n.fqdn || '-'}</span><span>IP: ${n.ip_address || '-'}</span><span>Port: ${n.port || '-'}</span><span>Mem: ${n.memory_mb || '?'}MB</span><span>Storage: ${n.storage_mb || '?'}MB</span><span>Last: ${n.last_seen_at ? new Date(n.last_seen_at).toLocaleString() : 'never'}</span><span>Servers: ${n.servers_count || 0}</span></div><div class="node-actions"><button onclick="openNodeDetail(${n.id})">Manage</button><button onclick="editNode(${n.id})">Edit</button><button onclick="regenerateNode(${n.id})">Regenerate</button><button onclick="deleteNode(${n.id})" style="border-color:rgba(239,68,68,.15);color:#ef4444">Delete</button></div></div>`;
          }).join('');
        }
      }
    }
    // Update node detail if nodedetail view is active
    if (activeView === 'view-nodedetail' && currentNdNode) {
      const updated = r.nodes.find(n => n.id === currentNdNode.id);
      if (!updated) return;
      const prevStatus = currentNdNode.status;
      currentNdNode = updated;
      const on = updated.status === 'online';
      document.getElementById('ndStatusDot').style.background = on ? '#34d399' : '#6b7280';
      if (prevStatus !== updated.status) {
        ndLoadOverview();
        ndLoadServers();
      } else {
        // Still update last-seen and overview quietly
        document.getElementById('ndOvStatus').textContent = on ? 'Online' : 'Offline';
        document.getElementById('ndOvStatus').style.color = on ? 'var(--green)' : '#6b7280';
        document.getElementById('ndOvLastSeen').textContent = updated.last_seen_at ? new Date(updated.last_seen_at).toLocaleString() : 'Never';
      }
    }
  }, 10000);
}

function showView(name) {
  document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
  document.getElementById('view-' + name).classList.add('active');
  document.querySelectorAll('.nav-item[data-view]').forEach(i => i.classList.remove('active'));
  const nav = document.querySelector('[data-view="' + name + '"]');
  if (nav) nav.classList.add('active');
}

async function openNodeDetail(nodeId) {
  // Navigate to nodedetail view (replace=true avoids extra history entry when called from handleRoute)
  navigate('nodedetail', null, nodeId, true);

  const r = await api('/panel/nodes');
  if (!r.success) return;
  const node = r.nodes.find(n => n.id === nodeId);
  if (!node) return;
  currentNdNode = node;

  // Build agent URL
  const host = node.fqdn || node.ip_address || 'localhost';
  const port = node.port || 8055;
  currentNdAgentUrl = `http://${host}:${port}`;

  // Update nav
  document.getElementById('nodeDetailNavLabel').textContent = node.name;
  document.getElementById('nodeDetailNav').style.display = '';

  // Fill header
  document.getElementById('ndName').textContent = node.name;
  document.getElementById('ndAddr').textContent = `${host}:${port}`;
  const on = node.status === 'online';
  document.getElementById('ndStatusDot').style.background = on ? '#34d399' : '#6b7280';

  // Load overview
  ndLoadOverview();
  ndLoadServers();
  // Connect WebSocket for live logs
  ndConnectWs();

  // Save for page title
  document.getElementById('pageTitle').textContent = 'Node: ' + node.name;

  startNodePoll();
}

// ── Node Detail Tab Switching ──
document.querySelectorAll('#ndTabs .server-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('#ndTabs .server-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    document.querySelectorAll('[id^="ndtab-"]').forEach(c => c.classList.remove('active'));
    document.getElementById('ndtab-' + tab.dataset.ndtab).classList.add('active');
  });
});

async function ndLoadOverview() {
  const node = currentNdNode;
  const on = node.status === 'online';
  document.getElementById('ndOvStatus').textContent = on ? 'Online' : 'Offline';
  document.getElementById('ndOvStatus').style.color = on ? 'var(--green)' : '#6b7280';
  document.getElementById('ndOvLocation').textContent = node.location?.short_code || '-';
  document.getElementById('ndOvConn').textContent = (node.fqdn || node.ip_address || '-') + ':' + (node.port || '-');
  document.getElementById('ndOvLastSeen').textContent = node.last_seen_at ? new Date(node.last_seen_at).toLocaleString() : 'Never';

  // Fetch system info from agent if online
  if (on) {
    ndFetchSystemInfo();
  }
}

async function ndFetchSystemInfo() {
  try {
    const r = await fetch(currentNdAgentUrl + '/api/system', {
      headers: { 'Authorization': 'Bearer ' + localStorage.getItem('panel_token') }
    });
    const data = await r.json();
    if (data.success) {
      const s = data.system;
      const cpuPct = s.cpu.load;
      const memUsed = s.memory.used;
      const memTotal = s.memory.total;
      const memPct = memTotal > 0 ? Math.round(memUsed / memTotal * 100) : 0;
      const diskUsed = s.disk.used;
      const diskTotal = s.disk.total;
      const diskPct = diskTotal > 0 ? Math.round(diskUsed / diskTotal * 100) : 0;
      const cpuCores = s.cpu.cores;

      document.getElementById('ndCpuBar').style.width = Math.min(cpuPct, 100) + '%';
      document.getElementById('ndCpuText').textContent = cpuPct + '%';
      document.getElementById('ndCpuDesc').textContent = 'of ' + cpuCores + ' cores';

      document.getElementById('ndMemBar').style.width = Math.min(memPct, 100) + '%';
      document.getElementById('ndMemText').textContent = memUsed + ' / ' + memTotal + ' MB';
      document.getElementById('ndMemDesc').textContent = memPct + '%';

      document.getElementById('ndDiskBar').style.width = Math.min(diskPct, 100) + '%';
      document.getElementById('ndDiskText').textContent = diskUsed + ' / ' + diskTotal + ' MB';
      document.getElementById('ndDiskDesc').textContent = diskPct + '%';
    }
  } catch (err) {
    // Agent not reachable
  }
}

async function ndLoadServers() {
  const node = currentNdNode;
  const list = document.getElementById('ndServerList');
  let servers = [];
  try {
    const r = await fetch(currentNdAgentUrl + '/api/servers', {
      headers: { 'Authorization': 'Bearer ' + localStorage.getItem('panel_token') }
    });
    const data = await r.json();
    if (data.success) servers = data.servers;
  } catch { goToError('505'); return; }

  const running = servers.filter(s => s.process_running).length;
  document.getElementById('ndOvServers').textContent = servers.length;
  document.getElementById('ndOvServersSub').textContent = running + ' running';

  if (servers.length === 0) {
    list.innerHTML = '<div class="empty-state">No servers assigned to this node.</div>';
    return;
  }

  list.innerHTML = '<table class="data-table"><tr><th>ID</th><th>Name</th><th>Status</th><th>Memory</th><th>Port</th><th>Actions</th></tr>' +
    servers.map(s => {
      const col = {online:'#34d399',offline:'#6b7280',starting:'#fbbf24',stopping:'#fbbf24',running:'#34d399'}[s.status]||'#888';
      const sStatus = s.process_running ? 'running' : s.status;
      const sCol = s.process_running ? '#34d399' : col;
      return `<tr>
        <td>${s.id}</td>
        <td><strong>${s.name}</strong></td>
        <td><span class="status-dot" style="background:${sCol};display:inline-block;width:7px;height:7px;border-radius:50%;margin-right:5px"></span>${sStatus}</td>
        <td>${s.memory_mb}MB</td>
        <td>${s.port || '-'}</td>
        <td class="actions">
          <button class="btn-sm primary" onclick="ndStartServer(${s.id})">Start</button>
          <button class="btn-sm danger" onclick="ndStopServer(${s.id})">Stop</button>
        </td>
      </tr>`;
    }).join('') + '</table>';

  // Populate file server select
  const sel = document.getElementById('ndFileServer');
  sel.innerHTML = '<option value="">Select a server...</option>' + servers.map(s => `<option value="${s.id}">${s.name} (ID: ${s.id})</option>`).join('');
}

async function ndStartServer(id) {
  try {
    const r = await fetch(currentNdAgentUrl + '/api/servers/' + id + '/start', {
      method: 'POST',
      headers: { 'Authorization': 'Bearer ' + localStorage.getItem('panel_token') }
    });
    const data = await r.json();
    showToast(data.success ? 'Server starting...' : (data.error || 'Failed'));
    if (data.success) setTimeout(ndLoadServers, 2000);
  } catch (err) {
    goToError('505');
  }
}

async function ndStopServer(id) {
  try {
    const r = await fetch(currentNdAgentUrl + '/api/servers/' + id + '/stop', {
      method: 'POST',
      headers: { 'Authorization': 'Bearer ' + localStorage.getItem('panel_token') }
    });
    const data = await r.json();
    showToast(data.success ? 'Server stopping...' : (data.error || 'Failed'));
    if (data.success) setTimeout(ndLoadServers, 2000);
  } catch (err) {
    goToError('505');
  }
}

async function ndRefresh() {
  if (!currentNdNode) return;
  ndLoadOverview();
  ndLoadServers();
  showToast('Refreshed');
}

async function ndShowToken() {
  if (!currentNdNode) return;
  const r = await api('/panel/nodes/' + currentNdNode.id + '/regenerate', { method: 'POST' });
  if (r.success) {
    document.getElementById('tokenDisplay').textContent = r.raw_token;
    openModal('tokenModal');
    ndRefresh();
  }
}

// ── Node Detail: WebSocket Live Logs ──

function ndConnectWs() {
  if (ndWs) { ndWs.close(); ndWs = null; }
  const node = currentNdNode;
  const host = node.fqdn || node.ip_address || 'localhost';
  const port = node.port || 8055;
  const token = localStorage.getItem('panel_token');
  if (!token) return;
  const url = `ws://${host}:${port}?token=${token}`;
  try {
    ndWs = new WebSocket(url);
    ndWs.onopen = () => {
      document.getElementById('ndConsoleOutput').innerHTML = '<div class="empty-state" style="border:none">Connected. Subscribe to a server above.</div>';
      // Auto-subscribe to first server
      const sel = document.getElementById('ndFileServer');
      if (sel.options.length > 1) {
        const firstId = sel.options[1].value;
        ndWs.send(JSON.stringify({ type: 'console:subscribe', serverId: parseInt(firstId) }));
      }
    };
    ndWs.onmessage = (e) => {
      const msg = JSON.parse(e.data);
      if (msg.type === 'console:data' && msg.lines) {
        const out = document.getElementById('ndConsoleOutput');
        for (const line of msg.lines) {
          const div = document.createElement('div');
          div.className = 'line';
          div.innerHTML = '<span class="time">' + new Date().toLocaleTimeString() + '</span>' + escapeHtml(line);
          out.appendChild(div);
        }
        out.scrollTop = out.scrollHeight;
      }
    };
    ndWs.onclose = () => {
      if (document.getElementById('ndConsoleOutput')) {
        document.getElementById('ndConsoleOutput').innerHTML += '<div class="line"><span class="time"></span><span class="warn">[Disconnected]</span></div>';
      }
    };
    ndWs.onerror = () => {
      document.getElementById('ndConsoleOutput').innerHTML = '<div class="empty-state" style="border:none;color:#ef4444">Connection failed. Agent may be offline. <a href="javascript:goToError(505)" style="color:var(--accent)">View details</a></div>';
    };
  } catch (err) {
    document.getElementById('ndConsoleOutput').innerHTML = '<div class="empty-state" style="border:none;color:#ef4444">WebSocket not supported.</div>';
  }
}

// ── Node Detail: File Browser ──

let ndCurrentPath = '/';

async function ndFileList(dir) {
  if (dir !== undefined) ndCurrentPath = dir;
  const serverId = document.getElementById('ndFileServer').value;
  if (!serverId) { document.getElementById('ndFileGrid').innerHTML = '<div class="empty-state">Select a server.</div>'; return; }
  try {
    const r = await fetch(currentNdAgentUrl + '/api/servers/' + serverId + '/files?path=' + encodeURIComponent(ndCurrentPath), {
      headers: { 'Authorization': 'Bearer ' + localStorage.getItem('panel_token') }
    });
    const data = await r.json();
    if (!data.success) { document.getElementById('ndFileGrid').innerHTML = '<div class="empty-state">Failed.</div>'; return; }
    const files = data.files;
    // Update path bar
    const bar = document.getElementById('ndFilePathBar');
    const parts = ndCurrentPath.split('/').filter(Boolean);
    bar.innerHTML = '<span onclick="ndFileList(\'/\')">/</span>' + parts.map((p, i) => {
      const path = '/' + parts.slice(0, i + 1).join('/');
      return '<span onclick="ndFileList(\'' + path + '\')">' + p + '</span>';
    }).join(' / ');

    if (files.length === 0) { document.getElementById('ndFileGrid').innerHTML = '<div class="empty-state">Empty directory.</div>'; return; }
    document.getElementById('ndFileGrid').innerHTML = files.map(f => {
      const icon = f.isDir ? '<i class="fas fa-folder" style="color:var(--accent2)"></i>' : '<i class="fas fa-file" style="color:var(--text3)"></i>';
      const size = f.isDir ? '' : '<span class="fi-size">' + (f.size > 1048576 ? (f.size/1048576).toFixed(1)+'MB' : f.size > 1024 ? (f.size/1024).toFixed(1)+'KB' : f.size+'B') + '</span>';
      const onclick = f.isDir ? `onclick="ndFileList('${ndCurrentPath}/${f.name}'.replace(/\\/+/g,'/'))"` : `onclick="ndViewFile('${ndCurrentPath}/${f.name}')"`;
      return `<div class="file-item" ${onclick}>${icon}<span class="fi-name">${f.name}</span>${size}</div>`;
    }).join('');
  } catch (err) {
    document.getElementById('ndFileGrid').innerHTML = '<div class="empty-state">Agent unreachable.</div>';
  }
}

function ndFileUp() {
  const parent = ndCurrentPath.split('/').slice(0, -1).join('/') || '/';
  ndFileList(parent);
}

async function ndViewFile(filePath) {
  const serverId = document.getElementById('ndFileServer').value;
  if (!serverId) return;
  try {
    const r = await fetch(currentNdAgentUrl + '/api/servers/' + serverId + '/files/read?path=' + encodeURIComponent(filePath), {
      headers: { 'Authorization': 'Bearer ' + localStorage.getItem('panel_token') }
    });
    const data = await r.json();
    if (data.success) {
      document.getElementById('fileEditContent').value = data.content;
      document.getElementById('fileEditTitle').textContent = filePath.split('/').pop();
      openModal('fileEditModal');
      // Override save to use node agent
      document.querySelector('#fileEditModal .btn-save').onclick = async () => {
        const content = document.getElementById('fileEditContent').value;
        const wr = await fetch(currentNdAgentUrl + '/api/servers/' + serverId + '/files/write', {
          method: 'PUT',
          headers: { 'Authorization': 'Bearer ' + localStorage.getItem('panel_token'), 'Content-Type': 'application/json' },
          body: JSON.stringify({ path: filePath, content })
        });
        const wd = await wr.json();
        if (wd.success) { closeModal('fileEditModal'); showToast('File saved.'); ndFileList(); }
        else showToast(wd.error || 'Failed', true);
      };
    }
  } catch (err) {
    showToast('Failed to read file', true);
  }
}

async function ndNewFolder() {
  const serverId = document.getElementById('ndFileServer').value;
  if (!serverId) return;
  document.getElementById('fileDirName').value = '';
  document.getElementById('fileDirModalTitle').textContent = 'New Folder in ' + ndCurrentPath;
  openModal('fileDirModal');
  document.querySelector('#fileDirModal .btn-save').onclick = async () => {
    const name = document.getElementById('fileDirName').value;
    if (!name) return;
    const path = (ndCurrentPath + '/' + name).replace(/\/+/g, '/');
    const r = await fetch(currentNdAgentUrl + '/api/servers/' + serverId + '/files/dir', {
      method: 'POST',
      headers: { 'Authorization': 'Bearer ' + localStorage.getItem('panel_token'), 'Content-Type': 'application/json' },
      body: JSON.stringify({ path })
    });
    const d = await r.json();
    if (d.success) { closeModal('fileDirModal'); ndFileList(); showToast('Folder created.'); }
    else showToast(d.error || 'Failed', true);
  };
}

async function ndFileUpload(files) {
  const serverId = document.getElementById('ndFileServer').value;
  if (!serverId) return;
  for (const file of files) {
    const fd = new FormData();
    fd.append('file', file);
    fd.append('path', ndCurrentPath + '/' + file.name);
    try {
      const r = await fetch(currentNdAgentUrl + '/api/servers/' + serverId + '/files/upload', {
        method: 'POST',
        headers: { 'Authorization': 'Bearer ' + localStorage.getItem('panel_token') },
        body: fd
      });
      const d = await r.json();
      if (d.success) showToast('Uploaded: ' + file.name);
      else showToast(d.error || 'Failed', true);
    } catch (err) { showToast('Upload failed', true); }
  }
  ndFileList();
}

document.getElementById('ndFileServer').addEventListener('change', () => {
  ndCurrentPath = '/';
  ndFileList('/');
});

// ── Admin: Servers ──

async function loadAdminServers() {
  const list = document.getElementById('adminServerList');
  const r = await api('/servers');
  if (!r.success) { list.innerHTML = '<div class="empty-state">Failed to load.</div>'; return; }
  if (r.servers.length === 0) { list.innerHTML = '<div class="empty-state">No servers created.</div>'; return; }
  list.innerHTML = '<table class="data-table"><tr><th>Name</th><th>User</th><th>Status</th><th>Memory</th><th>Port</th><th>Actions</th></tr>' +
    r.servers.map(s => {
      const col = {online:'#34d399',offline:'#6b7280',starting:'#fbbf24',stopping:'#fbbf24'}[s.status]||'#888';
      return `<tr>
        <td><strong>${s.name}</strong></td>
        <td>${s.user_email || 'N/A'}</td>
        <td><span class="status-dot" style="background:${col};display:inline-block;width:7px;height:7px;border-radius:50%;margin-right:5px"></span>${s.status}</td>
        <td>${s.memory_mb}MB</td>
        <td>${s.port || '-'}</td>
        <td class="actions">
          <button class="btn-sm" onclick="editAdminServer(${s.id})">Edit</button>
          <button class="btn-sm danger" onclick="deleteAdminServer(${s.id})">Delete</button>
        </td>
      </tr>`;
    }).join('') + '</table>';
}

async function showAdminServerCreateModal() {
  document.getElementById('adminServerModalTitle').textContent = 'Create Server';
  document.getElementById('adminServerName').value = '';
  document.getElementById('adminServerVersion').value = '1.21.4';
  document.getElementById('adminServerMemory').value = '1024';
  document.getElementById('adminServerStorage').value = '5120';
  document.getElementById('adminServerPort').value = '';
  document.querySelector('#adminServerModal .btn-save').textContent = 'Create';
  document.querySelector('#adminServerModal .btn-save').onclick = () => confirmAdminServer();

  const [uRes, nRes] = await Promise.all([
    api('/panel/admin/users'),
    api('/panel/nodes'),
  ]);
  const userSel = document.getElementById('adminServerUser');
  if (!uRes.users || uRes.users.length === 0) {
    showToast('No users found. Create a user first.', true);
    return;
  }
  userSel.innerHTML = uRes.users.map(u => `<option value="${u.id}">${u.email}</option>`).join('');
  const nodeSel = document.getElementById('adminServerNode');
  nodeSel.innerHTML = '<option value="">None</option>' + (nRes.nodes?.map(n => `<option value="${n.id}">${n.name}</option>`).join('') || '');

  document.getElementById('adminServerModal').dataset.editId = '';
  openModal('adminServerModal');
}

async function editAdminServer(id) {
  const r = await api('/servers/' + id);
  if (!r.success) return;
  const s = r.server;
  document.getElementById('adminServerModalTitle').textContent = 'Edit Server';
  document.getElementById('adminServerName').value = s.name;
  document.getElementById('adminServerVersion').value = s.version;
  document.getElementById('adminServerMemory').value = s.memory_mb;
  document.getElementById('adminServerStorage').value = s.storage_mb;
  document.getElementById('adminServerPort').value = s.port || '';

  const [uRes, nRes] = await Promise.all([
    api('/panel/admin/users'),
    api('/panel/nodes'),
  ]);
  const userSel = document.getElementById('adminServerUser');
  if (!uRes.users || uRes.users.length === 0) {
    showToast('No users found.', true);
    return;
  }
  userSel.innerHTML = uRes.users.map(u => `<option value="${u.id}" ${u.id === s.user_id ? 'selected' : ''}>${u.email}</option>`).join('');
  const nodeSel = document.getElementById('adminServerNode');
  nodeSel.innerHTML = '<option value="">None</option>' + (nRes.nodes?.map(n => `<option value="${n.id}" ${n.id === s.node_id ? 'selected' : ''}>${n.name}</option>`).join('') || '');

  document.getElementById('adminServerModal').dataset.editId = id;
  document.querySelector('#adminServerModal .btn-save').textContent = 'Save';
  document.querySelector('#adminServerModal .btn-save').onclick = () => confirmAdminServer(id);
  openModal('adminServerModal');
}

async function confirmAdminServer(editId) {
  editId = editId || document.getElementById('adminServerModal').dataset.editId || null;
  const uid = parseInt(document.getElementById('adminServerUser').value);
  const nid = parseInt(document.getElementById('adminServerNode').value);
  const mem = parseInt(document.getElementById('adminServerMemory').value);
  const sto = parseInt(document.getElementById('adminServerStorage').value);
  const prt = parseInt(document.getElementById('adminServerPort').value);
  const body = {
    name: document.getElementById('adminServerName').value,
    user_id: isNaN(uid) ? null : uid,
    node_id: isNaN(nid) ? null : nid,
    type: document.getElementById('adminServerType').value,
    version: document.getElementById('adminServerVersion').value,
    memory_mb: isNaN(mem) ? 1024 : mem,
    storage_mb: isNaN(sto) ? 5120 : sto,
    port: isNaN(prt) ? null : prt,
  };
  if (!body.user_id) { showToast('Select a user.', true); return; }
  const method = editId ? 'PUT' : 'POST';
  const url = editId ? '/servers/' + editId : '/servers';
  const r = await api(url, { method, body: JSON.stringify(body) });
  if (r.success) {
    closeModal('adminServerModal');
    showToast(editId ? 'Server updated.' : 'Server created.');
    loadAdminServers();
  } else {
    showToast(r.error || 'Failed.', true);
  }
}

async function deleteAdminServer(id) {
  if (!confirm('Delete this server? All data will be lost.')) return;
  const r = await api('/servers/' + id, { method: 'DELETE' });
  if (r.success) { loadAdminServers(); showToast('Server deleted.'); } else showToast(r.error || 'Failed.', true);
}

// ── Admin: Locations ──

async function loadAdminLocations() {
  const r = await api('/panel/admin/locations');
  if (!r.success) return;
  const list = document.getElementById('locationList');
  if (r.locations.length === 0) { list.innerHTML = '<div class="empty-state">No locations.</div>'; return; }
  list.innerHTML = '<table class="data-table"><tr><th>Code</th><th>Name</th><th>Allocations</th><th>Actions</th></tr>' + r.locations.map(l =>
    '<tr><td>' + l.short_code + '</td><td>' + l.long_name + '</td><td>' + (l.allocations_count || 0) + '</td><td class="actions"><button class="btn-sm" onclick="editLocation(' + l.id + ',\'' + l.short_code + '\',\'' + l.long_name + '\',\'' + (l.description || '') + '\')">Edit</button><button class="btn-sm danger" onclick="deleteLocation(' + l.id + ')">Delete</button></td></tr>'
  ).join('') + '</table>';
}

function showLocationCreateModal() {
  document.getElementById('locModalTitle').textContent = 'Add Location';
  document.getElementById('locShortCode').value = '';
  document.getElementById('locLongName').value = '';
  document.getElementById('locDesc').value = '';
  document.getElementById('locationModal').dataset.editId = '';
  openModal('locationModal');
}

function editLocation(id, code, name, desc) {
  document.getElementById('locModalTitle').textContent = 'Edit Location';
  document.getElementById('locShortCode').value = code;
  document.getElementById('locLongName').value = name;
  document.getElementById('locDesc').value = desc;
  document.getElementById('locationModal').dataset.editId = id;
  openModal('locationModal');
}

async function saveLocation() {
  const body = { short_code: document.getElementById('locShortCode').value, long_name: document.getElementById('locLongName').value, description: document.getElementById('locDesc').value };
  const editId = document.getElementById('locationModal').dataset.editId;
  const r = editId ? await api('/panel/admin/locations/' + editId, { method: 'PUT', body: JSON.stringify(body) }) : await api('/panel/admin/locations', { method: 'POST', body: JSON.stringify(body) });
  closeModal('locationModal'); if (r.success) { loadAdminLocations(); showToast(editId ? 'Location updated.' : 'Location created.'); }
}

async function deleteLocation(id) {
  if (!confirm('Delete this location?')) return;
  await api('/panel/admin/locations/' + id, { method: 'DELETE' });
  loadAdminLocations();
}

// ── Admin: Allocations ──

async function loadNodeLocationSelects() {
  const nodes = await api('/panel/nodes');
  const locs = await api('/panel/admin/locations');
  if (nodes.success) document.getElementById('allocNodeId').innerHTML = nodes.nodes.map(n => '<option value="' + n.id + '">' + n.name + '</option>').join('');
  if (locs.success) document.getElementById('allocLocationId').innerHTML = locs.locations.map(l => '<option value="' + l.id + '">' + l.short_code + '</option>').join('');
}

async function loadAdminAllocations() {
  const r = await api('/panel/admin/allocations');
  if (!r.success) return;
  const list = document.getElementById('allocationList');
  if (r.allocations.length === 0) { list.innerHTML = '<div class="empty-state">No allocations.</div>'; return; }
  list.innerHTML = '<table class="data-table"><tr><th>Node</th><th>Location</th><th>IP</th><th>Port</th><th>Server</th><th>Actions</th></tr>' + r.allocations.map(a =>
    '<tr><td>' + (a.node ? a.node.name : '-') + '</td><td>' + (a.location ? a.location.short_code : '-') + '</td><td>' + a.ip + '</td><td>' + a.port + '</td><td>' + (a.server ? a.server.name : 'Unassigned') + '</td><td class="actions"><button class="btn-sm danger" onclick="deleteAllocation(' + a.id + ')">Delete</button></td></tr>'
  ).join('') + '</table>';
}

function showAllocationCreateModal() { loadNodeLocationSelects(); openModal('allocationModal'); }

async function saveAllocation() {
  const r = await api('/panel/admin/allocations', {
    method: 'POST', body: JSON.stringify({ node_id: parseInt(document.getElementById('allocNodeId').value) || null, location_id: parseInt(document.getElementById('allocLocationId').value), ip: document.getElementById('allocIp').value, port: parseInt(document.getElementById('allocPort').value) })
  });
  closeModal('allocationModal'); if (r.success) { loadAdminAllocations(); showToast('Allocation created.'); }
}

async function deleteAllocation(id) {
  if (!confirm('Delete this allocation?')) return;
  await api('/panel/admin/allocations/' + id, { method: 'DELETE' });
  loadAdminAllocations();
}

// ── Admin: Settings ──

async function loadAdminSettings() {
  const r = await api('/panel/admin/settings');
  if (!r.success) return;
  const s = r.settings || {};
  const logoUrl = s['panel:logo'] || '';
  document.getElementById('settingsForm').innerHTML =
    '<label style="font-size:11px;text-transform:uppercase;letter-spacing:.03em;color:var(--text2);display:block;margin-bottom:3px">Panel Name</label><input type="text" id="setPanelName" value="' + escHtml(s['panel:name'] || 'DragoraPanel') + '" style="width:100%;padding:9px 11px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;outline:none;margin-bottom:10px">' +
    '<label style="font-size:11px;text-transform:uppercase;letter-spacing:.03em;color:var(--text2);display:block;margin-bottom:3px">Panel Logo</label><div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">' +
    (logoUrl ? '<img id="logoPreview" src="' + logoUrl + '" style="width:36px;height:36px;border-radius:8px;object-fit:cover;border:1px solid var(--border)">' : '<div id="logoPreview" style="width:36px;height:36px;border-radius:8px;background:var(--surface2);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:14px;color:var(--text3)"><i class="fas fa-image"></i></div>') +
    '<input type="file" id="setLogoInput" accept="image/png,image/jpeg,image/gif,image/webp" style="font-size:12px;flex:1"></div>' +
    '<label style="font-size:11px;text-transform:uppercase;letter-spacing:.03em;color:var(--text2);display:block;margin-bottom:3px">Locale</label><select id="setLocale" style="width:100%;padding:9px 11px;background:var(--input-bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;outline:none;margin-bottom:14px"><option value="en"' + (s['panel:locale'] === 'en' ? ' selected' : '') + '>English</option><option value="es"' + (s['panel:locale'] === 'es' ? ' selected' : '') + '>Spanish</option></select>' +
    '<label style="font-size:11px;text-transform:uppercase;letter-spacing:.03em;color:var(--text2);display:block;margin-bottom:6px"><input type="checkbox" id="setMaintenance" value="1"' + (s['panel:maintenance'] === '1' ? ' checked' : '') + ' style="margin-right:6px">Maintenance Mode (blocks panel access)</label>' +
    '<div style="display:flex;gap:8px"><button class="power-btn start" onclick="saveAdminSettings()"><i class="fas fa-save"></i> Save Settings</button></div>';
}

async function saveAdminSettings() {
  const name = document.getElementById('setPanelName').value;
  const locale = document.getElementById('setLocale').value;
  const maintenance = document.getElementById('setMaintenance').checked ? '1' : '0';
  const logoInput = document.getElementById('setLogoInput');
  let logoUrl = null;

  if (logoInput && logoInput.files && logoInput.files[0]) {
    const fd = new FormData();
    fd.append('logo', logoInput.files[0]);
    try {
      const uploadRes = await fetch('/api/panel/admin/logo', {
        method: 'POST',
        headers: { 'Authorization': 'Bearer ' + localStorage.getItem(TOKEN_KEY) },
        body: fd
      });
      const uploadData = await uploadRes.json();
      if (uploadData.success) logoUrl = uploadData.url;
      else showToast(uploadData.error || 'Logo upload failed', true);
    } catch (e) {
      showToast('Logo upload failed', true);
    }
  }

  const r = await api('/panel/admin/settings', {
    method: 'PUT', body: JSON.stringify({ settings: { 'panel:name': name, 'panel:locale': locale, 'panel:maintenance': maintenance } })
  });
  if (r.success) {
    showToast('Settings saved.');
    if (logoUrl) applyPanelBranding(name, logoUrl);
    else applyPanelBranding(name);
  }
}

function applyPanelBranding(name, logoUrl) {
  document.title = name;
  document.getElementById('pageTitle').textContent = name;
  const brand = document.querySelector('.sidebar-brand');
  if (brand) brand.innerHTML = '<i class="fas fa-cog"></i> ' + name;
  if (logoUrl) {
    const logo = document.querySelector('.sidebar-logo');
    if (logo) {
      logo.innerHTML = '<img src="' + logoUrl + '" style="width:34px;height:34px;border-radius:10px;object-fit:cover">';
      logo.style.background = 'none';
      logo.style.boxShadow = 'none';
    }
    let favicon = document.querySelector('link[rel="icon"]');
    if (!favicon) {
      favicon = document.createElement('link');
      favicon.rel = 'icon';
      document.head.appendChild(favicon);
    }
    favicon.href = logoUrl;
  }
}

// ── Admin: Web Server ──

let wsConfigTypes = [];
let wsGeneratedContent = '';

async function loadWebServerTab() {
  const [cfgRes, staRes] = await Promise.all([
    api('/panel/webserver/configs'),
    api('/panel/webserver/status'),
  ]);
  if (!cfgRes.success) return;

  wsConfigTypes = cfgRes.configs || [];
  const sel = document.getElementById('wsConfigType');
  sel.innerHTML = wsConfigTypes.map(c => `<option value="${c.id}">${c.label}</option>`).join('');

  const statusDiv = document.getElementById('webserverStatus');
  const servers = staRes.success ? staRes.servers : {};
  let html = '<div style="display:flex;gap:12px;flex-wrap:wrap">';
  for (const [name, info] of Object.entries(servers)) {
    const col = info.service === 'running' ? '#34d399' : info.installed ? '#fbbf24' : '#6b7280';
    html += '<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:10px 14px;font-size:12px;min-width:140px">' +
      '<div style="font-weight:600;margin-bottom:4px;text-transform:capitalize">' + name + '</div>' +
      '<span class="status-dot" style="background:' + col + ';display:inline-block;width:7px;height:7px;border-radius:50%;margin-right:5px"></span>' +
      (info.service || (info.installed ? 'installed' : 'not installed')) +
      (info.path ? '<div style="color:var(--text3);margin-top:2px;font-size:11px;word-break:break-all">' + info.path + '</div>' : '') +
      '</div>';
  }
  html += '</div>';
  statusDiv.innerHTML = html;

  document.getElementById('webserverForm').style.display = 'block';
  updateWsSslFields();
}

function updateWsSslFields() {
  const id = document.getElementById('wsConfigType').value;
  const cfg = wsConfigTypes.find(c => c.id === id);
  document.getElementById('wsSslFields').style.display = cfg && cfg.ssl && cfg.server !== 'caddy' ? 'block' : 'none';
  document.getElementById('wsCaddySslField').style.display = cfg && cfg.ssl && cfg.server === 'caddy' ? 'block' : 'none';
}

document.addEventListener('change', function (e) {
  if (e.target.id === 'wsConfigType') updateWsSslFields();
});

async function previewWebServerConfig() {
  const id = document.getElementById('wsConfigType').value;
  const domain = document.getElementById('wsDomain').value.trim();
  if (!domain) { showToast('Enter a domain.', true); return; }

  const body = { config_id: id, domain: domain };
  const cfg = wsConfigTypes.find(c => c.id === id);
  if (cfg && cfg.ssl) {
    if (cfg.server === 'caddy') {
      body.ssl_email = document.getElementById('wsSslEmail').value.trim();
    } else {
      body.ssl_cert = document.getElementById('wsSslCert').value.trim();
      body.ssl_key = document.getElementById('wsSslKey').value.trim();
    }
  }

  const r = await api('/panel/webserver/generate', { method: 'POST', body: JSON.stringify(body) });
  if (!r.success) { showToast(r.error || 'Failed to generate config.', true); return; }

  wsGeneratedContent = r.content;
  document.getElementById('wsPreviewContent').textContent = r.content;
  document.getElementById('wsPreview').style.display = 'block';
}

async function downloadWebServerConfig() {
  if (!wsGeneratedContent) { showToast('Preview the config first.', true); return; }
  const cfg = wsConfigTypes.find(c => c.id === document.getElementById('wsConfigType').value);
  const filename = cfg ? cfg.filename : 'config.conf';
  const blob = new Blob([wsGeneratedContent], { type: 'text/plain;charset=utf-8' });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = filename;
  a.click();
  URL.revokeObjectURL(a.href);
}

function copyWebServerConfig() {
  if (!wsGeneratedContent) { showToast('Preview the config first.', true); return; }
  navigator.clipboard.writeText(wsGeneratedContent).then(() => showToast('Copied!')).catch(() => showToast('Failed to copy.', true));
}

async function installWebServer() {
  const id = document.getElementById('wsConfigType').value;
  const cfg = wsConfigTypes.find(c => c.id === id);
  if (!cfg) { showToast('Select a config type.', true); return; }

  const btn = document.getElementById('wsInstallBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Installing...';

  const r = await api('/panel/webserver/install', { method: 'POST', body: JSON.stringify({ server: cfg.server }) });

  if (r.success) {
    showToast(r.message || cfg.server + ' installed!');
    loadWebServerTab();
  } else {
    showToast(r.error || 'Installation failed.', true);
  }

  btn.disabled = false;
  btn.innerHTML = '<i class="fas fa-download"></i> Install';
}

// ── Admin: Activity ──

async function loadAdminActivity() {
  const r = await api('/panel/admin/activity');
  if (!r.success) return;
  const list = document.getElementById('adminActivityList');
  if (r.logs.length === 0) { list.innerHTML = '<div class="empty-state">No activity recorded.</div>'; return; }
  list.innerHTML = '<table class="data-table"><tr><th>Action</th><th>User</th><th>IP</th><th>Time</th></tr>' + r.logs.map(l =>
    '<tr><td>' + l.action + '</td><td>' + (l.user ? l.user.email : '-') + '</td><td>' + (l.ip_address || '-') + '</td><td>' + (l.created_at ? new Date(l.created_at).toLocaleString() : '-') + '</td></tr>'
  ).join('') + '</table>';
}

const DESIGN_DEFAULTS = {
  topbar_type:'default', login_text:'log in',
  hero_title:'server management,<br><span class="highlight">simplified<svg viewBox="0 0 80 12" preserveAspectRatio="none"><path d="M0,8 Q10,4 20,8 T40,8 T60,8 T80,8" stroke="var(--accent)" fill="none" stroke-width="2.5" opacity=".4"/></svg></span>',
  hero_subtitle:'Full-featured game server management panel with real-time console, file manager, database administration, and automated backups \u2014 all from your browser.',
  hero_btn1_text:'get started', hero_btn2_text:'explore features',
  stat1_num:'15', stat1_label:'servers online', stat1_icon:'fa-server',
  stat2_num:'2.5K', stat2_label:'active users', stat2_icon:'fa-users',
  stat3_num:'99.9%', stat3_label:'uptime SLA', stat3_icon:'fa-chart-line',
  card1_label:'servers running', card1_value:'3 active', card1_icon:'fa-desktop',
  card2_label:'last backup', card2_value:'2m ago', card2_icon:'fa-database',
  card3_label:'players online', card3_value:'23 connected', card3_icon:'fa-gamepad',
  card4_label:'system status', card4_value:'operational', card4_icon:'fa-check-circle',
  features_header:'everything you need to manage<br>your game servers',
  features_subtitle:'A comprehensive panel designed for server administrators who need reliability and control.',
  testimonial_quote:'We evaluated several management panels before deploying HostIt across our infrastructure. The intuitive interface and reliable console access made it the clear choice for our community.',
  testimonial_author:'Alex Chen', testimonial_handle:'Server Administrator \u00b7 MC Network',
  cta_title:'ready to get started?', cta_text:'Deploy your first server in minutes. No credit card required.',
  cta_btn_text:'start free trial',
  footer_text:'HostIt &copy; 2026 &mdash; Game Server Management Platform',
  features:[
    {icon:'fa-terminal',title:'live console',text:'Real-time terminal access to your server. Execute commands, monitor output, and manage your server directly from the browser.',note:'full TTY support with command history',tall:true,accent:false},
    {icon:'fa-folder-open',title:'file manager',text:'Full file system access with drag-and-drop upload, inline editing, and directory management.',note:'edit config files directly in the browser',tall:false,accent:true},
    {icon:'fa-database',title:'databases',text:'Provision MySQL databases with one click. Includes phpMyAdmin for advanced management.',note:'automatic user and permission setup',tall:false,accent:false},
    {icon:'fa-cloud-upload-alt',title:'automated backups',text:'Schedule automatic backups with configurable retention. Manual snapshots available at any time.',note:'daily, weekly, and manual backup modes',tall:true,accent:false},
    {icon:'fa-puzzle-piece',title:'plugin manager',text:'Upload, enable, disable, and configure plugins through an intuitive interface.',note:'compatible with Bukkit, Spigot, and Paper',tall:false,accent:false},
    {icon:'fa-shield-alt',title:'authentication',text:'Multi-provider authentication with Google, Discord, and email/password login. Role-based access control included.',note:'supports OAuth2, SSO, and 2FA',tall:true,accent:true},
  ],
};

async function loadDesigner() {
  const el = document.getElementById('designerContent');
  const r = await api('/panel/admin/settings');
  let d = {};
  if (r.success && r.settings['design:page']) {
    try { d = JSON.parse(r.settings['design:page']); } catch(e) {}
  }
  const merged = { ...DESIGN_DEFAULTS };
  for (const k in d) {
    if (k === 'features' && Array.isArray(d.features)) merged.features = d.features;
    else if (d[k] !== null && d[k] !== undefined && k !== 'features') merged[k] = d[k];
  }
  const v = k => merged[k] !== undefined ? merged[k] : '';
  const esc = s => String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  const features = v('features');
  const featRows = features.map((f,i) => `
    <div class="ds-feat" data-idx="${i}" style="background:var(--bg2);border:1px solid var(--border);border-radius:8px;padding:12px;margin-bottom:8px">
      <div style="display:flex;gap:6px;flex-wrap:wrap">
        <div class="field" style="flex:1;min-width:100px"><label>Icon</label><input class="ds-f-icon" value="${esc(f.icon||'')}" oninput="this.nextElementSibling.firstChild.className='fas '+this.value"><span class="icon-preview"><i class="fas ${esc(f.icon||'fa-star')}"></i></span></div>
        <div class="field" style="flex:2;min-width:120px"><label>Title</label><input class="ds-f-title" value="${esc(f.title||'')}"></div>
        <div class="field" style="flex:1;min-width:80px"><label>Tall?</label><select class="ds-f-tall"><option value="1" ${f.tall?'selected':''}>Tall</option><option value="0" ${!f.tall?'selected':''}>Short</option></select></div>
        <div class="field" style="flex:1;min-width:80px"><label>Accent?</label><select class="ds-f-accent"><option value="1" ${f.accent?'selected':''}>Yes</option><option value="0" ${!f.accent?'selected':''}>No</option></select></div>
      </div>
      <div class="field"><label>Text</label><textarea class="ds-f-text" rows="2">${esc(f.text||'')}</textarea></div>
      <div class="field"><label>Note (small text at bottom)</label><input class="ds-f-note" value="${esc(f.note||'')}"></div>
    </div>`).join('');

  el.innerHTML = `
<style>
.designer-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;max-width:1100px}
.designer-section{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;margin-bottom:16px}
.designer-section h4{font-size:13px;font-weight:600;margin-bottom:14px;color:var(--text2);text-transform:uppercase;letter-spacing:.05em}
.designer-section .field{margin-bottom:10px;position:relative}
.designer-section .field label{font-size:11px;color:var(--text3);display:block;margin-bottom:3px}
.designer-section .field input,.designer-section .field select,.designer-section .field textarea{width:100%;padding:8px 10px;border-radius:6px;border:1px solid var(--border);background:var(--bg2);color:var(--text);font-size:13px;outline:none;transition:border-color .2s;font-family:inherit}
.designer-section .field input:focus,.designer-section .field select:focus,.designer-section .field textarea:focus{border-color:var(--accent2)}
.designer-section .field textarea{resize:vertical;min-height:50px}
.designer-section .field .icon-preview{position:absolute;right:8px;bottom:8px;font-size:14px;opacity:.5;pointer-events:none}
.designer-actions{display:flex;gap:8px;margin-top:16px;flex-wrap:wrap}
</style>
<div class="designer-grid">
  <div>
    <div class="designer-section">
      <h4><i class="fas fa-bars"></i> Topbar</h4>
      <div class="field"><label>Topbar Style</label>
        <select id="ds_topbar_type">
          <option value="default" ${v('topbar_type')==='default'?'selected':''}>Default</option>
          <option value="centered" ${v('topbar_type')==='centered'?'selected':''}>Centered</option>
          <option value="transparent" ${v('topbar_type')==='transparent'?'selected':''}>Transparent</option>
          <option value="minimal" ${v('topbar_type')==='minimal'?'selected':''}>Minimal</option>
        </select>
      </div>
      <div class="field"><label>Get Started Button (navbar-right)</label><input id="ds_hero_btn1_text" value="${esc(v('hero_btn1_text'))}"></div>
      <div class="field"><label>Login Link Text (navbar)</label><input id="ds_login_text" value="${esc(v('login_text'))}"></div>
    </div>
    <div class="designer-section">
      <h4><i class="fas fa-heading"></i> Hero</h4>
      <div class="field"><label>Title (HTML) — use &lt;span class=&quot;highlight&quot;&gt;word&lt;/span&gt; + SVG underline</label><textarea id="ds_hero_title" rows="3">${esc(v('hero_title'))}</textarea></div>
      <div class="field"><label>Subtitle</label><textarea id="ds_hero_subtitle" rows="2">${esc(v('hero_subtitle'))}</textarea></div>
      <div class="field"><label>Left Button Text</label><input id="ds_hero_btn1" value="${esc(v('hero_btn1_text'))}"></div>
      <div class="field"><label>Right Button Text</label><input id="ds_hero_btn2" value="${esc(v('hero_btn2_text'))}"></div>
    </div>
    <div class="designer-section">
      <h4><i class="fas fa-chart-simple"></i> Stats Row</h4>
      ${[1,2,3].map(i => `
        <div style="display:flex;gap:6px;margin-bottom:8px">
          <div class="field" style="flex:1"><label>Stat ${i} #</label><input class="ds-stat-num" value="${esc(v('stat'+i+'_num'))}"></div>
          <div class="field" style="flex:2"><label>Label</label><input class="ds-stat-label" value="${esc(v('stat'+i+'_label'))}"></div>
          <div class="field" style="flex:1"><label>Icon</label><input class="ds-stat-icon" value="${esc(v('stat'+i+'_icon'))}" oninput="this.nextElementSibling.firstChild.className='fas '+this.value"><span class="icon-preview"><i class="fas ${esc(v('stat'+i+'_icon'))}"></i></span></div>
        </div>
      `).join('')}
    </div>
    <div class="designer-section">
      <h4><i class="fas fa-cubes"></i> Floating Cards</h4>
      ${[1,2,3,4].map(i => `
        <div style="display:flex;gap:6px;margin-bottom:8px">
          <div class="field" style="flex:2"><label>Card ${i}</label><input class="ds-card-label" placeholder="Label" value="${esc(v('card'+i+'_label'))}"></div>
          <div class="field" style="flex:2"><input class="ds-card-value" placeholder="Value" value="${esc(v('card'+i+'_value'))}"></div>
          <div class="field" style="flex:1"><input class="ds-card-icon" placeholder="Icon" value="${esc(v('card'+i+'_icon'))}" oninput="this.nextElementSibling.firstChild.className='fas '+this.value"><span class="icon-preview"><i class="fas ${esc(v('card'+i+'_icon'))}"></i></span></div>
        </div>
      `).join('')}
    </div>
  </div>
  <div>
    <div class="designer-section">
      <h4><i class="fas fa-star"></i> Features Header</h4>
      <div class="field"><label>Heading (HTML)</label><textarea id="ds_features_header" rows="2">${esc(v('features_header'))}</textarea></div>
      <div class="field"><label>Subtitle</label><textarea id="ds_features_subtitle" rows="2">${esc(v('features_subtitle'))}</textarea></div>
    </div>
    <div class="designer-section">
      <h4><i class="fas fa-puzzle-piece"></i> Feature Cards</h4>
      <div id="ds-feat-list">${featRows}</div>
      <button class="power-btn start" style="font-size:12px;margin-top:4px" onclick="addFeat()"><i class="fas fa-plus"></i> Add Feature</button>
    </div>
    <div class="designer-section">
      <h4><i class="fas fa-quote-right"></i> Testimonial</h4>
      <div class="field"><label>Quote</label><textarea id="ds_testimonial_quote" rows="3">${esc(v('testimonial_quote'))}</textarea></div>
      <div class="field"><label>Author</label><input id="ds_testimonial_author" value="${esc(v('testimonial_author'))}"></div>
      <div class="field"><label>Handle</label><input id="ds_testimonial_handle" value="${esc(v('testimonial_handle'))}"></div>
    </div>
    <div class="designer-section">
      <h4><i class="fas fa-rectangle-ad"></i> Call To Action</h4>
      <div class="field"><label>Title</label><input id="ds_cta_title" value="${esc(v('cta_title'))}"></div>
      <div class="field"><label>Text</label><textarea id="ds_cta_text" rows="2">${esc(v('cta_text'))}</textarea></div>
      <div class="field"><label>Button Text</label><input id="ds_cta_btn_text" value="${esc(v('cta_btn_text'))}"></div>
    </div>
    <div class="designer-section">
      <h4><i class="fas fa-copyright"></i> Footer</h4>
      <div class="field"><label>Footer HTML</label><textarea id="ds_footer_text" rows="2">${esc(v('footer_text'))}</textarea></div>
    </div>
    <div class="designer-actions">
      <button class="power-btn start" onclick="saveDesign()"><i class="fas fa-floppy-disk"></i> Save Design</button>
      <button class="power-btn restart" onclick="loadDesigner()"><i class="fas fa-rotate"></i> Reload</button>
      <button class="power-btn stop" onclick="resetDesign()" style="font-size:12px"><i class="fas fa-trash"></i> Reset to Defaults</button>
    </div>
  </div>
</div>`;
}
function addFeat() {
  const list = document.getElementById('ds-feat-list');
  const idx = list.children.length;
  const div = document.createElement('div');
  div.className = 'ds-feat';
  div.dataset.idx = idx;
  div.style.cssText = 'background:var(--bg2);border:1px solid var(--border);border-radius:8px;padding:12px;margin-bottom:8px';
  div.innerHTML = `
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <div class="field" style="flex:1;min-width:100px"><label>Icon</label><input class="ds-f-icon" oninput="this.nextElementSibling.firstChild.className='fas '+this.value"><span class="icon-preview"><i class="fas fa-star"></i></span></div>
      <div class="field" style="flex:2;min-width:120px"><label>Title</label><input class="ds-f-title"></div>
      <div class="field" style="flex:1;min-width:80px"><label>Size</label><select class="ds-f-tall"><option value="1">Tall</option><option value="0" selected>Short</option></select></div>
      <div class="field" style="flex:1;min-width:80px"><label>Accent</label><select class="ds-f-accent"><option value="1">Yes</option><option value="0" selected>No</option></select></div>
    </div>
    <div class="field"><label>Text</label><textarea class="ds-f-text" rows="2"></textarea></div>
    <div class="field"><label>Note</label><input class="ds-f-note"></div>`;
  list.appendChild(div);
}
async function saveDesign() {
  const g = id => document.getElementById(id);
  const q = (sel, parent) => (parent || document).querySelectorAll(sel);
  const design = {
    topbar_type: g('ds_topbar_type').value,
    login_text: g('ds_login_text').value,
    hero_title: g('ds_hero_title').value,
    hero_subtitle: g('ds_hero_subtitle').value,
    hero_btn1_text: g('ds_hero_btn1').value,
    hero_btn2_text: g('ds_hero_btn2').value,
    features_header: g('ds_features_header').value,
    features_subtitle: g('ds_features_subtitle').value,
    testimonial_quote: g('ds_testimonial_quote').value,
    testimonial_author: g('ds_testimonial_author').value,
    testimonial_handle: g('ds_testimonial_handle').value,
    cta_title: g('ds_cta_title').value,
    cta_text: g('ds_cta_text').value,
    cta_btn_text: g('ds_cta_btn_text').value,
    footer_text: g('ds_footer_text').value,
  };
  q('.ds-stat-num').forEach((el,i) => { design['stat'+(i+1)+'_num'] = el.value });
  q('.ds-stat-label').forEach((el,i) => { design['stat'+(i+1)+'_label'] = el.value });
  q('.ds-stat-icon').forEach((el,i) => { design['stat'+(i+1)+'_icon'] = el.value });
  q('.ds-card-label').forEach((el,i) => { design['card'+(i+1)+'_label'] = el.value });
  q('.ds-card-value').forEach((el,i) => { design['card'+(i+1)+'_value'] = el.value });
  q('.ds-card-icon').forEach((el,i) => { design['card'+(i+1)+'_icon'] = el.value });
  design.features = [];
  q('.ds-feat').forEach(el => {
    design.features.push({
      icon: el.querySelector('.ds-f-icon').value,
      title: el.querySelector('.ds-f-title').value,
      text: el.querySelector('.ds-f-text').value,
      note: el.querySelector('.ds-f-note').value,
      tall: el.querySelector('.ds-f-tall').value === '1',
      accent: el.querySelector('.ds-f-accent').value === '1',
    });
  });
  const r = await api('/panel/admin/settings', { method: 'PUT', body: JSON.stringify({ settings: { 'design:page': JSON.stringify(design) } }) });
  if (r.success) showToast('Design saved! Refreshing...');
  else showToast('Failed to save design', true);
}
async function resetDesign() {
  if (!confirm('Reset all website design to defaults?')) return;
  const r = await api('/panel/admin/settings', { method: 'PUT', body: JSON.stringify({ settings: { 'design:page': '{}' } }) });
  if (r.success) { showToast('Defaults restored'); loadDesigner(); }
  else showToast('Failed to reset', true);
}

// ── Theme ──

let debugEnabled = false;
let debugRequests = [];
let debugInterval = null;

function debugToggle() {
  debugEnabled = !debugEnabled;
  document.getElementById('debugToggle').classList.toggle('active', debugEnabled);
  document.getElementById('debugPanel').classList.toggle('open', debugEnabled);
  if (debugEnabled) { debugDumpAll(); debugInterval = setInterval(debugDumpAll, 5000); }
  else { clearInterval(debugInterval); }
}

function debugLog(method, path, data, status, ok) {
  debugRequests.unshift({ method, path, data, status, ok, ts: new Date().toLocaleTimeString() });
  if (debugRequests.length > 200) debugRequests.length = 200;
  if (debugEnabled) debugRenderReqs();
}

function debugClearReqs() { debugRequests = []; debugRenderReqs(); }

function escapeJson(s) {
  if (typeof s !== 'string') return String(s);
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function renderJsonValue(v, indent) {
  indent = indent || 0;
  const pad = '  '.repeat(indent);
  if (v === null || v === undefined) return '<span class="debug-null">null</span>';
  if (typeof v === 'boolean') return '<span class="debug-bool">' + v + '</span>';
  if (typeof v === 'number') return '<span class="debug-number">' + v + '</span>';
  if (typeof v === 'string') return '<span class="debug-string">"' + escapeJson(v) + '"</span>';
  if (Array.isArray(v)) {
    if (v.length === 0) return '<span class="debug-null">[]</span>';
    let h = '[\n';
    v.forEach((item, i) => {
      h += pad + '  ' + renderJsonValue(item, indent + 1) + (i < v.length - 1 ? ',' : '') + '\n';
    });
    return h + pad + ']';
  }
  if (typeof v === 'object') {
    const keys = Object.keys(v);
    if (keys.length === 0) return '<span class="debug-null">{}</span>';
    let h = '{\n';
    keys.forEach((k, i) => {
      h += pad + '  <span class="debug-key">"' + escapeJson(k) + '"</span>: ' + renderJsonValue(v[k], indent + 1) + (i < keys.length - 1 ? ',' : '') + '\n';
    });
    return h + pad + '}';
  }
  return escapeJson(String(v));
}

function makeSection(title, content) {
  const id = 'dbg-' + title.replace(/[^a-z0-9]/gi, '_');
  return '<div class="debug-section"><div class="debug-section-title" onclick="const c=document.getElementById(\'' + id + '\');c.classList.toggle(\'open\')"><i class="fas fa-chevron-right" style="font-size:8px;transition:transform .2s" id="chev-' + id + '"></i> ' + title + ' (' + (content.match(/\n/g)||[]).length + ' lines)</div><div class="debug-section-content" id="' + id + '">' + content + '</div></div>';
}

function debugDumpAll() {
  if (!debugEnabled) return;
  const body = document.getElementById('debugPanelBody');
  let html = '';

  // API requests
  let reqHtml = '';
  debugRequests.slice(0, 50).forEach(r => {
    reqHtml += '<div class="debug-req-row"><span class="ts" style="color:var(--text3)">' + escapeJson(r.ts) + '</span><span class="method">' + escapeJson(r.method) + '</span><span class="path">' + escapeJson(r.path) + '</span><span class="status ' + (r.ok ? 'ok' : 'err') + '">' + r.status + '</span></div>';
  });
  if (!reqHtml) reqHtml = '<div style="padding:8px 14px;color:var(--text3);font-size:10px">No API requests yet.</div>';
  html += makeSection('API Requests (' + debugRequests.length + ')', reqHtml);

  // User data
  html += makeSection('User Data', renderJsonValue(userData || { not_loaded: true }));

  // Current server
  const cs = { currentServerId, currentFilePath, currentServerData };
  html += makeSection('Current Server', renderJsonValue(cs));

  // All servers (from last dashboard load)
  const lastServers = window.__lastServers || [];
  html += makeSection('Servers (' + lastServers.length + ')', renderJsonValue(lastServers));

  // All nodes (from last load)
  const lastNodes = window.__lastNodes || [];
  html += makeSection('Nodes (' + lastNodes.length + ')', renderJsonValue(lastNodes));

  // All users (from last load)
  const lastUsers = window.__lastUsers || [];
  html += makeSection('Users (' + lastUsers.length + ')', renderJsonValue(lastUsers));

  // Local storage
  const ls = {};
  for (let i = 0; i < localStorage.length; i++) {
    const k = localStorage.key(i);
    ls[k] = localStorage.getItem(k);
  }
  html += makeSection('localStorage', renderJsonValue(ls));

  // Panel info
  const info = {
    api_base: API_BASE,
    token: (localStorage.getItem(TOKEN_KEY) || '').substring(0, 20) + '...',
    viewport: window.innerWidth + 'x' + window.innerHeight,
    user_agent: navigator.userAgent.substring(0, 120),
    href: window.location.href,
  };
  html += makeSection('Panel Info', renderJsonValue(info));

  body.innerHTML = html;

  // Auto-open first 3 sections
  body.querySelectorAll('.debug-section-content').forEach((el, i) => { if (i < 3) el.classList.add('open'); });
}

function debugRenderReqs() {
  const body = document.getElementById('debugPanelBody');
  if (!body || !body.innerHTML) return;
  // Just update the requests section in-place
  const reqSection = body.querySelector('.debug-section:first-child .debug-section-content');
  if (!reqSection) return;
  let reqHtml = '';
  debugRequests.slice(0, 50).forEach(r => {
    reqHtml += '<div class="debug-req-row"><span class="ts" style="color:var(--text3)">' + escapeJson(r.ts) + '</span><span class="method">' + escapeJson(r.method) + '</span><span class="path">' + escapeJson(r.path) + '</span><span class="status ' + (r.ok ? 'ok' : 'err') + '">' + r.status + '</span></div>';
  });
  reqSection.innerHTML = reqHtml || '<div style="padding:8px 14px;color:var(--text3);font-size:10px">No API requests yet.</div>';
}

// Hook into api() to log requests
const origApi = api;
api = async function(path, opts) {
  const result = await origApi(path, opts);
  const ok = result && result.success !== false;
  debugLog((opts && opts.method) || 'GET', path, (opts && opts.body) || null, ok ? 200 : (result && result.error ? 400 : 0), ok);
  return result;
};

// Snapshot data after loads (without duplicate API calls)
const origLoadDashboard = loadDashboard;
loadDashboard = async function() {
  const result = await origLoadDashboard();
  // Servers already loaded by the original function, snapshot from api hook
  return result;
};

const origLoadAdminUsers = loadAdminUsers;
loadAdminUsers = async function() {
  const result = await origLoadAdminUsers();
  return result;
};

// Intercept api responses to snapshot data
const origApiForSnapshot = api;
api = async function(path, opts) {
  const result = await origApiForSnapshot(path, opts);
  if (path === '/servers' && result && result.success) window.__lastServers = result.servers;
  if (path === '/panel/nodes' && result && result.success) window.__lastNodes = result.nodes;
  if (path === '/panel/admin/users' && result && result.success) window.__lastUsers = result.users;
  return result;
};

const dt = document.getElementById('debugToggle'); if (dt) dt.addEventListener('click', debugToggle);

// ── Mouse glow ──

let glowEl = document.getElementById('mouseGlow');
let gt;
if (glowEl) document.addEventListener('mousemove', e => { glowEl.style.left = e.clientX + 'px'; glowEl.style.top = e.clientY + 'px'; glowEl.classList.add('visible'); clearTimeout(gt); gt = setTimeout(() => glowEl.classList.remove('visible'), 2000); });
if (glowEl) document.addEventListener('mouseleave', () => glowEl.classList.remove('visible'));

// ── Real Console Polling ──

let consolePollInterval = null;

async function loadConsoleLogs() {
  if (!currentServerId) return;
  const r = await api('/servers/' + currentServerId + '/console');
  if (!r.success) return;
  const out = document.getElementById('consoleOutput');
  if (!out) return;
  // Only update if new content
  const currentText = out.textContent;
  const newText = r.logs.join('\n');
  if (newText !== currentText) {
    out.innerHTML = r.logs.map(line => {
      const typeClass = line.includes('[OK]') ? 'ok' : line.includes('[WARN]') ? 'warn' : line.includes('[ERROR]') ? 'err' : line.includes('[CMD]') ? 'info' : 'info';
      return '<div class="line"><span class="time"></span><span class="' + typeClass + '">' + escHtml(line) + '</span></div>';
    }).join('');
    out.scrollTop = out.scrollHeight;
  }
}

function escHtml(s) {
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function startConsolePolling() {
  if (consolePollInterval) clearInterval(consolePollInterval);
  loadConsoleLogs();
  consolePollInterval = setInterval(loadConsoleLogs, 3000);
}

function stopConsolePolling() {
  if (consolePollInterval) { clearInterval(consolePollInterval); consolePollInterval = null; }
}

// Start/stop polling when server tabs change
document.querySelectorAll('.server-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    if (tab.dataset.stab === 'console') {
      startConsolePolling();
    } else {
      stopConsolePolling();
    }
  });
});

// Also start polling when opening a server and update URL
const origOpenServer = openServer;
openServer = function(name, id) {
  origOpenServer(name, id);
  startConsolePolling();
  const targetUrl = '/panel/servers/' + id;
  const cur = window.location.pathname;
  if (cur !== targetUrl) history.pushState({ viewName: 'server', param: id }, '', targetUrl);
  document.getElementById('pageTitle').textContent = name;
  const nav = document.querySelector('[data-view="server"]');
  if (nav) nav.classList.add('active');
};



// ── Greeting ──

(function() {
  const h = new Date().getHours();
  const msgs = ['good morning','good morning','good afternoon','good evening'];
  const idx = h < 6 ? 0 : h < 12 ? 1 : h < 17 ? 2 : 3;
  const el = document.querySelector('.greet-head');
  if (el) el.textContent = msgs[idx] + ',';
})();

// ── Logout ──

window.addEventListener('beforeunload', function() { var c = document.getElementById('consoleOutput'); if (c) c.innerHTML = ''; });
document.getElementById('logoutBtn').addEventListener('click', async () => {
  await api('/auth?action=logout', { method: 'POST' });
  localStorage.removeItem(TOKEN_KEY);
  window.location.href = '/auth/login';
});

init();
</script>
</body>
</html>
