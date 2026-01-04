<?php require_once 'db.php'; require_once 'header.php'; ?>

<header class="hero-section">
    <div class="container">
        <h1 class="hero-title">Наші Послуги</h1>
        <p class="hero-text">Оберіть рішення, яке підходить саме вам.</p>
    </div>
</header>

<div class="container overlap-container">
    <div class="row g-4">
        <?php
        $sql = "SELECT title, description, stats_count FROM services";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $i = 0;
            $icons = ['bi-briefcase-fill', 'bi-people-fill', 'bi-graph-up-arrow', 'bi-laptop', 'bi-search', 'bi-building'];

            while($row = $result->fetch_assoc()) {
                $color_idx = ($i % 3) + 1;
                $card_class = 'card-' . $color_idx;
                $icon_class = 'bg-icon-' . $color_idx;
                $icon = $icons[$i % count($icons)];
                $i++;
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card <?php echo $card_class; ?> h-100 d-flex flex-column text-start">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="icon-box <?php echo $icon_class; ?>">
                                <i class="bi <?php echo $icon; ?>"></i>
                            </div>
                            <?php if($row['stats_count'] > 0): ?>
                                <span class="badge bg-light text-dark border align-self-center px-3 py-2 rounded-pill">
                                    <?php echo $row['stats_count']; ?>+ проєктів
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="fw-bold mb-3"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class="text-muted flex-grow-1">
                            <?php echo htmlspecialchars($row['description']); ?>
                        </p>
                        
                        <div class="mt-4 pt-3 border-top">
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <a href="contacts.php" class="btn btn-primary-custom btn-sm w-100">Замовити консультацію</a>
                            <?php else: ?>
                                <a href="register.php" class="btn btn-primary-custom btn-sm w-100">Почати зараз</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='col-12 text-center text-muted py-5'>Послуги ще не додані.</div>";
        }
        ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>