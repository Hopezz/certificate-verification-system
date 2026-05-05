<?php
require_once __DIR__ . '/../includes/auth.php';

if (current_admin()) {
    redirect('dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }

    if ($password === '') {
        $errors['password'] = 'Enter your password.';
    }

    if (!$errors && login_admin($email, $password)) {
        redirect('dashboard.php');
    }

    if (!$errors) {
        $errors['login'] = 'Invalid email or password.';
    }
}

$pageTitle = 'Admin Login - IAEC University Togo';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="auth-section">
    <div class="panel auth-panel">
        <h1>Admin Login</h1>
        <p>Sign in to manage graduate records.</p>
        <?php if (isset($errors['login'])): ?><div class="alert alert-error"><?= e($errors['login']) ?></div><?php endif; ?>
        <form method="post" action="login.php" class="stack-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="<?= e(old('email', $_POST)) ?>" required>
                <?php if (isset($errors['email'])): ?><small class="error"><?= e($errors['email']) ?></small><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
                <?php if (isset($errors['password'])): ?><small class="error"><?= e($errors['password']) ?></small><?php endif; ?>
            </div>
            <button class="button primary full" type="submit">Login</button>
        </form>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

