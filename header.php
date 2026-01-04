<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR PRO System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="index.php">
        <i class="bi bi-layers-half me-2"></i>HR PRO
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
            <a class="nav-link <?php echo ($page=='index.php')?'active':''; ?>" href="index.php">Головна</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page=='about.php')?'active':''; ?>" href="about.php">Про нас</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page=='services.php')?'active':''; ?>" href="services.php">Послуги</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page=='contacts.php')?'active':''; ?>" href="contacts.php">Контакти</a>
        </li>
      </ul>
      
      <div class="d-flex align-items-center gap-3">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php 
                $role = $_SESSION['role'];
                $cabinet_link = '#';
                $btn_text = 'Кабінет';

                if ($role == 'admin') {
                    $cabinet_link = 'admin.php';
                    $btn_text = 'Адмін-панель';
                } elseif ($role == 'employee') {
                    $cabinet_link = 'employee.php';
                    $btn_text = 'Кабінет Рекрутера';
                } elseif ($role == 'candidate') {
                    $cabinet_link = 'candidate.php';
                    $btn_text = 'Мій Кабінет';
                }
            ?>

            <span class="fw-bold text-dark d-none d-md-block">
                <?php echo htmlspecialchars($_SESSION['username']); ?>
            </span>

            <a href="<?php echo $cabinet_link; ?>" class="btn btn-primary-custom px-4" style="width: auto;">
                <?php echo $btn_text; ?>
            </a>

            <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-circle" title="Вийти">
                <i class="bi bi-box-arrow-right"></i>
            </a>

        <?php else: ?>
            <a href="login.php" class="fw-bold text-dark text-decoration-none">Увійти</a>
            <a href="register.php" class="btn btn-primary-custom px-4" style="width: auto;">Реєстрація</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>