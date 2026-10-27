<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTC Event Hub | Modern Student & Admin Portal</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts Inter & Orbitron -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Orbitron:wght@600;800;900&display=swap" rel="stylesheet">
    <link href="/PTC_event_hub/assets/css/style.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        keris: {
                            50: '#f0f4ff', 100: '#e0e9fe', 500: '#6366f1', 600: '#4f46e5',
                            700: '#4338ca', 900: '#0f172a', cyan: '#06b6d4', magenta: '#ec4899', gold: '#f59e0b'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Orbitron', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-indigo-500 selection:text-white">

    <!-- Interactive Background Canvas -->
    <canvas id="bg-canvas" class="fixed top-0 left-0 w-full h-full -z-10 pointer-events-none"></canvas>

    <!-- HEADER / NAVIGATION BAR -->
    <header class="sticky top-0 z-40 glass-panel border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            <!-- Brand Logo -->
            <a href="<?= (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? '/PTC_event_hub/admin/dashboard.php' : '/PTC_event_hub/index.php' ?>" class="flex items-center gap-3 cursor-pointer">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-indigo-600 via-cyan-500 to-emerald-400 p-0.5 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                        <i class="fa-solid fa-shield-halved text-cyan-400 text-xl"></i>
                    </div>
                </div>
                <div>
                    <span class="font-heading font-extrabold text-xl tracking-wider bg-gradient-to-r from-white via-indigo-200 to-cyan-400 bg-clip-text text-transparent">PTC</span>
                    <span class="block text-[10px] uppercase tracking-widest text-indigo-400 font-bold -mt-1">Event Hub</span>
                </div>
            </a>

            <!-- Navigation Tabs -->
            <nav class="hidden md:flex items-center space-x-1 bg-slate-900/80 p-1.5 rounded-2xl border border-slate-800">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
                    <!-- Admin Navigation -->
                    <a href="/PTC_event_hub/admin/dashboard.php" class="px-4 py-2 rounded-xl text-sm font-semibold text-amber-400 bg-slate-800 transition flex items-center gap-2">
                        <i class="fa-solid fa-user-shield text-amber-400"></i> Admin Portal
                    </a>
                <?php else: ?>
                    <!-- Student Navigation -->
                    <a href="/PTC_event_hub/index.php" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-2">
                        <i class="fa-solid fa-calendar-days text-indigo-400"></i> Events Catalog
                    </a>
                    <a href="/PTC_event_hub/my_registrations.php" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-2">
                        <i class="fa-solid fa-ticket text-cyan-400"></i> My Tickets
                    </a>
                <?php endif; ?>
            </nav>

            <!-- Auth Buttons / User Profile Badge -->
            <div class="flex items-center gap-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="text-right hidden sm:block">
                        <span class="block text-xs font-bold text-white"><?= htmlspecialchars($_SESSION['full_name']) ?></span>
                        <span class="block text-[10px] <?= $_SESSION['role'] === 'admin' ? 'text-amber-400 font-bold' : 'text-slate-400' ?> uppercase"><?= strtoupper($_SESSION['role']) ?></span>
                    </div>
                    <a href="/PTC_event_hub/logout.php" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition border border-slate-700">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="/PTC_event_hub/login.php" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition border border-slate-700">Log In</a>
                    <a href="/PTC_event_hub/register.php" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white font-semibold text-xs transition shadow-lg shadow-indigo-600/20">Register Student Account</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">