<?php
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $venue = trim($_POST['venue']);
    $capacity = (int)$_POST['capacity'];
    $rules = trim($_POST['rules']);

    $stmt = $pdo->prepare("INSERT INTO events (title, description, category, event_date, start_time, end_time, venue, capacity, rules, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$title, $description, $category, $event_date, $start_time, $end_time, $venue, $capacity, $rules, $_SESSION['user_id']])) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Failed to create event.';
    }
}

require_once '../includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="glass-panel p-8 rounded-3xl border border-amber-500/30 relative shadow-2xl space-y-6">
        <h2 class="text-2xl font-bold font-heading text-white">Create New Event</h2>
        <?php if ($error): ?><div class="p-3 rounded-xl bg-rose-950/80 text-rose-200 text-xs"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Event Title</label>
                <input type="text" name="title" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Category</label>
                    <select name="category" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
                        <option value="Online Games Tournament">Online Games Tournament</option>
                        <option value="Seminars">Seminars</option>
                        <option value="Foundation Day">Foundation Day</option>
                        <option value="Sportfest">Sportfest</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Max Capacity</label>
                    <input type="number" name="capacity" min="1" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Date</label>
                    <input type="date" name="event_date" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Start Time</label>
                    <input type="time" name="start_time" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">End Time</label>
                    <input type="time" name="end_time" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Venue</label>
                <input type="text" name="venue" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Description</label>
                <textarea name="description" rows="3" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Rules & Guidelines</label>
                <input type="text" name="rules" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
            </div>
            <button type="submit" class="w-full py-3.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm transition">Publish Event</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>