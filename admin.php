<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}
$uid = $_SESSION['user_id'];
$active_tab = $_GET['tab'] ?? 'users';

if (isset($_POST['create_user'])) {
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $_POST['username'], $_POST['email'], $p, $_POST['role']); $stmt->execute();
    header("Location: admin.php?tab=users"); exit();
}
if (isset($_POST['edit_user'])) {
    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
    $stmt->bind_param("sssi", $_POST['username'], $_POST['email'], $_POST['role'], $_POST['user_id']); $stmt->execute();
    header("Location: admin.php?tab=users"); exit();
}
if (isset($_POST['delete_user'])) {
    if($_POST['user_id'] != $uid) $conn->query("DELETE FROM users WHERE id={$_POST['user_id']}");
    header("Location: admin.php?tab=users"); exit();
}

if (isset($_POST['create_vacancy'])) {
    $stmt = $conn->prepare("INSERT INTO vacancies (title, salary, description, created_by, status) VALUES (?, ?, ?, ?, 'active')");
    $stmt->bind_param("sssi", $_POST['title'], $_POST['salary'], $_POST['description'], $_POST['author_id']); $stmt->execute();
    header("Location: admin.php?tab=vacancies"); exit();
}
if (isset($_POST['edit_vacancy'])) {
    $stmt = $conn->prepare("UPDATE vacancies SET title=?, salary=?, description=?, status=? WHERE id=?");
    $stmt->bind_param("ssssi", $_POST['title'], $_POST['salary'], $_POST['description'], $_POST['status'], $_POST['vac_id']); $stmt->execute();
    header("Location: admin.php?tab=vacancies"); exit();
}
if (isset($_POST['delete_vacancy'])) {
    $conn->query("DELETE FROM applications WHERE vacancy_id={$_POST['vac_id']}");
    $conn->query("DELETE FROM vacancies WHERE id={$_POST['vac_id']}");
    header("Location: admin.php?tab=vacancies"); exit();
}

