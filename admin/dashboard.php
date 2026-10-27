<?php
session_start();
require_once '../config/db.php';

// Auth guard: check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$total_events = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$total_students = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
$total_registrations = $pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();

$events = $pdo->query("
    SELECT e.*, COUNT(r.id) as total_attendees 
    FROM events e 
    LEFT JOIN registrations r ON e.id = r.event_id 
    GROUP BY e.id 
    ORDER BY e.event_date DESC
")->fetchAll();

require_once '../includes/header.php';
?>

<!-- Header Banner & Create Event Action -->
<div class="glass-panel p-6 sm:p-8 rounded-3xl border border-amber-500/20 space-y-6 mb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-400 bg-amber-500/10 px-3 py-1 rounded-full border border-amber-500/20 mb-2">
                <i class="fa-solid fa-user-shield"></i> Active Admin Portal Session
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white">Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h1>
            <p class="text-slate-400 text-sm">Oversee campus event creation, monitor real-time student attendance, and track portal analytics.</p>
        </div>
        <a href="add_event.php" class="px-6 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 transition flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus-circle text-base"></i> Create New Event
        </a>
    </div>

    <!-- Analytics Counters -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-800">
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 flex items-center justify-between">
            <div>
                <div class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Total Campus Events</div>
                <div class="text-3xl font-bold font-heading text-white mt-1"><?= number_format($total_events) ?></div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xl">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 flex items-center justify-between">
            <div>
                <div class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Total Registrations</div>
                <div class="text-3xl font-bold font-heading text-indigo-400 mt-1"><?= number_format($total_registrations) ?></div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-xl">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 flex items-center justify-between">
            <div>
                <div class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Registered Students</div>
                <div class="text-3xl font-bold font-heading text-cyan-400 mt-1"><?= number_format($total_students) ?></div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 text-xl">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>
</div>

<!-- Events Management Table -->
<div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
    <div class="flex items-center justify-between pb-2">
        <h2 class="text-xl font-bold font-heading text-white flex items-center gap-2">
            <i class="fa-solid fa-list-check text-amber-400"></i> Event Directory & Attendance Tracking
        </h2>
        <span class="text-xs text-slate-400 font-semibold bg-slate-900 px-3 py-1 rounded-lg border border-slate-800">
            <?= count($events) ?> Total Listed
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-800 text-xs uppercase text-slate-400 font-semibold tracking-wider bg-slate-900/40">
                    <th class="py-4 px-4 rounded-l-xl">Event Title</th>
                    <th class="py-4 px-4">Category</th>
                    <th class="py-4 px-4">Event Date</th>
                    <th class="py-4 px-4">Capacity Tracker</th>
                    <th class="py-4 px-4 text-right rounded-r-xl">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 text-sm">
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-500 space-y-2">
                            <i class="fa-solid fa-folder-open text-3xl"></i>
                            <p class="text-sm font-semibold">No events created yet. Click "Create New Event" above to get started.</p>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($events as $evt): 
                    $isFull = $evt['total_attendees'] >= $evt['capacity'];
                ?>
                <tr class="hover:bg-slate-900/50 transition">
                    <td class="py-4 px-4 font-semibold text-white">
                        <?= htmlspecialchars($evt['title']) ?>
                        <span class="block text-xs font-normal text-slate-400"><i class="fa-solid fa-location-dot text-slate-500 me-1"></i><?= htmlspecialchars($evt['venue']) ?></span>
                    </td>
                    <td class="py-4 px-4 text-xs">
                        <span class="px-2.5 py-1 rounded-md bg-indigo-500/10 text-indigo-300 font-bold border border-indigo-500/20">
                            <?= htmlspecialchars($evt['category']) ?>
                        </span>
                    </td>
                    <td class="py-4 px-4 text-xs text-slate-300">
                        <?= date('M j, Y', strtotime($evt['event_date'])) ?>
                    </td>
                    <td class="py-4 px-4 text-xs font-bold">
                        <span class="<?= $isFull ? 'text-rose-400' : 'text-cyan-400' ?>">
                            <?= $evt['total_attendees'] ?> / <?= $evt['capacity'] ?>
                        </span>
                        <?php if ($isFull): ?>
                            <span class="ms-2 px-2 py-0.5 rounded text-[10px] bg-rose-500/10 text-rose-400 border border-rose-500/20">FULL</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-4 px-4 text-right space-x-2">
                        <a href="attendees.php?event_id=<?= $evt['id'] ?>" class="px-3 py-1.5 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 text-xs font-bold transition inline-flex items-center gap-1">
                            <i class="fa-solid fa-users text-[10px]"></i> Attendees
                        </a>
                        <a href="edit_event.php?id=<?= $evt['id'] ?>" class="px-3 py-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 text-xs font-bold transition inline-flex items-center gap-1">
                            <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                        </a>
                        <a href="delete_event.php?id=<?= $evt['id'] ?>" onclick="return confirm('Are you sure you want to delete this event? All student registrations for it will also be deleted.')" class="px-3 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 text-xs font-bold transition inline-flex items-center gap-1">
                            <i class="fa-solid fa-trash text-[10px]"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>