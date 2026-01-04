<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    if ($role == 'employee') header("Location: employee.php");
    elseif ($role == 'candidate') header("Location: candidate.php");
    elseif ($role == 'admin') header("Location: admin.php");
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            
            if ($row['role'] == 'employee') header("Location: employee.php");
            elseif ($row['role'] == 'candidate') header("Location: candidate.php");
            elseif ($row['role'] == 'admin') header("Location: admin.php");
            exit();
        } else {
            $error = "Невірний пароль!";
        }
    } else {
        $error = "Користувача не знайдено.";
    }
}
require_once 'header.php';
?>

<header class="hero-section">
    <div class="container">
        <h1 class="hero-title">Вхід у систему</h1>
        <p class="hero-text">Раді бачити вас знову!</p>
    </div>
</header>

<div class="container overlap-container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="form-card">
                <div class="text-center mb-4">
                    <div class="icon-box bg-icon-1 mb-3 mx-auto"><i class="bi bi-person-lock"></i></div>
                    <h4 class="fw-bold">Авторизація</h4>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger text-center small border-0 bg-danger bg-opacity-10 text-danger mb-4">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">EMAIL</label>
                        <input type="email" name="email" class="form-control" placeholder="name@email.com" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">ПАРОЛЬ</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-primary-custom shadow-sm">Увійти</button>
                    
                    <div class="text-center mt-4">
                        <span class="text-muted small">Немає акаунту? </span>
                        <a href="register.php" class="text-primary fw-bold text-decoration-none small">Зареєструватися</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
</body>
</html>