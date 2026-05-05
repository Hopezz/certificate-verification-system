<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $errors = validate_graduate($_POST);

    if (!$errors) {
        $stmt = db()->prepare(
            'INSERT INTO graduates (name, department, program, grade, year_of_graduation, current_status, matric_number, ref_number)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
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
        ]);

        flash('success', 'Graduate record added successfully.');
        redirect('manage_graduates.php');
    }
}

$settings = getSettings();
$pageTitle = 'Add Graduate - ' . $settings['school_name'];
$isAdmin = true;
require_once __DIR__ . '/../includes/header.php';
?>
<section class="admin-section">
    <div class="container narrow-admin">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Graduate Records</p>
                <h1>Add Graduate</h1>
            </div>
        </div>
        <div class="panel">
            <?php
            $action = 'add_graduate.php';
            $buttonLabel = 'Add Graduate';
            require __DIR__ . '/../includes/graduate_form.php';
            ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
