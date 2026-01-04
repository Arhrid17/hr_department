<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php"); exit();
}
$uid = $_SESSION['user_id'];
$active_tab = $_GET['tab'] ?? 'vacancies';
$msg = ''; 

if (isset($_POST['create_vacancy'])) {
    $title = trim($_POST['title']);
    $salary = trim($_POST['salary']);
    $desc = trim($_POST['description']);
    
    if (empty($title) || empty($desc)) {
        $msg = "<div class='alert alert-danger'>Заповніть обов'язкові поля (Посада, Опис).</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO vacancies (title, salary, description, created_by, status) VALUES (?, ?, ?, ?, 'active')");
        $stmt->bind_param("sssi", $title, $salary, $desc, $uid);
        if ($stmt->execute()) {
            header("Location: employee.php?tab=vacancies"); exit();
        } else {
            $msg = "<div class='alert alert-danger'>Помилка бази даних.</div>";
        }
    }
}

if (isset($_POST['edit_vacancy'])) {
    $title = trim($_POST['title']);
    $salary = trim($_POST['salary']);
    $desc = trim($_POST['description']);
    $vid = $_POST['vac_id'];

    if (empty($title) || empty($desc)) {
        $msg = "<div class='alert alert-danger'>Назва та опис не можуть бути пустими.</div>";
    } else {
        $stmt = $conn->prepare("UPDATE vacancies SET title=?, salary=?, description=? WHERE id=? AND created_by=?");
        $stmt->bind_param("sssii", $title, $salary, $desc, $vid, $uid);
        $stmt->execute();
        header("Location: employee.php?tab=vacancies"); exit();
    }
}

if (isset($_POST['archive'])) { $conn->query("UPDATE vacancies SET status='closed' WHERE id={$_POST['vac_id']} AND created_by=$uid"); header("Location: employee.php?tab=vacancies"); exit(); }
if (isset($_POST['restore'])) { $conn->query("UPDATE vacancies SET status='active' WHERE id={$_POST['vac_id']} AND created_by=$uid"); header("Location: employee.php?tab=archive"); exit(); }
if (isset($_POST['delete'])) { 
    $vid = $_POST['vac_id'];
    $conn->query("DELETE FROM applications WHERE vacancy_id=$vid");
    $conn->query("DELETE FROM vacancies WHERE id=$vid AND created_by=$uid");
    header("Location: employee.php?tab=archive"); exit(); 
}

if (isset($_POST['schedule'])) {
    if (!empty($_POST['date'])) {
        $conn->query("UPDATE applications SET status='interview', interview_date='{$_POST['date']}' WHERE id={$_POST['aid']}");
    } else {
        $msg = "<div class='alert alert-warning'>Вкажіть дату співбесіди.</div>";
    }
}

if (isset($_POST['result'])) {
    $aid = $_POST['aid'];
    $res = $_POST['result']; 
    $conn->query("UPDATE applications SET status='$res' WHERE id=$aid");
    if ($res == 'offer') {
        $vid = $conn->query("SELECT vacancy_id FROM applications WHERE id=$aid")->fetch_assoc()['vacancy_id'];
        $conn->query("UPDATE vacancies SET status='closed' WHERE id=$vid");
    }
}

if (isset($_POST['update_profile'])) {
    $name = trim($_POST['full_name']);
    $pos = trim($_POST['position']);
    $about = trim($_POST['about_text']);
    
    $check = $conn->query("SELECT user_id FROM employee_profiles WHERE user_id=$uid");
    $photo_sql = "";

    if (!empty($_FILES["photo"]["name"])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $ext = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($ext, $allowed)) {
            if ($_FILES["photo"]["size"] < 5000000) {
                $fn = "emp_" . $uid . "_" . time() . "." . $ext;
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $fn)) {
                    $photo_sql = ", photo='$fn'";
                    $new_photo_name = $fn; 
                } else {
                    $msg = "<div class='alert alert-danger'>Помилка завантаження файлу.</div>";
                }
            } else {
                $msg = "<div class='alert alert-warning'>Файл занадто великий (макс 5MB).</div>";
            }
        } else {
            $msg = "<div class='alert alert-warning'>Дозволені формати: JPG, PNG, WEBP.</div>";
        }
    }
    
    if (empty($msg)) {
        if ($check->num_rows > 0) {
            $conn->query("UPDATE employee_profiles SET full_name='$name', position='$pos', about_text='$about' $photo_sql WHERE user_id=$uid");
        } else {
            $p_val = isset($new_photo_name) ? $new_photo_name : '';
            $stmt = $conn->prepare("INSERT INTO employee_profiles (user_id, full_name, position, about_text, photo) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $uid, $name, $pos, $about, $p_val);
            $stmt->execute();
        }
        header("Location: employee.php?tab=profile"); exit();
    }
}

