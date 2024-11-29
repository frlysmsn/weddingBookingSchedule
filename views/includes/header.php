<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME ?></title>
    
    <!-- CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.css' rel='stylesheet' />
    <link href='https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css' rel='stylesheet' />
    <link href='assets/css/style.css' rel='stylesheet' />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="main-nav">
        <div class="container">
            <a href="index.php" class="logo"><?= SITE_NAME ?></a>
            <div class="nav-links">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['role'] == 'admin'): ?>
                        <a href="index.php?page=admin-dashboard">Dashboard</a>
                    <?php else: ?>
                    <?php endif; ?>
                <?php else: ?>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="container"> 