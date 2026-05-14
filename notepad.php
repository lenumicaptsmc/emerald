<?php
require_once __DIR__ . '/core/config.php';

if (isset($_GET['api'])) {
    header('Content-Type: application/json');
    $users_data = getDB($users_db);
    
    if ($_GET['api'] == 'list_users') {
        $output = [];
        foreach($users_data as $uname => $udata) {
            $output[] = [
                'username' => $uname,
                'avatar' => !empty($udata['avatar']) ? $udata['avatar'] : "https://ui-avatars.com/api/?name=".urlencode($uname)."&background=0ea5e9&color=fff&rounded=true&bold=true",
                'last_active' => $udata['last_active'] ?? 0
            ];
        }
        echo json_encode($output); exit; 
    }
    
    if ($_GET['api'] == 'verify_access') {
        $user = sanitize($_POST['user']); $pass = $_POST['password'];
        if (verifyUserPassword($user, $pass)) { echo json_encode(['status' => 'success']); } 
        else { echo json_encode(['status' => 'error', 'message' => 'Invalid password.']); }
        exit;
    }
    
    if ($_GET['api'] == 'list_notes') {
        $user = sanitize($_POST['user']); $user_dir = DIR_NOTEPAD . "/{$user}";
        if(!is_dir($user_dir)) mkdir($user_dir, 0755, true);
        clearstatcache();
        $notes = [];
        foreach(scandir($user_dir) as $file) { if($file !== '.' && $file !== '..') $notes[] = $file; }
        echo json_encode(['status' => 'success', 'notes' => array_values($notes)]); exit;
    }

    if ($_GET['api'] == 'load_note') {
        $user = sanitize($_POST['user']); $file = sanitize($_POST['file']);
        $path = DIR_NOTEPAD . "/{$user}/{$file}";
        $content = file_exists($path) ? file_get_contents($path) : '';
        echo json_encode(['status' => 'success', 'content' => $content]); exit;
    }
    
    if ($_GET['api'] == 'save_note') {
        $user = sanitize($_POST['user']); $file = sanitize($_POST['file']); $content = $_POST['content']; $pass = $_POST['password'];
        if (!verifyUserPassword($user, $pass)) exit(json_encode(['status' => 'error']));
        $user_dir = DIR_NOTEPAD . "/{$user}";
        if(!is_dir($user_dir)) mkdir($user_dir, 0755, true);
        file_put_contents("{$user_dir}/{$file}", $content);
        echo json_encode(['status' => 'success']); exit;
    }
    
    if ($_GET['api'] == 'create_note') {
        $user = sanitize($_POST['user']); $file = sanitize($_POST['file']); $pass = $_POST['password'];
        if (!verifyUserPassword($user, $pass)) exit(json_encode(['status' => 'error']));
        if(strpos($file, '.txt') === false) $file .= '.txt';
        $user_dir = DIR_NOTEPAD . "/{$user}";
        if(!is_dir($user_dir)) mkdir($user_dir, 0755, true);
        if(!file_exists("{$user_dir}/{$file}")) { file_put_contents("{$user_dir}/{$file}", ''); }
        echo json_encode(['status' => 'success']); exit;
    }

    if ($_GET['api'] == 'delete_note') {
        $user = sanitize($_POST['user']); $file = sanitize($_POST['file']); $pass = $_POST['password'];
        if (!verifyUserPassword($user, $pass)) exit(json_encode(['status' => 'error']));
        $path = DIR_NOTEPAD . "/{$user}/{$file}";
        if(file_exists($path)) unlink($path);
        echo json_encode(['status' => 'success']); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMERALD - Public Notepad</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <style>
        body { background-color: #f8fafc; color: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif; overflow: hidden; transition: filter 0.5s ease; }
        .glass-panel { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(24px); border-bottom: 1px solid rgba(226, 232, 240, 0.8); }
        .user-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; border: 1px solid rgba(226, 232, 240, 0.8); background: #ffffff; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.03); }
        .user-card:hover { transform: translateY(-8px) scale(1.02); border-color: #10b981; box-shadow: 0 20px 40px -5px rgba(16,185,129,0.15); }
        .tab-btn { transition: all 0.3s ease; }
        .tab-btn.active { background: linear-gradient(90deg, rgba(16,185,129,0.1) 0%, rgba(255,255,255,0) 100%); color: #059669; border-left: 3px solid #10b981; }
        
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .btn-animated { position: relative; overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .btn-animated:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
        .btn-animated:active { transform: translateY(1px); box-shadow: 0 5px 10px -5px rgba(0,0,0,0.1); }
        
        .ql-toolbar.ql-snow { border: none !important; border-bottom: 1px solid #e2e8f0 !important; background: rgba(255,255,255,0.9) !important; backdrop-filter: blur(10px); font-family: 'Plus Jakarta Sans', sans-serif; padding: 16px 24px !important; position: sticky; top: 0; z-index: 10; }
        .ql-container.ql-snow { border: none !important; background: #ffffff !important; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; height: 100%; display: flex; flex-direction: column; }
        .ql-editor { flex: 1; color: #334155 !important; counter-reset: line; padding: 24px 32px 24px 64px !important; line-height: 1.8; width: 100%; }
        .ql-editor p, .ql-editor h1, .ql-editor h2, .ql-editor blockquote, .ql-editor pre { position: relative; }
        .ql-editor p::before, .ql-editor h1::before, .ql-editor h2::before, .ql-editor blockquote::before, .ql-editor pre::before {
            counter-increment: line; content: counter(line); position: absolute; left: -45px; top: 0;
            color: #cbd5e1; font-family: 'Fira Code', monospace; font-size: 12px; text-align: right; width: 30px; user-select: none; pointer-events: none;
        }
        
        .ql-snow .ql-stroke { stroke: #64748b !important; stroke-width: 2 !important; }
        .ql-snow .ql-fill { fill: #64748b !important; }
        .ql-snow .ql-picker { color: #64748b !important; font-weight: 600; }
        .ql-snow.ql-toolbar button:hover .ql-stroke, .ql-snow .ql-toolbar button:hover .ql-stroke { stroke: #10b981 !important; }
        .ql-snow.ql-toolbar button:hover .ql-fill, .ql-snow .ql-toolbar button:hover .ql-fill { fill: #10b981 !important; }
        .ql-snow.ql-toolbar button.ql-active .ql-stroke { stroke: #10b981 !important; }
        .ql-snow.ql-toolbar button.ql-active .ql-fill { fill: #10b981 !important; }
        
        #authModal { visibility: hidden; opacity: 0; pointer-events: none; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); z-index: 99999; }
        #authModal.flex { visibility: visible; opacity: 1; pointer-events: auto; }
        #authModalContent { transform: scale(0.95) translateY(20px); transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        #authModal.flex #authModalContent { transform: scale(1) translateY(0); }
        
        .modal { opacity: 0; pointer-events: none; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); z-index: 99999; backdrop-filter: blur(8px); }
        .modal.active { opacity: 1; pointer-events: auto; }
        .modal-content { transform: scale(0.95) translateY(20px); transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        .modal.active .modal-content { transform: scale(1) translateY(0); }
        
        .bg-mesh { background-image: radial-gradient(at 40% 20%, hsla(160,100%,74%,0.15) 0px, transparent 50%), radial-gradient(at 80% 0%, hsla(189,100%,56%,0.15) 0px, transparent 50%), radial-gradient(at 0% 50%, hsla(340,100%,76%,0.15) 0px, transparent 50%); }
    </style>
</head>
<body class="flex flex-col h-screen relative selection:bg-emerald-500 selection:text-white bg-mesh">

    <header class="h-20 glass-panel flex items-center justify-between px-10 z-20">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-[1rem] bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-[0_8px_16px_-4px_rgba(16,185,129,0.3)] border border-emerald-300/30">
                <i class="fa-solid fa-book-open text-white text-xl"></i>
            </div>
            <div>
                <h1 class="font-extrabold text-2xl tracking-tight text-slate-800 leading-tight">Public Notepad</h1>
                <p class="text-[10px] text-emerald-600 font-mono tracking-widest uppercase font-bold">Secure Global Workspace</p>
            </div>
        </div>
        <div class="flex gap-6 items-center">
            <div class="hidden md:flex items-center gap-3 text-slate-500 text-xs font-medium">
                <span>Shortcuts:</span>
                <kbd class="bg-slate-100 border border-slate-200 px-2 py-1 rounded-md font-mono text-slate-700 shadow-sm">Ctrl+F</kbd> Find
                <kbd class="bg-slate-100 border border-slate-200 px-2 py-1 rounded-md font-mono text-slate-700 shadow-sm">Ctrl+S</kbd> Save
            </div>
            <button onclick="window.location.href='/dashboard'" class="px-6 py-2.5 rounded-[1rem] bg-white border border-slate-200 text-slate-700 hover:text-emerald-600 transition-all font-bold text-sm shadow-sm hover:border-emerald-200 hover:bg-emerald-50 btn-animated flex items-center gap-2">
                <i class="fa-solid fa-arrow-right-to-bracket rotate-180"></i> Hub
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-12 relative z-10" id="mainView">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-slate-800 mb-3">Select Identity Workspace</h2>
                <p class="text-slate-500 font-medium">Authentication required to view and manage personal notes.</p>
            </div>
            <div class="flex flex-wrap justify-center gap-8" id="userContainerList"></div>
        </div>
    </div>

    <div id="authModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md items-center justify-center">
        <div class="bg-white rounded-[2rem] w-full max-w-sm p-10 text-center shadow-2xl border border-slate-100 relative overflow-hidden" id="authModalContent">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
            <div class="w-20 h-20 bg-emerald-50 rounded-[1.2rem] border border-emerald-100 flex items-center justify-center mx-auto mb-6 shadow-inner">
                <i class="fa-solid fa-lock text-3xl text-emerald-500"></i>
            </div>
            <h3 class="font-extrabold text-2xl text-slate-800 mb-2">Workspace Auth</h3>
            <p class="text-sm text-slate-500 mb-8 font-medium">Unlocking workspace for <b class="text-emerald-600" id="authModalUser">User</b>.</p>
            
            <div class="relative group mb-8">
                <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                <input type="password" id="customAuthPass" class="w-full bg-slate-50 text-slate-800 font-bold text-lg rounded-[1.2rem] py-4 pr-4 pl-12 border border-slate-200 focus:outline-none focus:border-emerald-400 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-inner placeholder-slate-400 tracking-widest" placeholder="Passphrase">
            </div>
            
            <div class="flex justify-center gap-3">
                <button onclick="closeNotepadAuth()" class="flex-1 bg-white hover:bg-slate-50 text-slate-600 font-bold py-3.5 rounded-[1.2rem] border border-slate-200 transition-all btn-animated">Cancel</button>
                <button onclick="submitNotepadAuth()" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3.5 rounded-[1.2rem] shadow-[0_8px_16px_-4px_rgba(16,185,129,0.3)] transition-all btn-animated flex items-center justify-center gap-2">
                    Unlock <i class="fa-solid fa-unlock-keyhole"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="flex-1 hidden overflow-hidden z-10" id="editorView">
        <div class="w-80 bg-slate-50 border-r border-slate-200 flex flex-col shadow-[4px_0_24px_rgba(0,0,0,0.02)] z-20">
            <div class="p-6 border-b border-slate-200 bg-white">
                <div class="flex items-center gap-4 mb-6 p-2 rounded-2xl border border-slate-100 bg-slate-50 shadow-inner">
                    <img id="activeUserAvatar" src="" class="w-12 h-12 rounded-xl border-2 border-white shadow-sm object-cover bg-white">
                    <div class="overflow-hidden">
                        <p class="text-[10px] text-emerald-500 font-mono tracking-widest uppercase font-bold mb-0.5">Active Node</p>
                        <h3 class="font-extrabold text-slate-800 text-lg truncate tracking-tight" id="currentUserDisplay">User</h3>
                    </div>
                </div>
                <div class="relative group mb-5">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                    <input type="text" id="searchNoteFiles" placeholder="Filter documents..." onkeyup="filterNotes()" class="w-full bg-white border border-slate-200 text-slate-700 rounded-xl py-3 pl-10 pr-4 text-sm font-medium outline-none focus:border-emerald-400 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                </div>
                <button onclick="promptCreateNote()" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold text-sm transition-all shadow-[0_8px_16px_-4px_rgba(16,185,129,0.3)] btn-animated flex items-center justify-center gap-2">
                    <i class="fa-solid fa-file-circle-plus"></i> New Document
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-1.5 custom-scrollbar bg-slate-50" id="notesList"></div>
            <div class="p-6 border-t border-slate-200 bg-white">
                <button onclick="backToList()" class="w-full py-3.5 border border-slate-200 rounded-xl text-slate-600 hover:text-red-500 hover:bg-red-50 hover:border-red-200 transition-all font-bold text-sm bg-white shadow-sm btn-animated flex items-center justify-center gap-2">
                    <i class="fa-solid fa-door-open"></i> Close Workspace
                </button>
            </div>
        </div>
        <div class="flex-1 flex flex-col relative bg-white">
            <div class="h-[72px] bg-white border-b border-slate-200 flex items-center px-8 justify-between z-20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center">
                        <i class="fa-solid fa-file-lines text-emerald-500"></i>
                    </div>
                    <div class="font-mono text-slate-700 font-bold tracking-tight text-lg" id="currentFileDisplay">No file selected</div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="deleteNotepadFile()" class="px-5 py-2.5 bg-white text-red-500 rounded-xl hover:bg-red-50 transition-all font-bold text-sm btn-animated border border-slate-200 hover:border-red-200 flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                    <button onclick="openFindModal()" class="px-5 py-2.5 bg-white text-slate-600 rounded-xl hover:text-emerald-600 hover:bg-emerald-50 transition-all font-bold text-sm btn-animated border border-slate-200 hover:border-emerald-200 flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-magnifying-glass"></i> Find
                    </button>
                    <button onclick="saveNotepad()" class="px-6 py-2.5 bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 transition-all font-bold text-sm shadow-[0_8px_16px_-4px_rgba(16,185,129,0.3)] btn-animated flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Save Source
                    </button>
                </div>
            </div>
            <div class="flex-1 relative w-full h-full flex flex-col overflow-hidden">
                <div id="quillEditor" class="flex-1 overflow-y-auto"></div>
            </div>
        </div>
    </div>

    <div class="modal fixed inset-0 flex items-center justify-center p-4" id="modalFindReplace">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" onclick="closeModal('modalFindReplace')"></div>
        <div class="modal-content bg-white rounded-[2rem] shadow-2xl w-full max-w-sm relative z-10 overflow-hidden p-8 border border-slate-100 text-center">
            <div class="w-16 h-16 bg-blue-50 rounded-[1rem] border border-blue-100 flex items-center justify-center mx-auto mb-5 shadow-inner">
                <i class="fa-solid fa-magnifying-glass text-2xl text-blue-500"></i>
            </div>
            <h3 class="font-extrabold text-2xl text-slate-800 mb-6">Find & Replace</h3>
            <div class="space-y-4">
                <input type="text" id="findText" placeholder="Search text..." class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl p-4 outline-none font-medium transition-all focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 shadow-inner">
                <input type="text" id="replaceText" placeholder="Replace with..." class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl p-4 outline-none font-medium transition-all focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 shadow-inner">
            </div>
            <div class="mt-8 flex gap-3">
                <button class="flex-1 py-3.5 bg-white text-slate-600 border border-slate-200 rounded-xl font-bold hover:bg-slate-50 transition-all btn-animated" onclick="executeFindReplace(false)">Replace</button>
                <button class="flex-1 py-3.5 bg-blue-500 text-white rounded-xl font-bold hover:bg-blue-600 transition-all btn-animated shadow-[0_8px_16px_-4px_rgba(59,130,246,0.3)]" onclick="executeFindReplace(true)">Replace All</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    let activeUser = ''; let activeFile = ''; let activePass = ''; let quill = null;
    let usersData = [];
    const Toast = Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 3000, background: '#ffffff', color: '#0f172a', customClass: { popup: 'border border-slate-200 shadow-[0_10px_30px_-10px_rgba(0,0,0,0.1)] rounded-[1rem]' }});

    document.addEventListener('DOMContentLoaded', async () => {
        usersData = await fetch('notepad.php?api=list_users').then(r => r.json());
        const grid = document.getElementById('userContainerList');
        const now = Math.floor(Date.now() / 1000);
        
        usersData.forEach(u => {
            const isOnline = (now - (u.last_active || 0)) < 30; 
            const statusDot = isOnline ? '<div class="w-5 h-5 rounded-full bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.8)] absolute bottom-0 right-0 border-4 border-white"></div>' : '<div class="w-5 h-5 rounded-full bg-slate-300 absolute bottom-0 right-0 border-4 border-white"></div>';

            grid.innerHTML += `
                <div class="user-card w-64 h-64 rounded-[2rem] flex flex-col items-center justify-center p-6 relative group" onclick="openAuthModal('${u.username}')">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-[2rem]"></div>
                    <div class="relative mb-6 z-10">
                        <img src="${u.avatar}" class="w-24 h-24 rounded-[1.5rem] border-4 border-white object-cover shadow-lg group-hover:scale-105 transition-transform">
                        ${statusDot}
                    </div>
                    <span class="font-extrabold text-slate-800 text-xl tracking-tight z-10">${u.username}</span>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2 z-10 group-hover:text-emerald-500 transition-colors">Access Terminal</span>
                </div>
            `;
        });

        quill = new Quill('#quillEditor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ],
                keyboard: {
                    bindings: {
                        customSave: {
                            key: 'S', shortKey: true,
                            handler: function(range, context) {
                                if(activeFile) saveNotepad();
                                return false; 
                            }
                        }
                    }
                }
            }
        });
        
        document.addEventListener('keydown', e => { 
            if (!document.getElementById('editorView').classList.contains('hidden')) {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') { 
                    e.preventDefault(); 
                    if(activeFile) saveNotepad(); 
                }
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'f') { 
                    e.preventDefault(); 
                    if(activeFile) openFindModal(); 
                }
            }
        }, { passive: false });

        const savedUser = localStorage.getItem('emerald_np_user');
        const savedPass = localStorage.getItem('emerald_np_pass');
        if(savedUser && savedPass) {
            const fd = new FormData(); fd.append('user', savedUser); fd.append('password', savedPass);
            const res = await fetch('notepad.php?api=verify_access', { method: 'POST', body: fd }).then(r=>r.json());
            if(res.status === 'success') {
                activePass = savedPass;
                await openUserWorkspace(savedUser);
                const savedFile = localStorage.getItem('emerald_np_file');
                if(savedFile) await loadFile(savedFile);
            } else {
                localStorage.removeItem('emerald_np_user'); localStorage.removeItem('emerald_np_pass');
            }
        }
    });

    function openAuthModal(user) {
        document.getElementById('authModalUser').innerText = user;
        document.getElementById('customAuthPass').value = '';
        document.getElementById('authModal').classList.add('flex');
        setTimeout(() => { document.getElementById('customAuthPass').focus(); }, 100);
    }

    function closeNotepadAuth() { document.getElementById('authModal').classList.remove('flex'); }

    async function submitNotepadAuth() {
        const user = document.getElementById('authModalUser').innerText;
        const pass = document.getElementById('customAuthPass').value;
        if(!pass) return Toast.fire({icon: 'error', title: 'Passphrase required'});

        const fd = new FormData(); fd.append('user', user); fd.append('password', pass);
        const res = await fetch('notepad.php?api=verify_access', { method: 'POST', body: fd }).then(r=>r.json());
        if(res.status === 'success') {
            closeNotepadAuth();
            activePass = pass;
            localStorage.setItem('emerald_np_user', user); 
            localStorage.setItem('emerald_np_pass', pass);
            openUserWorkspace(user);
        } else { Toast.fire({icon: 'error', title: res.message}); }
    }

    function openFindModal() { document.getElementById('modalFindReplace').classList.add('active'); }
    function closeModal(id) { document.getElementById(id).classList.remove('active'); }

    function executeFindReplace(replaceAll) {
        const findText = document.getElementById('findText').value;
        const repText = document.getElementById('replaceText').value;
        if(!findText) return;
        
        let content = quill.root.innerHTML;
        if(replaceAll) { content = content.split(findText).join(repText); } 
        else { content = content.replace(findText, repText); }
        
        quill.clipboard.dangerouslyPasteHTML(content);
        closeModal('modalFindReplace');
        Toast.fire({ icon: 'success', title: 'Replacement executed' });
    }

    async function openUserWorkspace(user) {
        activeUser = user;
        document.getElementById('currentUserDisplay').innerText = user;
        const udata = usersData.find(x => x.username === user);
        document.getElementById('activeUserAvatar').src = udata ? udata.avatar : '';
        
        document.getElementById('mainView').classList.add('hidden');
        document.getElementById('editorView').classList.remove('hidden'); document.getElementById('editorView').classList.add('flex');
        
        await refreshNotesList();
    }

    async function refreshNotesList() {
        const fd = new FormData(); fd.append('user', activeUser);
        const res = await fetch('notepad.php?api=list_notes', { method: 'POST', body: fd }).then(r=>r.json());
        const list = document.getElementById('notesList'); list.innerHTML = '';
        if(res.notes && res.notes.length === 0) {
            quill.setText(''); quill.disable(); document.getElementById('currentFileDisplay').innerText = 'No workspace active'; activeFile = ''; localStorage.removeItem('emerald_np_file');
        } else if (res.notes) {
            res.notes.forEach(f => {
                const isActive = (f === activeFile) ? 'active bg-white border border-emerald-100 shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 border-l-transparent border border-transparent';
                list.innerHTML += `<button onclick="loadFile('${f}')" class="tab-btn w-full text-left px-5 py-3.5 rounded-xl font-medium text-sm truncate flex items-center group ${isActive}"><i class="fa-solid fa-file-lines mr-3 ${f === activeFile ? 'text-emerald-500' : 'text-slate-400 group-hover:text-slate-500'}"></i>${f}</button>`;
            });
            if(!activeFile || !res.notes.includes(activeFile)) loadFile(res.notes[0]);
        }
    }

    function filterNotes() {
        const q = document.getElementById('searchNoteFiles').value.toLowerCase();
        document.querySelectorAll('.tab-btn').forEach(btn => {
            if(btn.innerText.toLowerCase().includes(q)) btn.style.display = 'flex';
            else btn.style.display = 'none';
        });
    }

    async function loadFile(file) {
        activeFile = file; document.getElementById('currentFileDisplay').innerText = file;
        localStorage.setItem('emerald_np_file', file);
        const fd = new FormData(); fd.append('user', activeUser); fd.append('file', file);
        const res = await fetch('notepad.php?api=load_note', { method: 'POST', body: fd }).then(r=>r.json());
        
        quill.enable();
        quill.clipboard.dangerouslyPasteHTML(res.content);
        
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.className = 'tab-btn w-full text-left px-5 py-3.5 rounded-xl font-medium text-sm truncate flex items-center group text-slate-600 hover:bg-slate-100 hover:text-slate-900 border-l-transparent border border-transparent';
            btn.querySelector('i').className = 'fa-solid fa-file-lines mr-3 text-slate-400 group-hover:text-slate-500';
            if(btn.innerText.trim() === file) {
                btn.className = 'tab-btn w-full text-left px-5 py-3.5 rounded-xl font-medium text-sm truncate flex items-center group active bg-white border border-emerald-100 shadow-sm';
                btn.querySelector('i').className = 'fa-solid fa-file-lines mr-3 text-emerald-500';
            }
        });
    }

    async function promptCreateNote() {
        const { value: fileName } = await Swal.fire({ 
            title: 'Initialize Document', input: 'text', background: '#ffffff', color: '#0f172a', 
            inputPlaceholder: 'filename.txt',
            customClass: {popup: 'border border-slate-200 rounded-[2rem] shadow-2xl p-6', title: 'font-extrabold text-2xl mb-4', input:'bg-slate-50 border border-slate-200 text-slate-800 rounded-xl outline-none p-4 w-[85%] mx-auto font-mono focus:border-emerald-400 focus:ring-4 focus:ring-emerald-500/10 shadow-inner', confirmButton:'bg-emerald-500 text-white rounded-xl px-8 py-3.5 ml-2 font-bold hover:bg-emerald-600 shadow-lg btn-animated', cancelButton:'bg-white border border-slate-200 text-slate-600 rounded-xl px-8 py-3.5 mr-2 hover:bg-slate-50 font-bold btn-animated'},
            buttonsStyling: false, showCancelButton: true
        });
        if(fileName) {
            const fd = new FormData(); fd.append('user', activeUser); fd.append('file', fileName); fd.append('password', activePass);
            await fetch('notepad.php?api=create_note', { method: 'POST', body: fd });
            activeFile = fileName.includes('.txt') ? fileName : fileName + '.txt';
            await refreshNotesList();
            await loadFile(activeFile);
            Toast.fire({ icon: 'success', title: 'Document initialized' });
        }
    }

    async function deleteNotepadFile() {
        if(!activeFile) return;
        Swal.fire({
            title: 'Purge Document?', html: `<p class="text-slate-500 font-medium mt-2">Permanently erase <b>${activeFile}</b>?</p>`, showCancelButton: true, confirmButtonText: 'Purge File',
            background: '#ffffff', color: '#0f172a',
            customClass: {popup: 'border border-slate-200 rounded-[2rem] shadow-2xl p-6', title: 'font-extrabold text-2xl', confirmButton:'bg-red-500 text-white rounded-xl px-8 py-3.5 ml-2 font-bold hover:bg-red-600 shadow-lg btn-animated', cancelButton:'bg-white border border-slate-200 text-slate-600 rounded-xl px-8 py-3.5 mr-2 hover:bg-slate-50 font-bold btn-animated'},
            buttonsStyling: false
        }).then(async (result) => {
            if(result.isConfirmed) {
                const fd = new FormData(); fd.append('user', activeUser); fd.append('file', activeFile); fd.append('password', activePass);
                await fetch('notepad.php?api=delete_note', { method: 'POST', body: fd });
                activeFile = ''; localStorage.removeItem('emerald_np_file'); await refreshNotesList(); Toast.fire({ icon: 'success', title: 'Document erased' });
            }
        });
    }

    function backToList() {
        document.getElementById('editorView').classList.remove('flex'); document.getElementById('editorView').classList.add('hidden');
        document.getElementById('mainView').classList.remove('hidden'); activeUser = ''; activeFile = ''; activePass = '';
        localStorage.removeItem('emerald_np_user'); localStorage.removeItem('emerald_np_pass'); localStorage.removeItem('emerald_np_file');
    }

    async function saveNotepad() {
        if(!activeFile) return;
        const fd = new FormData(); fd.append('user', activeUser); fd.append('file', activeFile); fd.append('password', activePass); fd.append('content', quill.root.innerHTML);
        await fetch('notepad.php?api=save_note', { method: 'POST', body: fd }); Toast.fire({ icon: 'success', title: 'Source synchronized' });
    }
</script>
</body>
</html>
