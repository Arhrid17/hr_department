<?php require_once 'db.php'; require_once 'header.php'; ?>

<header class="hero-section">
    <div class="container">
        <h1 class="hero-title">Майбутнє HR вже тут</h1>
        <p class="hero-text mb-5">Потужна платформа для автоматизації рекрутингу. <br>Швидко, зручно, ефективно.</p>
        
        <div class="d-flex justify-content-center gap-3">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn-hero-primary">Почати зараз</a>
                <a href="about.php" class="btn-hero-outline">Дізнатись більше</a>
            <?php else: ?>
                <?php 
                    $link = 'index.php';
                    if($_SESSION['role'] == 'employee') $link = 'employee.php';
                    if($_SESSION['role'] == 'candidate') $link = 'candidate.php';
                    if($_SESSION['role'] == 'admin') $link = 'admin.php';
                ?>
                <a href="<?php echo $link; ?>" class="btn-hero-primary">Перейти в кабінет</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="container overlap-container">
    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="feature-card card-1 text-center">
                <div class="icon-box bg-icon-1 mb-3"><i class="bi bi-rocket-takeoff-fill"></i></div>
                <h3>Швидкість</h3>
                <p class="text-muted">Миттєвий доступ до бази кандидатів.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card card-2 text-center">
                <div class="icon-box bg-icon-2 mb-3"><i class="bi bi-shield-check"></i></div>
                <h3>Безпека</h3>
                <p class="text-muted">Захищені канали зв'язку.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card card-3 text-center">
                <div class="icon-box bg-icon-3 mb-3"><i class="bi bi-bar-chart-fill"></i></div>
                <h3>Аналітика</h3>
                <p class="text-muted">Зручні дашборди для відстеження.</p>
            </div>
        </div>
    </div>

    <div class="text-center mt-5 pt-5 mb-5">
        <h2 class="fw-bold display-6">Що кажуть клієнти</h2>
        <div class="mx-auto bg-primary rounded mt-3" style="width: 60px; height: 4px;"></div>
    </div>

    <div class="position-relative px-4">
        <div id="reviewsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                $sql = "SELECT author_name, review_text, rating FROM reviews ORDER BY id DESC LIMIT 9";
                $result = $conn->query($sql);
                $all_reviews = [];
                
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $all_reviews[] = $row;
                    }
                }

                $chunks = array_chunk($all_reviews, 3);
                $is_first = true;

                if (count($chunks) > 0) {
                    foreach ($chunks as $chunk) {
                        $active = $is_first ? 'active' : '';
                        ?>
                        <div class="carousel-item <?php echo $active; ?>">
                            <div class="row g-4">
                                <?php foreach ($chunk as $review): ?>
                                    <div class="col-md-4">
                                        <div class="feature-card text-center h-100 p-4 d-flex flex-column">
                                            <div class="text-warning fs-5 mb-3">
                                                <?php for($i=0; $i<$review['rating']; $i++) echo '<i class="bi bi-star-fill"></i>'; ?>
                                            </div>
                                            <p class="fst-italic text-muted flex-grow-1">
                                                "<?php echo htmlspecialchars($review['review_text']); ?>"
                                            </p>
                                            <h6 class="fw-bold text-primary mt-4 mb-0 text-uppercase">
                                                <?php echo htmlspecialchars($review['author_name']); ?>
                                            </h6>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php
                        $is_first = false;
                    }
                } else {
                    echo "<p class='text-center text-muted'>Відгуків поки немає.</p>";
                }
                ?>
            </div>

            <?php if(count($chunks) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#reviewsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#reviewsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>