<?php
session_start();
require_once 'config/db.php';

$error = '';
$active_tab = $_POST['login_type'] ?? 'student';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $login_type = $_POST['login_type'] ?? 'student';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        /* Get password value checking password_hash first since seed_admin uses it */
        $stored_pass = $user['password_hash'] ?? $user['password'] ?? '';

        /* Check if stored password is a hash and verify, otherwise check plain text match */
        $is_valid = false;
        if (!empty($stored_pass)) {
            if (strpos($stored_pass, '$') === 0) {
                $is_valid = password_verify($password, $stored_pass);
            } else {
                $is_valid = ($password === $stored_pass);
            }
        }

        if ($is_valid) {
            /* Enforce tab role match */
            if ($login_type === 'admin' && $user['role'] !== 'admin') {
                $error = 'Access denied. You are not authorized as an administrator.';
            } elseif ($login_type === 'student' && $user['role'] === 'admin') {
                $error = 'Admin accounts must use the Administrator Login tab.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['student_id'] = $user['student_id'] ?? null;
                $_SESSION['year_section'] = $user['year_section'] ?? null;

                if ($user['role'] === 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            }
        } else {
            $error = 'Invalid email address or password.';
        }
    } else {
        $error = 'Invalid email address or password.';
    }
}

require_once 'includes/header.php';
?>

<div class="max-w-md mx-auto my-12">
    <div class="glass-panel p-8 rounded-3xl border border-slate-700 relative shadow-2xl bg-slate-900/80 backdrop-blur-md">
        
        <!-- Toggle Tabs -->
        <div class="flex bg-slate-800/80 p-1 rounded-2xl mb-6 border border-slate-700">
            <button id="studentTabBtn" type="button" onclick="switchTab('student')" 
                    class="flex-1 py-2 text-xs font-bold rounded-xl transition-all duration-200 text-white bg-indigo-600 shadow-md">
                <i class="fa-solid fa-user-graduate me-2"></i>Student
            </button>
            <button id="adminTabBtn" type="button" onclick="switchTab('admin')" 
                    class="flex-1 py-2 text-xs font-bold rounded-xl transition-all duration-200 text-slate-400 hover:text-white">
                <i class="fa-solid fa-user-shield me-2"></i>Admin
            </button>
        </div>

        <h2 id="formTitle" class="text-2xl font-bold font-heading text-white mb-6 flex items-center">
            <i id="formIcon" class="fa-solid fa-right-to-bracket text-indigo-400 me-2"></i>
            <span id="formTitleText">Student Login</span>
        </h2>
        
        <?php if ($error): ?>
            <div class="p-3 mb-4 rounded-xl bg-rose-950/80 border border-rose-500/50 text-rose-200 text-xs font-semibold">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="login_type" id="loginTypeInput" value="<?= htmlspecialchars($active_tab) ?>">

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Email Address</label>
                <input type="email" name="email" required id="emailInput" 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="student@ptc.edu.ph" 
                       class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••" 
                       class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none">
            </div>

            <button type="submit" id="submitBtn" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm transition shadow-lg shadow-indigo-600/30">
                Log In to Hub &rarr;
            </button>
        </form>

        <div id="adminFooter" class="mt-8 pt-4 border-t border-slate-800 text-xs text-slate-400 space-y-2 hidden">
            <p class="font-bold text-amber-400 uppercase">Pre-configured Admin Accounts (Default Password: 123):</p>
            <ul class="space-y-1 font-mono text-[11px] text-slate-300">
                <li>admin.dean@ptc.edu.ph (Dean Admin)</li>
                <li>admin.pcc@ptc.edu.ph (IT & Esports Lead)</li>
                <li>admin.sports@ptc.edu.ph (Sports Director)</li>
                <li>admin.council@ptc.edu.ph (Student Council)</li>
            </ul>
        </div>
    </div>
</div>

<script>
function switchTab(type) {
    const studentBtn = document.getElementById('studentTabBtn');
    const adminBtn = document.getElementById('adminTabBtn');
    const loginTypeInput = document.getElementById('loginTypeInput');
    const formTitleText = document.getElementById('formTitleText');
    const emailInput = document.getElementById('emailInput');
    const submitBtn = document.getElementById('submitBtn');
    const adminFooter = document.getElementById('adminFooter');

    loginTypeInput.value = type;

    if (type === 'admin') {
        adminBtn.className = "flex-1 py-2 text-xs font-bold rounded-xl transition-all duration-200 text-white bg-amber-600 shadow-md";
        studentBtn.className = "flex-1 py-2 text-xs font-bold rounded-xl transition-all duration-200 text-slate-400 hover:text-white";
        
        formTitleText.innerText = "Administrator Login";
        emailInput.placeholder = "admin.dean@ptc.edu.ph";
        submitBtn.className = "w-full py-3 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-sm transition shadow-lg shadow-amber-600/30";
        adminFooter.classList.remove('hidden');
    } else {
        studentBtn.className = "flex-1 py-2 text-xs font-bold rounded-xl transition-all duration-200 text-white bg-indigo-600 shadow-md";
        adminBtn.className = "flex-1 py-2 text-xs font-bold rounded-xl transition-all duration-200 text-slate-400 hover:text-white";
        
        formTitleText.innerText = "Student Login";
        emailInput.placeholder = "student@ptc.edu.ph";
        submitBtn.className = "w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm transition shadow-lg shadow-indigo-600/30";
        adminFooter.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    switchTab('<?= htmlspecialchars($active_tab) ?>');
});
</script>

<?php require_once 'includes/footer.php'; ?>