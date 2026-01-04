<footer style="background-color: #1e293b; color: #94a3b8; padding: 25px 0; margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1);">
    <div class="container">
        <div class="row align-items-center">
            
            <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                <a href="index.php" class="d-flex align-items-center justify-content-center justify-content-md-start text-white text-decoration-none mb-1">
                    <i class="bi bi-layers-half fs-5 me-2 text-primary"></i>
                    <span class="fw-bold">HR PRO</span>
                </a>
                <small class="opacity-75" style="font-size: 0.8rem;">&copy; <?php echo date('Y'); ?> Всі права захищено.</small>
            </div>

            <div class="col-md-4 text-center mb-3 mb-md-0">
                <div class="d-flex justify-content-center gap-3 mb-1">
                    <a href="tel:+380441234567" class="text-reset text-decoration-none small hover-white">
                        <i class="bi bi-telephone-fill text-primary me-1"></i> +38 (044) 123-45-67
                    </a>
                    <a href="mailto:hr@hrpro.com" class="text-reset text-decoration-none small hover-white">
                        <i class="bi bi-envelope-fill text-primary me-1"></i> hr@hrpro.com
                    </a>
                </div>
                <div>
                    <a href="https://maps.app.goo.gl/zaGXDbf5bU3812jZ9" target="_blank" class="text-reset text-decoration-none small hover-white">
                        <i class="bi bi-geo-alt-fill text-primary me-1"></i> м. Львів, вулиця Князя Романа, 1-3
                    </a>
                </div>
            </div>

            <div class="col-md-4 text-center text-md-end">
                <div class="d-inline-flex gap-2">
                    <a href="https://facebook.com" target="_blank" class="social-mini-btn"><i class="bi bi-facebook"></i></a>
                    <a href="https://instagram.com" target="_blank" class="social-mini-btn"><i class="bi bi-instagram"></i></a>
                    <a href="https://linkedin.com" target="_blank" class="social-mini-btn"><i class="bi bi-linkedin"></i></a>
                    <a href="https://t.me" target="_blank" class="social-mini-btn"><i class="bi bi-telegram"></i></a>
                </div>
            </div>

        </div>
    </div>

    <style>
        .hover-white:hover { color: white !important; transition: 0.3s; }
        
        .social-mini-btn {
            width: 32px; 
            height: 32px; 
            border-radius: 50%; 
            background: rgba(255,255,255,0.05);
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: white; 
            text-decoration: none; 
            transition: 0.3s;
            font-size: 0.9rem;
        }
        .social-mini-btn:hover { 
            background: var(--primary); 
            transform: translateY(-3px); 
        }
    </style>
</footer>