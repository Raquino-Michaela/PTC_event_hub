<?php
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

$event_id = $_GET['event_id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.ticket_code, r.registered_at, u.full_name, u.email, u.student_id, u.year_section 
    FROM registrations r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.event_id = ? 
    ORDER BY r.registered_at ASC
");
$stmt->execute([$event_id]);
$attendees = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold font-heading text-white">Attendee Database: <?= htmlspecialchars($event['title']) ?></h2>
            <p class="text-slate-400 text-xs">Total Registrations: <?= count($attendees) ?> / <?= $event['capacity'] ?></p>
        </div>
        <a href="dashboard.php" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-200 text-xs font-semibold">&larr; Back to Dashboard</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-800 text-xs uppercase text-slate-400 font-semibold tracking-wider">
                    <th class="py-4 px-4">Ticket ID</th>
                    <th class="py-4 px-4">Student Name</th>
                    <th class="py-4 px-4">Student ID / Email</th>
                    <th class="py-4 px-4">Year & Section</th>
                    <th class="py-4 px-4">Registration Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 text-sm">
                <?php if (empty($attendees)): ?>
                    <tr><td colspan="5" class="py-8 text-center text-slate-500">No student registrations found for this event.</td></tr>
                <?php endif; ?>
                <?php foreach ($attendees as $r): ?>
                <tr class="hover:bg-slate-900/50 transition">
                    <td class="py-3 px-4 font-mono text-xs font-bold text-amber-400"><?= htmlspecialchars($r['ticket_code']) ?></td>
                    <td class="py-3 px-4 font-semibold text-white"><?= htmlspecialchars($r['full_name']) ?></td>
                    <td class="py-3 px-4 text-xs text-slate-300">
                        <div><?= htmlspecialchars($r['student_id'] ?? 'N/A') ?></div>
                        <div class="text-slate-500"><?= htmlspecialchars($r['email']) ?></div>
                    </td>
                    <td class="py-3 px-4 text-xs text-slate-300"><?= htmlspecialchars($r['year_section']) ?></td>
                    <td class="py-3 px-4 text-xs text-slate-400"><?= date('M j, Y h:i A', strtotime($r['registered_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>