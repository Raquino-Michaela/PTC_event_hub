<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect Admin accounts away from Student catalog directly to Admin Portal
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

require_once 'config/db.php';
require_once 'includes/header.php';

$categoryFilter = $_GET['category'] ?? 'ALL';

$sql = "SELECT e.*, 
        (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) as registered_count 
        FROM events e";
if ($categoryFilter !== 'ALL') {
    $sql .= " WHERE e.category = " . $pdo->quote($categoryFilter);
}
$sql .= " ORDER BY e.event_date ASC";

$stmt = $pdo->query($sql);
$events = $stmt->fetchAll();
?>

<!-- Hero Banner -->
<div class="relative rounded-3xl overflow-hidden glass-panel p-8 sm:p-12 border border-indigo-500/20 shadow-2xl mb-12">
    <div class="relative z-10 max-w-3xl space-y-6">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-xs font-semibold uppercase tracking-wider">
            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
            Official Student & Campus Activity Portal
        </div>
        <h1 class="text-4xl sm:text-6xl font-extrabold font-heading tracking-tight text-white leading-tight">
            Connect, Compete & Excel at <span class="bg-gradient-to-r from-indigo-400 via-cyan-400 to-teal-300 bg-clip-text text-transparent">PTC Event Hub</span>
        </h1>
        <p class="text-slate-300 text-base sm:text-lg leading-relaxed font-light">
            Your centralized gateway for Esports Tournaments, Tech Seminars, Sportsfests, and Foundation Day Events. Register instantly, collect digital passes, and elevate your campus experience.
        </p>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="glass-panel p-6 rounded-3xl space-y-4 border border-slate-800 mb-8">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-heading text-white">Campus Events Catalog</h2>
            <p class="text-slate-400 text-sm">Browse, register, and lock in your tickets for upcoming events.</p>
        </div>
    </div>

    <!-- Category Pills -->
    <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-800/80">
        <a href="index.php?category=ALL" class="px-4 py-2 rounded-xl text-xs font-semibold <?= $categoryFilter === 'ALL' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' ?> transition">All Categories</a>
        <a href="index.php?category=Online+Games+Tournament" class="px-4 py-2 rounded-xl text-xs font-semibold <?= $categoryFilter === 'Online Games Tournament' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' ?> transition">Online Games (PCC)</a>
        <a href="index.php?category=Seminars" class="px-4 py-2 rounded-xl text-xs font-semibold <?= $categoryFilter === 'Seminars' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' ?> transition">Seminars & Workshops</a>
        <a href="index.php?category=Foundation+Day" class="px-4 py-2 rounded-xl text-xs font-semibold <?= $categoryFilter === 'Foundation Day' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' ?> transition">Foundation Day</a>
        <a href="index.php?category=Sportfest" class="px-4 py-2 rounded-xl text-xs font-semibold <?= $categoryFilter === 'Sportfest' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' ?> transition">Sportsfest</a>
    </div>
</div>

<!-- Events Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($events)): ?>
        <div class="col-span-full py-12 text-center text-slate-400 space-y-3">
            <i class="fa-solid fa-calendar-xmark text-4xl text-slate-600"></i>
            <p class="text-base font-semibold">No campus events match this category.</p>
        </div>
    <?php endif; ?>

    <?php foreach ($events as $evt): 
        $registeredCount = $evt['registered_count'];
        $isFull = $registeredCount >= $evt['capacity'];
    ?>
    <div class="glass-card rounded-2xl p-6 flex flex-col justify-between space-y-5 border border-slate-800">
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-[11px] font-bold uppercase tracking-wider">
                    <?= htmlspecialchars($evt['category']) ?>
                </span>
                <span class="text-xs font-semibold <?= $isFull ? 'text-rose-400' : 'text-emerald-400' ?> bg-slate-900/80 px-2.5 py-1 rounded-lg border border-slate-800">
                    <?= $registeredCount ?>/<?= $evt['capacity'] ?> Seats Taken
                </span>
            </div>

            <h3 class="text-lg font-bold text-white font-heading leading-snug"><?= htmlspecialchars($evt['title']) ?></h3>
            <p class="text-slate-300 text-xs line-clamp-2 font-light"><?= htmlspecialchars($evt['description']) ?></p>
        </div>

        <div class="space-y-2 border-t border-b border-slate-800/80 py-3 text-xs text-slate-300">
            <div class="flex items-center gap-2"><i class="fa-solid fa-calendar text-indigo-400 w-4"></i> <span><?= date('M j, Y', strtotime($evt['event_date'])) ?> (<?= date('h:i A', strtotime($evt['start_time'])) ?>)</span></div>
            <div class="flex items-center gap-2"><i class="fa-solid fa-location-dot text-cyan-400 w-4"></i> <span><?= htmlspecialchars($evt['venue']) ?></span></div>
        </div>

        <div class="pt-1 flex gap-2">
            <a href="event_details.php?id=<?= $evt['id'] ?>" class="flex-1 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold text-center transition">Details</a>
            <a href="register_event.php?id=<?= $evt['id'] ?>" class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold text-center transition shadow-lg shadow-indigo-600/20 <?= $isFull ? 'pointer-events-none opacity-50' : '' ?>">
                <?= $isFull ? 'Full' : 'Register' ?>
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>