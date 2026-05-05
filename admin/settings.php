<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$settings = getSettings();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $schoolName = trim((string) ($_POST['school_name'] ?? ''));
    $primaryColor = trim((string) ($_POST['primary_color'] ?? ''));
    $secondaryColor = trim((string) ($_POST['secondary_color'] ?? ''));
    $logoPath = $settings['logo_path'] ?: null;

    if ($schoolName === '') {
        $errors['school_name'] = 'School name is required.';
    }

    if (!is_valid_hex_color($primaryColor)) {
        $errors['primary_color'] = 'Enter a valid hex color, for example #469b45.';
    }

    if (!is_valid_hex_color($secondaryColor)) {
        $errors['secondary_color'] = 'Enter a valid hex color, for example #f4821f.';
    }

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            $errors['logo'] = 'Logo upload failed. Please try again.';
        } else {
            $tmpName = (string) $_FILES['logo']['tmp_name'];
            $imageInfo = getimagesize($tmpName);
            $allowedTypes = ['image/png', 'image/jpeg'];
            $mimeType = $imageInfo['mime'] ?? '';

            if (!$imageInfo || !in_array($mimeType, $allowedTypes, true)) {
                $errors['logo'] = 'Upload a valid PNG or JPG logo.';
            } elseif ((int) $_FILES['logo']['size'] > 2 * 1024 * 1024) {
                $errors['logo'] = 'Logo must be 2MB or smaller.';
            } else {
                $uploadDir = __DIR__ . '/../uploads';
                $targetPath = $uploadDir . '/logo.png';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if ($mimeType === 'image/png') {
                    if (!move_uploaded_file($tmpName, $targetPath)) {
                        $errors['logo'] = 'Could not save the uploaded logo.';
                    }
                } else {
                    if (!function_exists('imagecreatefromjpeg') || !function_exists('imagepng')) {
                        $errors['logo'] = 'JPG upload requires the PHP GD extension. Upload a PNG logo instead.';
                    } else {
                        $image = imagecreatefromjpeg($tmpName);

                        if (!$image || !imagepng($image, $targetPath)) {
                            $errors['logo'] = 'Could not convert the JPG logo to PNG.';
                        }

                        if ($image) {
                            imagedestroy($image);
                        }
                    }
                }

                if (!isset($errors['logo'])) {
                    $logoPath = '/uploads/logo.png';
                }
            }
        }
    }

    if (!$errors) {
        $stmt = db()->prepare(
            'INSERT INTO settings (id, school_name, primary_color, secondary_color, logo_path)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                school_name = VALUES(school_name),
                primary_color = VALUES(primary_color),
                secondary_color = VALUES(secondary_color),
                logo_path = VALUES(logo_path),
                updated_at = CURRENT_TIMESTAMP'
        );
        $stmt->execute([
            (int) $settings['id'],
            $schoolName,
            strtolower($primaryColor),
            strtolower($secondaryColor),
            $logoPath,
        ]);

        flash('success', 'School branding settings updated.');
        redirect('settings.php');
    }
}

$pageTitle = 'Settings - ' . $settings['school_name'];
$isAdmin = true;
require_once __DIR__ . '/../includes/header.php';
?>
<section class="admin-section">
    <div class="container narrow-admin">
        <div class="page-heading">
            <div>
                <p class="eyebrow">School Branding</p>
                <h1>Settings</h1>
            </div>
        </div>

        <div class="panel">
            <form method="post" action="settings.php" enctype="multipart/form-data" class="form-grid">
                <?= csrf_field() ?>
                <div class="form-group span-2">
                    <label for="school_name">School Name</label>
                    <input id="school_name" name="school_name" type="text" value="<?= e(old('school_name', $_POST, $settings['school_name'])) ?>" required>
                    <?php if (isset($errors['school_name'])): ?><small class="error"><?= e($errors['school_name']) ?></small><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="primary_color">Primary Color</label>
                    <input id="primary_color" name="primary_color" type="color" value="<?= e(old('primary_color', $_POST, $settings['primary_color'])) ?>" required>
                    <?php if (isset($errors['primary_color'])): ?><small class="error"><?= e($errors['primary_color']) ?></small><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="secondary_color">Secondary Color</label>
                    <input id="secondary_color" name="secondary_color" type="color" value="<?= e(old('secondary_color', $_POST, $settings['secondary_color'])) ?>" required>
                    <?php if (isset($errors['secondary_color'])): ?><small class="error"><?= e($errors['secondary_color']) ?></small><?php endif; ?>
                </div>

                <div class="form-group span-2">
                    <label for="logo">Logo Image <span>PNG or JPG, max 2MB</span></label>
                    <?php if (!empty($settings['logo_path'])): ?>
                        <div class="logo-preview">
                            <img src="<?= e(BASE_URL . $settings['logo_path']) ?>" alt="<?= e($settings['school_name']) ?> logo">
                        </div>
                    <?php endif; ?>
                    <input id="logo" name="logo" type="file" accept=".png,.jpg,.jpeg,image/png,image/jpeg">
                    <?php if (isset($errors['logo'])): ?><small class="error"><?= e($errors['logo']) ?></small><?php endif; ?>
                </div>

                <div class="form-actions span-2">
                    <button class="button primary" type="submit">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
