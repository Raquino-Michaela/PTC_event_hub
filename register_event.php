<?php
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$event_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$evt = $stmt->fetch();

if (!$evt) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = false;
$ticket_code = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cap_stmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ?");
    $cap_stmt->execute([$event_id]);
    $current_count = $cap_stmt->fetchColumn();

    if ($current_count >= $evt['capacity']) {
        $error = "Sorry, this event has reached full capacity.";
    } else {
        $dup_stmt = $pdo->prepare("SELECT id FROM registrations WHERE user_id = ? AND event_id = ?");
        $dup_stmt->execute([$user_id, $event_id]);
        if ($dup_stmt->fetch()) {
            $error = "You are already registered for this event.";
        } else {
            $ticket_code = 'KERIS-' . rand(100000, 900000);
            $ins_stmt = $pdo->prepare("INSERT INTO registrations (user_id, event_id, ticket_code) VALUES (?, ?, ?)");
            if ($ins_stmt->execute([$user_id, $event_id, $ticket_code])) {
                $success = true;
            } else {
                $error = "Registration failed. Try again.";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="max-w-xl mx-auto">
    <?php if ($success): ?>
        <div id="printable-ticket-card" class="glass-panel rounded-3xl border border-indigo-500/30 p-8 relative shadow-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600/20 border border-indigo-500/40 flex items-center justify-center text-indigo-400 font-bold">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <div>
                        <span class="font-heading font-extrabold text-lg text-white">KERIS E-TICKET PASS</span>
                        <span class="block text-[10px] text-cyan-400 font-bold tracking-widest uppercase">Official Student Event Ticket</span>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold uppercase">
                    Confirmed Pass
                </span>
            </div>

            <div class="no-print bg-cyan-950/40 border border-cyan-500/30 rounded-2xl p-4 flex items-center gap-3 text-xs text-cyan-200">
                <i class="fa-solid fa-paper-plane text-cyan-400 text-base"></i>
                <div>
                    <strong>Email Confirmation Dispatched!</strong>
                    <span class="block text-cyan-300/80">A digital registration copy has been dispatched to <strong><?= htmlspecialchars($_SESSION['email']) ?></strong>.</span>
                </div>
            </div>

            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-400"><?= htmlspecialchars($evt['category']) ?></span>
                        <h3 class="text-xl font-bold text-white font-heading"><?= htmlspecialchars($evt['title']) ?></h3>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] uppercase text-slate-400 font-mono block">Ticket ID</span>
                        <span class="font-mono text-sm font-bold text-amber-400"><?= $ticket_code ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t border-b border-slate-800/80 py-3 text-xs">
                    <div>
                        <span class="text-slate-400 block">Student Name:</span>
                        <span class="font-semibold text-white"><?= htmlspecialchars($_SESSION['full_name']) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Student ID / Section:</span>
                        <span class="font-semibold text-white"><?= htmlspecialchars($_SESSION['student_id'] ?? '2026-REG') ?> (<?= htmlspecialchars($_SESSION['year_section']) ?>)</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Date & Time:</span>
                        <span class="font-semibold text-cyan-300"><?= date('M j, Y', strtotime($evt['event_date'])) ?> | <?= date('h:i A', strtotime($evt['start_time'])) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Venue:</span>
                        <span class="font-semibold text-cyan-300"><?= htmlspecialchars($evt['venue']) ?></span>
                    </div>
                </div>
            </div>

            <div class="no-print flex items-center justify-end gap-3 pt-2">
                <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-sm transition flex items-center gap-2 border border-slate-700">
                    <i class="fa-solid fa-print"></i> Print E-Ticket
                </button>
                <a href="my_registrations.php" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm transition">My Tickets</a>
            </div>
        </div>
    <?php else: ?>
        <div class="glass-panel p-8 rounded-3xl border border-slate-700 relative shadow-2xl">
            <h2 class="text-2xl font-bold font-heading text-white mb-2">Confirm Event Registration</h2>
            <p class="text-slate-400 text-xs mb-6"><?= htmlspecialchars($evt['title']) ?></p>

            <?php if ($error): ?><div class="p-3 mb-4 rounded-xl bg-rose-950/80 border border-rose-500/50 text-rose-200 text-xs font-semibold"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Full Name</label>
                        <input type="text" readonly value="<?= htmlspecialchars($_SESSION['full_name']) ?>" class="w-full bg-slate-900/60 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Email Account</label>
                        <input type="email" readonly value="<?= htmlspecialchars($_SESSION['email']) ?>" class="w-full bg-slate-900/60 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-300">
                    </div>
                </div>

                <div class="bg-indigo-950/30 p-4 rounded-2xl border border-indigo-500/20 space-y-3">
                    <p class="text-xs text-indigo-200 leading-relaxed font-light">
                        <strong>KERIS Event Agreement:</strong> I agree to adhere to university guidelines, wear my official student badge, observe sportsmanship, and receive my printable ticket pass via email.
                    </p>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" required class="w-4 h-4 rounded text-indigo-600 bg-slate-900 border-slate-700">
                        <span class="text-xs text-white font-semibold">I understand and accept all rules and agreements.</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 via-cyan-600 to-teal-500 hover:from-indigo-500 hover:to-teal-400 text-white font-bold text-sm transition shadow-xl">
                    Submit Registration & Issue E-Ticket
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>