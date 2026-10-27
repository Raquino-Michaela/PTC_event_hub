<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Redirect admins away from student ticket pages directly to the admin dashboard
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT r.*, e.title, e.category, e.event_date, e.start_time, e.venue 
    FROM registrations r 
    JOIN events e ON r.event_id = e.id 
    WHERE r.user_id = ? 
    ORDER BY r.registered_at DESC
");
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold font-heading text-white flex items-center gap-3">
            <i class="fa-solid fa-ticket text-cyan-400"></i> My Registered Tickets & Passes
        </h1>
        <p class="text-slate-400 text-sm">View your active event registrations and printable E-Tickets.</p>
    </div>
    <a href="index.php" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Register For More Events
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <?php if (empty($tickets)): ?>
        <div class="col-span-full glass-panel p-8 text-center rounded-2xl text-slate-400 space-y-2 border border-slate-800">
            <i class="fa-solid fa-ticket-simple text-4xl text-slate-600 mb-2"></i>
            <p class="text-sm font-semibold">You haven't registered for any campus events yet.</p>
            <p class="text-xs text-slate-500">Explore the events catalog to secure your passes for upcoming PTC activities.</p>
        </div>
    <?php endif; ?>

    <?php foreach ($tickets as $reg): ?>
    <div class="glass-card rounded-2xl p-6 border border-slate-800 space-y-4">
        <div class="flex justify-between items-start">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400"><?= htmlspecialchars($reg['category']) ?></span>
                <h3 class="text-lg font-bold text-white font-heading"><?= htmlspecialchars($reg['title']) ?></h3>
            </div>
            <span class="font-mono text-xs font-bold text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-lg border border-amber-500/20"><?= htmlspecialchars($reg['ticket_code']) ?></span>
        </div>

        <div class="text-xs text-slate-300 space-y-1">
            <div><i class="fa-solid fa-calendar text-indigo-400 w-4"></i> <?= date('M j, Y', strtotime($reg['event_date'])) ?> (<?= date('h:i A', strtotime($reg['start_time'])) ?>)</div>
            <div><i class="fa-solid fa-location-dot text-cyan-400 w-4"></i> <?= htmlspecialchars($reg['venue']) ?></div>
        </div>

        <div class="pt-2 flex items-center justify-between border-t border-slate-800/80">
            <span class="text-[11px] text-slate-400">Registered: <?= date('M j, Y', strtotime($reg['registered_at'])) ?></span>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-indigo-600/20">
                <i class="fa-solid fa-print"></i> Print Pass
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>