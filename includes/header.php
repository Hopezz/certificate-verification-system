<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? APP_NAME;
$isAdmin = $isAdmin ?? false;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="<?= $isAdmin ? 'dashboard.php' : 'index.php' ?>">
                <span class="brand-mark">IAEC</span>
                <span>
                    <strong>IAEC University Togo</strong>
                    <small>Certificate Verification</small>
                </span>
            </a>
            <?php if ($isAdmin): ?>
                <nav class="admin-nav">
                    <a href="dashboard.php">Dashboard</a>
                    <a href="add_graduate.php">Add Graduate</a>
                    <a href="manage_graduates.php">Records</a>
                    <a href="manage_admins.php">Admins</a>
                    <a href="logout.php">Logout</a>
                </nav>
            <?php else: ?>
                <a class="nav-link" href="../admin/login.php">Admin Login</a>
            <?php endif; ?>
        </div>
    </header>
    <main class="main">
        <?php foreach (get_flashes() as $flash): ?>
            <div class="container">
                <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
            </div>
        <?php endforeach; ?>
