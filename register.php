<?php
session_start();
require_once 'db.php';
require_once 'functions.php'; 

if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$error = ''; 
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = clean($_POST['username']);
    $email = clean($_POST['email']);
    $password = $_POST['password']; 

    $validation_errors = validate_registration($username, $email, $password);

    if (!empty($validation_errors)) {
        $error = implode("<br>", $validation_errors);
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Цей email вже зареєстрований!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'candidate')");
            $stmt->bind_param("sss", $username, $email, $hashed);

            if ($stmt->execute()) {
                $success = "Акаунт створено! <a href='login.php' class='fw-bold text-success'>Увійти</a>";
            } else {
                $error = "Помилка бази даних.";
            }
        }
    }
}
require_once 'header.php';
?>
<script src="js/validation.js"></script> 

<header class="hero-section">
    <div class="container"><h1 class="hero-title">Створити акаунт</h1></div>
</header>

<div class="container overlap-container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="form-card">
                <div class="text-center mb-4"><h4 class="fw-bold">Реєстрація</h4></div>

                <?php if($error): ?><div class="alert alert-danger text-center small"><?php echo $error; ?></div><?php endif; ?>
                <?php if($success): ?><div class="alert alert-success text-center"><?php echo $success; ?></div><?php endif; ?>

                <?php if(!$success): ?>
                <form method="post" novalidate> <div class="mb-3">
                        <label class="form-label">ІМ'Я</label>
                        <input type="text" name="username" class="form-control bg-light border-0" required minlength="2">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">EMAIL</label>
                        <input type="email" name="email" class="form-control bg-light border-0" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">ПАРОЛЬ (Мін. 6)</label>
                        <input type="password" name="password" class="form-control bg-light border-0" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary-custom shadow-sm">Зареєструватися</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>