if (isset($_POST['create_service'])) {
    $stmt = $conn->prepare("INSERT INTO services (title, description, stats_count) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $_POST['title'], $_POST['description'], $_POST['stats']); 
    $stmt->execute();
    header("Location: admin.php?tab=services"); exit();
}
if (isset($_POST['edit_service'])) {
    $stmt = $conn->prepare("UPDATE services SET title=?, description=?, stats_count=? WHERE id=?");
    $stmt->bind_param("ssii", $_POST['title'], $_POST['description'], $_POST['stats'], $_POST['service_id']); 
    $stmt->execute();
    header("Location: admin.php?tab=services"); exit();
}
if (isset($_POST['delete_service'])) {
    $conn->query("DELETE FROM services WHERE id={$_POST['service_id']}");
    header("Location: admin.php?tab=services"); exit();
}

if (isset($_POST['delete_app'])) {
    $conn->query("DELETE FROM applications WHERE id={$_POST['app_id']}");
    header("Location: admin.php?tab=applications"); exit();
}

if (isset($_POST['delete_review'])) {
    $conn->query("DELETE FROM reviews WHERE id={$_POST['rev_id']}");
    header("Location: admin.php?tab=reviews"); exit();
}

$recruiters = $conn->query("SELECT id, username, role FROM users WHERE role IN ('employee', 'admin')");

$stat_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$stat_vacancies = $conn->query("SELECT COUNT(*) FROM vacancies")->fetch_row()[0];
$stat_services = $conn->query("SELECT COUNT(*) FROM services")->fetch_row()[0];
$stat_reviews = $conn->query("SELECT COUNT(*) FROM reviews")->fetch_row()[0];
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        .admin-nav { background: #1e293b !important; }
        .admin-nav .navbar-brand { color: white !important; background: none; -webkit-text-fill-color: initial; }
        .stat-card { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: center; }
        .table-admin th { background: #f8fafc; text-transform: uppercase; font-size: 0.75rem; color: #64748b; letter-spacing: 1px; }
        .action-btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: 0.2s; border: 1px solid #eee; background: white; }
        .action-btn:hover { background: #f1f5f9; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg admin-nav sticky-top">
  <div class="container">
    <span class="navbar-brand"><i class="bi bi-shield-lock-fill me-2"></i>ADMIN PANEL</span>
    <div class="d-flex align-items-center">
        <span class="text-white-50 me-3 small d-none d-md-block"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php" class="btn btn-sm btn-danger rounded-pill px-4">Вихід</a>
    </div>
  </div>
</nav>

<header class="hero-section">
    <div class="container"><h1 class="hero-title">Керування Системою</h1><p class="hero-text opacity-75">Повний доступ до бази даних.</p></div>
</header>

<div class="container overlap-container">
    
    <div class="row g-3 mb-5">
        <div class="col-6 col-md-3"><div class="stat-card"><h2 class="fw-bold text-primary mb-0"><?php echo $stat_users; ?></h2><small class="text-muted">ЮЗЕРИ</small></div></div>
        <div class="col-6 col-md-3"><div class="stat-card"><h2 class="fw-bold text-success mb-0"><?php echo $stat_vacancies; ?></h2><small class="text-muted">ВАКАНСІЇ</small></div></div>
        <div class="col-6 col-md-3"><div class="stat-card"><h2 class="fw-bold text-warning mb-0"><?php echo $stat_services; ?></h2><small class="text-muted">ПОСЛУГИ</small></div></div>
        <div class="col-6 col-md-3"><div class="stat-card"><h2 class="fw-bold text-danger mb-0"><?php echo $stat_reviews; ?></h2><small class="text-muted">ВІДГУКИ</small></div></div>
    </div>

    <div class="text-center mb-4">
        <ul class="nav nav-pills justify-content-center d-inline-flex bg-white rounded-pill p-1 shadow-sm" id="pills-tab">
            <li class="nav-item"><a class="nav-link <?php echo $active_tab=='users'?'active':''; ?>" href="?tab=users">Користувачі</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $active_tab=='vacancies'?'active':''; ?>" href="?tab=vacancies">Вакансії</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $active_tab=='services'?'active':''; ?>" href="?tab=services">Послуги</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $active_tab=='applications'?'active':''; ?>" href="?tab=applications">Заявки</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $active_tab=='reviews'?'active':''; ?>" href="?tab=reviews">Відгуки</a></li>
        </ul>
    </div>

    <div class="tab-content">
        
        <div class="tab-pane fade <?php echo $active_tab=='users'?'show active':''; ?>">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="dashboard-card sticky-top" style="top: 80px;">
                        <h5 class="fw-bold mb-3">Додати юзера</h5>
                        <form method="POST">
                            <input type="text" name="username" class="form-control mb-2 bg-light border-0" placeholder="Ім'я" required>
                            <input type="email" name="email" class="form-control mb-2 bg-light border-0" placeholder="Email" required>
                            <input type="password" name="password" class="form-control mb-2 bg-light border-0" placeholder="Пароль" required>
                            <select name="role" class="form-select mb-3 bg-light border-0">
                                <option value="candidate">Кандидат</option>
                                <option value="employee">Рекрутер</option>
                                <option value="admin">Адмін</option>
                            </select>
                            <button name="create_user" class="btn btn-primary-custom w-100">Створити</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="table-responsive">
                            <table class="table align-middle table-hover table-admin mb-0">
                                <thead><tr><th>Користувач</th><th>Роль</th><th class="text-end">Дії</th></tr></thead>
                                <tbody>
                                    <?php $users=$conn->query("SELECT * FROM users ORDER BY id DESC"); while($u=$users->fetch_assoc()): 
                                        $bg = match($u['role']){'admin'=>'bg-danger','employee'=>'bg-primary',default=>'bg-success'}; ?>
                                    <tr>
                                        <td><div class="fw-bold text-dark"><?php echo htmlspecialchars($u['username']); ?></div><div class="small text-muted"><?php echo htmlspecialchars($u['email']); ?></div></td>
                                        <td><span class="badge <?php echo $bg; ?> rounded-pill bg-opacity-10 text-dark border"><?php echo strtoupper($u['role']); ?></span></td>
                                        <td class="text-end">
                                            <?php if($u['id']!=$uid): ?>
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="action-btn text-primary" onclick="editUser('<?php echo $u['id']; ?>','<?php echo $u['username']; ?>','<?php echo $u['email']; ?>','<?php echo $u['role']; ?>')" data-bs-toggle="modal" data-bs-target="#editUserM"><i class="bi bi-pencil-fill"></i></button>
                                                <form method="POST" onsubmit="return confirm('Видалити?');"><input type="hidden" name="user_id" value="<?php echo $u['id']; ?>"><button name="delete_user" class="action-btn text-danger"><i class="bi bi-trash-fill"></i></button></form>
                                            </div>
                                            <?php else: echo '<span class="badge bg-light text-muted">Ви</span>'; endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade <?php echo $active_tab=='vacancies'?'show active':''; ?>">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="dashboard-card sticky-top" style="top: 80px;">
                        <h5 class="fw-bold mb-3">Додати вакансію</h5>
                        <form method="POST">
                            <input type="text" name="title" class="form-control mb-2 bg-light border-0" placeholder="Посада" required>
                            <input type="text" name="salary" class="form-control mb-2 bg-light border-0" placeholder="Зарплата">
                            <label class="small fw-bold text-muted ms-1">АВТОР:</label>
                            <select name="author_id" class="form-select mb-2 bg-light border-0">
                                <?php $recruiters->data_seek(0); while($r = $recruiters->fetch_assoc()): ?>
                                    <option value="<?php echo $r['id']; ?>" <?php if($r['id']==$uid) echo 'selected'; ?>><?php echo htmlspecialchars($r['username']); ?></option>
                                <?php endwhile; ?>
                            </select>
                            <textarea name="description" class="form-control mb-3 bg-light border-0" rows="3" placeholder="Опис"></textarea>
                            <button name="create_vacancy" class="btn btn-primary-custom w-100">Опублікувати</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="table-responsive">
                            <table class="table align-middle table-hover table-admin mb-0">
                                <thead><tr><th>Вакансія</th><th>Автор</th><th>Статус</th><th class="text-end">Дії</th></tr></thead>
                                <tbody>
                                    <?php $vacs=$conn->query("SELECT v.*, u.username FROM vacancies v LEFT JOIN users u ON v.created_by=u.id ORDER BY v.id DESC"); while($v=$vacs->fetch_assoc()): ?>
                                    <tr>
                                        <td><div class="fw-bold"><?php echo htmlspecialchars($v['title']); ?></div><div class="small text-muted"><?php echo htmlspecialchars($v['salary']); ?></div></td>
                                        <td class="small text-muted"><?php echo htmlspecialchars($v['username']); ?></td>
                                        <td><?php if($v['status']=='active'): ?><span class="badge bg-success rounded-pill">Active</span><?php else: ?><span class="badge bg-secondary rounded-pill">Closed</span><?php endif; ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="action-btn text-primary" onclick="editVac('<?php echo $v['id']; ?>','<?php echo $v['title']; ?>','<?php echo $v['salary']; ?>',`<?php echo $v['description']; ?>`,'<?php echo $v['status']; ?>')" data-bs-toggle="modal" data-bs-target="#editVacM"><i class="bi bi-pencil-fill"></i></button>
                                                <form method="POST" onsubmit="return confirm('Видалити?');"><input type="hidden" name="vac_id" value="<?php echo $v['id']; ?>"><button name="delete_vacancy" class="action-btn text-danger"><i class="bi bi-trash-fill"></i></button></form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade <?php echo $active_tab=='services'?'show active':''; ?>">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="dashboard-card sticky-top" style="top: 80px;">
                        <h5 class="fw-bold mb-3">Додати послугу</h5>
                        <form method="POST">
                            <input type="text" name="title" class="form-control mb-2 bg-light border-0" placeholder="Назва послуги" required>
                            <input type="number" name="stats" class="form-control mb-2 bg-light border-0" placeholder="Кількість проєктів">
                            <textarea name="description" class="form-control mb-3 bg-light border-0" rows="3" placeholder="Опис послуги"></textarea>
                            <button name="create_service" class="btn btn-primary-custom w-100">Додати</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="table-responsive">
                            <table class="table align-middle table-hover table-admin mb-0">
                                <thead><tr><th>Назва</th><th>Проєкти</th><th>Опис</th><th class="text-end">Дії</th></tr></thead>
                                <tbody>
                                    <?php $servs=$conn->query("SELECT * FROM services ORDER BY id DESC"); while($s=$servs->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($s['title']); ?></td>
                                        <td><span class="badge bg-light text-dark border"><?php echo $s['stats_count']; ?>+</span></td>
                                        <td class="small text-muted text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($s['description']); ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="action-btn text-primary" onclick="editServ('<?php echo $s['id']; ?>','<?php echo $s['title']; ?>','<?php echo $s['stats_count']; ?>',`<?php echo $s['description']; ?>`)" data-bs-toggle="modal" data-bs-target="#editServM"><i class="bi bi-pencil-fill"></i></button>
                                                <form method="POST" onsubmit="return confirm('Видалити?');"><input type="hidden" name="service_id" value="<?php echo $s['id']; ?>"><button name="delete_service" class="action-btn text-danger"><i class="bi bi-trash-fill"></i></button></form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade <?php echo $active_tab=='applications'?'show active':''; ?>">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="dashboard-card">
                        <h5 class="fw-bold mb-4">Всі заявки</h5>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover table-admin mb-0">
                                <thead><tr><th>Кандидат</th><th>Вакансія</th><th>Рекрутер</th><th>Статус</th><th class="text-end">Видалити</th></tr></thead>
                                <tbody>
                                    <?php 
                                    $apps = $conn->query("
                                        SELECT a.id, a.status, a.applied_at, 
                                               c.username as cand_name, 
                                               v.title as vac_title, 
                                               r.username as rec_name,
                                               res.file_path
                                        FROM applications a
                                        LEFT JOIN users c ON a.user_id = c.id
                                        LEFT JOIN vacancies v ON a.vacancy_id = v.id
                                        LEFT JOIN users r ON v.created_by = r.id
                                        LEFT JOIN resumes res ON c.id = res.user_id
                                        ORDER BY a.id DESC
                                    ");
                                    while($app = $apps->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo $app['cand_name']; ?></div>
                                            <?php if($app['file_path']): ?><a href="uploads/<?php echo $app['file_path']; ?>" target="_blank" class="badge bg-primary text-decoration-none">Резюме</a><?php endif; ?>
                                        </td>
                                        <td><?php echo $app['vac_title']; ?></td>
                                        <td class="small text-muted"><?php echo $app['rec_name']; ?></td>
                                        <td><span class="badge bg-secondary rounded-pill"><?php echo $app['status']; ?></span></td>
                                        <td class="text-end">
                                            <form method="POST" onsubmit="return confirm('Видалити заявку?');"><input type="hidden" name="app_id" value="<?php echo $app['id']; ?>"><button name="delete_app" class="action-btn text-danger ms-auto"><i class="bi bi-trash"></i></button></form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade <?php echo $active_tab=='reviews'?'show active':''; ?>">
            <div class="row g-3">
                <?php $revs=$conn->query("SELECT * FROM reviews ORDER BY id DESC"); 
                if($revs->num_rows > 0): while($r = $revs->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="dashboard-card p-4 h-100 position-relative d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-primary"><?php echo htmlspecialchars($r['author_name']); ?></h6>
                            <div class="text-warning small"><?php for($i=0; $i<$r['rating']; $i++) echo '★'; ?></div>
                        </div>
                        <p class="text-muted small fst-italic mb-0 flex-grow-1">"<?php echo htmlspecialchars($r['review_text']); ?>"</p>
                        <form method="POST" class="position-absolute top-0 start-100 translate-middle-y me-3" onsubmit="return confirm('Видалити відгук?');">
                            <input type="hidden" name="rev_id" value="<?php echo $r['id']; ?>">
                            <button name="delete_review" class="action-btn text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
                <?php endwhile; else: echo "<div class='col-12 text-center text-muted'>Відгуків немає.</div>"; endif; ?>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="editUserM" tabindex="-1"><div class="modal-dialog"><div class="modal-content p-4 border-0 rounded-4"><h5 class="fw-bold mb-3">Редагувати Юзера</h5><form method="POST"><input type="hidden" name="user_id" id="uid"><input type="text" name="username" id="uname" class="form-control mb-2"><input type="email" name="email" id="uemail" class="form-control mb-2"><select name="role" id="urole" class="form-select mb-3"><option value="candidate">Candidate</option><option value="employee">Recruiter</option><option value="admin">Admin</option></select><button name="edit_user" class="btn btn-primary-custom w-100">Зберегти</button></form></div></div></div>
<div class="modal fade" id="editVacM" tabindex="-1"><div class="modal-dialog"><div class="modal-content p-4 border-0 rounded-4"><h5 class="fw-bold mb-3">Редагувати Вакансію</h5><form method="POST"><input type="hidden" name="vac_id" id="vid"><input type="text" name="title" id="vtitle" class="form-control mb-2"><input type="text" name="salary" id="vsalary" class="form-control mb-2"><textarea name="description" id="vdesc" class="form-control mb-2" rows="3"></textarea><select name="status" id="vstat" class="form-select mb-3"><option value="active">Active</option><option value="closed">Closed</option></select><button name="edit_vacancy" class="btn btn-primary-custom w-100">Зберегти</button></form></div></div></div>
<div class="modal fade" id="editServM" tabindex="-1"><div class="modal-dialog"><div class="modal-content p-4 border-0 rounded-4"><h5 class="fw-bold mb-3">Редагувати Послугу</h5><form method="POST"><input type="hidden" name="service_id" id="sid"><input type="text" name="title" id="stitle" class="form-control mb-2"><input type="number" name="stats" id="sstats" class="form-control mb-2"><textarea name="description" id="sdesc" class="form-control mb-3"></textarea><button name="edit_service" class="btn btn-primary-custom w-100">Зберегти</button></form></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editUser(id,name,email,role){document.getElementById('uid').value=id;document.getElementById('uname').value=name;document.getElementById('uemail').value=email;document.getElementById('urole').value=role;}
function editVac(id,t,s,d,st){document.getElementById('vid').value=id;document.getElementById('vtitle').value=t;document.getElementById('vsalary').value=s;document.getElementById('vdesc').value=d;document.getElementById('vstat').value=st;}
function editServ(id,t,s,d){document.getElementById('sid').value=id;document.getElementById('stitle').value=t;document.getElementById('sstats').value=s;document.getElementById('sdesc').value=d;}
</script>
</body>
</html>