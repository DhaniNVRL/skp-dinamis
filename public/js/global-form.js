document.addEventListener("DOMContentLoaded", function () {

    // VALIDATION FUNCTIONS

    function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    function isValidNumber(number) {
        const regex = /^[0-9]+$/;
        return regex.test(number);
    }

    function validateUsername(username) {
        if (username.trim() === "") return "Username wajib diisi";
        if (username.length < 6) return "Minimal 6 karakter";
        return null;
    }

    function validatePassword(password) {
        if (password.trim() === "") return "Password wajib diisi";
        if (password.length < 10) return "Minimal 10 karakter";
        if (!/[A-Z]/.test(password)) return "Harus ada huruf besar";
        if (!/[0-9]/.test(password)) return "Harus ada angka";
        if (!/[!@#$%^&*(),.?":{}|<>_\-\\[\]/+=~`]/.test(password)) return "Harus ada simbol";
        return null;
    }

    // ERROR HANDLING

    function showError(input, message) {
        // <<< REVISI: selector sebelumnya salah ".input error" harus ".input-error"
        let error = input.parentNode.querySelector(".input-error");
        if (error) error.remove();

        input.classList.add("border-red-500");

        const errorMessage = document.createElement("p");
        errorMessage.className = "text-red-500 text-xs mt-1 input-error";
        errorMessage.innerText = message;

        input.parentNode.appendChild(errorMessage);
    }

    function clearError(input) {
        let error = input.parentNode.querySelector(".input-error");
        if (error) error.remove();
        input.classList.remove("border-red-500");
    }

    // EMAIL VALIDATION

    const emailInputs = document.querySelectorAll(".validate-email");

    emailInputs.forEach(function (input) {
        function validate() {
            clearError(input);

            if (input.value.trim() === "") {
                showError(input, "Email wajib diisi");
                return;
            }

            if (!isValidEmail(input.value)) {
                showError(input, "Format email tidak valid");
            }
        }
        input.addEventListener("input", validate);
        if (input.value.trim() === "") {
            validate();
        }
    });

    // NUMBER VALIDATION

    const numberInputs = document.querySelectorAll(".validate-number");

numberInputs.forEach(function (input) {

    let touched = false;

    input.addEventListener("input", function () {
        touched = true;

        if (!isValidNumber(input.value)) {

            showError(input, "Hanya boleh angka");
            input.value = input.value.replace(/[^0-9]/g, "");

        } else {
            clearError(input);
        }
    });

    input.addEventListener("blur", function () {
        if (touched && input.value.trim() === "") {
            showError(input, "Field wajib diisi");
        }
    });

});

    // USERNAME VALIDATION


    document.addEventListener("input", function (e) {
        if (e.target.classList.contains("validate-username")) {
            clearError(e.target);
            const errorMessage = validateUsername(e.target.value);
            if (errorMessage !== null) {
                showError(e.target, errorMessage);
            }
        }
    });

    const usernameInputs = document.querySelectorAll(".validate-username");
    usernameInputs.forEach(function (input) {
        if (input.value.trim() === "") {
            const msg = validateUsername(input.value);
            if (msg) showError(input, msg);
        }
    });

    // PASSWORD VALIDATION

   document.addEventListener("input", function (e) {
        if (e.target.classList.contains("validate-password")) {
            clearError(e.target);
            const errorMessage = validatePassword(e.target.value);
            if (errorMessage !== null) {
                showError(e.target, errorMessage);
            }
        }
    });

    const passwordInputs = document.querySelectorAll(".validate-password");
    passwordInputs.forEach(function (input) {
        if (input.value.trim() === "") {
            const msg = validatePassword(input.value);
            if (msg) showError(input, msg);
        }
    });

    // REQUIRED FIELDS VALIDATION

    const requiredInputs = document.querySelectorAll(".validate-required");

    requiredInputs.forEach(function (input) {

        function validate() {
            clearError(input);

            if (input.value.trim() === "") {
                showError(input, "Field wajib diisi");
            }
        }

        // <<< TAMBAHAN: khusus select pakai change
        if (input.tagName === "SELECT") {
            input.addEventListener("change", validate); // <<< TAMBAHAN
        } else {
            input.addEventListener("input", validate);
        }

        // <<< EDIT: JANGAN auto merah kalau sudah ada value (edit form fix)
        if (input.value.trim() === "") {
            validate();
        } else {
            clearError(input); // <<< TAMBAHAN: pastikan tidak merah kalau ada value
        }

    });

});
