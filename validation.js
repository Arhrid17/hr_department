document.addEventListener("DOMContentLoaded", function() {
    const forms = document.querySelectorAll("form");

    forms.forEach(form => {
        form.addEventListener("submit", function(event) {
            let isValid = true;

            const inputs = form.querySelectorAll("input[required], textarea[required], select[required]");

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add("is-invalid"); 

                    input.addEventListener("input", function() {
                        input.classList.remove("is-invalid");
                    });
                } else {
                    input.classList.remove("is-invalid");
                    
                    if (input.type === "email") {
                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailPattern.test(input.value)) {
                            isValid = false;
                            input.classList.add("is-invalid");
                        }
                    }
                }
            });

            if (!isValid) {
                event.preventDefault(); 
                alert("Будь ласка, заповніть всі поля коректно!");
            }
        });
    });
});