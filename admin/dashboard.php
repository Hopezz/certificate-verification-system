<?php
require_once __DIR__ . '/../includes/auth.php';
$admin = require_admin();

$totalGraduates = (int) db()->query('SELECT COUNT(*) FROM graduates')->fetchColumn();
$totalAdmins = (int) db()->query('SELECT COUNT(*) FROM admins')->fetchColumn();
$latestStmt = db()->query('SELECT name, matric_number, program, year_of_graduation FROM graduates ORDER BY id DESC LIMIT 5');
$latestGraduates = $latestStmt->fetchAll();

$pageTitle = 'Admin Dashboard - IAEC University Togo';
$isAdmin = true;
require_once __DIR__ . '/../includes/header.php';
?>
<section class="admin-section">
    <div class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Administration</p>
                <h1>Dashboard</h1>
            </div>
            <a class="button primary" href="add_graduate.php">Add Graduate</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <span>Total Graduates</span>
                <strong><?= $totalGraduates ?></strong>
            </div>
            <div class="stat-card">
                <span>Total Admins</span>
                <strong><?= $totalAdmins ?></strong>
            </div>
            <div class="stat-card">
                <span>Signed In As</span>
                <strong><?= e($admin['role']) ?></strong>
            </div>
        </div>

        <div class="panel">
            <div class="panel-heading">
                <h2>Recent Graduate Records</h2>
                <a href="manage_graduates.php">View all</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Matric Number</th>
                            <th>Program</th>
                            <th>Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latestGraduates as $graduate): ?>
                            <tr>
                                <td><?= e($graduate['name']) ?></td>
                                <td><?= e($graduate['matric_number']) ?></td>
                                <td><?= e(program_label($graduate['program'])) ?></td>
                                <td><?= e((string) $graduate['year_of_graduation']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$latestGraduates): ?>
                            <tr><td colspan="4" class="empty">No graduate records yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

