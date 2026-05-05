<?php
require_once __DIR__ . '/../includes/auth.php';
$admin = require_superadmin();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $role = (string) ($_POST['role'] ?? 'admin');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }

        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }

        if (!in_array($role, ['admin', 'superadmin'], true)) {
            $errors['role'] = 'Choose a valid role.';
        }

        $stmt = db()->prepare('SELECT id FROM admins WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'This email is already registered.';
        }

        if (!$errors) {
            $stmt = db()->prepare('INSERT INTO admins (email, password, role) VALUES (?, ?, ?)');
            $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), $role]);
            flash('success', 'Admin account created.');
            redirect('manage_admins.php');
        }
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id === (int) $admin['id']) {
            flash('error', 'You cannot delete your own admin account while signed in.');
            redirect('manage_admins.php');
        }

        $stmt = db()->prepare('DELETE FROM admins WHERE id = ?');
        $stmt->execute([$id]);
        flash('success', 'Admin account deleted.');
        redirect('manage_admins.php');
    }
}

$admins = db()->query('SELECT id, email, role, created_at FROM admins ORDER BY id DESC')->fetchAll();

$settings = getSettings();
$pageTitle = 'Manage Admins - ' . $settings['school_name'];
$isAdmin = true;
require_once __DIR__ . '/../includes/header.php';
?>
<section class="admin-section">
    <div class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Admin Management</p>
                <h1>Manage Admins</h1>
            </div>
        </div>

        <div class="two-column">
            <div class="panel">
                <h2>Add New Admin</h2>
                <form method="post" action="manage_admins.php" class="stack-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="add">
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
                    <div class="form-group">
                        <label for="role">Role</label>
                        <?php $role = old('role', $_POST, 'admin'); ?>
                        <select id="role" name="role" required>
                            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="superadmin" <?= $role === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                        </select>
                        <?php if (isset($errors['role'])): ?><small class="error"><?= e($errors['role']) ?></small><?php endif; ?>
                    </div>
                    <button class="button primary" type="submit">Add Admin</button>
                </form>
            </div>

            <div class="panel">
                <h2>Admin Accounts</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $row): ?>
                                <tr>
                                    <td><?= e($row['email']) ?></td>
                                    <td><?= e($row['role']) ?></td>
                                    <td>
                                        <?php if ((int) $row['id'] !== (int) $admin['id']): ?>
                                            <form method="post" action="manage_admins.php" onsubmit="return confirm('Delete this admin account?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                <button class="text-danger" type="submit">Delete</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="muted">Current user</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
