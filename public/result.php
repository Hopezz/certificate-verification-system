<?php
require_once __DIR__ . '/../includes/functions.php';

$verificationInput = trim((string) ($_GET['matric_number'] ?? ''));
$graduate = null;

if ($verificationInput !== '') {
    $stmt = db()->prepare(
        'SELECT * FROM graduates
         WHERE matric_number = :matric_input
         OR ref_number = :ref_input
         LIMIT 1'
    );
    $stmt->execute([
        'matric_input' => $verificationInput,
        'ref_input' => $verificationInput,
    ]);
    $graduate = $stmt->fetch() ?: null;
}

$pageTitle = $graduate ? 'Verified Student - IAEC University Togo' : 'Verification Result - IAEC University Togo';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="result-section">
    <div class="container narrow">
        <?php if ($graduate): ?>
            <div class="result-card verified">
                <div class="status-badge success">VERIFIED</div>
                <h1>VERIFIED STUDENT OF IAEC UNIVERSITY TOGO</h1>
                <div class="details-grid">
                    <div>
                        <span>Name</span>
                        <strong><?= e($graduate['name']) ?></strong>
                    </div>
                    <div>
                        <span>Department</span>
                        <strong><?= e($graduate['department']) ?></strong>
                    </div>
                    <div>
                        <span>Program</span>
                        <strong><?= e(program_label($graduate['program'])) ?></strong>
                    </div>
                    <?php if ($graduate['program'] === 'bachelor'): ?>
                        <div>
                            <span>Grade</span>
                            <strong><?= e($graduate['grade'] ?: 'Not specified') ?></strong>
                        </div>
                    <?php endif; ?>
                    <div>
                        <span>Year of Graduation</span>
                        <strong><?= e((string) $graduate['year_of_graduation']) ?></strong>
                    </div>
                    <div>
                        <span>Current Status</span>
                        <strong><?= e($graduate['current_status']) ?></strong>
                    </div>
                    <div>
                        <span>Matric Number</span>
                        <strong><?= e($graduate['matric_number']) ?></strong>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="result-card not-verified">
                <div class="status-badge danger">NOT VERIFIED</div>
                <h1>NOT VERIFIED</h1>
                <p>No IAEC University Togo graduate record matched the submitted matric or reference number.</p>
            </div>
        <?php endif; ?>
        <div class="center-actions">
            <a class="button secondary" href="index.php">Search Again</a>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
