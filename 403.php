<?php
$detected_ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Firewall Reject</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;600&family=Inter:wght@400;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #03050a; color: #fff; font-family: 'Inter', sans-serif; overflow: hidden; margin: 0; }
        .glass-panel { background: rgba(10, 10, 15, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(239, 68, 68, 0.3); box-shadow: 0 0 50px rgba(239, 68, 68, 0.1); }
        .cyber-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(239,68,68,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(239,68,68,0.05) 1px, transparent 1px); background-size: 30px 30px; animation: gridMove 20s linear infinite; z-index: 1; }
        @keyframes gridMove { 0% { transform: translateY(0); } 100% { transform: translateY(30px); } }
        .scanner { position: absolute; top: 0; left: 0; width: 100%; height: 2px; background: rgba(239,68,68,0.8); box-shadow: 0 0 20px 5px rgba(239,68,68,0.5); animation: scan 3s ease-in-out infinite alternate; z-index: 2; }
        @keyframes scan { 0% { top: 0; opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { top: 100%; opacity: 0; } }
        .glitch-text { animation: glitch 2s linear infinite; }
        @keyframes glitch { 2%, 64% { transform: translate(2px, 0) skew(0deg); } 4%, 60% { transform: translate(-2px, 0) skew(0deg); } 62% { transform: translate(0, 0) skew(5deg); } }
        .pulse-icon { animation: pulseWarning 2s ease-in-out infinite; }
        @keyframes pulseWarning { 0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,0.7); } 70% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(239,68,68,0); } 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,0); } }
    </style>
</head>
<body class="h-screen w-full flex items-center justify-center relative">
    <div class="cyber-grid"></div>
    <div class="scanner"></div>

    <div class="glass-panel p-12 rounded-[2rem] w-full max-w-4xl relative z-10 text-center border-t-4 border-t-red-600 transform transition-all flex flex-col items-center">
        <div class="pulse-icon w-32 h-32 bg-red-500/10 rounded-full border border-red-500/50 flex items-center justify-center mx-auto mb-8">
            <i class="fa-solid fa-hand-paper text-7xl text-red-500"></i>
        </div>
        <h1 class="text-6xl font-extrabold tracking-widest uppercase text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-red-800 mb-4 glitch-text">403 RESTRICTED</h1>
        <p class="text-lg text-red-400 font-mono tracking-[0.4em] uppercase mb-10">CONNECTION REJECTED BY FIREWALL</p>
        
        <div class="bg-black/60 border border-red-500/30 rounded-2xl p-8 text-left font-mono text-base text-gray-400 shadow-inner relative overflow-hidden w-full">
            <div class="absolute left-0 top-0 w-1 h-full bg-red-600"></div>
            <p class="mb-3"><span class="text-red-500 font-bold">root@emerald:~#</span> verify_connection</p>
            <p class="mb-3 text-green-400">> Analyzing incoming packet...</p>
            <p class="mb-5 text-red-400 font-bold">> FATAL ERROR: IP IDENTITY NOT IN SYSTEM WHITELIST.</p>
            <p class="text-sm text-gray-500 border-t border-white/5 pt-4 text-center">Contact the System Administrator to authorize this terminal node.</p>
        </div>
    </div>
</body>
</html>
