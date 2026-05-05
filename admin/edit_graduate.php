<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM graduates WHERE id = ?');
$stmt->execute([$id]);
$graduate = $stmt->fetch();

if (!$graduate) {
    flash('error', 'Graduate record not found.');
    redirect('manage_graduates.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $errors = validate_graduate($_POST, $id);

    if (!$errors) {
        $stmt = db()->prepare(
            'UPDATE graduates
             SET name = ?, department = ?, program = ?, grade = ?, year_of_graduation = ?, current_status = ?, matric_number = ?, ref_number = ?
             WHERE id = ?'
        );
        $refNumber = trim((string) ($_POST['ref_number'] ?? ''));
        $stmt->execute([
            trim((string) $_POST['name']),
            trim((string) $_POST['department']),
            $_POST['program'],
            $_POST['program'] === 'bachelor' ? trim((string) ($_POST['grade'] ?? '')) : null,
            (int) $_POST['year_of_graduation'],
            trim((string) $_POST['current_status']),
            trim((string) $_POST['matric_number']),
            $refNumber !== '' ? $refNumber : null,
            $id,
        ]);

        flash('success', 'Graduate record updated successfully.');
        redirect('manage_graduates.php');
    }
}

$pageTitle = 'Edit Graduate - IAEC University Togo';
$isAdmin = true;
require_once __DIR__ . '/../includes/header.php';
?>
<section class="admin-section">
    <div class="container narrow-admin">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Graduate Records</p>
                <h1>Edit Graduate</h1>
            </div>
        </div>
        <div class="panel">
            <?php
            $action = 'edit_graduate.php?id=' . $id;
            $buttonLabel = 'Update Graduate';
            require __DIR__ . '/../includes/graduate_form.php';
            ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
