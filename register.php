<?php
require_once 'config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $student_id = trim($_POST['student_id']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $year_section = trim($_POST['year_section']);

    if (empty($full_name) || empty($email) || empty($password) || empty($year_section) || empty($student_id)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'This email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, student_id, email, password_hash, year_section, role) VALUES (?, ?, ?, ?, ?, 'student')");
            if ($stmt->execute([$full_name, $student_id, $email, $hash, $year_section])) {
                $success = 'Account created successfully! You can now log in.';
            } else {
                $error = 'Failed to create account.';
            }
        }
    }
}
require_once 'includes/header.php';
?>

<div class="max-w-md mx-auto my-12">
    <div class="glass-panel p-8 rounded-3xl border border-slate-700 relative shadow-2xl bg-slate-900/80 backdrop-blur-md">
        <h2 class="text-2xl font-bold font-heading text-white mb-6"><i class="fa-solid fa-user-plus text-cyan-400 me-2"></i>Register Student Account</h2>
        
        <?php if ($error): ?><div class="p-3 mb-4 rounded-xl bg-rose-950/80 border border-rose-500/50 text-rose-200 text-xs font-semibold"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="p-3 mb-4 rounded-xl bg-emerald-950/80 border border-emerald-500/50 text-emerald-200 text-xs font-semibold"><?= htmlspecialchars($success) ?></div><?php endif; ?>

        <form method="POST" class="space-y-3">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Full Name</label>
                <input type="text" name="full_name" required placeholder="e.g. Juan Cruz" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Student ID Number</label>
                <input type="text" name="student_id" required placeholder="2026-08129" value="<?= htmlspecialchars($_POST['student_id'] ?? '') ?>" class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Year & Section</label>
                <input type="text" name="year_section" required placeholder="BSIT 3-A" value="<?= htmlspecialchars($_POST['year_section'] ?? '') ?>" class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Campus Email</label>
                <input type="email" name="email" required placeholder="student@ptc.edu.ph" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Create Password</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none">
            </div>
            <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white font-bold text-sm transition shadow-lg">
                Create Student Account &rarr;
            </button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>