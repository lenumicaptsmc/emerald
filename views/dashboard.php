<!DOCTYPE html>
<html lang="en" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Central Hub</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], mono: ['Fira Code', 'monospace'] },
                    colors: { 
                        brand: { 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7' },
                        base: 'var(--bg-base)', panel: 'var(--bg-panel)', input: 'var(--bg-input)', border: 'var(--border-color)', primary: 'var(--text-main)', muted: 'var(--text-muted)'
                    },
                    animation: { 'slide-up': 'slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards' },
                    keyframes: { slideUp: { '0%': { transform: 'translateY(15px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } } }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/monokai.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/dialog/dialog.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/search/search.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/search/searchcursor.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/dialog/dialog.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/php/php.min.js"></script>

    <style>
        :root {
            --bg-base: #f1f5f9; --bg-panel: rgba(255, 255, 255, 0.9); --bg-input: #ffffff;
            --border-color: rgba(0, 0, 0, 0.1); --text-main: #111827; --text-muted: #6b7280; --shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            --glass-bg: rgba(255, 255, 255, 0.7); --hover-bg: rgba(0,0,0,0.05);
        }
        html.dark {
            --bg-base: #030712; --bg-panel: rgba(15, 23, 42, 0.6); --bg-input: rgba(0,0,0,0.5);
            --border-color: rgba(255, 255, 255, 0.05); --text-main: #ffffff; --text-muted: #9ca3af; --shadow: 0 10px 25px -5px rgba(0,0,0,0.5);
            --glass-bg: rgba(11, 17, 32, 0.85); --hover-bg: rgba(255,255,255,0.05);
        }
        
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }
        
        body { overflow: hidden; background-color: var(--bg-base); color: var(--text-main); transition: background-color 0.3s, color 0.3s; }
        
        .modal { opacity: 0; pointer-events: none; visibility: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); z-index: 9999; backdrop-filter: blur(8px); }
        .modal.active { opacity: 1; pointer-events: auto; visibility: visible; }
        .modal-content { transform: scale(0.95) translateY(20px); transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); background: var(--bg-panel); border: 1px solid var(--border-color); box-shadow: var(--shadow); }
        .modal.active .modal-content { transform: scale(1) translateY(0); }
        
        .container-card { transition: all 0.2s ease; border: 1px solid var(--border-color); background: var(--bg-panel); backdrop-filter: blur(10px); border-radius: 1.25rem; overflow: hidden; cursor: pointer; }
        .container-card:hover { transform: translateY(-5px); box-shadow: var(--shadow); border-color: var(--text-muted); }
        
        .editor-wrapper { border-radius: 1rem; overflow: hidden; border: 1px solid var(--border-color); display: flex; flex-direction: column; background: #1e1e1e; }
        .CodeMirror { flex: 1; height: 100% !important; font-family: 'Fira Code', monospace; font-size: 14px; background: #1e1e1e !important; color: #d4d4d4 !important; }
        html:not(.dark) .CodeMirror { background: #ffffff !important; color: #111827 !important; }
        .CodeMirror-dialog { background: var(--bg-panel); backdrop-filter: blur(10px); border: 1px solid var(--border-color); color: var(--text-main); padding: 10px 15px; border-radius: 8px; }
        .CodeMirror-dialog input { background: var(--bg-input); border: 1px solid var(--border-color); color: var(--text-main); border-radius: 4px; padding: 4px 8px; outline: none; }

        .view-section { display: none; opacity: 0; transform: translateY(15px); transition: all 0.5s ease; }
        .view-section.active { display: block; opacity: 1; transform: translateY(0); }

        .btn-animated { position: relative; overflow: hidden; transition: all 0.3s ease; cursor: pointer; }
        .btn-animated:hover { transform: translateY(-2px); box-shadow: var(--shadow); }
        .btn-animated:active { transform: translateY(1px); }
        .btn-gradient { background: linear-gradient(90deg, #0ea5e9, #38bdf8, #8b5cf6, #0ea5e9); background-size: 300% auto; transition: 0.5s; color: white; border: none; }
        .btn-gradient:hover { background-position: right center; box-shadow: 0 0 25px rgba(14,165,233,0.5); }

        .modal-input { background: var(--bg-input) !important; border-color: var(--border-color) !important; color: var(--text-main) !important; transition: all 0.3s ease; }
        .modal-input:focus { border-color: #0ea5e9 !important; box-shadow: 0 0 0 2px rgba(14,165,233,0.2) !important; background: var(--bg-input) !important; }

        .role-text-owner { color: #ef4444; font-weight: 900; }
        .role-text-admin { color: #10b981; font-weight: 900; }
        .role-text-guest { color: #6b7280; font-weight: 900; }
        
        .auth-secret { color: transparent; text-shadow: 0 0 12px var(--text-muted); cursor: pointer; transition: all 0.2s; user-select: all; }
        .auth-secret:active, .auth-secret:focus, .auth-secret.revealed { color: var(--text-main); text-shadow: none; background: rgba(14,165,233,0.2); border-radius: 4px; padding: 0 4px; }
        .auth-secret::selection { background: #0ea5e9; color: #fff; text-shadow: none; }

        .theme-toggle-btn { transition: all 0.3s ease; }
        html:not(.dark) .theme-toggle-btn { background-color: #10b981; }

        .online-dot { position: absolute; bottom: 0; right: 0; width: 14px; height: 14px; border-radius: 50%; border: 2px solid var(--bg-panel); transition: all 0.3s ease; }
        .online-dot.is-online { background-color: #10b981; box-shadow: 0 0 10px rgba(16,185,129,0.8); }
        .online-dot.is-offline { background-color: #4b5563; }
        
        .file-checkbox { width: 1.2rem; height: 1.2rem; border-radius: 0.375rem; border: 1px solid var(--border-color); appearance: none; cursor: pointer; background: var(--bg-input); transition: all 0.2s; }
        .file-checkbox:checked { background: #0ea5e9; border-color: #0ea5e9; background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e"); }
    </style>
</head>
<body class="selection:bg-[#0ea5e9] selection:text-white" id="bodyTheme">
    <script>if (localStorage.getItem('emerald_theme') !== 'light') document.documentElement.classList.add('dark');</script>

<div class="fixed inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-brand-900/10 via-transparent to-transparent -z-10 pointer-events-none"></div>

<div id="dropOverlay" class="fixed inset-0 bg-brand-500/10 backdrop-blur-sm z-[1000] hidden items-center justify-center border-4 border-dashed border-brand-500 m-4 rounded-3xl pointer-events-none transition-all">
    <div class="text-center">
        <i class="fa-solid fa-cloud-arrow-up text-6xl text-brand-400 mb-4 animate-bounce"></i>
        <h2 class="text-3xl font-extrabold text-primary">Drop to Upload</h2>
    </div>
</div>

<div class="flex h-screen overflow-hidden relative z-10">

    <!-- SIDEBAR -->
    <aside class="w-72 bg-[var(--glass-bg)] backdrop-blur-2xl border-r border-border flex flex-col shadow-2xl transition-all duration-300">
        <div class="h-24 flex items-center px-8 border-b border-border">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center shadow-[0_0_20px_rgba(14,165,233,0.4)]">
                    <i class="fa-solid fa-gem text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="font-extrabold text-2xl tracking-tighter text-primary">EMERALD</h1>
                    <p class="text-[10px] text-brand-500 font-mono tracking-widest uppercase">System Core v7.0</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-5 py-8 space-y-3 overflow-y-auto custom-scrollbar" id="mainNav">
            <a href="/dashboard" onclick="route(event, 'dashboard')" class="nav-item w-full flex items-center gap-4 px-5 py-3.5 text-muted hover:bg-[var(--hover-bg)] hover:text-primary border border-transparent rounded-2xl font-medium transition-all" data-target="dashboard">
                <i class="fa-solid fa-chart-pie w-6 text-center"></i> System Overview
            </a>
            <a href="/assets" onclick="route(event, 'files')" class="nav-item w-full flex items-center gap-4 px-5 py-3.5 text-muted hover:bg-[var(--hover-bg)] hover:text-primary border border-transparent rounded-2xl font-medium transition-all" data-target="files">
                <i class="fa-solid fa-folder-open w-6 text-center"></i> Assets Manager
            </a>
            <a href="/containers" onclick="route(event, 'notes')" class="nav-item w-full flex items-center gap-4 px-5 py-3.5 text-muted hover:bg-[var(--hover-bg)] hover:text-primary border border-transparent rounded-2xl font-medium transition-all" data-target="notes">
                <i class="fa-solid fa-layer-group w-6 text-center"></i> Containers
            </a>
            <a href="/cloaking" onclick="route(event, 'cloaking')" class="nav-item w-full flex items-center gap-4 px-5 py-3.5 text-muted hover:bg-[var(--hover-bg)] hover:text-primary border border-transparent rounded-2xl font-medium transition-all" data-target="cloaking">
                <i class="fa-solid fa-masks-theater w-6 text-center"></i> Cloaking Data
            </a>
            <a href="/users" onclick="route(event, 'users')" class="nav-item w-full flex items-center gap-4 px-5 py-3.5 text-muted hover:bg-[var(--hover-bg)] hover:text-primary border border-transparent rounded-2xl font-medium transition-all" data-target="users">
                <i class="fa-solid fa-users-gear w-6 text-center"></i> System Users
            </a>
            <a href="/firewall" onclick="route(event, 'firewall')" class="nav-item w-full flex items-center gap-4 px-5 py-3.5 text-muted hover:bg-[var(--hover-bg)] hover:text-primary border border-transparent rounded-2xl font-medium transition-all" data-target="firewall">
                <i class="fa-solid fa-shield-virus w-6 text-center"></i> Firewall / IP
            </a>
            
            <div class="pt-6 pb-2">
                <p class="text-[10px] font-bold text-muted uppercase tracking-widest px-5">Public Tools</p>
            </div>
            <a href="/notepad" target="_blank" class="w-full flex items-center gap-4 px-5 py-3.5 text-emerald-500 hover:bg-emerald-500/10 border border-transparent rounded-2xl font-medium transition-all btn-animated">
                <i class="fa-solid fa-book-open w-6 text-center"></i> Public Notepad
            </a>
        </nav>

        <div class="p-6 border-t border-border bg-[var(--bg-panel)]">
            <div class="flex items-center justify-between mb-4 cursor-pointer hover:bg-[var(--hover-bg)] p-2 rounded-xl transition-colors" onclick="openProfileModal()">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="relative">
                        <img id="sidebarAvatar" src="" class="w-12 h-12 rounded-full border-2 border-brand-500 shadow-[0_0_15px_rgba(14,165,233,0.3)] object-cover">
                        <div id="sidebarDot" class="w-3.5 h-3.5 rounded-full absolute -bottom-1 -right-1 border-2 border-[var(--bg-panel)]"></div>
                    </div>
                    <div class="overflow-hidden">
                        <div class="font-bold text-primary text-sm truncate" id="sidebarUsername"><?= htmlspecialchars($_SESSION['emerald_user']) ?></div>
                        <div class="text-[10px] font-mono mt-1 role-display uppercase tracking-wider" id="sidebarRole">Fetching...</div>
                    </div>
                </div>
                <i class="fa-solid fa-gear text-muted hover:text-primary transition-colors"></i>
            </div>
            <a href="index.php?action=logout" class="block w-full text-center px-4 py-2.5 bg-red-500/10 border border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white rounded-xl transition-all text-sm font-bold shadow-lg btn-animated">
                <i class="fa-solid fa-power-off mr-2"></i> Terminate Session
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col overflow-hidden relative">
        <header class="h-24 bg-[var(--glass-bg)] backdrop-blur-2xl flex items-center justify-between px-10 z-20 border-b border-border shadow-md">
            <div class="flex flex-col">
                <h1 class="text-3xl font-extrabold text-primary tracking-tight" id="pageTitle">System Overview</h1>
                <div id="breadcrumb" class="text-sm font-mono text-brand-500 mt-1 flex items-center gap-2 opacity-0 transition-opacity">
                    <i class="fa-solid fa-house"></i> / root
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div class="hidden lg:flex items-center gap-3 bg-[var(--bg-input)] px-4 py-2 rounded-xl border border-border shadow-inner">
                    <span class="text-xs text-muted font-mono">Terminal Node:</span>
                    <span class="font-bold text-primary"><?= htmlspecialchars($_SESSION['emerald_user']) ?></span>
                    <span id="headerRoleBadge" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[var(--hover-bg)]">User</span>
                </div>
                <div class="relative group">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-muted group-focus-within:text-brand-500 transition-colors"></i>
                    <input type="text" id="globalSearch" oninput="performSearch()" placeholder="Ctrl+F to Find..." class="text-sm pl-12 pr-4 py-3 border border-border bg-[var(--bg-input)] rounded-2xl focus:outline-none focus:border-brand-500 w-80 text-primary transition-all shadow-inner placeholder-muted">
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-10 custom-scrollbar relative z-10 animate-slide-up" id="mainAreaWrapper">
            
            <!-- VIEW: DASHBOARD -->
            <div id="view_dashboard" class="view-section active">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-panel border border-border rounded-2xl p-6 shadow-[var(--shadow)] backdrop-blur-md relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-brand-500/10 rounded-full blur-xl group-hover:scale-150 transition-transform"></div>
                        <h4 class="text-primary text-lg font-bold mb-4 flex items-center gap-2 relative z-10"><i class="fa-solid fa-shield-halved text-[#0ea5e9]"></i> Security Cipher</h4>
                        <ul class="space-y-3 font-mono text-xs relative z-10" id="sysEntityList">
                            <li class="flex justify-between border-b border-border pb-2"><span class="text-muted">Encryption Standard</span><span class="text-emerald-500 font-bold">AES-256-CBC</span></li>
                            <li class="flex justify-between border-b border-border pb-2"><span class="text-muted">Database Integrity</span><span class="text-emerald-500 font-bold">OPTIMAL</span></li>
                        </ul>
                    </div>
                    <div class="bg-panel border border-border rounded-2xl p-6 shadow-[var(--shadow)] backdrop-blur-md relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl group-hover:scale-150 transition-transform"></div>
                        <h4 class="text-primary text-lg font-bold mb-4 flex items-center gap-2 relative z-10"><i class="fa-solid fa-network-wired text-emerald-500"></i> Active Protection</h4>
                        <ul class="space-y-3 font-mono text-xs relative z-10" id="sysFirewallList">
                            <li class="flex justify-between border-b border-border pb-2"><span class="text-muted">Firewall Status</span><span class="text-emerald-500 font-bold">ACTIVE</span></li>
                            <li class="flex justify-between border-b border-border pb-2"><span class="text-muted">Active Node IP</span><span class="text-primary font-bold"><?= $_SERVER['REMOTE_ADDR'] ?></span></li>
                        </ul>
                    </div>
                    <div class="bg-panel border border-border rounded-2xl p-6 shadow-[var(--shadow)] backdrop-blur-md relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-orange-500/10 rounded-full blur-xl group-hover:scale-150 transition-transform"></div>
                        <h4 class="text-primary text-lg font-bold mb-4 flex items-center gap-2 relative z-10"><i class="fa-solid fa-palette text-orange-500"></i> Workspace Theme</h4>
                        <div class="flex items-center justify-between bg-input border border-border p-4 rounded-xl mb-2 cursor-pointer btn-animated" onclick="toggleTheme()">
                            <span class="text-sm font-bold text-primary" id="themeText">Dark Mode</span>
                            <div class="w-12 h-6 bg-gray-600 rounded-full flex items-center px-1 theme-toggle-btn"><div class="w-4 h-4 bg-white rounded-full transition-all" id="themeCircle"></div></div>
                        </div>
                        <p class="text-[10px] text-muted mt-2">Toggle interface display mode.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
                    <div class="bg-panel border border-border rounded-2xl overflow-hidden shadow-2xl backdrop-blur-md flex flex-col h-[400px]">
                        <div class="px-8 py-5 border-b border-border bg-[var(--hover-bg)] flex items-center gap-3 shrink-0">
                            <i class="fa-solid fa-shield-halved text-brand-500"></i>
                            <h3 class="font-bold text-primary text-lg">System Access Logs</h3>
                        </div>
                        <div class="overflow-y-auto flex-1 custom-scrollbar">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-input border-b border-border text-[10px] font-bold text-muted uppercase tracking-widest sticky top-0 z-10">
                                        <th class="px-6 py-3">Time</th>
                                        <th class="px-4 py-3">User</th>
                                        <th class="px-4 py-3">IP</th>
                                        <th class="px-4 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="logsList" class="divide-y divide-[var(--border-color)] text-sm font-medium"></tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="bg-panel border border-border rounded-2xl overflow-hidden shadow-2xl backdrop-blur-md flex flex-col h-[400px]">
                        <div class="px-8 py-5 border-b border-border bg-[var(--hover-bg)] flex items-center gap-3 shrink-0">
                            <i class="fa-solid fa-clipboard-list text-purple-500"></i>
                            <h3 class="font-bold text-primary text-lg">Activity Journal</h3>
                        </div>
                        <div class="overflow-y-auto flex-1 custom-scrollbar">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-input border-b border-border text-[10px] font-bold text-muted uppercase tracking-widest sticky top-0 z-10">
                                        <th class="px-6 py-3">Time</th>
                                        <th class="px-4 py-3">User</th>
                                        <th class="px-4 py-3">Action Details</th>
                                    </tr>
                                </thead>
                                <tbody id="activityList" class="divide-y divide-[var(--border-color)] text-sm font-medium"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VIEW: FILE MANAGER -->
            <div id="view_files" class="view-section">
                <!-- Bulk Actions Toolbar -->
                <div class="flex items-center gap-3 mb-6 bg-input p-4 rounded-xl border border-border hidden shadow-lg" id="bulkToolbar">
                    <span class="text-primary font-bold text-sm mr-2 border-r border-border pr-4"><span id="selCount" class="text-brand-500 text-lg">0</span> selected</span>
                    <button class="bg-[var(--hover-bg)] text-red-500 px-4 py-2.5 rounded-lg text-xs font-bold hover:bg-red-500 hover:text-white transition-all btn-animated" onclick="bulkAction('delete')"><i class="fa-solid fa-trash mr-1"></i> Delete</button>
                    <button class="bg-[var(--hover-bg)] text-brand-500 px-4 py-2.5 rounded-lg text-xs font-bold hover:bg-brand-500 hover:text-white transition-all btn-animated" onclick="bulkAction('copy')"><i class="fa-solid fa-copy mr-1"></i> Copy</button>
                    <button class="bg-[var(--hover-bg)] text-purple-500 px-4 py-2.5 rounded-lg text-xs font-bold hover:bg-purple-500 hover:text-white transition-all btn-animated" onclick="bulkAction('cut')"><i class="fa-solid fa-scissors mr-1"></i> Cut</button>
                </div>
                
                <div class="flex items-center gap-3 mb-6 bg-brand-500/10 p-4 rounded-xl border border-brand-500/30 hidden shadow-lg" id="pasteToolbar">
                    <span class="text-brand-500 font-bold text-sm mr-2 border-r border-brand-500/30 pr-4" id="pasteInfo"></span>
                    <button class="bg-emerald-500 text-white px-5 py-2.5 rounded-lg text-xs font-bold hover:bg-emerald-600 transition-all btn-animated shadow-lg" onclick="executePaste()"><i class="fa-solid fa-paste mr-1"></i> Paste Here</button>
                    <button class="bg-transparent border border-gray-500 text-gray-500 px-4 py-2.5 rounded-lg text-xs font-bold hover:bg-gray-500 hover:text-white transition-all btn-animated" onclick="cancelPaste()"><i class="fa-solid fa-xmark mr-1"></i> Cancel</button>
                </div>

                <div class="flex justify-between items-center mb-8">
                    <div class="flex gap-2">
                        <button class="bg-panel border border-border text-primary px-5 py-3 text-sm font-bold rounded-xl hover:bg-input transition-all btn-animated" onclick="navigateUp()" id="btnNavUp" style="display:none;">
                            <i class="fa-solid fa-arrow-left mr-2"></i> Back
                        </button>
                    </div>
                    <div class="flex gap-3">
                        <button class="bg-panel border border-border text-brand-500 px-4 py-3 text-sm font-bold rounded-xl hover:border-brand-500/50 transition-all btn-animated flex items-center gap-2" onclick="promptCreateFolder()">
                            <i class="fa-solid fa-folder-plus text-lg"></i> New Folder
                        </button>
                        <button class="bg-panel border border-border text-emerald-500 px-4 py-3 text-sm font-bold rounded-xl hover:border-emerald-500/50 transition-all btn-animated flex items-center gap-2" onclick="promptCreateFile()">
                            <i class="fa-solid fa-file-circle-plus text-lg"></i> New File
                        </button>
                        <button class="btn-gradient px-4 py-3 text-sm font-bold rounded-xl btn-animated flex items-center gap-2" onclick="document.getElementById('fileInput').click()">
                            <i class="fa-solid fa-file-arrow-up text-lg"></i> Upload File
                        </button>
                        <button class="btn-gradient px-4 py-3 text-sm font-bold rounded-xl btn-animated flex items-center gap-2" onclick="document.getElementById('folderInput').click()">
                            <i class="fa-solid fa-folder-arrow-up text-lg"></i> Upload Folder
                        </button>
                        <input type="file" id="fileInput" class="hidden" multiple onchange="handleStandardUpload(this.files, false)">
                        <input type="file" id="folderInput" class="hidden" webkitdirectory directory multiple onchange="handleStandardUpload(this.files, true)">
                        
                        <button class="bg-panel border border-border text-muted px-4 py-3 text-sm font-bold rounded-xl hover:bg-input hover:text-primary transition-all btn-animated" onclick="loadFiles(currentPath)">
                            <i class="fa-solid fa-rotate-right"></i>
                        </button>
                    </div>
                </div>

                <div class="bg-panel border border-border rounded-2xl overflow-hidden shadow-[var(--shadow)] backdrop-blur-md flex flex-col h-[65vh]">
                    <div class="overflow-y-auto flex-1 custom-scrollbar">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-input border-b border-border text-xs font-bold text-muted uppercase tracking-widest sticky top-0 z-10">
                                    <th class="px-6 py-5 w-12 text-center">
                                        <input type="checkbox" id="selectAllCheckbox" class="file-checkbox" onclick="event.stopPropagation(); toggleSelectAll(this)">
                                    </th>
                                    <th class="px-4 py-5">Name & Endpoint</th>
                                    <th class="px-6 py-5">Type</th>
                                    <th class="px-6 py-5">Size</th>
                                    <th class="px-6 py-5">Last Modified</th>
                                    <th class="px-6 py-5">Owner</th>
                                    <th class="px-6 py-5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="filesList" class="divide-y divide-border text-sm font-medium"></tbody>
                        </table>
                    </div>
                    <div id="assetsPagination" class="bg-input border-t border-border shrink-0"></div>
                </div>
            </div>

            <!-- VIEW: CONTAINERS -->
            <div id="view_notes" class="view-section">
                <div class="flex justify-between items-center mb-8">
                    <p class="text-muted font-medium">Unified secure storage. Click cards to view comprehensive data.</p>
                    <button class="btn-gradient px-6 py-3 text-sm font-bold rounded-xl btn-animated flex items-center gap-2" onclick="openContainerModal()">
                        <i class="fa-solid fa-layer-group text-lg"></i> Build Container
                    </button>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="notesListArea"></div>
            </div>

            <!-- VIEW: CLOAKING -->
            <div id="view_cloaking" class="view-section">
                <div class="flex justify-between items-center mb-8">
                    <p class="text-muted font-medium">Advanced SEO Cloaking Management.</p>
                    <button class="bg-purple-600 text-white px-6 py-3 text-sm font-bold rounded-xl hover:bg-purple-500 transition-all btn-animated shadow-[0_0_20px_rgba(168,85,247,0.3)] flex items-center gap-2" onclick="openCloakingModal()">
                        <i class="fa-solid fa-masks-theater text-lg"></i> Add Cloak Data
                    </button>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6" id="cloakingGrid"></div>
            </div>

            <!-- VIEW: USERS -->
            <div id="view_users" class="view-section">
                <div class="flex justify-between items-center mb-8">
                    <p class="text-muted font-medium">Manage System Identities and Privileges.</p>
                    <button class="bg-emerald-600 text-white px-6 py-3 text-sm font-bold rounded-xl hover:bg-emerald-500 transition-all btn-animated shadow-[0_0_20px_rgba(16,185,129,0.3)] flex items-center gap-2" onclick="openUserModal()">
                        <i class="fa-solid fa-user-plus text-lg"></i> Register User
                    </button>
                </div>
                <div class="bg-panel border border-border rounded-2xl overflow-hidden shadow-[var(--shadow)] max-w-5xl backdrop-blur-md">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-input border-b border-border text-xs font-bold text-muted uppercase tracking-widest">
                                <th class="px-8 py-5">Identity Status</th>
                                <th class="px-6 py-5">Privilege Level</th>
                                <th class="px-6 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersList" class="divide-y divide-border text-sm"></tbody>
                    </table>
                </div>
            </div>

            <!-- VIEW: FIREWALL -->
            <div id="view_firewall" class="view-section">
                <div class="flex justify-between items-center mb-8">
                    <p class="text-muted font-medium">Manage System Access IP Whitelist.</p>
                    <button class="bg-red-600 text-white px-6 py-3 text-sm font-bold rounded-xl hover:bg-red-500 transition-all btn-animated shadow-[0_0_20px_rgba(239,68,68,0.3)] flex items-center gap-2" onclick="openFirewallModal()">
                        <i class="fa-solid fa-shield-virus text-lg"></i> Add IP Whitelist
                    </button>
                </div>
                <div class="bg-panel border border-border rounded-2xl overflow-hidden shadow-[var(--shadow)] max-w-5xl backdrop-blur-md">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-input border-b border-border text-xs font-bold text-muted uppercase tracking-widest">
                                <th class="px-8 py-5">IP Address</th>
                                <th class="px-6 py-5">Notes</th>
                                <th class="px-6 py-5">Date Added</th>
                                <th class="px-6 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="firewallList" class="divide-y divide-border text-sm font-mono"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- ================= MODALS ================= -->

<div class="modal fixed inset-0 flex items-center justify-center p-4" id="modalAuthPrompt">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" onclick="closeModal('modalAuthPrompt')"></div>
    <div class="modal-content rounded-3xl w-full max-w-sm relative z-10 overflow-hidden">
        <div class="p-10 text-center">
            <div class="w-24 h-24 bg-red-500/10 rounded-full border border-red-500/30 flex items-center justify-center mx-auto mb-6 shadow-[0_0_40px_rgba(239,68,68,0.2)]">
                <i class="fa-solid fa-lock text-5xl text-red-500"></i>
            </div>
            <h3 class="font-extrabold text-2xl text-primary mb-2">Auth Required</h3>
            <p class="text-muted text-sm mb-8 font-medium">Verify identity to execute destructive protocols.</p>
            <input type="password" id="authPassword" placeholder="Passphrase" class="w-full text-center bg-input border border-border text-primary rounded-2xl p-4 outline-none font-bold text-lg mb-6 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all shadow-inner modal-input tracking-widest">
            <div class="flex gap-3">
                <button class="flex-1 py-3.5 border border-border rounded-xl font-bold text-muted hover:bg-hover-bg transition-all btn-animated" onclick="closeModal('modalAuthPrompt')">Cancel</button>
                <button class="flex-1 py-3.5 bg-red-600 text-white rounded-xl font-bold hover:bg-red-500 transition-all btn-animated shadow-[0_0_20px_rgba(239,68,68,0.4)]" onclick="executeAuthorizedAction()">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fixed inset-0 flex items-center justify-center p-4" id="modalUploadProgress">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm"></div>
    <div class="modal-content rounded-3xl w-full max-w-md relative z-10 overflow-hidden p-8 text-center bg-panel">
        <div class="w-20 h-20 bg-brand-500/10 rounded-full border border-brand-500/30 flex items-center justify-center mx-auto mb-6 shadow-[0_0_40px_rgba(14,165,233,0.2)] animate-pulse">
            <i class="fa-solid fa-cloud-arrow-up text-4xl text-brand-500"></i>
        </div>
        <h3 class="font-extrabold text-2xl text-primary mb-2">Processing Payload</h3>
        <p class="text-muted text-sm mb-6 font-medium" id="uploadProgressText">Initializing secure tunnel...</p>
        <div class="w-full bg-input rounded-full h-3 mb-2 overflow-hidden border border-border shadow-inner">
            <div class="bg-brand-500 h-3 rounded-full transition-all duration-300" id="uploadProgressBar" style="width: 0%"></div>
        </div>
    </div>
</div>

<div class="modal fixed inset-0 flex items-center justify-center p-4" id="modalProfile">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" onclick="closeModal('modalProfile')"></div>
    <div class="modal-content rounded-3xl w-full max-w-lg relative z-10 overflow-hidden">
        <div class="px-8 py-6 border-b border-border flex justify-between items-center bg-[var(--hover-bg)]">
            <h3 class="font-extrabold text-xl text-primary">Profile Identity</h3>
            <button onclick="closeModal('modalProfile')" class="text-muted hover:text-primary transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <div class="p-8">
            <div class="text-center mb-6 relative">
                <img id="profileAvatarPreview" src="" class="w-32 h-32 rounded-full border-4 border-brand-500 shadow-[0_0_30px_rgba(14,165,233,0.3)] object-cover mx-auto cursor-pointer hover:opacity-80 transition-opacity" onclick="openAvatarZoom(this.src)">
                <button onclick="document.getElementById('avatarUpload').click()" class="absolute bottom-0 right-1/2 translate-x-12 w-10 h-10 bg-brand-600 rounded-full border-2 border-[var(--bg-panel)] text-white flex items-center justify-center hover:bg-brand-500 transition-all cursor-pointer"><i class="fa-solid fa-camera"></i></button>
                <input type="file" id="avatarUpload" class="hidden" accept="image/*" onchange="handleAvatarUpload(event)">
                <input type="hidden" id="profAvatarBase64">
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">Username</label>
                    <input type="text" id="profUsername" class="w-full bg-input border border-border text-primary rounded-xl p-3 outline-none font-bold transition-all modal-input">
                </div>
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">New Password</label>
                    <input type="password" id="profPassword" placeholder="Leave blank to keep" class="w-full bg-input border border-border text-primary rounded-xl p-3 outline-none font-bold transition-all modal-input">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">Security Question</label>
                    <input type="text" id="profSecQ" placeholder="e.g. Pet name?" class="w-full bg-input border border-border text-primary rounded-xl p-3 outline-none font-bold transition-all modal-input">
                </div>
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">Security Answer</label>
                    <input type="text" id="profSecA" placeholder="Answer..." class="w-full bg-input border border-border text-primary rounded-xl p-3 outline-none font-bold transition-all modal-input">
                </div>
            </div>
            <button class="w-full mt-8 py-4 btn-gradient text-white rounded-xl font-bold btn-animated text-lg tracking-wide" onclick="updateProfile()">Update Identity</button>
        </div>
    </div>
</div>

<div class="modal fixed inset-0 flex items-center justify-center p-4 md:p-8 z-[10000]" id="modalAvatarZoom">
    <div class="fixed inset-0 bg-black/95 backdrop-blur-md" onclick="closeModal('modalAvatarZoom')"></div>
    <img id="avatarZoomImg" src="" class="max-w-full max-h-full rounded-2xl relative z-10 shadow-[0_0_50px_rgba(14,165,233,0.5)] object-contain border border-[var(--border-color)]">
</div>

<!-- Editor Modal Full Height Fix -->
<div class="modal fixed inset-0 flex items-center justify-center p-4 md:p-8" id="modalEditor">
    <div class="fixed inset-0 bg-black/90 backdrop-blur-md" onclick="closeModal('modalEditor')"></div>
    <div class="modal-content rounded-3xl w-full max-w-7xl flex flex-col h-[95vh] relative z-10 overflow-hidden border-0 shadow-none bg-transparent">
        <div class="flex flex-col h-full bg-[var(--bg-panel)] border border-[var(--border-color)] rounded-3xl overflow-hidden shadow-2xl">
            <div class="px-8 py-4 border-b border-[var(--border-color)] flex justify-between items-center bg-[var(--hover-bg)] shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-brand-500/20 flex items-center justify-center border border-brand-500/30">
                        <i id="editorIcon" class="fa-solid fa-code text-[#0ea5e9] text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-xl text-primary font-mono tracking-tight flex items-center gap-3">
                            <span id="editorTitle">filename.ext</span>
                            <span id="editorSize" class="text-xs bg-input text-muted border border-[var(--border-color)] px-2 py-0.5 rounded font-sans">0 KB</span>
                        </h3>
                        <div class="text-xs text-muted mt-1 flex items-center gap-3">
                            <span><i class="fa-regular fa-clock mr-1"></i> <span id="editorModified">...</span></span>
                            <span><i class="fa-solid fa-keyboard mr-1"></i> <kbd class="bg-input border border-[var(--border-color)] px-1 rounded font-mono">Ctrl+F</kbd> Find | <kbd class="bg-input border border-[var(--border-color)] px-1 rounded font-mono">Ctrl+S</kbd> Save</span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button class="bg-transparent border border-[var(--border-color)] text-primary px-6 py-3 text-sm font-bold rounded-xl hover:bg-input transition-all btn-animated" onclick="closeModal('modalEditor')">Close</button>
                    <button class="btn-gradient text-white px-8 py-3 text-sm font-bold rounded-xl btn-animated flex items-center gap-2" onclick="saveFileEditor()">
                        <i class="fa-solid fa-floppy-disk"></i> Save Source
                    </button>
                </div>
            </div>
            <div class="flex-1 relative w-full flex flex-col bg-[#1e1e1e]" id="editorContainer">
                <textarea id="codeEditor"></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Unified Container BUILD/EDIT Modal -->
<div class="modal fixed inset-0 flex items-center justify-center p-4" id="modalContainer">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" onclick="closeModal('modalContainer')"></div>
    <div class="modal-content rounded-3xl w-full max-w-7xl relative z-10 max-h-[95vh] flex flex-col overflow-hidden">
        <div class="px-8 py-6 border-b border-[var(--border-color)] flex justify-between items-center bg-[var(--hover-bg)]">
            <h3 class="font-extrabold text-2xl text-primary flex items-center gap-4">
                <div class="w-12 h-12 bg-brand-500/20 rounded-xl flex items-center justify-center border border-brand-500/30 shadow-[0_0_15px_rgba(14,165,233,0.2)]">
                    <i class="fa-solid fa-layer-group text-brand-500"></i>
                </div>
                Configure Container
            </h3>
            <button onclick="closeModal('modalContainer')" class="w-10 h-10 flex items-center justify-center rounded-full bg-input text-muted hover:bg-red-500 hover:text-white transition-all border border-[var(--border-color)]"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <div class="p-8 overflow-y-auto custom-scrollbar flex-1 bg-transparent">
            <input type="hidden" id="containerId">
            <div class="space-y-8">
                <div>
                    <label class="block text-sm font-bold text-muted uppercase tracking-widest mb-3 ml-2">Container Identifier</label>
                    <input type="text" id="containerTitle" placeholder="e.g., Project Alpha Assets" class="w-full bg-input border-2 border-[var(--border-color)] text-primary rounded-2xl p-5 outline-none font-bold text-lg transition-all focus:border-brand-500 shadow-inner modal-input">
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    <!-- Text List Section -->
                    <div class="bg-input border border-[var(--border-color)] rounded-3xl p-6 flex flex-col shadow-lg">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-500 border border-emerald-500/30"><i class="fa-solid fa-list"></i></div>
                                <h4 class="font-bold text-primary text-lg tracking-wide">Text & Gsocket List</h4>
                            </div>
                            <span class="text-[10px] bg-[var(--bg-panel)] text-muted px-2 py-1 rounded font-mono border border-[var(--border-color)]">Auto parses Links & Gsockets</span>
                        </div>
                        <textarea id="containerTextList" class="w-full flex-1 bg-[var(--bg-panel)] border border-[var(--border-color)] text-emerald-500 rounded-2xl p-6 font-mono text-sm outline-none min-h-[350px] custom-scrollbar whitespace-nowrap overflow-x-auto modal-input focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 leading-relaxed shadow-inner" placeholder="Enter links, notes, or gsocket commands..."></textarea>
                    </div>
                    <!-- Server Auth Section -->
                    <div class="bg-input border border-[var(--border-color)] rounded-3xl p-6 shadow-lg">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-purple-500/20 flex items-center justify-center text-purple-500 border border-purple-500/30"><i class="fa-solid fa-server"></i></div>
                            <h4 class="font-bold text-primary text-lg tracking-wide">Server Authentication</h4>
                        </div>
                        <div class="space-y-6 mt-4">
                            <div class="flex items-center bg-[var(--bg-panel)] border border-[var(--border-color)] rounded-2xl p-2 relative modal-input focus-within:border-purple-500 focus-within:ring-1 focus-within:ring-purple-500 transition-all group shadow-inner">
                                <span class="text-xs font-extrabold text-muted w-24 text-center tracking-widest group-focus-within:text-purple-500 transition-colors">HOST</span>
                                <input type="text" id="containerHost" class="flex-1 bg-transparent text-primary py-4 px-4 font-mono text-base outline-none border-l border-[var(--border-color)] placeholder-muted">
                            </div>
                            <div class="flex items-center bg-[var(--bg-panel)] border border-[var(--border-color)] rounded-2xl p-2 relative modal-input focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-all group shadow-inner">
                                <span class="text-xs font-extrabold text-muted w-24 text-center tracking-widest group-focus-within:text-blue-500 transition-colors">USER</span>
                                <input type="text" id="containerUser" class="flex-1 bg-transparent text-blue-500 py-4 px-4 font-mono text-base outline-none border-l border-[var(--border-color)] placeholder-muted">
                            </div>
                            <div class="flex items-center bg-[var(--bg-panel)] border border-[var(--border-color)] rounded-2xl p-2 relative modal-input focus-within:border-red-500 focus-within:ring-1 focus-within:ring-red-500 transition-all group shadow-inner">
                                <span class="text-xs font-extrabold text-muted w-24 text-center tracking-widest group-focus-within:text-red-500 transition-colors">PASS</span>
                                <input type="text" id="containerPass" class="flex-1 bg-transparent text-red-500 py-4 px-4 font-mono text-base outline-none border-l border-[var(--border-color)] placeholder-muted">
                            </div>
                            <div class="flex items-center bg-[var(--bg-panel)] border border-[var(--border-color)] rounded-2xl p-2 relative modal-input focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all group shadow-inner">
                                <span class="text-xs font-extrabold text-muted w-24 text-center tracking-widest group-focus-within:text-brand-500 transition-colors">DIR</span>
                                <input type="text" id="containerDir" class="flex-1 bg-transparent text-brand-500 py-4 px-4 font-mono text-base outline-none border-l border-[var(--border-color)] placeholder-muted">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-10 py-6 border-t border-[var(--border-color)] bg-[var(--hover-bg)] flex justify-end gap-4 rounded-b-3xl">
            <button class="px-8 py-3.5 border border-[var(--border-color)] rounded-xl text-base font-bold text-muted hover:bg-[var(--bg-input)] hover:text-primary transition-all btn-animated" onclick="closeModal('modalContainer')">Cancel</button>
            <button class="px-10 py-3.5 btn-gradient text-white rounded-xl text-base font-bold btn-animated flex items-center gap-3" onclick="saveContainer()">
                <i class="fa-solid fa-check text-xl"></i> Store Container
            </button>
        </div>
    </div>
</div>

<!-- View Container POPUP Modal (View Only Data) -->
<div class="modal fixed inset-0 flex items-center justify-center p-4 md:p-8" id="modalViewContainer">
    <div class="fixed inset-0 bg-black/90 backdrop-blur-md" onclick="closeModal('modalViewContainer')"></div>
    <div class="modal-content rounded-3xl w-full max-w-6xl relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-8 py-6 border-b border-[var(--border-color)] flex justify-between items-center bg-[var(--hover-bg)]">
            <div class="flex items-center gap-4">
                <img id="viewContainerAvatar" src="" class="w-14 h-14 rounded-full border-2 border-brand-500 shadow-[0_0_15px_rgba(14,165,233,0.3)] object-cover cursor-pointer hover:opacity-80" onclick="openAvatarZoom(this.src)">
                <div>
                    <h3 class="font-extrabold text-2xl text-primary tracking-wide" id="viewContainerTitle">Container</h3>
                    <p class="text-xs text-brand-500 font-mono mt-0.5 uppercase tracking-widest" id="viewContainerOwner">Owner</p>
                </div>
            </div>
            <div class="flex gap-2" id="viewContainerActions"></div>
        </div>
        <div class="p-8 overflow-y-auto custom-scrollbar flex-1 space-y-8 bg-transparent" id="viewContainerContent"></div>
    </div>
</div>

<div class="modal fixed inset-0 flex items-center justify-center p-4" id="modalCloaking">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" onclick="closeModal('modalCloaking')"></div>
    <div class="modal-content rounded-3xl w-full max-w-4xl relative z-10 overflow-hidden flex flex-col">
        <div class="px-8 py-6 border-b border-[var(--border-color)] flex justify-between items-center bg-[var(--hover-bg)]">
            <h3 class="font-extrabold text-2xl text-primary flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center border border-purple-500/30 shadow-[0_0_15px_rgba(168,85,247,0.2)]">
                    <i class="fa-solid fa-masks-theater text-purple-500"></i>
                </div>
                SEO Cloaking Config
            </h3>
            <button onclick="closeModal('modalCloaking')" class="text-muted hover:text-primary transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <div class="p-8 space-y-6 flex-1 overflow-y-auto custom-scrollbar">
            <input type="hidden" id="cloakId">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2 ml-2">Domain Target</label>
                    <input type="text" id="cloakDomain" placeholder="target.com" class="w-full bg-[var(--bg-input)] border border-[var(--border-color)] text-primary rounded-2xl p-4 outline-none font-bold transition-all modal-input focus:border-purple-500 shadow-inner">
                </div>
                <div>
                    <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2 ml-2">Cloak Path</label>
                    <input type="text" id="cloakPath" placeholder="/seo-landing" class="w-full bg-[var(--bg-input)] border border-[var(--border-color)] text-primary rounded-2xl p-4 outline-none font-bold transition-all modal-input focus:border-purple-500 shadow-inner">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2 ml-2">Visibility</label>
                <select id="cloakType" class="w-full bg-[var(--bg-input)] border border-[var(--border-color)] text-primary rounded-2xl p-4 outline-none font-bold transition-all modal-input focus:border-purple-500 appearance-none shadow-inner">
                    <option value="personal">Personal (Private to you)</option>
                    <option value="global">Global (Shared across system)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2 ml-2">Injection Payload (HTML/JS)</label>
                <textarea id="cloakContent" class="w-full bg-[var(--bg-input)] border border-[var(--border-color)] text-purple-500 rounded-2xl p-5 font-mono text-sm outline-none h-64 modal-input focus:border-purple-500 shadow-inner"></textarea>
            </div>
        </div>
        <div class="px-8 py-6 border-t border-[var(--border-color)] bg-[var(--hover-bg)] flex justify-end gap-3 rounded-b-3xl">
            <button class="px-8 py-3 border border-[var(--border-color)] rounded-xl font-bold text-muted hover:bg-[var(--bg-input)] transition-all btn-animated" onclick="closeModal('modalCloaking')">Cancel</button>
            <button class="px-8 py-3 bg-purple-600 text-white rounded-xl font-bold hover:bg-purple-500 transition-all btn-animated shadow-[0_0_20px_rgba(168,85,247,0.4)] flex items-center gap-2" onclick="saveCloaking()">
                <i class="fa-solid fa-bolt"></i> Deploy Cloak
            </button>
        </div>
    </div>
</div>

<div class="modal fixed inset-0 flex items-center justify-center p-4" id="modalUser">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" onclick="closeModal('modalUser')"></div>
    <div class="modal-content rounded-3xl w-full max-w-sm relative z-10 overflow-hidden p-8">
        <div class="w-24 h-24 bg-emerald-500/10 rounded-3xl border border-emerald-500/30 flex items-center justify-center mx-auto mb-6 shadow-[0_0_30px_rgba(16,185,129,0.2)]">
            <i class="fa-solid fa-user-shield text-5xl text-emerald-500"></i>
        </div>
        <h3 class="font-extrabold text-2xl text-primary text-center mb-8">Register Identity</h3>
        <div class="space-y-5">
            <input type="text" id="newUserName" placeholder="Username" class="w-full bg-[var(--bg-input)] border border-[var(--border-color)] text-primary rounded-2xl p-4 outline-none font-bold transition-all text-center modal-input focus:border-emerald-500 shadow-inner">
            <input type="password" id="newUserPass" placeholder="Password" class="w-full bg-[var(--bg-input)] border border-[var(--border-color)] text-primary rounded-2xl p-4 outline-none font-bold transition-all text-center modal-input focus:border-emerald-500 shadow-inner">
            <select id="newUserRole" class="w-full bg-[var(--bg-input)] border border-[var(--border-color)] text-primary rounded-2xl p-4 outline-none font-bold transition-all text-center modal-input focus:border-emerald-500 appearance-none shadow-inner">
                <option value="guest">Guest (Read-Only)</option>
                <option value="admin">Admin (Standard)</option>
                <option value="owner">Owner (Full Privileges)</option>
            </select>
        </div>
        <div class="mt-8 flex gap-3">
            <button class="flex-1 py-3.5 border border-[var(--border-color)] text-muted rounded-xl font-bold hover:bg-[var(--bg-input)] transition-all btn-animated" onclick="closeModal('modalUser')">Cancel</button>
            <button class="flex-1 py-3.5 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-500 transition-all btn-animated shadow-[0_0_20px_rgba(16,185,129,0.4)]" onclick="saveUser()">Register</button>
        </div>
    </div>
</div>

<div class="modal fixed inset-0 flex items-center justify-center p-4" id="modalDeleteUser">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" onclick="closeModal('modalDeleteUser')"></div>
    <div class="modal-content rounded-3xl w-full max-w-md relative z-10 overflow-hidden p-8">
        <div class="w-24 h-24 bg-red-500/10 rounded-3xl border border-red-500/30 flex items-center justify-center mx-auto mb-6 shadow-[0_0_30px_rgba(239,68,68,0.2)]">
            <i class="fa-solid fa-user-xmark text-5xl text-red-500"></i>
        </div>
        <h3 class="font-extrabold text-2xl text-primary text-center mb-8">Delete Identity</h3>
        <div class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2 ml-2">Target User</label>
                <input type="text" id="delTargetUser" class="w-full bg-[var(--bg-input)] border border-[var(--border-color)] text-red-500 rounded-2xl p-4 outline-none font-bold text-center shadow-inner" readonly>
            </div>
            <div>
                <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2 ml-2">Migrate Data To (Optional)</label>
                <select id="delMigrateTo" class="w-full bg-[var(--bg-input)] border border-[var(--border-color)] text-primary rounded-2xl p-4 outline-none font-bold transition-all text-center modal-input focus:border-red-500 appearance-none shadow-inner">
                    <option value="">-- Delete All Data --</option>
                </select>
            </div>
        </div>
        <div class="mt-8 flex gap-3">
            <button class="flex-1 py-3.5 border border-[var(--border-color)] text-muted rounded-xl font-bold hover:bg-[var(--bg-input)] transition-all btn-animated" onclick="closeModal('modalDeleteUser')">Cancel</button>
            <button class="flex-1 py-3.5 bg-red-600 text-white rounded-xl font-bold hover:bg-red-500 transition-all btn-animated shadow-[0_0_20px_rgba(239,68,68,0.4)]" onclick="executeDeleteUser()">Proceed</button>
        </div>
    </div>
</div>

<div class="modal fixed inset-0 flex items-center justify-center p-4" id="modalFirewall">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" onclick="closeModal('modalFirewall')"></div>
    <div class="modal-content rounded-3xl w-full max-w-sm relative z-10 overflow-hidden p-8">
        <div class="w-20 h-20 bg-red-500/10 rounded-2xl border border-red-500/30 flex items-center justify-center mx-auto mb-6 shadow-[0_0_20px_rgba(239,68,68,0.2)]">
            <i class="fa-solid fa-shield-virus text-4xl text-red-500"></i>
        </div>
        <h3 class="font-extrabold text-2xl text-primary text-center mb-8">Whitelist IP</h3>
        <div class="space-y-5">
            <input type="text" id="fwIP" placeholder="Enter IP Address" class="w-full bg-[var(--bg-input)] border border-[var(--border-color)] text-primary rounded-2xl p-4 outline-none font-bold transition-all text-center modal-input focus:border-red-500 shadow-inner">
            <input type="text" id="fwNote" placeholder="Notes (e.g. Office PC)" class="w-full bg-[var(--bg-input)] border border-[var(--border-color)] text-primary rounded-2xl p-4 outline-none font-bold transition-all text-center modal-input focus:border-red-500 shadow-inner">
        </div>
        <div class="mt-8 flex gap-3">
            <button class="flex-1 py-3.5 border border-[var(--border-color)] text-muted rounded-xl font-bold hover:bg-[var(--bg-input)] transition-all btn-animated" onclick="closeModal('modalFirewall')">Cancel</button>
            <button class="flex-1 py-3.5 bg-red-600 text-white rounded-xl font-bold hover:bg-red-500 transition-all btn-animated shadow-[0_0_20px_rgba(239,68,68,0.4)]" onclick="saveFirewallIP()">Authorize IP</button>
        </div>
    </div>
</div>

<script>
    let currentEditorFile = ''; let editorInstance = null; let globalFilesData = []; let globalNotesData = []; let globalCloakData = []; let globalUsers = []; let currentPath = ''; 
    let currentAssetsPage = 1; const ASSETS_PER_PAGE = 100;
    const currentUser = '<?= $_SESSION['emerald_user'] ?>';
    let clipboard = { action: '', files: [], sourcePath: '' };

    const Toast = Swal.mixin({
        toast: true, position: 'bottom-end', showConfirmButton: false, timer: 3000,
        background: 'var(--bg-panel)', color: 'var(--text-main)', customClass: { popup: 'border border-[var(--border-color)] shadow-2xl rounded-2xl' }
    });

    const swalDark = Swal.mixin({
        background: 'var(--bg-panel)', color: 'var(--text-main)',
        customClass: {
            popup: 'border border-[var(--border-color)] shadow-2xl rounded-3xl',
            title: 'text-2xl font-extrabold text-primary mt-2',
            input: 'bg-[var(--bg-input)] border border-[var(--border-color)] text-primary rounded-xl p-4 text-center font-mono focus:border-brand-500 outline-none w-[80%] mx-auto',
            confirmButton: 'bg-brand-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-brand-500 transition-all shadow-[0_0_15px_rgba(14,165,233,0.3)] mx-2',
            cancelButton: 'bg-transparent border border-[var(--border-color)] text-muted px-8 py-3 rounded-xl font-bold hover:bg-[var(--bg-input)] transition-all mx-2',
            actions: 'mt-8'
        },
        buttonsStyling: false
    });

    function route(event, pathName) {
        event.preventDefault(); window.history.pushState({}, '', '/' + (pathName === 'dashboard' ? '' : pathName));
        const el = document.querySelector(`a[data-target="${pathName}"]`) || document.querySelector('#mainNav a');
        switchTab(pathName, el);
    }
    window.addEventListener('popstate', () => {
        let path = window.location.pathname.replace(/^\/|\/$/g, '');
        if(path === '') path = 'dashboard';
        const el = document.querySelector(`a[data-target="${path}"]`) || document.querySelector('#mainNav a');
        switchTab(path, el);
    });

    document.addEventListener('DOMContentLoaded', () => {
        const theme = localStorage.getItem('emerald_theme');
        if (theme === 'light') { document.documentElement.classList.remove('dark'); document.getElementById('themeText').innerText = 'Light Mode'; document.getElementById('themeCircle').style.transform = 'translateX(20px)'; document.querySelector('.theme-toggle-btn').classList.add('bg-emerald-500'); document.querySelector('.theme-toggle-btn').classList.remove('bg-gray-600'); }

        const dropOverlay = document.getElementById('dropOverlay');
        let dragTimer;
        document.body.addEventListener('dragover', function(e) {
            e.preventDefault();
            if(document.getElementById('view_files').classList.contains('active')){
                dropOverlay.classList.remove('hidden'); dropOverlay.classList.add('flex');
            }
        });
        document.body.addEventListener('dragleave', function(e) {
            if (e.relatedTarget === null) {
                dropOverlay.classList.add('hidden'); dropOverlay.classList.remove('flex');
            }
        });
        document.body.addEventListener('drop', async (e) => { 
            e.preventDefault();
            dropOverlay.classList.add('hidden'); dropOverlay.classList.remove('flex');
            if(document.getElementById('view_files').classList.contains('active')){
                const items = e.dataTransfer.items;
                if(items && items.length > 0 && items[0].webkitGetAsEntry) {
                    processDropUpload(items);
                } else { handleStandardUpload(e.dataTransfer.files, false); }
            }
        });

        loadUsers(); 
        
        editorInstance = CodeMirror.fromTextArea(document.getElementById("codeEditor"), {
            lineNumbers: true, theme: "monokai", mode: "htmlmixed", 
            matchBrackets: true, autoCloseBrackets: true, lineWrapping: true,
            extraKeys: { "Ctrl-F": "findPersistent", "Ctrl-S": function(cm) { saveFileEditor(); } }
        });
        if(theme === 'light') editorInstance.setOption('theme', 'default');

        document.addEventListener('keydown', e => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'f') {
                if (!document.getElementById('modalEditor').classList.contains('active')) {
                    e.preventDefault(); document.getElementById('globalSearch').focus();
                }
            }
        });

        let initialPath = window.location.pathname.replace(/^\/|\/$/g, '');
        if(initialPath === '') initialPath = 'dashboard';
        const el = document.querySelector(`a[data-target="${initialPath}"]`);
        if(el) switchTab(initialPath, el); else switchTab('dashboard', document.querySelector('#mainNav a'));
    });

    async function processDropUpload(items) {
        document.getElementById('modalUploadProgress').classList.add('active');
        document.getElementById('uploadProgressText').innerText = 'Scanning directories...';
        let currentUpload = 0;
        
        document.getElementById('uploadProgressText').innerText = 'Uploading payloads...';
        for (let i=0; i<items.length; i++) {
            const item = items[i].webkitGetAsEntry();
            if(item) await traverseFileTree(item, currentPath ? currentPath + '/' : '', function(){
                currentUpload++;
                const pct = Math.min(100, Math.round((currentUpload / (currentUpload+2)) * 100)); // Fake counter for recursive unknowns
                document.getElementById('uploadProgressBar').style.width = pct + '%';
            });
        }
        document.getElementById('modalUploadProgress').classList.remove('active');
        document.getElementById('uploadProgressBar').style.width = '0%';
        Toast.fire({ icon: 'success', title: 'Upload Completed' });
        loadFiles(currentPath);
    }

    async function traverseFileTree(item, path, callback = null) {
        if (item.isFile) {
            return new Promise((resolve) => {
                item.file(async (file) => {
                    const fd = new FormData(); fd.append('file', file); fd.append('path', currentPath); fd.append('relative_path', path + file.name);
                    await fetch('index.php?api=upload', { method: 'POST', body: fd });
                    if(callback) callback();
                    resolve();
                });
            });
        } else if (item.isDirectory) {
            let dirReader = item.createReader();
            return new Promise((resolve) => {
                dirReader.readEntries(async (entries) => {
                    for (let i=0; i<entries.length; i++) { await traverseFileTree(entries[i], path + item.name + "/", callback); }
                    resolve();
                });
            });
        }
    }

    async function handleStandardUpload(files, isFolderInput) {
        if (!files || files.length === 0) return;
        document.getElementById('modalUploadProgress').classList.add('active');
        for (let i = 0; i < files.length; i++) {
            document.getElementById('uploadProgressText').innerText = `Uploading ${isFolderInput ? (files[i].webkitRelativePath || files[i].name) : files[i].name}...`;
            const fd = new FormData(); fd.append('file', files[i]); fd.append('path', currentPath);
            if(isFolderInput) fd.append('relative_path', files[i].webkitRelativePath || files[i].name);
            await fetch('index.php?api=upload', { method: 'POST', body: fd });
            document.getElementById('uploadProgressBar').style.width = Math.round(((i+1)/files.length)*100) + '%';
        }
        document.getElementById('modalUploadProgress').classList.remove('active');
        document.getElementById('uploadProgressBar').style.width = '0%';
        Toast.fire({ icon: 'success', title: 'Upload completed' }); loadFiles(currentPath);
    }

    function toggleTheme() {
        const body = document.documentElement; const text = document.getElementById('themeText'); const circle = document.getElementById('themeCircle'); const btn = document.querySelector('.theme-toggle-btn');
        body.classList.toggle('dark');
        if (!body.classList.contains('dark')) {
            localStorage.setItem('emerald_theme', 'light'); text.innerText = 'Light Mode'; circle.style.transform = 'translateX(20px)'; btn.classList.add('bg-emerald-500'); btn.classList.remove('bg-gray-600');
            if(editorInstance) editorInstance.setOption('theme', 'default');
        } else {
            localStorage.setItem('emerald_theme', 'dark'); text.innerText = 'Dark Mode'; circle.style.transform = 'translateX(0)'; btn.classList.remove('bg-emerald-500'); btn.classList.add('bg-gray-600');
            if(editorInstance) editorInstance.setOption('theme', 'monokai');
        }
    }

    function stringToColor(str) { let hash = 0; for (let i = 0; i < str.length; i++) hash = str.charCodeAt(i) + ((hash << 5) - hash); return `hsl(${Math.abs(hash) % 360}, 70%, 50%)`; }

    function switchTab(tabId, el) {
        document.querySelectorAll('.nav-item').forEach(n => {
            n.className = 'nav-item w-full flex items-center gap-4 px-5 py-3.5 text-muted hover:bg-[var(--hover-bg)] hover:text-primary border border-transparent rounded-2xl font-medium transition-all';
            n.querySelector('i').classList.remove('text-[#0ea5e9]');
        });
        if(el) {
            el.className = 'nav-item w-full flex items-center gap-4 px-5 py-3.5 bg-brand-500/10 text-primary border border-brand-500/30 rounded-2xl font-semibold transition-all shadow-[0_0_15px_rgba(14,165,233,0.1)]';
            el.querySelector('i').classList.add('text-[#0ea5e9]');
        }

        document.querySelectorAll('.view-section').forEach(v => v.classList.remove('active'));
        document.getElementById('view_' + tabId).classList.add('active');
        
        const titles = {'dashboard': 'System Overview', 'files': 'Assets Manager', 'notes': 'Unified Containers', 'cloaking': 'SEO Cloaking', 'users': 'System Users', 'firewall': 'Firewall & IPs'};
        document.getElementById('pageTitle').innerText = titles[tabId];
        document.getElementById('breadcrumb').style.opacity = (tabId === 'files') ? '1' : '0';

        if (tabId === 'dashboard') loadSysInfo();
        if (tabId === 'files') loadFiles(currentPath);
        if (tabId === 'notes') loadNotes();
        if (tabId === 'cloaking') loadCloaking();
        if (tabId === 'users') loadUsers();
        if (tabId === 'firewall') loadFirewall();
    }

    function closeModal(id) { document.getElementById(id).classList.remove('active'); }

    async function loadSysInfo() {
        const res = await fetch('index.php?api=sys_info').then(r=>r.json()).catch(e => { return {stats:{}, logs:[], activity:[]}; });
        if(!res.stats) return;
        const stats = res.stats;
        
        document.getElementById('sysStatsList').innerHTML = `
            <li class="flex justify-between border-b border-[var(--border-color)] pb-2"><span class="text-muted">Domain</span><span class="text-[#0ea5e9]">${stats.domain}</span></li>
            <li class="flex justify-between border-b border-[var(--border-color)] pb-2"><span class="text-muted">Server IP</span><span class="text-primary">${stats.server_ip}</span></li>
            <li class="flex justify-between border-b border-[var(--border-color)] pb-2"><span class="text-muted">Software</span><span class="text-primary truncate max-w-[120px]" title="${stats.software}">${stats.software}</span></li>
            <li class="flex justify-between"><span class="text-muted">PHP Version</span><span class="text-blue-500">v${stats.php_version}</span></li>
        `;

        const logsList = document.getElementById('logsList'); logsList.innerHTML = '';
        if(Array.isArray(res.logs)) {
            res.logs.forEach(l => {
                const date = new Date(l.time * 1000).toLocaleString();
                const status = l.status === 'Success' ? '<span class="text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded text-[10px] font-bold shadow-inner uppercase tracking-wider">SUCCESS</span>' : '<span class="text-red-500 bg-red-500/10 px-2 py-1 rounded text-[10px] font-bold shadow-inner uppercase tracking-wider">FAILED</span>';
                logsList.innerHTML += `<tr class="border-b border-[var(--border-color)] hover:bg-[var(--hover-bg)] transition-colors"><td class="px-8 py-4 text-muted text-xs font-mono">${date}</td><td class="px-6 py-4 text-primary font-bold text-sm"><i class="fa-solid fa-user-shield text-muted mr-2 text-xs"></i>${l.user}</td><td class="px-6 py-4 text-[#0ea5e9] font-mono text-xs">${l.ip}</td><td class="px-6 py-4">${status}</td></tr>`;
            });
        }
        
        const activityList = document.getElementById('activityList'); 
        if(activityList && Array.isArray(res.activity)) {
            activityList.innerHTML = '';
            res.activity.forEach(a => {
                const date = new Date(a.time * 1000).toLocaleString();
                activityList.innerHTML += `<tr class="border-b border-[var(--border-color)] hover:bg-[var(--hover-bg)] transition-colors"><td class="px-8 py-4 text-muted text-xs font-mono">${date}</td><td class="px-6 py-4 text-primary font-bold text-sm"><i class="fa-solid fa-user-shield text-muted mr-2 text-xs"></i>${a.user}</td><td class="px-6 py-4 text-purple-500 font-mono text-xs">${a.detail}</td></tr>`;
            });
        }
    }

    let authTarget = { action: '', id: '', path: '', extra: '' };
    function promptAuth(action, id, path = '', extra = '') {
        authTarget = { action, id, path, extra }; document.getElementById('authPassword').value = ''; document.getElementById('modalAuthPrompt').classList.add('active');
    }

    async function executeAuthorizedAction() {
        const pass = document.getElementById('authPassword').value;
        if(!pass) return Toast.fire({icon:'error', title:'Passphrase empty'});
        
        const fd = new FormData(); fd.append('auth_pass', pass);
        
        if (authTarget.action === 'delete_file') {
            fd.append('file', authTarget.id); fd.append('path', authTarget.path);
            const res = await fetch('index.php?api=delete_file', { method: 'POST', body: fd }).then(r=>r.json());
            handleAuthResponse(res, () => loadFiles(currentPath));
        } else if (authTarget.action === 'multi_delete') {
            fd.append('files', JSON.stringify(authTarget.id)); fd.append('path', currentPath);
            const res = await fetch('index.php?api=multi_delete', { method: 'POST', body: fd }).then(r=>r.json());
            handleAuthResponse(res, () => { loadFiles(currentPath); resetSelection(); });
        } else if (authTarget.action === 'delete_note') {
            fd.append('id', authTarget.id);
            const res = await fetch('index.php?api=delete_note', { method: 'POST', body: fd }).then(r=>r.json());
            handleAuthResponse(res, () => loadNotes());
        } else if (authTarget.action === 'delete_cloaking') {
            fd.append('id', authTarget.id);
            const res = await fetch('index.php?api=delete_cloaking', { method: 'POST', body: fd }).then(r=>r.json());
            handleAuthResponse(res, () => loadCloaking());
        } else if (authTarget.action === 'delete_user') {
            fd.append('target_user', authTarget.id); fd.append('migrate_to', authTarget.extra);
            const res = await fetch('index.php?api=delete_user', { method: 'POST', body: fd }).then(r=>r.json());
            handleAuthResponse(res, () => { closeModal('modalDeleteUser'); loadUsers(); });
        } else if (authTarget.action === 'delete_firewall') {
            fd.append('id', authTarget.id);
            const res = await fetch('index.php?api=delete_firewall', { method: 'POST', body: fd }).then(r=>r.json());
            handleAuthResponse(res, () => loadFirewall());
        }
    }

    function handleAuthResponse(res, successCallback) {
        if(res.status === 'success') { closeModal('modalAuthPrompt'); Toast.fire({icon:'success', title:'Action Executed'}); successCallback(); } 
        else { swalDark.fire({icon:'error', title:'Denied', html:`<p class="text-muted">${res.message}</p>`}); }
    }

    function openProfileModal() {
        document.getElementById('profUsername').value = currentUser; document.getElementById('profPassword').value = '';
        document.getElementById('profSecQ').value = ''; document.getElementById('profSecA').value = '';
        document.getElementById('profileAvatarPreview').src = document.getElementById('sidebarAvatar').src;
        document.getElementById('modalProfile').classList.add('active');
    }

    function openAvatarZoom(src) {
        document.getElementById('avatarZoomImg').src = src; document.getElementById('modalAvatarZoom').classList.add('active');
    }

    function handleAvatarUpload(event) {
        const file = event.target.files[0]; if(!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas'); const MAX_WIDTH = 256; const MAX_HEIGHT = 256;
                let width = img.width; let height = img.height;
                if (width > height) { if (width > MAX_WIDTH) { height *= MAX_WIDTH / width; width = MAX_WIDTH; } } else { if (height > MAX_HEIGHT) { width *= MAX_HEIGHT / height; height = MAX_HEIGHT; } }
                canvas.width = width; canvas.height = height; const ctx = canvas.getContext('2d'); ctx.drawImage(img, 0, 0, width, height);
                const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
                document.getElementById('profileAvatarPreview').src = dataUrl; document.getElementById('profAvatarBase64').value = dataUrl;
            }
            img.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
    
    async function updateProfile() {
        const fd = new FormData();
        fd.append('username', document.getElementById('profUsername').value); fd.append('password', document.getElementById('profPassword').value);
        fd.append('avatar', document.getElementById('profAvatarBase64').value); fd.append('sec_q', document.getElementById('profSecQ').value); fd.append('sec_a', document.getElementById('profSecA').value);
        const res = await fetch('index.php?api=update_profile', { method: 'POST', body: fd }).then(r=>r.json());
        if(res.status === 'success') {
            Toast.fire({icon:'success', title:'Identity Updated'});
            if(res.new_user !== currentUser) window.location.reload(); 
            closeModal('modalProfile'); loadUsers(); 
        } else { Toast.fire({icon:'error', title:res.message}); }
    }

    // --- BULK FILE ACTIONS ---
    function toggleSelectAll(source) {
        const checkboxes = document.querySelectorAll('.file-sel');
        checkboxes.forEach(cb => cb.checked = source.checked);
        updateBulkToolbar();
    }

    function updateBulkToolbar() {
        const checked = document.querySelectorAll('.file-sel:checked');
        const toolbar = document.getElementById('bulkToolbar');
        const pasteToolbar = document.getElementById('pasteToolbar');
        
        if (clipboard.files.length > 0) {
            pasteToolbar.classList.remove('hidden'); pasteToolbar.classList.add('flex');
            document.getElementById('pasteInfo').innerText = `${clipboard.files.length} items to ${clipboard.action}`;
        } else {
            pasteToolbar.classList.add('hidden'); pasteToolbar.classList.remove('flex');
        }

        if (checked.length > 0) {
            toolbar.classList.remove('hidden'); toolbar.classList.add('flex');
            document.getElementById('selCount').innerText = checked.length;
        } else {
            toolbar.classList.add('hidden'); toolbar.classList.remove('flex');
        }
    }

    function bulkAction(action) {
        const checked = Array.from(document.querySelectorAll('.file-sel:checked')).map(cb => cb.value);
        if(checked.length === 0) return;
        
        if (action === 'delete') {
            swalDark.fire({
                title: 'Purge Selected?', html: `<p class="text-muted">Delete ${checked.length} selected items permanently?</p>`,
                showCancelButton: true, confirmButtonText: 'Yes, Purge'
            }).then((result) => {
                if(result.isConfirmed) { promptAuth('multi_delete', checked, currentPath); }
            });
        } else {
            clipboard = { action: action, files: checked, sourcePath: currentPath };
            resetSelection();
            Toast.fire({ icon: 'info', title: `${checked.length} items ready to ${action}` });
        }
    }

    async function executePaste() {
        if(clipboard.files.length === 0) return;
        Toast.fire({ icon: 'info', title: `Processing ${clipboard.action}...` });
        const fd = new FormData();
        fd.append('files', JSON.stringify(clipboard.files)); fd.append('source_path', clipboard.sourcePath); fd.append('target_path', currentPath); fd.append('mode', clipboard.action);
        
        const res = await fetch('index.php?api=paste_files', { method: 'POST', body: fd }).then(r=>r.json());
        if(res.status === 'success') {
            if(clipboard.action === 'cut') clipboard = { action: '', files: [], sourcePath: '' };
            Toast.fire({ icon: 'success', title: 'Action Successful' });
            loadFiles(currentPath); resetSelection();
        } else { Toast.fire({ icon: 'error', title: res.message }); }
    }

    function cancelPaste() { clipboard = { action: '', files: [], sourcePath: '' }; updateBulkToolbar(); }

    function resetSelection() {
        document.querySelectorAll('.file-sel').forEach(cb => cb.checked = false);
        const sa = document.getElementById('selectAllCheckbox'); if(sa) sa.checked = false;
        updateBulkToolbar();
    }

    // Paginated Load Files
    async function loadFiles(path = '') {
        currentPath = path;
        const res = await fetch(`index.php?api=list_files&path=${path}`).then(r => r.json()).catch(e => { return {files:[]}; });
        globalFilesData = res.files;
        
        const breadcrumb = document.getElementById('breadcrumb'); const btnUp = document.getElementById('btnNavUp');
        if(path) { breadcrumb.innerHTML = `<i class="fa-solid fa-house"></i> / root / <span class="text-[#0ea5e9]">${path}</span>`; btnUp.style.display = 'block'; } 
        else { breadcrumb.innerHTML = `<i class="fa-solid fa-house"></i> / root`; btnUp.style.display = 'none'; }
        renderFiles(res.files, 1); resetSelection();
    }

    function navigateUp() { if(!currentPath) return; let parts = currentPath.split('/'); parts.pop(); loadFiles(parts.join('/')); }
    function navigateDown(folder) { let newPath = currentPath ? currentPath + '/' + folder : folder; loadFiles(newPath); }

    function getExtIcon(ext) {
        ext = ext.toLowerCase();
        if(ext === 'zip') return '<i class="fa-solid fa-file-zipper text-orange-500 text-2xl drop-shadow-[0_0_8px_rgba(249,115,22,0.4)]"></i>';
        if(['php','html','css','js','json'].includes(ext)) return '<i class="fa-solid fa-file-code text-[#0ea5e9] text-2xl drop-shadow-[0_0_8px_rgba(14,165,233,0.4)]"></i>';
        if(['png','jpg','jpeg','gif'].includes(ext)) return '<i class="fa-solid fa-image text-[#10b981] text-2xl drop-shadow-[0_0_8px_rgba(16,185,129,0.4)]"></i>';
        if(ext === 'txt') return '<i class="fa-solid fa-file-lines text-muted text-2xl"></i>';
        return '<i class="fa-solid fa-file text-muted text-2xl"></i>';
    }

    function renderFiles(files, page = 1) {
        currentAssetsPage = page;
        const tbody = document.getElementById('filesList'); tbody.innerHTML = '';
        
        const sortedFiles = files.sort((a,b) => b.is_dir - a.is_dir || a.name.localeCompare(b.name));
        const totalItems = sortedFiles.length;
        const totalPages = Math.ceil(totalItems / ASSETS_PER_PAGE);
        const startIdx = (page - 1) * ASSETS_PER_PAGE;
        const endIdx = startIdx + ASSETS_PER_PAGE;
        const paginatedFiles = sortedFiles.slice(startIdx, endIdx);

        paginatedFiles.forEach(f => {
            const icon = f.is_dir ? '<i class="fa-solid fa-folder text-[#0ea5e9] text-3xl drop-shadow-[0_0_10px_rgba(14,165,233,0.5)]"></i>' : getExtIcon(f.ext);
            const baseUrl = window.location.origin; const fileRoute = currentPath ? `${currentPath}/${f.link_name}` : f.link_name;
            const cleanUrl = `${baseUrl}/view/${fileRoute}`;
            
            const color = stringToColor(f.owner);
            // Single Click Open
            const trClick = `onclick="if(${f.is_dir}) { navigateDown('${f.name}') } else { editFile('${f.name}') }"`;
            const cursor = 'cursor-pointer';
            const wgetCmd = `wget ${cleanUrl} -O ${f.name}`;

            tbody.innerHTML += `
                <tr class="hover:bg-[var(--hover-bg)] transition-colors group ${cursor}" ${trClick}>
                    <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                        <input type="checkbox" class="file-sel file-checkbox" value="${f.name}" onchange="updateBulkToolbar()">
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-5">
                            <div class="w-12 h-12 rounded-xl bg-[var(--bg-input)] border border-[var(--border-color)] flex items-center justify-center shrink-0 shadow-inner group-hover:scale-110 transition-transform">
                                ${icon}
                            </div>
                            <div>
                                <div class="font-bold text-primary tracking-wide text-base group-hover:text-[#0ea5e9] transition-colors">${f.name}</div>
                                ${!f.is_dir ? `<div class="text-xs text-muted font-mono mt-1 truncate max-w-[250px]" title="${cleanUrl}">${cleanUrl}</div>` : `<div class="text-xs text-gray-600 font-mono mt-1">Directory Component</div>`}
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-mono text-muted font-bold uppercase tracking-widest text-xs">${f.ext}</td>
                    <td class="px-6 py-4 text-muted font-medium text-sm">${f.size}</td>
                    <td class="px-6 py-4 text-muted font-mono text-xs">${f.modified}</td>
                    <td class="px-6 py-4" onclick="event.stopPropagation()">
                        <span class="px-2.5 py-1 rounded border text-[10px] font-bold uppercase tracking-wider bg-[var(--bg-input)] shadow-inner" style="color: ${color}; border-color: ${color}40;">${f.owner}</span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity" onclick="event.stopPropagation()">
                        ${(!f.is_dir && f.ext === 'zip') ? `<button onclick="attemptAction('unzip_file', '${f.name}', '${f.owner}')" class="w-10 h-10 rounded-xl bg-[var(--bg-input)] border border-[var(--border-color)] text-orange-500 hover:text-white hover:bg-orange-500 transition-all shadow-lg" title="Extract Zip"><i class="fa-solid fa-box-open"></i></button>` : ''}
                        ${(!f.is_dir && f.ext !== 'zip') ? `<button onclick="attemptAction('zip_file', '${f.name}', '${f.owner}')" class="w-10 h-10 rounded-xl bg-[var(--bg-input)] border border-[var(--border-color)] text-muted hover:text-orange-500 hover:bg-[var(--hover-bg)] transition-all shadow-lg" title="Compress File"><i class="fa-solid fa-file-zipper"></i></button>` : ''}
                        ${f.is_dir ? `<button onclick="attemptAction('zip_file', '${f.name}', '${f.owner}')" class="w-10 h-10 rounded-xl bg-[var(--bg-input)] border border-[var(--border-color)] text-muted hover:text-orange-500 hover:bg-[var(--hover-bg)] transition-all shadow-lg" title="Compress Directory"><i class="fa-solid fa-file-zipper"></i></button>` : ''}

                        ${!f.is_dir ? `<button onclick="copyToClipboard('${wgetCmd.replace(/'/g,"\\'").replace(/"/g,"&quot;")}')" class="w-10 h-10 rounded-xl bg-[var(--bg-input)] border border-[var(--border-color)] text-[#0ea5e9] hover:text-white hover:bg-[#0ea5e9] transition-all shadow-lg" title="Copy Wget Command"><i class="fa-solid fa-terminal"></i></button>` : ''}
                        ${!f.is_dir ? `<a href="${cleanUrl}" target="_blank" class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-[var(--bg-input)] border border-[var(--border-color)] text-[#10b981] hover:text-white hover:bg-[#10b981] transition-all shadow-lg" title="Open Link"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>` : ''}
                        ${!f.is_dir ? `<a href="/emerald_assets/${currentPath ? currentPath+'/' : ''}${f.name}" download class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-[var(--bg-input)] border border-[var(--border-color)] text-muted hover:text-primary hover:bg-[var(--hover-bg)] transition-all shadow-lg" title="Download"><i class="fa-solid fa-download"></i></a>` : ''}
                        <button onclick="attemptAction('delete_file', '${f.name}', '${f.owner}')" class="w-10 h-10 rounded-xl bg-[var(--bg-input)] border border-[var(--border-color)] text-muted hover:text-red-500 hover:bg-red-500/10 transition-all shadow-lg" title="Erase"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            `;
        });

        // Pagination UI
        let pagHtml = `<div class="flex items-center justify-between p-4 bg-[var(--bg-input)] border-t border-[var(--border-color)] shrink-0 z-10"><span class="text-sm text-muted font-bold">Showing ${startIdx + 1} to ${Math.min(endIdx, totalItems)} of ${totalItems} Assets</span><div class="flex gap-2">`;
        if (page > 1) pagHtml += `<button class="px-4 py-2 bg-[var(--bg-panel)] border border-[var(--border-color)] rounded-xl text-primary font-bold hover:bg-[var(--hover-bg)] transition-all btn-animated shadow-sm" onclick="renderFiles(globalFilesData, ${page - 1})">Previous</button>`;
        if (page < totalPages) pagHtml += `<button class="px-4 py-2 bg-[var(--bg-panel)] border border-[var(--border-color)] rounded-xl text-primary font-bold hover:bg-[var(--hover-bg)] transition-all btn-animated shadow-sm" onclick="renderFiles(globalFilesData, ${page + 1})">Next</button>`;
        pagHtml += `</div></div>`;
        document.getElementById('assetsPagination').innerHTML = pagHtml;
    }

    async function promptCreateFolder() {
        const { value: folderName } = await swalDark.fire({ 
            title: 'New Directory', html: '<p class="text-sm text-muted mb-6">Enter a name for the new secure directory.</p>',
            input: 'text', inputPlaceholder: 'e.g., project_assets', showCancelButton: true 
        });
        if (folderName) {
            const fd = new FormData(); fd.append('folder', folderName); fd.append('path', currentPath);
            const res = await fetch('index.php?api=create_folder', { method: 'POST', body: fd }).then(r => r.json());
            if(res.status === 'success') loadFiles(currentPath); else Toast.fire({icon:'error', title:res.message});
        }
    }

    async function promptCreateFile() {
        const { value: fileName } = await swalDark.fire({ 
            title: 'New File', html: '<p class="text-sm text-muted mb-6">Enter filename with extension.</p>',
            input: 'text', inputPlaceholder: 'script.php', showCancelButton: true 
        });
        if (fileName) {
            const fd = new FormData(); fd.append('file', fileName); fd.append('path', currentPath);
            const res = await fetch('index.php?api=create_file', { method: 'POST', body: fd }).then(r => r.json());
            if(res.status === 'success') { loadFiles(currentPath); editFile(fileName); } else Toast.fire({icon:'error', title:res.message});
        }
    }

    async function editFile(filename) {
        const fd = new FormData(); fd.append('file', filename); fd.append('path', currentPath);
        const res = await fetch('index.php?api=read_file', { method: 'POST', body: fd }).then(r => r.json());
        if (res.status === 'success') {
            currentEditorFile = filename;
            const ext = filename.split('.').pop().toLowerCase();
            document.getElementById('editorTitle').innerText = filename;
            document.getElementById('editorModified').innerText = res.modified;
            document.getElementById('editorSize').innerText = res.size;
            
            let mode = "htmlmixed"; if (ext === 'php') mode = "php"; else if (ext === 'js') mode = "javascript"; else if (ext === 'css') mode = "css";
            editorInstance.setOption("mode", mode);
            editorInstance.setValue(res.content);
            document.getElementById('modalEditor').classList.add('active');
            setTimeout(() => editorInstance.refresh(), 100);
        }
    }

    async function saveFileEditor() {
        const fd = new FormData(); fd.append('file', currentEditorFile); fd.append('path', currentPath); fd.append('content', editorInstance.getValue());
        await fetch('index.php?api=save_file', { method: 'POST', body: fd });
        Toast.fire({ icon: 'success', title: 'Source synchronized' }); closeModal('modalEditor'); loadFiles(currentPath);
    }

    // --- CONTAINERS ---
    async function loadNotes() {
        const res = await fetch('index.php?api=list_notes').then(r => r.json()).catch(e => { return []; });
        globalNotesData = res; renderNotes(res);
    }

    function renderNotes(notes) {
        const listArea = document.getElementById('notesListArea'); listArea.innerHTML = '';
        notes.sort((a,b) => b.timestamp - a.timestamp).forEach(note => {
            const avatarUrl = note.avatar || `https://ui-avatars.com/api/?name=${note.owner}&background=0ea5e9&color=fff&rounded=true&bold=true`;
            const date = new Date(note.timestamp * 1000).toLocaleString();
            
            listArea.innerHTML += `
                <div class="flex items-center p-5 bg-[var(--bg-input)] border border-[var(--border-color)] rounded-2xl hover:bg-[var(--hover-bg)] cursor-pointer transition-all shadow-[var(--shadow)] group hover:-translate-y-1" onclick="viewContainer('${note.id}')">
                    <img src="${avatarUrl}" class="w-14 h-14 rounded-full border-2 border-brand-500/50 object-cover shadow-[0_0_10px_rgba(14,165,233,0.3)] mr-5">
                    <div class="flex-1 overflow-hidden">
                        <h3 class="font-extrabold text-primary text-xl tracking-wide truncate group-hover:text-brand-500 transition-colors">${note.title}</h3>
                        <p class="text-xs text-muted font-mono mt-1"><i class="fa-regular fa-clock mr-1"></i> ${date}</p>
                    </div>
                </div>`;
        });
    }

    window.viewContainer = function(id) {
        const note = globalNotesData.find(n => n.id === id);
        if(!note) return;
        
        let data = { auth: {host:'', user:'', pass:'', dir:''}, list: '' };
        try { data = JSON.parse(note.data); } catch(e){}

        document.getElementById('viewContainerTitle').innerText = note.title;
        document.getElementById('viewContainerOwner').innerHTML = `<i class="fa-solid fa-user-shield mr-1"></i> ${note.owner}`;
        document.getElementById('viewContainerAvatar').src = note.avatar || `https://ui-avatars.com/api/?name=${note.owner}&background=0ea5e9&color=fff&rounded=true&bold=true`;
        
        document.getElementById('viewContainerActions').innerHTML = `
            <button onclick="closeModal('modalViewContainer'); editContainer('${note.id}')" class="bg-[var(--bg-input)] border border-[var(--border-color)] text-brand-500 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-brand-500 hover:text-white transition-all btn-animated"><i class="fa-solid fa-pen mr-2"></i>Edit</button>
            <button onclick="closeModal('modalViewContainer'); promptAuth('delete_note', '${note.id}')" class="bg-[var(--bg-input)] border border-[var(--border-color)] text-red-500 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-red-500 hover:text-white transition-all btn-animated"><i class="fa-solid fa-trash mr-2"></i>Delete</button>
            <button onclick="closeModal('modalViewContainer')" class="bg-[var(--hover-bg)] border border-[var(--border-color)] text-muted px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-[var(--bg-input)] hover:text-primary transition-all ml-2 btn-animated">Close</button>
        `;

        let authHTML = '';
        if(data.auth.host || data.auth.user || data.auth.dir) {
            authHTML = `
                <div class="mb-6 bg-[var(--bg-input)] border border-[var(--border-color)] rounded-3xl p-6 shadow-[var(--shadow)]">
                    <div class="flex items-center gap-3 mb-4 border-b border-[var(--border-color)] pb-3">
                        <i class="fa-solid fa-server text-purple-500"></i><span class="text-sm font-bold text-primary tracking-widest uppercase">Authentication Array</span>
                    </div>
                    <ul class="text-sm font-mono text-muted space-y-3">
                        <li class="flex items-center bg-[var(--bg-panel)] p-3 rounded-xl border border-[var(--border-color)]"><span class="text-muted font-bold w-20 shrink-0">HOST</span><span class="auth-secret truncate text-purple-500 font-bold" title="Double click to reveal" ondblclick="this.classList.toggle('revealed')">${data.auth.host || '-'}</span></li>
                        <li class="flex items-center bg-[var(--bg-panel)] p-3 rounded-xl border border-[var(--border-color)]"><span class="text-muted font-bold w-20 shrink-0">USER</span><span class="auth-secret truncate text-blue-500 font-bold" title="Double click to reveal" ondblclick="this.classList.toggle('revealed')">${data.auth.user || '-'}</span></li>
                        <li class="flex items-center bg-[var(--bg-panel)] p-3 rounded-xl border border-[var(--border-color)]"><span class="text-muted font-bold w-20 shrink-0">PASS</span><span class="auth-secret truncate text-red-500 font-bold" title="Double click to reveal" ondblclick="this.classList.toggle('revealed')">${data.auth.pass || '-'}</span></li>
                        <li class="flex items-center bg-[var(--bg-panel)] p-3 rounded-xl border border-[var(--border-color)]"><span class="text-muted font-bold w-20 shrink-0">DIR</span><span class="auth-secret truncate text-brand-500 font-bold" title="Double click to reveal" ondblclick="this.classList.toggle('revealed')">${data.auth.dir || '-'}</span></li>
                    </ul>
                </div>
            `;
        }

        let listHTML = ''; let gsocketHTML = '';
        if(data.list && data.list.trim() !== '') {
            const lines = data.list.split('\n');
            lines.forEach(line => {
                let cl = line.trim(); if(!cl) return;
                if(cl.startsWith('http://') || cl.startsWith('https://')) {
                    listHTML += `<div class="flex items-center gap-3 py-3 border-b border-[var(--border-color)] last:border-0 hover:bg-[var(--hover-bg)] transition-colors px-4 rounded-lg group"><a href="${cl}" target="_blank" class="text-sm truncate text-[#10b981] hover:text-[#059669] font-mono flex-1 transition-colors hover:underline">${cl}</a><i class="fa-solid fa-arrow-up-right-from-square text-[#10b981] text-[10px] opacity-0 group-hover:opacity-100 transition-opacity"></i></div>`;
                } else if(cl.includes('gs-netcat') || cl.startsWith('S=')) {
                    gsocketHTML += `<div class="flex items-center gap-3 py-3 border-b border-[var(--border-color)] last:border-0 hover:bg-[var(--hover-bg)] transition-colors px-4 rounded-lg"><i class="fa-solid fa-terminal text-[#0ea5e9] text-[10px]"></i><span class="text-sm truncate text-brand-500 font-mono flex-1 click-to-copy" onclick="copyToClipboard('${cl.replace(/'/g,"\\'").replace(/"/g,"&quot;")}')">${cl}</span></div>`;
                } else { 
                    listHTML += `<div class="flex items-center gap-3 py-3 border-b border-[var(--border-color)] last:border-0 hover:bg-[var(--hover-bg)] transition-colors px-4 rounded-lg"><i class="fa-solid fa-align-left text-muted text-[10px]"></i><span class="text-sm truncate text-primary font-mono flex-1 select-all">${cl}</span></div>`;
                }
            });
        }

        document.getElementById('viewContainerContent').innerHTML = `
            ${authHTML}
            ${listHTML ? `<div class="bg-[var(--bg-input)] border border-[var(--border-color)] rounded-3xl p-6 shadow-[var(--shadow)] mb-6"><div class="text-sm font-bold text-primary tracking-widest mb-4 flex items-center gap-3 border-b border-[var(--border-color)] pb-3 uppercase"><i class="fa-solid fa-list text-[#10b981]"></i> Assets / Texts</div>${listHTML}</div>` : ''}
            ${gsocketHTML ? `<div class="bg-[var(--bg-input)] border border-brand-500/30 rounded-3xl p-6 shadow-[var(--shadow)]"><div class="text-sm font-bold text-primary tracking-widest mb-4 flex items-center gap-3 border-b border-[var(--border-color)] pb-3 uppercase"><i class="fa-solid fa-terminal text-[#0ea5e9]"></i> Gsocket Commands</div>${gsocketHTML}</div>` : ''}
        `;
        document.getElementById('modalViewContainer').classList.add('active');
    };

    window.editContainer = function(id) { 
        const note = globalNotesData.find(n => n.id === id); 
        if(!note) return;
        document.getElementById('containerId').value = note.id; document.getElementById('containerTitle').value = note.title;
        let data = { auth: {host:'', user:'', pass:'', dir:''}, list: '' };
        try { data = JSON.parse(note.data); } catch(e){}
        document.getElementById('containerHost').value = data.auth.host; document.getElementById('containerUser').value = data.auth.user; 
        document.getElementById('containerPass').value = data.auth.pass; document.getElementById('containerDir').value = data.auth.dir;
        document.getElementById('containerTextList').value = data.list;
        document.getElementById('modalContainer').classList.add('active');
    };

    function openContainerModal() {
        document.getElementById('containerId').value = ''; document.getElementById('containerTitle').value = '';
        document.getElementById('containerHost').value = ''; document.getElementById('containerUser').value = ''; 
        document.getElementById('containerPass').value = ''; document.getElementById('containerDir').value = '';
        document.getElementById('containerTextList').value = '';
        document.getElementById('modalContainer').classList.add('active');
    }

    async function saveContainer() {
        const fd = new FormData();
        fd.append('id', document.getElementById('containerId').value); fd.append('title', document.getElementById('containerTitle').value || 'Untitled'); 
        fd.append('host', document.getElementById('containerHost').value); fd.append('user', document.getElementById('containerUser').value);
        fd.append('pass', document.getElementById('containerPass').value); fd.append('dir', document.getElementById('containerDir').value);
        fd.append('text_list', document.getElementById('containerTextList').value);
        await fetch('index.php?api=save_note', { method: 'POST', body: fd }); 
        closeModal('modalContainer'); loadNotes(); Toast.fire({icon:'success', title:'Container Built'});
    }

    // --- CLOAKING ---
    async function loadCloaking() {
        const res = await fetch('index.php?api=list_cloaking').then(r => r.json()).catch(e=>{return [];});
        globalCloakData = res; renderCloaking(res);
    }

    function renderCloaking(cloaks) {
        const grid = document.getElementById('cloakingGrid'); grid.innerHTML = '';
        cloaks.sort((a,b) => b.timestamp - a.timestamp).forEach(c => {
            const isGlobal = c.type === 'global';
            const badgeClass = isGlobal ? 'bg-purple-500/10 text-purple-500 border-purple-500/30' : 'bg-brand-500/10 text-brand-500 border-brand-500/30';
            const avatarUrl = c.avatar || `https://ui-avatars.com/api/?name=${c.owner}&background=0ea5e9&color=fff&rounded=true&bold=true`;
            
            grid.innerHTML += `
                <div class="bg-[var(--bg-input)] border border-[var(--border-color)] rounded-3xl p-6 shadow-[var(--shadow)] relative group hover:-translate-y-2 transition-all cursor-pointer" onclick="viewCloaking('${c.id}')">
                    <div class="absolute top-6 right-6 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity" onclick="event.stopPropagation()">
                        <button onclick="editCloaking('${c.id}')" class="w-10 h-10 rounded-xl bg-[var(--bg-panel)] border border-[var(--border-color)] text-muted hover:text-brand-500 flex items-center justify-center shadow-lg"><i class="fa-solid fa-pen"></i></button>
                        <button onclick="promptAuth('delete_cloaking', '${c.id}')" class="w-10 h-10 rounded-xl bg-[var(--bg-panel)] border border-[var(--border-color)] text-muted hover:text-red-500 flex items-center justify-center shadow-lg"><i class="fa-solid fa-trash"></i></button>
                    </div>
                    <div class="flex items-center gap-4 mb-5">
                        <img src="${avatarUrl}" class="w-12 h-12 rounded-xl object-cover border border-[var(--border-color)] shadow-inner">
                        <div>
                            <h3 class="font-bold text-primary text-xl">${c.domain}</h3>
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest border shadow-inner ${badgeClass}">${c.type}</span>
                        </div>
                    </div>
                    <div class="bg-[var(--bg-panel)] border border-[var(--border-color)] rounded-2xl p-4 font-mono text-sm text-brand-500 truncate mb-5 shadow-inner">
                        <span class="text-muted">Path:</span> ${c.path}
                    </div>
                    <div class="text-[11px] text-muted uppercase tracking-widest font-bold">Owner: <span class="text-primary">${c.owner}</span></div>
                </div>
            `;
        });
    }

    window.viewCloaking = function(id) {
        const c = globalCloakData.find(x => x.id === id); if(!c) return;
        document.getElementById('viewContainerTitle').innerText = c.domain;
        document.getElementById('viewContainerOwner').innerHTML = `<i class="fa-solid fa-user-shield mr-1"></i> ${c.owner}`;
        document.getElementById('viewContainerAvatar').src = c.avatar || `https://ui-avatars.com/api/?name=${c.owner}&background=0ea5e9&color=fff&rounded=true&bold=true`;
        
        document.getElementById('viewContainerActions').innerHTML = `
            <button onclick="closeModal('modalViewContainer'); editCloaking('${c.id}')" class="bg-[var(--bg-input)] border border-brand-500/30 text-brand-500 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-brand-500 hover:text-white transition-all btn-animated"><i class="fa-solid fa-pen mr-2"></i>Edit</button>
            <button onclick="closeModal('modalViewContainer'); promptAuth('delete_cloaking', '${c.id}')" class="bg-[var(--bg-input)] border border-red-500/30 text-red-500 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-red-500 hover:text-white transition-all btn-animated"><i class="fa-solid fa-trash mr-2"></i>Delete</button>
            <button onclick="closeModal('modalViewContainer')" class="bg-[var(--hover-bg)] border border-[var(--border-color)] text-muted px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-[var(--bg-input)] hover:text-primary transition-all ml-2 btn-animated">Close</button>
        `;

        document.getElementById('viewContainerContent').innerHTML = `
            <div class="bg-[var(--bg-input)] border border-[var(--border-color)] rounded-3xl p-6 shadow-lg mb-6">
                <div class="flex items-center gap-3 mb-4 border-b border-[var(--border-color)] pb-3">
                    <i class="fa-solid fa-globe text-purple-500"></i><span class="text-sm font-bold text-primary tracking-widest uppercase">Target Details</span>
                </div>
                <ul class="text-sm font-mono text-muted space-y-3">
                    <li class="flex items-center bg-[var(--bg-panel)] p-3 rounded-xl border border-[var(--border-color)]"><span class="text-muted font-bold w-24 shrink-0">DOMAIN</span><span class="click-to-copy truncate text-primary font-bold" onclick="copyToClipboard('${c.domain}')">${c.domain}</span></li>
                    <li class="flex items-center bg-[var(--bg-panel)] p-3 rounded-xl border border-[var(--border-color)]"><span class="text-muted font-bold w-24 shrink-0">PATH</span><span class="click-to-copy truncate text-primary font-bold" onclick="copyToClipboard('${c.path}')">${c.path}</span></li>
                    <li class="flex items-center bg-[var(--bg-panel)] p-3 rounded-xl border border-[var(--border-color)]"><span class="text-muted font-bold w-24 shrink-0">VISIBILITY</span><span class="truncate text-primary font-bold uppercase">${c.type}</span></li>
                </ul>
            </div>
            <div class="bg-[var(--bg-input)] border border-[var(--border-color)] rounded-3xl p-6 shadow-lg mb-6">
                <div class="flex items-center gap-3 mb-4 border-b border-[var(--border-color)] pb-3">
                    <i class="fa-solid fa-code text-brand-500"></i><span class="text-sm font-bold text-primary tracking-widest uppercase">Payload Content</span>
                </div>
                <pre class="bg-[#1e1e1e] border border-[var(--border-color)] text-[#d4d4d4] p-4 rounded-xl font-mono text-sm overflow-x-auto whitespace-pre-wrap select-all cursor-text">${c.content}</pre>
            </div>
        `;
        document.getElementById('modalViewContainer').classList.add('active');
    };

    function openCloakingModal(cloak = null) {
        document.getElementById('cloakId').value = cloak ? cloak.id : ''; document.getElementById('cloakDomain').value = cloak ? cloak.domain : '';
        document.getElementById('cloakPath').value = cloak ? cloak.path : ''; document.getElementById('cloakType').value = cloak ? cloak.type : 'personal';
        document.getElementById('cloakContent').value = cloak ? cloak.content : ''; document.getElementById('modalCloaking').classList.add('active');
    }

    function editCloaking(id) { const c = globalCloakData.find(x => x.id === id); if(c) openCloakingModal(c); }

    async function saveCloaking() {
        const fd = new FormData(); fd.append('id', document.getElementById('cloakId').value); fd.append('domain', document.getElementById('cloakDomain').value);
        fd.append('path', document.getElementById('cloakPath').value); fd.append('type', document.getElementById('cloakType').value); fd.append('content', document.getElementById('cloakContent').value);
        await fetch('index.php?api=save_cloaking', { method: 'POST', body: fd }); closeModal('modalCloaking'); loadCloaking(); Toast.fire({icon:'success', title:'Cloak Deployed'});
    }

    // --- USERS ---
    async function loadUsers() {
        const res = await fetch('index.php?api=list_users').then(r => r.json()).catch(e=>{return [];});
        globalUsers = res;
        const tbody = document.getElementById('usersList'); tbody.innerHTML = '';
        res.forEach(u => { 
            let roleClass = 'role-text-guest';
            if(u.role === 'owner') roleClass = 'role-text-owner';
            if(u.role === 'admin') roleClass = 'role-text-admin';

            const avatarUrl = u.avatar ? u.avatar : `https://ui-avatars.com/api/?name=${u.username}&background=0ea5e9&color=fff&rounded=true&bold=true`;
            if(u.username === currentUser) {
                document.getElementById('sidebarAvatar').src = avatarUrl;
                document.getElementById('sidebarRole').innerText = u.role;
                document.getElementById('sidebarRole').className = `text-[10px] font-mono mt-1 uppercase tracking-wider ${roleClass}`;
                document.getElementById('headerRoleBadge').innerText = u.role;
                document.getElementById('headerRoleBadge').className = `px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[var(--hover-bg)] ${roleClass}`;
            }

            const isOnline = (Math.floor(Date.now()/1000) - (u.last_active || 0)) < 30; 
            const statusDot = isOnline ? '<div class="w-4 h-4 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.8)] absolute -bottom-1 -right-1 border-2 border-[var(--bg-input)]"></div>' : '<div class="w-4 h-4 rounded-full bg-gray-600 absolute -bottom-1 -right-1 border-2 border-[var(--bg-input)]"></div>';

            tbody.innerHTML += `
            <tr class="border-b border-[var(--border-color)] hover:bg-[var(--hover-bg)] transition-colors group">
                <td class="px-8 py-5 font-bold text-primary flex items-center gap-5">
                    <div class="relative cursor-pointer hover:opacity-80 transition-opacity" onclick="openAvatarZoom('${avatarUrl}')">
                        <img src="${avatarUrl}" class="w-14 h-14 rounded-full border-2 border-brand-500/50 shadow-[0_0_15px_rgba(14,165,233,0.3)] object-cover">
                        <div id="user_dot_${u.username}">${statusDot}</div>
                    </div>
                    <div>
                        <span class="text-xl block tracking-wide">${u.username}</span>
                        <span id="user_status_${u.username}" class="text-[10px] font-mono uppercase tracking-widest ${isOnline ? 'text-emerald-500' : 'text-muted'}"><i class="fa-solid fa-circle text-[8px] mr-1"></i>${isOnline ? 'Online' : 'Offline'}</span>
                    </div>
                </td>
                <td class="px-6 py-5">
                    <span class="px-5 py-2 bg-[var(--bg-input)] border border-[var(--border-color)] rounded-xl text-xs tracking-widest uppercase shadow-inner ${roleClass}"><i class="fa-solid fa-shield-halved mr-2"></i>${u.role}</span>
                </td>
                <td class="px-6 py-5 text-right opacity-0 group-hover:opacity-100 transition-opacity">
                    ${u.username !== currentUser ? `<button onclick="promptDeleteUser('${u.username}')" class="w-10 h-10 rounded-xl bg-[var(--bg-input)] border border-[var(--border-color)] text-muted hover:text-red-500 hover:border-red-500/50 hover:bg-red-500/10 transition-all shadow-lg" title="Delete Identity"><i class="fa-solid fa-user-minus"></i></button>` : ''}
                </td>
            </tr>`; 
        });
    }

    function promptDeleteUser(username) {
        document.getElementById('delTargetUser').value = username;
        const select = document.getElementById('delMigrateTo');
        select.innerHTML = '<option value="">-- Delete All Data --</option>';
        globalUsers.forEach(u => {
            if(u.username !== username) select.innerHTML += `<option value="${u.username}">Migrate Data To: ${u.username}</option>`;
        });
        document.getElementById('modalDeleteUser').classList.add('active');
    }

    function executeDeleteUser() {
        const target = document.getElementById('delTargetUser').value;
        const migrate = document.getElementById('delMigrateTo').value;
        promptAuth('delete_user', target, '', migrate);
    }

    function openUserModal() {
        if(getMyRole() !== 'owner') return swalDark.fire({icon:'error', title:'Denied', html:'<p class="text-muted">Only Owners can register users.</p>'});
        document.getElementById('modalUser').classList.add('active');
    }

    async function saveUser() {
        const user = document.getElementById('newUserName').value; const pass = document.getElementById('newUserPass').value; const role = document.getElementById('newUserRole').value;
        if(!user || !pass) return; const fd = new FormData(); fd.append('username', user); fd.append('password', pass); fd.append('role', role);
        const res = await fetch('index.php?api=add_user', { method: 'POST', body: fd }).then(r => r.json());
        if(res.status === 'success') { closeModal('modalUser'); loadUsers(); Toast.fire({icon:'success',title:'Identity Registered'}); } else Toast.fire({icon:'error', title:res.message});
    }

    // --- FIREWALL ---
    async function loadFirewall() {
        const res = await fetch('index.php?api=list_firewall').then(r => r.json()).catch(e=>{return [];});
        const tbody = document.getElementById('firewallList'); tbody.innerHTML = '';
        res.forEach(fw => { 
            const date = new Date(fw.added * 1000).toLocaleString();
            tbody.innerHTML += `
            <tr class="border-b border-[var(--border-color)] hover:bg-[var(--hover-bg)] transition-colors group">
                <td class="px-8 py-5 text-emerald-500 font-bold text-lg">${fw.ip}</td>
                <td class="px-6 py-5 text-primary font-sans text-sm">${fw.note}</td>
                <td class="px-6 py-5 text-muted text-xs">${date}</td>
                <td class="px-6 py-5 text-right opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="promptAuth('delete_firewall', '${fw.id}')" class="w-10 h-10 rounded-xl bg-[var(--bg-input)] border border-[var(--border-color)] text-muted hover:text-red-500 hover:border-red-500/50 hover:bg-red-500/10 transition-all shadow-lg" title="Remove IP"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>`; 
        });
    }

    function openFirewallModal() {
        if(getMyRole() !== 'owner') return swalDark.fire({icon:'error', title:'Denied', html:'<p class="text-muted">Only Owners can manage Firewall.</p>'});
        document.getElementById('fwIP').value = ''; document.getElementById('fwNote').value = '';
        document.getElementById('modalFirewall').classList.add('active');
    }

    async function saveFirewallIP() {
        const ip = document.getElementById('fwIP').value; const note = document.getElementById('fwNote').value;
        if(!ip) return; const fd = new FormData(); fd.append('ip', ip); fd.append('note', note);
        const res = await fetch('index.php?api=add_firewall', { method: 'POST', body: fd }).then(r => r.json());
        if(res.status === 'success') { closeModal('modalFirewall'); loadFirewall(); Toast.fire({icon:'success',title:'IP Whitelisted'}); } else Toast.fire({icon:'error', title:res.message});
    }

    function copyToClipboard(text) { if(!text || text === '-') return; navigator.clipboard.writeText(text); Toast.fire({ icon: 'success', title: 'Copied' }); }
    
    function performSearch() {
        const q = document.getElementById('globalSearch').value.toLowerCase();
        if (document.getElementById('view_files').classList.contains('active')) renderFiles(globalFilesData.filter(f => f.name.toLowerCase().includes(q)));
        if (document.getElementById('view_notes').classList.contains('active')) renderNotes(globalNotesData.filter(n => n.title.toLowerCase().includes(q) || n.data.toLowerCase().includes(q)));
        if (document.getElementById('view_cloaking').classList.contains('active')) renderCloaking(globalCloakData.filter(c => c.domain.toLowerCase().includes(q) || c.path.toLowerCase().includes(q)));
    }

    // Interval Heartbeat Ringan (Hanya DOM updates)
    setInterval(async () => { 
        const res = await fetch('index.php?api=heartbeat').then(r=>r.json());
        if(res.status === 'success' && res.online_data && document.getElementById('view_users').classList.contains('active')) {
            const now = Math.floor(Date.now() / 1000);
            globalUsers.forEach(u => {
                if(res.online_data[u.username]) u.last_active = res.online_data[u.username];
                const isOnline = (now - (u.last_active || 0)) < 30; 
                
                if(u.username === currentUser) {
                    const sbDot = document.getElementById('sidebarDot');
                    if(sbDot) sbDot.className = `w-3.5 h-3.5 rounded-full absolute -bottom-1 -right-1 border-2 border-[var(--bg-panel)] ${isOnline ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.8)]' : 'bg-gray-600'}`;
                }

                const uDot = document.getElementById(`user_dot_${u.username}`);
                if(uDot) uDot.innerHTML = isOnline ? '<div class="w-4 h-4 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.8)] absolute -bottom-1 -right-1 border-2 border-[var(--bg-input)]"></div>' : '<div class="w-4 h-4 rounded-full bg-gray-600 absolute -bottom-1 -right-1 border-2 border-[var(--bg-input)]"></div>';
                
                const uText = document.getElementById(`user_status_${u.username}`);
                if(uText) {
                    uText.innerHTML = isOnline ? '<i class="fa-solid fa-circle text-[8px] mr-1"></i>Online' : '<i class="fa-solid fa-circle text-[8px] mr-1"></i>Offline';
                    uText.className = `text-[10px] font-mono uppercase tracking-widest ${isOnline ? 'text-emerald-500' : 'text-muted'}`;
                }
            });
        }
    }, 15000);
</script>
</body>
</html>
