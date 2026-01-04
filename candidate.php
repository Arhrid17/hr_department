<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'candidate') {
    header("Location: login.php"); exit();
}
$uid = $_SESSION['user_id'];
$username = $_SESSION['username']; 
$message = '';

$active_tab = 'profile'; 
if (isset($_GET['search_history']) || isset($_GET['sort_history']) || isset($_POST['add_review'])) $active_tab = 'apps';
if (isset($_GET['search_vacancies']) || isset($_GET['sort_vacancies']) || isset($_POST['apply_id'])) $active_tab = 'jobs';

if (isset($_POST['add_review'])) {
    $vid = (int)$_POST['review_vac_id'];
    $text = trim($_POST['review_text']);
    $rating = (int)$_POST['rating'];
    
    if (empty($text) || strlen($text) < 5) {
        $message = "<div class='alert alert-danger'>Відгук занадто короткий.</div>";
    } elseif ($rating < 1 || $rating > 5) {
        $message = "<div class='alert alert-danger'>Некоректна оцінка.</div>";
    } else {
        $check = $conn->query("SELECT id FROM reviews WHERE user_id=$uid AND vacancy_id=$vid");
        if ($check->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO reviews (user_id, vacancy_id, author_name, review_text, rating) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $uid, $vid, $username, $text, $rating);
            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>Дякуємо! Ваш відгук опубліковано.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Помилка бази даних.</div>";
            }
        } else {
            $message = "<div class='alert alert-warning'>Ви вже залишили відгук на цю вакансію.</div>";
        }
    }
}

if (isset($_POST['upload_resume'])) {
    $summary = $conn->real_escape_string(trim($_POST['summary']));
    $skills = $conn->real_escape_string(trim($_POST['skills']));
    
    if (!empty($_FILES["resume_file"]["name"])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $fileType = strtolower(pathinfo($_FILES["resume_file"]["name"], PATHINFO_EXTENSION));
        $fileName = "resume_" . $uid . "_" . time() . "." . $fileType;
        $target_file = $target_dir . $fileName;
        $uploadOk = 1;

        $allowed_types = ['pdf', 'doc', 'docx'];
        if (!in_array($fileType, $allowed_types)) {
            $message = "<div class='alert alert-danger'>Дозволені тільки формати: PDF, DOC, DOCX.</div>";
            $uploadOk = 0;
        }

        if ($_FILES["resume_file"]["size"] > 5 * 1024 * 1024) {
            $message = "<div class='alert alert-danger'>Файл занадто великий (макс 5MB).</div>";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["resume_file"]["tmp_name"], $target_file)) {
                $old_res = $conn->query("SELECT file_path FROM resumes WHERE user_id=$uid")->fetch_assoc();
                if ($old_res && !empty($old_res['file_path']) && file_exists($target_dir . $old_res['file_path'])) {
                    unlink($target_dir . $old_res['file_path']);
                }

                $check = $conn->query("SELECT id FROM resumes WHERE user_id=$uid");
                if ($check->num_rows > 0) {
                    $stmt = $conn->prepare("UPDATE resumes SET summary=?, skills=?, file_path=? WHERE user_id=?");
                    $stmt->bind_param("sssi", $summary, $skills, $fileName, $uid);
                } else {
                    $stmt = $conn->prepare("INSERT INTO resumes (user_id, summary, skills, file_path) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isss", $uid, $summary, $skills, $fileName);
                }
                $stmt->execute();
                $message = "<div class='alert alert-success'>Резюме успішно оновлено!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Помилка при завантаженні файлу.</div>";
            }
        }
    } else {
        $check = $conn->query("SELECT id FROM resumes WHERE user_id=$uid");
        if ($check->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE resumes SET summary=?, skills=? WHERE user_id=?");
            $stmt->bind_param("ssi", $summary, $skills, $uid);
        } else {
            $stmt = $conn->prepare("INSERT INTO resumes (user_id, summary, skills) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $uid, $summary, $skills);
        }
        $stmt->execute();
        $message = "<div class='alert alert-success'>Текстові дані оновлено!</div>";
    }
}

if (isset($_POST['delete_resume_file'])) {
    $res = $conn->query("SELECT file_path FROM resumes WHERE user_id=$uid")->fetch_assoc();
    if ($res && !empty($res['file_path'])) {
        if (file_exists("uploads/" . $res['file_path'])) unlink("uploads/" . $res['file_path']);
        $conn->query("UPDATE resumes SET file_path=NULL WHERE user_id=$uid");
        $message = "<div class='alert alert-info'>Файл видалено.</div>";
    }
}

