<?php
require_once __DIR__ . '/../includes/functions.php';
$settings = getSettings();
$pageTitle = 'Verify Certificate - ' . $settings['school_name'];
require_once __DIR__ . '/../includes/header.php';
?>
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <p class="eyebrow">Official Verification Portal</p>
            <h1>Verify <?= e($settings['school_name']) ?> graduate records.</h1>
            <p>Enter a matric number or reference number to confirm a graduate certificate record in the university database.</p>
        </div>
        <div class="panel verification-panel">
            <h2>Certificate Verification</h2>
            <form method="get" action="result.php" class="verify-form">
                <label for="matric_number">Enter Matric or Reference Number</label>
                <input id="matric_number" name="matric_number" type="text" placeholder="e.g. IAEC/2024/001" required>
                <button class="button primary full" type="submit">Verify</button>
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
