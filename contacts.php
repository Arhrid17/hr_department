<?php require_once 'header.php'; ?>

<header class="hero-section">
    <div class="container">
        <h1 class="hero-title">Контакти</h1>
        <p class="hero-text">Знайдіть нас на карті або напишіть у соцмережах.</p>
    </div>
</header>

<div class="container overlap-container">
    <div class="row g-4">
        
        <div class="col-lg-5">
            <div class="contact-card h-100 d-flex flex-column justify-content-between">
                <div>
                    <h3 class="fw-bold mb-4 text-primary">Наші дані</h3>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-box bg-icon-1 me-3 mb-0" style="width: 50px; height: 50px; font-size: 1.2rem;">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <div class="small fw-bold text-muted">АДРЕСА</div>
                            <a href="https://maps.app.goo.gl/zaGXDbf5bU3812jZ9" target="_blank" class="text-dark text-decoration-none fw-bold fs-5">
                                м. Львів, вулиця Князя Романа, 1-3
                            </a>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-box bg-icon-2 me-3 mb-0" style="width: 50px; height: 50px; font-size: 1.2rem;">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div>
                            <div class="small fw-bold text-muted">ТЕЛЕФОН</div>
                            <a href="tel:+380930000000" class="text-dark text-decoration-none fw-bold fs-5">
                                +38 (093) 000-00-00
                            </a>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-box bg-icon-3 me-3 mb-0" style="width: 50px; height: 50px; font-size: 1.2rem;">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div>
                            <div class="small fw-bold text-muted">EMAIL</div>
                            <a href="mailto:hr@hrpro.com" class="text-dark text-decoration-none fw-bold fs-5">
                                hr@hrpro.com
                            </a>
                        </div>
                    </div>
                </div>

                <div>
                    <hr class="my-4">
                    <h6 class="fw-bold mb-3 text-muted">Ми в соцмережах:</h6>
                    <div class="d-flex gap-3">
                        <a href="https://facebook.com" target="_blank" class="social-btn" title="Facebook">
                            <i class="bi bi-facebook fs-5"></i>
                        </a>
                        <a href="https://instagram.com" target="_blank" class="social-btn" title="Instagram">
                            <i class="bi bi-instagram fs-5"></i>
                        </a>
                        <a href="https://t.me/hr_pro" target="_blank" class="social-btn" title="Telegram">
                            <i class="bi bi-telegram fs-5"></i>
                        </a>
                        <a href="https://linkedin.com" target="_blank" class="social-btn" title="LinkedIn">
                            <i class="bi bi-linkedin fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="contact-card p-0 overflow-hidden h-100" style="min-height: 450px;">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1122.4412010228584!2d24.032712483665097!3d49.83797911670837!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x473add6eb391da13%3A0x855529e697bc4a22!2z0IbQvdGB0YLQuNGC0YPRgiDQv9GA0LDQstCwINGC0LAg0L_RgdC40YXQvtC70L7Qs9GW0Zc!5e1!3m2!1suk!2sua!4v1764784925762!5m2!1suk!2sua" 
                    width="100%" 
                    height="100%" 
                    style="border:0; min-height: 450px;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>

    </div>
</div>

<?php require_once 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>