$my_profile = $conn->query("SELECT * FROM employee_profiles WHERE user_id=$uid")->fetch_assoc();
$photo_url = !empty($my_profile['photo']) ? "uploads/".$my_profile['photo'] : "https://via.placeholder.com/150";
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>HR Панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        .profile-preview-fix {
            width: 150px; height: 150px; object-fit: cover; border-radius: 50%;
            border: 4px solid var(--primary); display: block; margin: 0 auto 20px auto; background-color: #f8fafc;
        }
        .isolated-nav { background: rgba(255, 255, 255, 0.98); box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 15px 0; position: sticky; top: 0; z-index: 1000; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg isolated-nav">
  <div class="container">
    <span class="navbar-brand text-primary fw-bold">
        <i class="bi bi-briefcase-fill me-2"></i>Панель Рекрутера
    </span>
    <div class="d-flex align-items-center">
        <span class="me-3 fw-bold text-dark d-none d-md-block"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">Вихід</a>
    </div>
  </div>
</nav>

<header class="hero-section">
    <div class="container"><h1 class="hero-title">Робочий стіл</h1></div>
</header>

<div class="container overlap-container">
    <?php echo $msg; ?>
    
    <div class="text-center">
        <ul class="nav nav-pills" id="pills-tab">
            <li class="nav-item"><a class="nav-link <?php echo $active_tab=='vacancies'?'active':''; ?>" href="?tab=vacancies">Вакансії</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $active_tab=='interviews'?'active':''; ?>" href="?tab=interviews">Співбесіди</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $active_tab=='archive'?'active':''; ?>" href="?tab=archive">Архів</a></li>
            <li class="nav-item"><a class="nav-link <?php echo $active_tab=='profile'?'active':''; ?>" href="?tab=profile">Профіль</a></li>
        </ul>
    </div>

    <div class="tab-content mt-4">
        
        <div class="tab-pane fade <?php echo $active_tab=='vacancies'?'show active':''; ?>">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="dashboard-card sticky-top" style="top:20px">
                        <h5 class="fw-bold mb-3 text-primary">Нова вакансія</h5>
                        <form method="POST">
                            <div class="mb-3"><label class="small text-muted fw-bold">ПОСАДА</label><input type="text" name="title" class="form-control bg-light border-0" required></div>
                            <div class="mb-3"><label class="small text-muted fw-bold">ЗАРПЛАТА</label><input type="text" name="salary" class="form-control bg-light border-0"></div>
                            <div class="mb-4"><label class="small text-muted fw-bold">ОПИС</label><textarea name="description" class="form-control bg-light border-0" rows="4" required></textarea></div>
                            <button name="create_vacancy" class="btn btn-primary-custom w-100">Опублікувати</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8">
                    <form method="GET" class="search-bar mb-4 d-flex justify-content-end">
                        <input type="hidden" name="tab" value="vacancies">
                        <div class="search-input-group shadow-sm">
                            <input type="text" name="s_vac" class="form-control" placeholder="Пошук..." value="<?php echo $_GET['s_vac']??''; ?>">
                            <button class="btn btn-primary-custom btn-sm">OK</button>
                        </div>
                    </form>

                    <?php
                    $sv=$_GET['s_vac']??'';
                    $sql="SELECT * FROM vacancies WHERE created_by=$uid AND status='active'";
                    if($sv) $sql.=" AND title LIKE '%$sv%'";
                    $vacs=$conn->query($sql." ORDER BY id DESC");
                    
                    if($vacs->num_rows>0): while($v=$vacs->fetch_assoc()): $vid=$v['id']; $cnt=$conn->query("SELECT id FROM applications WHERE vacancy_id=$vid AND status IN ('new','viewed')")->num_rows; ?>
                    <div class="dashboard-card mb-3 p-4 h-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div><h5 class="fw-bold mb-0 text-primary"><?php echo htmlspecialchars($v['title']); ?></h5><small class="text-muted"><?php echo htmlspecialchars($v['salary']); ?></small></div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-light border rounded-circle" data-bs-toggle="modal" data-bs-target="#editM" onclick="fillEdit('<?php echo $vid; ?>','<?php echo $v['title']; ?>','<?php echo $v['salary']; ?>',`<?php echo $v['description']; ?>`)"><i class="bi bi-pencil"></i></button>
                                <form method="POST" onsubmit="return confirm('В архів?');"><input type="hidden" name="vac_id" value="<?php echo $vid; ?>"><button name="archive" class="btn btn-sm btn-light border rounded-circle text-warning"><i class="bi bi-archive"></i></button></form>
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#c<?php echo $vid; ?>">Нові <span class="badge bg-primary"><?php echo $cnt; ?></span></button>
                            </div>
                        </div>
                        <div class="collapse" id="c<?php echo $vid; ?>"><div class="bg-light p-3 rounded mt-3 border"><?php $apps=$conn->query("SELECT a.id, u.username, r.file_path FROM applications a JOIN users u ON a.user_id=u.id LEFT JOIN resumes r ON u.id=r.user_id WHERE a.vacancy_id=$vid AND a.status IN ('new','viewed')"); while($a=$apps->fetch_assoc()): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-1">
                                <span><strong><?php echo $a['username']; ?></strong> <?php if($a['file_path']) echo "<a href='uploads/{$a['file_path']}' target='_blank'>[Резюме]</a>"; ?></span>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-warning py-0 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#intM" onclick="setInt('<?php echo $a['id']; ?>')">Співбесіда</button>
                                    <form method="POST"><input type="hidden" name="aid" value="<?php echo $a['id']; ?>"><input type="hidden" name="res" value="rejected"><button name="result" class="btn btn-sm btn-danger py-0 rounded-circle">X</button></form>
                                </div>
                            </div>
                        <?php endwhile; ?></div></div>
                    </div>
                    <?php endwhile; else: echo "<div class='alert alert-light text-center'>Немає активних вакансій.</div>"; endif; ?>
                </div>
            </div>
        </div>

        <div class="tab-pane fade <?php echo $active_tab=='interviews'?'show active':''; ?>">
            <div class="row justify-content-center"><div class="col-lg-10"><div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Календар</h4>
                    <form method="GET" class="compact-search">
                        <input type="hidden" name="tab" value="interviews">
                        <div class="search-input-group shadow-sm">
                            <input type="text" name="s_int" class="form-control" placeholder="Пошук..." value="<?php echo $_GET['s_int']??''; ?>">
                            <button class="btn btn-primary-custom btn-sm">OK</button>
                        </div>
                    </form>
                </div>
                <?php $si=$_GET['s_int']??''; $sql="SELECT a.id, a.interview_date, u.username, v.title FROM applications a JOIN vacancies v ON a.vacancy_id=v.id JOIN users u ON a.user_id=u.id WHERE v.created_by=$uid AND a.status='interview'"; if($si)$sql.=" AND u.username LIKE '%$si%'"; $ints=$conn->query($sql." ORDER BY a.interview_date ASC"); if($ints->num_rows>0): ?>
                <table class="table align-middle"><thead class="table-light"><tr><th>Дата</th><th>Кандидат</th><th>Вакансія</th><th>Дія</th></tr></thead><tbody><?php while($i=$ints->fetch_assoc()): ?>
                <tr><td class="fw-bold text-primary"><?php echo date('d.m H:i',strtotime($i['interview_date'])); ?></td><td><?php echo $i['username']; ?></td><td class="small text-muted"><?php echo $i['title']; ?></td><td>
                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="aid" value="<?php echo $i['id']; ?>">
                        <button name="result" value="offer" class="btn btn-sm btn-success rounded-pill fw-bold" onclick="return confirm('Вакансія закриється?');">ОФЕР</button>
                        <button name="result" value="rejected" class="btn btn-sm btn-outline-danger rounded-pill">Відмова</button>
                    </form>
                </td></tr>
                <?php endwhile; ?></tbody></table><?php else: echo "<p class='text-center text-muted'>Співбесід немає.</p>"; endif; ?>
            </div></div></div>
        </div>

        <div class="tab-pane fade <?php echo $active_tab=='archive'?'show active':''; ?>">
            <div class="row justify-content-center"><div class="col-lg-8">
                 <div class="d-flex justify-content-end mb-3">
                    <form method="GET" class="compact-search w-100">
                        <input type="hidden" name="tab" value="archive">
                        <div class="search-input-group shadow-sm">
                            <input type="text" name="s_arch" class="form-control" placeholder="Пошук в архіві..." value="<?php echo $_GET['s_arch']??''; ?>">
                            <button class="btn btn-primary-custom btn-sm">Знайти</button>
                        </div>
                    </form>
                 </div>
                <?php $sa=$_GET['s_arch']??''; $sql="SELECT * FROM vacancies WHERE created_by=$uid AND status='closed'"; if($sa)$sql.=" AND title LIKE '%$sa%'"; $arch=$conn->query($sql." ORDER BY id DESC"); 
                while($v=$arch->fetch_assoc()): $off=$conn->query("SELECT u.username FROM applications a JOIN users u ON a.user_id=u.id WHERE a.vacancy_id={$v['id']} AND a.status='offer'")->fetch_assoc(); ?>
                <div class="dashboard-card mb-3 p-3 h-auto bg-light border-start border-4 border-secondary"><div class="d-flex justify-content-between align-items-center"><div><h6 class="fw-bold mb-1"><?php echo $v['title']; ?></h6><?php if($off): ?><span class="badge bg-success rounded-pill">Найнято: <?php echo $off['username']; ?></span><?php else: ?><span class="badge bg-secondary rounded-pill">Закрито</span><?php endif; ?></div><div class="d-flex gap-2"><form method="POST"><input type="hidden" name="vac_id" value="<?php echo $v['id']; ?>"><button name="restore" class="btn btn-sm btn-outline-success rounded-pill">Відновити</button></form><form method="POST" onsubmit="return confirm('Видалити назавжди?');"><input type="hidden" name="vac_id" value="<?php echo $v['id']; ?>"><button name="delete" class="btn btn-sm btn-outline-danger rounded-circle"><i class="bi bi-trash"></i></button></form></div></div></div>
                <?php endwhile; ?>
            </div></div>
        </div>

        <div class="tab-pane fade <?php echo $active_tab=='profile'?'show active':''; ?>">
            <div class="row justify-content-center"><div class="col-md-6"><div class="dashboard-card text-center"><h4 class="fw-bold mb-4">Профіль</h4><form method="POST" enctype="multipart/form-data">
                <div class="d-flex justify-content-center">
                    <img src="<?php echo $photo_url; ?>" id="preview" class="profile-preview-fix shadow">
                </div>
                <label class="btn btn-outline-primary btn-sm rounded-pill mt-3 mb-4">Змінити фото <input type="file" name="photo" style="display:none" onchange="previewImage(this)"></label>
                <input type="text" name="full_name" class="form-control mb-3 bg-light border-0" value="<?php echo $my_profile['full_name']??''; ?>" placeholder="Ім'я" required>
                <input type="text" name="position" class="form-control mb-3 bg-light border-0" value="<?php echo $my_profile['position']??''; ?>" placeholder="Посада">
                <textarea name="about_text" class="form-control mb-4 bg-light border-0" rows="3" placeholder="Про мене"><?php echo $my_profile['about_text']??''; ?></textarea>
                <button name="update_profile" class="btn btn-primary-custom w-100">Зберегти</button>
            </form></div></div></div>
        </div>
    </div>
</div>

<div class="modal fade" id="editM" tabindex="-1"><div class="modal-dialog"><div class="modal-content p-4 rounded-4 border-0"><h5 class="fw-bold mb-3">Редагувати</h5><form method="POST"><input type="hidden" name="vac_id" id="eid"><input type="text" name="title" id="et" class="form-control mb-2"><input type="text" name="salary" id="es" class="form-control mb-2"><textarea name="description" id="ed" class="form-control mb-3"></textarea><button name="edit_vacancy" class="btn btn-primary-custom w-100">Зберегти</button></form></div></div></div>
<div class="modal fade" id="intM" tabindex="-1"><div class="modal-dialog modal-sm"><div class="modal-content p-4 rounded-4 border-0"><h5 class="fw-bold mb-3">Час співбесіди</h5><form method="POST"><input type="hidden" name="aid" id="iaid"><input type="datetime-local" name="date" class="form-control mb-3" required><button name="schedule" class="btn btn-warning w-100 fw-bold">Призначити</button></form></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewImage(i){if(i.files&&i.files[0]){var r=new FileReader();r.onload=function(e){document.getElementById('preview').src=e.target.result;};r.readAsDataURL(i.files[0]);}}
function fillEdit(id,t,s,d){document.getElementById('eid').value=id;document.getElementById('et').value=t;document.getElementById('es').value=s;document.getElementById('ed').value=d;}
function setInt(id){document.getElementById('iaid').value=id;}
</script>
</body>
</html>