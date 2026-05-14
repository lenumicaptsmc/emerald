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
        $user = sanitize($_POST['user']); $user_dir = NOTEPAD_DIR . "/{$user}";
        if(!is_dir($user_dir)) mkdir($user_dir, 0755, true);
        clearstatcache();
        $notes = [];
        foreach(scandir($user_dir) as $file) { if($file !== '.' && $file !== '..') $notes[] = $file; }
        echo json_encode(['status' => 'success', 'notes' => array_values($notes)]); exit;
    }

    if ($_GET['api'] == 'load_note') {
        $user = sanitize($_POST['user']); $file = sanitize($_POST['file']);
        $path = NOTEPAD_DIR . "/{$user}/{$file}";
        $content = file_exists($path) ? file_get_contents($path) : '';
        echo json_encode(['status' => 'success', 'content' => $content]); exit;
    }
    
    if ($_GET['api'] == 'save_note') {
        $user = sanitize($_POST['user']); $file = sanitize($_POST['file']); $content = $_POST['content']; $pass = $_POST['password'];
        if (!verifyUserPassword($user, $pass)) exit(json_encode(['status' => 'error']));
        $user_dir = NOTEPAD_DIR . "/{$user}";
        if(!is_dir($user_dir)) mkdir($user_dir, 0755, true);
        file_put_contents("{$user_dir}/{$file}", $content);
        echo json_encode(['status' => 'success']); exit;
    }
    
    if ($_GET['api'] == 'create_note') {
        $user = sanitize($_POST['user']); $file = sanitize($_POST['file']); $pass = $_POST['password'];
        if (!verifyUserPassword($user, $pass)) exit(json_encode(['status' => 'error']));
        if(strpos($file, '.txt') === false) $file .= '.txt';
        $user_dir = NOTEPAD_DIR . "/{$user}";
        if(!is_dir($user_dir)) mkdir($user_dir, 0755, true);
        if(!file_exists("{$user_dir}/{$file}")) { file_put_contents("{$user_dir}/{$file}", ''); }
        echo json_encode(['status' => 'success']); exit;
    }

    if ($_GET['api'] == 'delete_note') {
        $user = sanitize($_POST['user']); $file = sanitize($_POST['file']); $pass = $_POST['password'];
        if (!verifyUserPassword($user, $pass)) exit(json_encode(['status' => 'error']));
        $path = NOTEPAD_DIR . "/{$user}/{$file}";
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
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <style>
        body { background-color: #f1f5f9; color: #111827; font-family: 'Inter', sans-serif; overflow: hidden; transition: filter 0.5s ease; }
        .glass-panel { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(0,0,0,0.05); }
        .user-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; border: 1px solid rgba(0,0,0,0.05); background: #ffffff; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .user-card:hover { transform: translateY(-5px); border-color: #10b981; box-shadow: 0 15px 40px rgba(16,185,129,0.15); }
        .tab-btn { transition: all 0.2s; }
        .tab-btn.active { background: rgba(16,185,129,0.1); color: #059669; border-left: 3px solid #10b981; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
        .btn-animated { position: relative; overflow: hidden; transition: all 0.3s ease; }
        .btn-animated:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .btn-animated:active { transform: translateY(1px); }
        
        .ql-toolbar.ql-snow { border: none !important; border-bottom: 1px solid rgba(0,0,0,0.05) !important; background: #ffffff !important; font-family: 'Inter', sans-serif; padding: 12px !important; }
        .ql-container.ql-snow { border: none !important; background: #f8fafc !important; font-family: 'Inter', sans-serif; font-size: 15px; }
        .ql-editor { color: #1f2937 !important; counter-reset: line; padding-left: 60px !important; }
        .ql-editor p, .ql-editor h1, .ql-editor h2, .ql-editor blockquote, .ql-editor pre { position: relative; }
        .ql-editor p::before, .ql-editor h1::before, .ql-editor h2::before, .ql-editor blockquote::before, .ql-editor pre::before {
            counter-increment: line; content: counter(line); position: absolute; left: -45px; top: 0;
            color: #9ca3af; font-family: 'Fira Code', monospace; font-size: 12px; text-align: right; width: 30px; user-select: none; pointer-events: none;
        }
        
        .ql-snow .ql-stroke { stroke: #6b7280 !important; }
        .ql-snow .ql-fill { fill: #6b7280 !important; }
        .ql-snow .ql-picker { color: #6b7280 !important; }
        .ql-snow.ql-toolbar button:hover .ql-stroke, .ql-snow .ql-toolbar button:hover .ql-stroke { stroke: #10b981 !important; }
        .ql-snow.ql-toolbar button:hover .ql-fill, .ql-snow .ql-toolbar button:hover .ql-fill { fill: #10b981 !important; }
        
        #authModal { visibility: hidden; opacity: 0; pointer-events: none; transition: all 0.3s ease; }
        #authModal.flex { visibility: visible; opacity: 1; pointer-events: auto; }
        
        .modal { opacity: 0; pointer-events: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); z-index: 9999; backdrop-filter: blur(8px); }
        .modal.active { opacity: 1; pointer-events: auto; }
        .modal-content { transform: scale(0.95) translateY(20px); transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
        .modal.active .modal-content { transform: scale(1) translateY(0); }
    </style>
</head>
<body class="flex flex-col h-screen relative selection:bg-emerald-500 selection:text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-gray-100 to-gray-200 -z-10"></div>

    <header class="h-20 glass-panel flex items-center justify-between px-10 z-20">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                <i class="fa-solid fa-book-open text-white text-xl"></i>
            </div>
            <h1 class="font-extrabold text-2xl tracking-tight text-gray-900">Public Notepad</h1>
        </div>
        <div class="flex gap-6 items-center">
            <p class="text-gray-400 font-mono text-xs hidden md:block">Press <kbd class="bg-gray-200 px-1.5 py-0.5 rounded text-gray-700">Ctrl+F</kbd> Find | <kbd class="bg-gray-200 px-1.5 py-0.5 rounded text-gray-700">Ctrl+S</kbd> Save</p>
            <button onclick="window.location.href='/dashboard'" class="px-6 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition-all font-bold text-sm shadow-sm btn-animated">Return to Hub</button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-12 relative z-10" id="mainView">
        <div class="flex flex-wrap justify-center gap-8 pt-10" id="userContainerList"></div>
    </div>

    <div id="authModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md items-center justify-center">
        <div class="bg-white rounded-3xl w-full max-w-sm p-10 text-center shadow-2xl transform transition-all scale-95 border border-gray-100" id="authModalContent">
            <h3 class="font-extrabold text-2xl text-gray-900 mb-2">Authorization Required</h3>
            <p class="text-sm text-gray-500 mb-8">Enter password to access <b class="text-emerald-600" id="authModalUser">User</b>'s workspace.</p>
            <input type="password" id="customAuthPass" class="w-full bg-gray-50 text-gray-900 text-center font-bold text-lg rounded-2xl p-4 mb-8 border border-gray-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all shadow-inner placeholder-gray-400 tracking-widest" placeholder="••••••••">
            <div class="flex justify-center gap-4">
                <button onclick="submitNotepadAuth()" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-500/30 transition-all btn-animated">OK</button>
                <button onclick="closeNotepadAuth()" class="flex-1 bg-white hover:bg-gray-50 text-gray-700 font-bold py-3.5 rounded-xl border border-gray-200 transition-all btn-animated">Cancel</button>
            </div>
        </div>
    </div>

    <div class="flex-1 hidden overflow-hidden z-10" id="editorView">
        <div class="w-80 bg-white border-r border-gray-200 flex flex-col shadow-2xl z-20">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-4 mb-6">
                    <img id="activeUserAvatar" src="" class="w-12 h-12 rounded-full border-2 border-emerald-500 shadow-md object-cover">
                    <h3 class="font-bold text-gray-900 text-xl truncate tracking-wide" id="currentUserDisplay">User</h3>
                </div>
                <div class="relative group mb-4">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchNoteFiles" placeholder="Search tabs..." onkeyup="filterNotes()" class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl py-2 pl-9 pr-3 text-sm outline-none focus:border-emerald-500 transition-all shadow-sm">
                </div>
                <button onclick="promptCreateNote()" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-emerald-500/30 btn-animated"><i class="fa-solid fa-file-circle-plus mr-2"></i> New Document</button>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar" id="notesList"></div>
            <div class="p-6 border-t border-gray-100 bg-gray-50/50">
                <button onclick="backToList()" class="w-full py-3.5 border border-gray-300 rounded-xl text-gray-600 hover:bg-white transition-all font-bold text-sm bg-white shadow-sm btn-animated"><i class="fa-solid fa-arrow-left mr-2"></i> Close Workspace</button>
            </div>
        </div>
        <div class="flex-1 flex flex-col relative bg-gray-50">
            <div class="h-16 bg-white border-b border-gray-200 flex items-center px-8 justify-between shadow-sm z-10">
                <div class="font-mono text-gray-700 font-bold tracking-widest text-sm bg-gray-100 px-4 py-2 rounded-lg border border-gray-200" id="currentFileDisplay">No file selected</div>
                <div class="flex items-center gap-3">
                    <button onclick="deleteNotepadFile()" class="px-5 py-2.5 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all font-bold text-sm btn-animated border border-red-100"><i class="fa-solid fa-trash mr-2"></i> Delete</button>
                    <button onclick="openFindModal()" class="px-5 py-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all font-bold text-sm btn-animated border border-blue-100"><i class="fa-solid fa-magnifying-glass mr-2"></i> Find / Replace</button>
                    <button onclick="saveNotepad()" class="px-6 py-2.5 bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 transition-all font-bold text-sm shadow-md shadow-emerald-500/20 btn-animated"><i class="fa-solid fa-floppy-disk mr-2"></i> Save Changes</button>
                </div>
            </div>
            <div class="flex-1 relative w-full h-full flex flex-col">
                <div id="quillEditor" class="flex-1"></div>
            </div>
        </div>
    </div>

    <div class="modal fixed inset-0 flex items-center justify-center p-4" id="modalFindReplace">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md" onclick="closeModal('modalFindReplace')"></div>
        <div class="modal-content bg-white rounded-3xl shadow-2xl w-full max-w-sm relative z-10 overflow-hidden p-8 border border-gray-100 text-center">
            <h3 class="font-extrabold text-2xl text-gray-900 mb-6"><i class="fa-solid fa-magnifying-glass text-blue-500 mr-2"></i> Find & Replace</h3>
            <div class="space-y-4">
                <input type="text" id="findText" placeholder="Find..." class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl p-4 outline-none font-bold transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-inner">
                <input type="text" id="replaceText" placeholder="Replace with..." class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl p-4 outline-none font-bold transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-inner">
            </div>
            <div class="mt-8 flex gap-3">
                <button class="flex-1 py-3.5 bg-blue-50 text-blue-600 border border-blue-200 rounded-xl font-bold hover:bg-blue-600 hover:text-white transition-all btn-animated" onclick="executeFindReplace(false)">Replace</button>
                <button class="flex-1 py-3.5 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all btn-animated shadow-lg shadow-blue-500/30" onclick="executeFindReplace(true)">Replace All</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    let activeUser = ''; let activeFile = ''; let activePass = ''; let quill = null;
    let usersData = [];
    const Toast = Swal.mixin({ toast: true, position: 'bottom-end', showConfirmButton: false, timer: 3000, background: '#ffffff', color: '#111827', customClass: { popup: 'border border-gray-200 shadow-2xl rounded-2xl' }});

    document.addEventListener('DOMContentLoaded', async () => {
        usersData = await fetch('notepad.php?api=list_users').then(r => r.json());
        const grid = document.getElementById('userContainerList');
        const now = Math.floor(Date.now() / 1000);
        
        usersData.forEach(u => {
            const isOnline = (now - (u.last_active || 0)) < 30; 
            const statusDot = isOnline ? '<div class="w-5 h-5 rounded-full bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.8)] absolute -bottom-1 -right-1 border-4 border-white"></div>' : '<div class="w-5 h-5 rounded-full bg-gray-400 absolute -bottom-1 -right-1 border-4 border-white"></div>';

            grid.innerHTML += `
                <div class="user-card w-64 h-64 rounded-[2rem] flex flex-col items-center justify-center p-6 relative" onclick="openAuthModal('${u.username}')">
                    <div class="relative mb-6">
                        <img src="${u.avatar}" class="w-24 h-24 rounded-full border-4 border-emerald-500/20 object-cover shadow-[0_0_20px_rgba(16,185,129,0.2)]">
                        ${statusDot}
                    </div>
                    <span class="font-extrabold text-gray-900 text-2xl tracking-wide">${u.username}</span>
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
        setTimeout(() => {
            document.getElementById('authModalContent').classList.remove('scale-95');
            document.getElementById('authModalContent').classList.add('scale-100');
            document.getElementById('customAuthPass').focus();
        }, 10);
    }

    function closeNotepadAuth() {
        document.getElementById('authModalContent').classList.add('scale-95');
        document.getElementById('authModalContent').classList.remove('scale-100');
        setTimeout(() => {
            document.getElementById('authModal').classList.remove('flex');
        }, 300);
    }

    async function submitNotepadAuth() {
        const user = document.getElementById('authModalUser').innerText;
        const pass = document.getElementById('customAuthPass').value;
        if(!pass) return Toast.fire({icon: 'error', title: 'Password required'});

        const fd = new FormData(); fd.append('user', user); fd.append('password', pass);
        const res = await fetch('notepad.php?api=verify_access', { method: 'POST', body: fd }).then(r=>r.json());
        if(res.status === 'success') {
            closeNotepadAuth();
            activePass = pass;
            localStorage.setItem('emerald_np_user', user); 
            localStorage.setItem('emerald_np_pass', pass);
            openUserWorkspace(user);
        } else {
            Toast.fire({icon: 'error', title: res.message});
        }
    }

    function openFindModal() { document.getElementById('modalFindReplace').classList.add('active'); }
    function closeModal(id) { document.getElementById(id).classList.remove('active'); }

    function executeFindReplace(replaceAll) {
        const findText = document.getElementById('findText').value;
        const repText = document.getElementById('replaceText').value;
        if(!findText) return;
        
        let content = quill.root.innerHTML;
        if(replaceAll) {
            content = content.split(findText).join(repText);
        } else {
            content = content.replace(findText, repText);
        }
        quill.clipboard.dangerouslyPasteHTML(content);
        closeModal('modalFindReplace');
        Toast.fire({ icon: 'success', title: 'Replace executed' });
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
            quill.setText(''); quill.disable(); document.getElementById('currentFileDisplay').innerText = 'No tabs active'; activeFile = ''; localStorage.removeItem('emerald_np_file');
        } else if (res.notes) {
            res.notes.forEach(f => {
                const isActive = (f === activeFile) ? 'active bg-emerald-500/10' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900 border-l-transparent';
                list.innerHTML += `<button onclick="loadFile('${f}')" class="tab-btn w-full text-left px-5 py-4 rounded-xl font-mono text-sm truncate ${isActive}"><i class="fa-solid fa-file-lines mr-3 ${f === activeFile ? 'text-emerald-500' : 'text-gray-400'}"></i>${f}</button>`;
            });
            if(!activeFile || !res.notes.includes(activeFile)) loadFile(res.notes[0]);
        }
    }

    function filterNotes() {
        const q = document.getElementById('searchNoteFiles').value.toLowerCase();
        document.querySelectorAll('.tab-btn').forEach(btn => {
            if(btn.innerText.toLowerCase().includes(q)) btn.style.display = 'block';
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
            btn.className = 'tab-btn w-full text-left px-5 py-4 rounded-xl font-mono text-sm truncate text-gray-500 border-l-transparent hover:bg-gray-100 hover:text-gray-900';
            btn.querySelector('i').className = 'fa-solid fa-file-lines mr-3 text-gray-400';
            if(btn.innerText.trim() === file) {
                btn.className = 'tab-btn w-full text-left px-5 py-4 rounded-xl font-mono text-sm truncate active bg-emerald-500/10';
                btn.querySelector('i').className = 'fa-solid fa-file-lines mr-3 text-emerald-500';
            }
        });
    }

    async function promptCreateNote() {
        const { value: fileName } = await Swal.fire({ 
            title: 'New Document', input: 'text', background: '#fff', color: '#111827', 
            customClass: {popup: 'border border-gray-200 rounded-3xl shadow-2xl', input:'bg-gray-50 border border-gray-200 text-gray-900 rounded-xl outline-none p-4 w-[80%] mx-auto font-mono focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500', confirmButton:'bg-emerald-500 text-white rounded-xl px-8 py-3 ml-2 font-bold hover:bg-emerald-600', cancelButton:'bg-white border border-gray-300 text-gray-700 rounded-xl px-8 py-3 mr-2 hover:bg-gray-50 font-bold'},
            buttonsStyling: false, showCancelButton: true
        });
        if(fileName) {
            const fd = new FormData(); fd.append('user', activeUser); fd.append('file', fileName); fd.append('password', activePass);
            await fetch('notepad.php?api=create_note', { method: 'POST', body: fd });
            activeFile = fileName.includes('.txt') ? fileName : fileName + '.txt';
            await refreshNotesList();
            await loadFile(activeFile);
        }
    }

    async function deleteNotepadFile() {
        if(!activeFile) return;
        Swal.fire({
            title: 'Delete Document?', html: `<p class="text-gray-500">Permanently delete <b>${activeFile}</b>?</p>`, showCancelButton: true, confirmButtonText: 'Delete',
            background: '#fff', color: '#111827',
            customClass: {popup: 'border border-gray-200 rounded-3xl shadow-2xl', confirmButton:'bg-red-500 text-white rounded-xl px-8 py-3 ml-2 font-bold hover:bg-red-600', cancelButton:'bg-white border border-gray-300 text-gray-700 rounded-xl px-8 py-3 mr-2 hover:bg-gray-50 font-bold'},
            buttonsStyling: false
        }).then(async (result) => {
            if(result.isConfirmed) {
                const fd = new FormData(); fd.append('user', activeUser); fd.append('file', activeFile); fd.append('password', activePass);
                await fetch('notepad.php?api=delete_note', { method: 'POST', body: fd });
                activeFile = ''; localStorage.removeItem('emerald_np_file'); await refreshNotesList(); Toast.fire({ icon: 'success', title: 'File Deleted' });
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
        await fetch('notepad.php?api=save_note', { method: 'POST', body: fd }); Toast.fire({ icon: 'success', title: 'Document Saved' });
    }
</script>
</body>
</html>
