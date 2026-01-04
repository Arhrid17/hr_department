<?php require_once 'db.php'; require_once 'header.php'; ?>

<header class="hero-section">
    <div class="container">
        <h1 class="hero-title">Наша Команда</h1>
        <p class="hero-text">Професіонали, які з'єднують таланти з можливостями.</p>
    </div>
</header>

<div class="container overlap-container">
    
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="feature-card text-center p-5">
                <h3 class="fw-bold mb-4 text-primary">Хто ми?</h3>
                <p class="text-muted lead mb-0">
                    HR PRO — це сучасна платформа для автоматизації рекрутингу. Ми допомагаємо компаніям знаходити найкращих фахівців, а кандидатам — роботу мрії. Наша місія — зробити процес найму прозорим, швидким та зручним для обох сторін.
                </p>
            </div>
        </div>
    </div>

    <h2 class="text-center fw-bold mb-5">Наші Рекрутери</h2>

    <div class="row g-4">
        <?php
        $sql = "SELECT u.username, ep.full_name, ep.position, ep.about_text, ep.photo 
                FROM users u 
                LEFT JOIN employee_profiles ep ON u.id = ep.user_id 
                WHERE u.role = 'employee'";
        
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $i = 0;
            while($row = $result->fetch_assoc()) {
                $border_color = 'card-' . (($i % 3) + 1);
                $i++;

                $name = !empty($row['full_name']) ? $row['full_name'] : $row['username'];
                $pos = !empty($row['position']) ? $row['position'] : 'HR Specialist';
                $desc = !empty($row['about_text']) ? $row['about_text'] : 'Досвідчений фахівець з підбору персоналу.';
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card <?php echo $border_color; ?> h-100 text-center">
                        
                        <?php if (!empty($row['photo'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['photo']); ?>" alt="<?php echo htmlspecialchars($name); ?>" class="team-photo">
                        <?php else: ?>
                            <div class="team-initials">
                                <?php echo mb_substr($name, 0, 1); ?>
                            </div>
                        <?php endif; ?>
                        
                        <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($name); ?></h4>
                        <p class="text-primary fw-bold small text-uppercase mb-3"><?php echo htmlspecialchars($pos); ?></p>
                        <p class="text-muted small">
                            <?php echo htmlspecialchars($desc); ?>
                        </p>
                        
                        <a href="mailto:contact@hrpro.com" class="btn btn-sm btn-outline-dark rounded-pill mt-3 px-4">Написати</a>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='col-12 text-center text-muted'>Список працівників поки порожній.</div>";
        }
        ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>