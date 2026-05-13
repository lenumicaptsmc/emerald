<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --bg-primary: #f3f4f6; --bg-secondary: #ffffff; --text-primary: #1f2937; --text-secondary: #4b5563; --accent: #10b981; --border: #e5e7eb; --input-bg: #ffffff; }
        [data-theme="dark"] { --bg-primary: #0f172a; --bg-secondary: #1e293b; --text-primary: #f8fafc; --text-secondary: #94a3b8; --accent: #10b981; --border: #334155; --input-bg: #0f172a; }
        body { background-color: var(--bg-primary); color: var(--text-primary); font-family: 'Inter', sans-serif; transition: background-color 0.3s, color 0.3s; }
        .panel { background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .input-field { background: var(--input-bg); border: 1px solid var(--border); color: var(--text-primary); border-radius: 0.5rem; padding: 0.5rem 1rem; width: 100%; transition: all 0.2s; }
        .input-field:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2); }
        .btn-primary { background: var(--accent); color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 500; cursor: pointer; border: none; transition: all 0.2s; }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
        .fade-in { animation: fadeIn 0.4s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body>
<div class="h-screen flex items-center justify-center p-4">
    <div class="panel p-8 w-full max-w-md fade-in">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold tracking-tight" style="color: var(--accent);"><?= APP_NAME ?></h1>
            <p class="text-sm mt-2" style="color: var(--text-secondary);">Secure Asset Management</p>
        </div>
        <form id="loginForm" onsubmit="handleLogin(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Username</label>
                    <input type="text" id="login_user" class="input-field" required autocomplete="off">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" id="login_pass" class="input-field" required>
                </div>
                <button type="submit" class="btn-primary w-full mt-6">Access Dashboard</button>
            </div>
        </form>
    </div>
</div>
<script>
    async function handleLogin(e) {
        e.preventDefault();
        const fd = new FormData();
        fd.append('username', document.getElementById('login_user').value);
        fd.append('password', document.getElementById('login_pass').value);
        
        const res = await fetch('index.php?action=login', { method: 'POST', body: fd }).then(r => r.json());
        if (res.status === 'success') location.reload();
        else Swal.fire({ icon: 'error', title: 'Access Denied', text: 'Invalid credentials', background: 'var(--bg-secondary)', color: 'var(--text-primary)' });
    }
</script>
</body>
</html>
