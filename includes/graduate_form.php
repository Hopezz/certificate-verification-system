<?php
$graduate = $graduate ?? [];
$errors = $errors ?? [];
$action = $action ?? '';
$buttonLabel = $buttonLabel ?? 'Save Graduate';
?>
<form class="form-grid" method="post" action="<?= e($action) ?>">
    <?= csrf_field() ?>
    <div class="form-group span-2">
        <label for="name">Full Name</label>
        <input id="name" name="name" type="text" value="<?= e(old('name', $_POST, $graduate['name'] ?? '')) ?>" required>
        <?php if (isset($errors['name'])): ?><small class="error"><?= e($errors['name']) ?></small><?php endif; ?>
    </div>

    <div class="form-group">
        <label for="department">Department</label>
        <input id="department" name="department" type="text" value="<?= e(old('department', $_POST, $graduate['department'] ?? '')) ?>" required>
        <?php if (isset($errors['department'])): ?><small class="error"><?= e($errors['department']) ?></small><?php endif; ?>
    </div>

    <div class="form-group">
        <label for="program">Program</label>
        <?php $program = old('program', $_POST, $graduate['program'] ?? 'bachelor'); ?>
        <select id="program" name="program" required>
            <option value="bachelor" <?= $program === 'bachelor' ? 'selected' : '' ?>>Bachelor</option>
            <option value="master" <?= $program === 'master' ? 'selected' : '' ?>>Master</option>
            <option value="phd" <?= $program === 'phd' ? 'selected' : '' ?>>PhD</option>
        </select>
        <?php if (isset($errors['program'])): ?><small class="error"><?= e($errors['program']) ?></small><?php endif; ?>
    </div>

    <div class="form-group">
        <label for="grade">Grade <span>Only for Bachelor</span></label>
        <input id="grade" name="grade" type="text" value="<?= e(old('grade', $_POST, $graduate['grade'] ?? '')) ?>">
        <?php if (isset($errors['grade'])): ?><small class="error"><?= e($errors['grade']) ?></small><?php endif; ?>
    </div>

    <div class="form-group">
        <label for="year_of_graduation">Year of Graduation</label>
        <input id="year_of_graduation" name="year_of_graduation" type="number" min="1900" max="<?= date('Y') + 1 ?>" value="<?= e(old('year_of_graduation', $_POST, $graduate['year_of_graduation'] ?? '')) ?>" required>
        <?php if (isset($errors['year_of_graduation'])): ?><small class="error"><?= e($errors['year_of_graduation']) ?></small><?php endif; ?>
    </div>

    <div class="form-group">
        <label for="current_status">Current Status</label>
        <input id="current_status" name="current_status" type="text" value="<?= e(old('current_status', $_POST, $graduate['current_status'] ?? '')) ?>" required>
        <?php if (isset($errors['current_status'])): ?><small class="error"><?= e($errors['current_status']) ?></small><?php endif; ?>
    </div>

    <div class="form-group">
        <label for="matric_number">Matric Number</label>
        <input id="matric_number" name="matric_number" type="text" value="<?= e(old('matric_number', $_POST, $graduate['matric_number'] ?? '')) ?>" required>
        <?php if (isset($errors['matric_number'])): ?><small class="error"><?= e($errors['matric_number']) ?></small><?php endif; ?>
    </div>

    <div class="form-actions span-2">
        <button class="button primary" type="submit"><?= e($buttonLabel) ?></button>
        <a class="button secondary" href="manage_graduates.php">Cancel</a>
    </div>
</form>
