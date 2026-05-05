<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verify_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = db()->prepare('DELETE FROM graduates WHERE id = ?');
    $stmt->execute([$id]);
    flash('success', 'Graduate record deleted.');
    redirect('manage_graduates.php');
}

$search = trim((string) ($_GET['search'] ?? ''));

if ($search !== '') {
    $stmt = db()->prepare('SELECT * FROM graduates WHERE matric_number LIKE ? ORDER BY id DESC');
    $stmt->execute(['%' . $search . '%']);
} else {
    $stmt = db()->query('SELECT * FROM graduates ORDER BY id DESC');
}

$graduates = $stmt->fetchAll();

$pageTitle = 'Manage Graduates - IAEC University Togo';
$isAdmin = true;
require_once __DIR__ . '/../includes/header.php';
?>
<section class="admin-section">
    <div class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Graduate Records</p>
                <h1>Manage Records</h1>
            </div>
            <a class="button primary" href="add_graduate.php">Add Graduate</a>
        </div>

        <div class="panel">
            <form method="get" action="manage_graduates.php" class="search-row">
                <input name="search" type="text" placeholder="Search by matric number" value="<?= e($search) ?>">
                <button class="button secondary" type="submit">Search</button>
                <?php if ($search !== ''): ?><a class="button ghost" href="manage_graduates.php">Clear</a><?php endif; ?>
            </form>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Program</th>
                            <th>Grade</th>
                            <th>Year</th>
                            <th>Status</th>
                            <th>Matric Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($graduates as $graduate): ?>
                            <tr>
                                <td><?= e($graduate['name']) ?></td>
                                <td><?= e($graduate['department']) ?></td>
                                <td><?= e(program_label($graduate['program'])) ?></td>
                                <td><?= $graduate['program'] === 'bachelor' ? e($graduate['grade'] ?: '-') : '-' ?></td>
                                <td><?= e((string) $graduate['year_of_graduation']) ?></td>
                                <td><?= e($graduate['current_status']) ?></td>
                                <td><?= e($graduate['matric_number']) ?></td>
                                <td class="actions">
                                    <a class="small-link" href="edit_graduate.php?id=<?= (int) $graduate['id'] ?>">Edit</a>
                                    <form method="post" action="manage_graduates.php" onsubmit="return confirm('Delete this graduate record?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $graduate['id'] ?>">
                                        <button class="text-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$graduates): ?>
                            <tr><td colspan="8" class="empty">No graduate records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

