<?php
require_once 'config/db.php';

$event_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("
    SELECT e.*, 
    (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) as registered_count
    FROM events e 
    WHERE e.id = ?
");
$stmt->execute([$event_id]);
$evt = $stmt->fetch();

if (!$evt) {
    header('Location: index.php');
    exit;
}

$remaining = $evt['capacity'] - $evt['registered_count'];
$isFull = $remaining <= 0;

require_once 'includes/header.php';
?>

<div class="max-w-3xl mx-auto">
    <div class="glass-panel p-8 rounded-3xl border border-slate-700 space-y-6">
        <div class="flex items-center justify-between">
            <span class="px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-xs font-bold uppercase tracking-wider">
                <?= htmlspecialchars($evt['category']) ?>
            </span>
            <a href="index.php" class="text-xs text-slate-400 hover:text-white">&larr; Back to Catalog</a>
        </div>

        <h1 class="text-3xl font-extrabold font-heading text-white"><?= htmlspecialchars($evt['title']) ?></h1>
        <p class="text-slate-300 text-sm leading-relaxed font-light"><?= nl2br(htmlspecialchars($evt['description'])) ?></p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-900/80 p-4 rounded-2xl border border-slate-800 text-xs text-slate-300">
            <div><i class="fa-solid fa-calendar text-indigo-400 me-2"></i> Date: <strong><?= date('M j, Y', strtotime($evt['event_date'])) ?></strong></div>
            <div><i class="fa-solid fa-clock text-indigo-400 me-2"></i> Time: <strong><?= date('h:i A', strtotime($evt['start_time'])) ?> - <?= date('h:i A', strtotime($evt['end_time'])) ?></strong></div>
            <div><i class="fa-solid fa-location-dot text-cyan-400 me-2"></i> Venue: <strong><?= htmlspecialchars($evt['venue']) ?></strong></div>
            <div><i class="fa-solid fa-chair text-emerald-400 me-2"></i> Remaining Slots: <strong><?= $remaining ?> / <?= $evt['capacity'] ?></strong></div>
        </div>

        <?php if (!empty($evt['rules'])): ?>
            <div class="p-4 rounded-2xl bg-indigo-950/30 border border-indigo-500/20 text-xs space-y-1">
                <span class="font-bold text-indigo-300 uppercase block">Event Rules & Guidelines:</span>
                <p class="text-slate-300"><?= htmlspecialchars($evt['rules']) ?></p>
            </div>
        <?php endif; ?>

        <a href="register_event.php?id=<?= $evt['id'] ?>" class="block w-full py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 via-cyan-600 to-teal-500 hover:from-indigo-500 hover:to-teal-400 text-white text-center font-bold text-sm transition shadow-xl <?= $isFull ? 'pointer-events-none opacity-50' : '' ?>">
            <?= $isFull ? 'Event Registration Full' : 'Proceed to Registration & Issue Pass' ?>
        </a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>