<?php
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$evt = $stmt->fetch();

if (!$evt) {
    header('Location: dashboard.php');
    exit;
}

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

    $stmt = $pdo->prepare("UPDATE events SET title=?, description=?, category=?, event_date=?, start_time=?, end_time=?, venue=?, capacity=?, rules=? WHERE id=?");
    $stmt->execute([$title, $description, $category, $event_date, $start_time, $end_time, $venue, $capacity, $rules, $id]);
    header('Location: dashboard.php');
    exit;
}

require_once '../includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="glass-panel p-8 rounded-3xl border border-amber-500/30 relative shadow-2xl space-y-6">
        <h2 class="text-2xl font-bold font-heading text-white">Edit Event</h2>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Event Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($evt['title']) ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Category</label>
                    <select name="category" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
                        <option value="Online Games Tournament" <?= $evt['category'] === 'Online Games Tournament' ? 'selected' : '' ?>>Online Games Tournament</option>
                        <option value="Seminars" <?= $evt['category'] === 'Seminars' ? 'selected' : '' ?>>Seminars</option>
                        <option value="Foundation Day" <?= $evt['category'] === 'Foundation Day' ? 'selected' : '' ?>>Foundation Day</option>
                        <option value="Sportfest" <?= $evt['category'] === 'Sportfest' ? 'selected' : '' ?>>Sportfest</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Max Capacity</label>
                    <input type="number" name="capacity" value="<?= $evt['capacity'] ?>" min="1" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Date</label>
                    <input type="date" name="event_date" value="<?= $evt['event_date'] ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Start Time</label>
                    <input type="time" name="start_time" value="<?= $evt['start_time'] ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">End Time</label>
                    <input type="time" name="end_time" value="<?= $evt['end_time'] ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Venue</label>
                <input type="text" name="venue" value="<?= htmlspecialchars($evt['venue']) ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Description</label>
                <textarea name="description" rows="3" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white"><?= htmlspecialchars($evt['description']) ?></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Rules & Guidelines</label>
                <input type="text" name="rules" value="<?= htmlspecialchars($evt['rules']) ?>" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white">
            </div>
            <button type="submit" class="w-full py-3.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm transition">Update Event Record</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>