if (isset($_POST['apply_id'])) {
    $vid = (int)$_POST['apply_id'];
    $has_resume = $conn->query("SELECT id FROM resumes WHERE user_id=$uid")->num_rows > 0;
    
    if ($has_resume) {
        $check_dup = $conn->query("SELECT id FROM applications WHERE user_id=$uid AND vacancy_id=$vid");
        if ($check_dup->num_rows == 0) {
            $conn->query("INSERT INTO applications (user_id, vacancy_id) VALUES ($uid, $vid)");
            $message = "<div class='alert alert-success'>Заявку надіслано!</div>";
        } else {
            $message = "<div class='alert alert-warning'>Ви вже подали заявку на цю вакансію.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Спочатку заповніть профіль!</div>";
    }
}

if (isset($_POST['respond_interview'])) {
    $aid = (int)$_POST['app_id']; 
    $resp = $_POST['response']; 
    $st = ($resp == 'confirmed') ? 'interview_confirmed' : 'interview_rejected';
    $conn->query("UPDATE applications SET status='$st' WHERE id=$aid");
}

$my_resume = $conn->query("SELECT * FROM resumes WHERE user_id=$uid")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Кабінет Кандидата</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <span class="navbar-brand">
        <i class="bi bi-person-badge-fill me-2"></i>Кабінет
    </span>
    <div class="d-flex align-items-center gap-3">
        <span class="fw-bold text-dark d-none d-md-block"><?php echo htmlspecialchars($username); ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-danger rounded-pill px-4">Вихід</a>
    </div>
  </div>
</nav>

<header class="hero-section">
    <div class="container"><h1 class="hero-title">Пошук роботи</h1><p class="hero-text">Керуйте профілем та шукайте вакансії.</p></div>
</header>

<div class="container overlap-container">
    <?php echo $message; ?>
    
    <div class="text-center mb-5">
        <ul class="nav nav-pills justify-content-center d-inline-flex bg-white rounded-pill p-1 shadow-sm" id="pills-tab" role="tablist">
            <li class="nav-item"><button class="nav-link rounded-pill px-4 <?php echo ($active_tab == 'profile') ? 'active' : ''; ?>" data-bs-toggle="pill" data-bs-target="#tab-profile">Профіль</button></li>
            <li class="nav-item"><button class="nav-link rounded-pill px-4 <?php echo ($active_tab == 'apps') ? 'active' : ''; ?>" data-bs-toggle="pill" data-bs-target="#tab-apps">Історія</button></li>
            <li class="nav-item"><button class="nav-link rounded-pill px-4 <?php echo ($active_tab == 'jobs') ? 'active' : ''; ?>" data-bs-toggle="pill" data-bs-target="#tab-jobs">Вакансії</button></li>
        </ul>
    </div>

    <div class="tab-content">
        
        <div class="tab-pane fade <?php echo ($active_tab == 'profile') ? 'show active' : ''; ?>" id="tab-profile">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <h4 class="fw-bold mb-4">Мій Профіль</h4>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3"><label class="small fw-bold text-muted">НАВИЧКИ</label><input type="text" name="skills" class="form-control bg-light border-0" value="<?php echo $my_resume['skills']??''; ?>"></div>
                                    <div class="mb-4"><label class="small fw-bold text-muted">ПРО СЕБЕ</label><textarea name="summary" class="form-control bg-light border-0" rows="5"><?php echo $my_resume['summary']??''; ?></textarea></div>
                                </div>
                                <div class="col-md-5 border-start ps-4">
                                    <label class="small fw-bold text-muted mb-2">ФАЙЛ</label>
                                    <?php if (!empty($my_resume['file_path'])): ?>
                                        <div class="p-3 bg-light rounded text-center mb-3">
                                            <i class="bi bi-file-earmark-check text-success fs-1"></i><br><small>Завантажено</small>
                                            <a href="uploads/<?php echo $my_resume['file_path']; ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill w-100 mt-2">Переглянути</a>
                                            <button type="submit" name="delete_resume_file" class="btn btn-sm btn-outline-danger rounded-pill w-100 mt-2" onclick="return confirm('Видалити?');">Видалити</button>
                                        </div>
                                    <?php else: ?>
                                        <div class="p-3 bg-light rounded text-center mb-3 border border-dashed"><small>Файл відсутній</small></div>
                                    <?php endif; ?>
                                    <input type="file" name="resume_file" class="form-control form-control-sm mb-3">
                                </div>
                            </div>
                            <button type="submit" name="upload_resume" class="btn btn-primary-custom w-auto px-5">Зберегти</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade <?php echo ($active_tab == 'apps') ? 'show active' : ''; ?>" id="tab-apps">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                            <h4 class="fw-bold mb-0">Мої відгуки</h4>
                            <form method="GET" class="d-flex gap-2">
                                <input type="hidden" name="tab" value="apps">
                                <input type="text" name="search_history" class="form-control form-control-sm" placeholder="Пошук..." value="<?php echo $_GET['search_history']??''; ?>">
                                <select name="filter_status" class="form-select form-select-sm w-auto">
                                    <option value="">Всі статуси</option>
                                    <option value="offer" <?php if(($_GET['filter_status']??'')=='offer') echo 'selected'; ?>>Оффери</option>
                                    <option value="interview" <?php if(($_GET['filter_status']??'')=='interview') echo 'selected'; ?>>Співбесіди</option>
                                </select>
                                <button class="btn btn-sm btn-primary-custom" style="width: auto;">OK</button>
                            </form>
                        </div>
                        <?php
                        $sh = $_GET['search_history'] ?? '';
                        $fs = $_GET['filter_status'] ?? '';
                        $sql = "SELECT a.id, a.status, v.title, v.id as vac_id, a.applied_at, a.interview_date 
                                FROM applications a 
                                JOIN vacancies v ON a.vacancy_id=v.id 
                                WHERE a.user_id=$uid";
                        if($sh) $sql .= " AND v.title LIKE '%" . $conn->real_escape_string($sh) . "%'";
                        if($fs) $sql .= " AND a.status LIKE '%" . $conn->real_escape_string($fs) . "%'";
                        $apps = $conn->query($sql." ORDER BY a.id DESC");
                        
                        if($apps->num_rows>0): ?>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover">
                                <thead class="table-light"><tr><th>Вакансія</th><th>Дата</th><th>Статус</th><th>Дії</th></tr></thead>
                                <tbody>
                                    <?php while($app = $apps->fetch_assoc()): 
                                        $bdg = 'bg-secondary'; $txt = $app['status'];
                                        if($txt=='interview') { $bdg='bg-warning text-dark'; $txt='Співбесіда'; }
                                        if($txt=='offer') { $bdg='bg-success'; $txt='ОФФЕР'; }
                                        if($txt=='rejected') { $bdg='bg-danger'; $txt='Відмова'; }

                                        $has_rev = $conn->query("SELECT id FROM reviews WHERE user_id=$uid AND vacancy_id={$app['vac_id']}")->num_rows > 0;
                                    ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($app['title']); ?></td>
                                        <td class="text-muted small"><?php echo date('d.m.Y', strtotime($app['applied_at'])); ?></td>
                                        <td>
                                            <span class="badge <?php echo $bdg; ?> rounded-pill px-3"><?php echo strtoupper($txt); ?></span>
                                            <?php if($app['interview_date'] && strpos($txt, 'interview') !== false): ?>
                                                <div class="small text-primary mt-1"><i class="bi bi-clock"></i> <?php echo date('d.m H:i', strtotime($app['interview_date'])); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($app['status'] == 'interview'): ?>
                                                <form method="POST" class="d-flex gap-1">
                                                    <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                                    <button name="respond_interview" value="confirmed" class="btn btn-sm btn-success rounded-circle" title="Підтвердити"><i class="bi bi-check"></i></button>
                                                    <button name="respond_interview" value="rejected" class="btn btn-sm btn-outline-danger rounded-circle" title="Відхилити"><i class="bi bi-x"></i></button>
                                                </form>
                                            <?php elseif($app['status'] == 'offer'): ?>
                                                <?php if(!$has_rev): ?>
                                                    <button class="btn btn-sm btn-warning rounded-pill fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#reviewModal" onclick="setReviewVac('<?php echo $app['vac_id']; ?>')">
                                                        <i class="bi bi-star-fill"></i> Відгук
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-light border text-success rounded-pill px-3 disabled"><i class="bi bi-check2-all"></i> Є відгук</button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: echo "<p class='text-center text-muted'>Історія порожня.</p>"; endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade <?php echo ($active_tab == 'jobs') ? 'show active' : ''; ?>" id="tab-jobs">
            <div class="dashboard-card mb-4 p-3">
                <form method="GET" class="row g-2">
                    <input type="hidden" name="tab" value="jobs">
                    <div class="col-md-7"><input type="text" name="search_vacancies" class="form-control" placeholder="Пошук..." value="<?php echo $_GET['search_vacancies']??''; ?>"></div>
                    <div class="col-md-3">
                        <select name="sort_vacancies" class="form-select">
                            <option value="newest">Найновіші</option>
                            <option value="salary_desc" <?php if(($_GET['sort_vacancies']??'')=='salary_desc') echo 'selected'; ?>>Зарплата (↓)</option>
                            <option value="salary_asc" <?php if(($_GET['sort_vacancies']??'')=='salary_asc') echo 'selected'; ?>>Зарплата (↑)</option>
                        </select>
                    </div>
                    <div class="col-md-2"><button class="btn btn-primary-custom">Знайти</button></div>
                </form>
            </div>
            <div class="row g-4">
                <?php
                $vs = $_GET['search_vacancies'] ?? '';
                $srt = $_GET['sort_vacancies'] ?? 'newest';
                $sql = "SELECT * FROM vacancies WHERE status='active'";
                if($vs) $sql .= " AND title LIKE '%" . $conn->real_escape_string($vs) . "%'";
                
                if($srt == 'salary_desc') $sql .= " ORDER BY salary DESC";
                elseif($srt == 'salary_asc') $sql .= " ORDER BY salary ASC";
                else $sql .= " ORDER BY id DESC";

                $vacs = $conn->query($sql);
                if($vacs && $vacs->num_rows>0): while($v = $vacs->fetch_assoc()):
                    $app = $conn->query("SELECT id FROM applications WHERE user_id=$uid AND vacancy_id=".$v['id'])->num_rows > 0;
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100 d-flex flex-column text-start">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="text-primary fw-bold mb-0"><?php echo htmlspecialchars($v['title']); ?></h6>
                            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($v['salary']); ?></span>
                        </div>
                        <p class="small text-muted flex-grow-1"><?php echo mb_substr($v['description'],0,100).'...'; ?></p>
                        <div class="mt-3 pt-3 border-top d-flex gap-2">
                            <button class="btn btn-sm btn-outline-dark rounded-pill w-100" data-bs-toggle="modal" data-bs-target="#vModal" onclick="showV('<?php echo htmlspecialchars($v['title']); ?>','<?php echo htmlspecialchars($v['salary']); ?>',`<?php echo htmlspecialchars($v['description']); ?>`,'<?php echo $v['id']; ?>',<?php echo $app?'true':'false'; ?>)">Деталі</button>
                            <?php if($app): ?><button class="btn btn-sm btn-success w-100 disabled rounded-pill">Подано</button>
                            <?php else: ?><form method="POST" class="w-100"><input type="hidden" name="apply_id" value="<?php echo $v['id']; ?>"><button class="btn btn-sm btn-primary-custom rounded-pill w-100" style="padding:6px;">Відгук</button></form><?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; else: echo "<div class='col-12 text-center text-muted'>Нічого не знайдено.</div>"; endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="vModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4 p-4"><h5 class="modal-title fw-bold text-primary" id="mT"></h5><span class="badge bg-warning text-dark mb-3" id="mS"></span><p id="mD" style="white-space: pre-line;"></p><div id="mBtnArea"></div></div></div></div>

<div class="modal fade" id="reviewModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content rounded-4 border-0 p-4"><h5 class="fw-bold mb-3 text-center">Поділіться враженнями</h5><form method="POST">
    <input type="hidden" name="review_vac_id" id="review_vac_id">
    <div class="mb-3"><label class="small text-muted fw-bold">ВАШ ВІДГУК</label><textarea name="review_text" class="form-control bg-light border-0" rows="4" required></textarea></div>
    <div class="mb-4"><select name="rating" class="form-select bg-light border-0"><option value="5">5 - Чудово</option><option value="4">4 - Добре</option><option value="3">3 - Нормально</option><option value="2">2 - Погано</option><option value="1">1 - Жахливо</option></select></div>
    <button name="add_review" class="btn btn-primary-custom w-100">Опублікувати</button>
</form></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showV(t,s,d,id,applied){
    document.getElementById('mT').textContent=t;document.getElementById('mS').textContent=s;document.getElementById('mD').textContent=d;
    let btn = applied ? '<button class="btn btn-success w-100 disabled">Вже подано</button>' : `<form method="POST"><input type="hidden" name="apply_id" value="${id}"><button class="btn btn-primary-custom w-100">Відгукнутися</button></form>`;
    document.getElementById('mBtnArea').innerHTML=btn;
}
function setReviewVac(id) {
    document.getElementById('review_vac_id').value = id;
}
</script>
</body>
</html>