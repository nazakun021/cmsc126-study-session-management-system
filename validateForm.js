function validateForm() {
    let isValid = true; // Flag to track overall validity

    // --- Clear all previous error messages ---
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

    // --- Get Form Elements ---
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const courseSelect = document.getElementById('course');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');

    // --- Get Error Display Elements ---
    const usernameError = document.getElementById('usernameError');
    const emailError = document.getElementById('emailError');
    const courseError = document.getElementById('courseError');
    const passwordError = document.getElementById('passwordError');
    const confirmPasswordError = document.getElementById('confirmPasswordError');

    // --- Check if elements exist before accessing value (Defensive Coding) ---
    if (!usernameInput || !emailInput || !courseSelect || !passwordInput || !confirmPasswordInput ||
        !usernameError || !emailError || !courseError || !passwordError || !confirmPasswordError) {
        console.error("Validation script could not find all required form elements or error divs.");
        return false; // Prevent submission if setup is wrong
    }

    // --- Get Values ---
    const username = usernameInput.value.trim();
    const email = emailInput.value.trim();
    const courseId = courseSelect.value;
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;


    // --- Validation Checks ---
    // (All the validation checks as before...)
     if (username === "") {
        usernameError.textContent = "Username is required.";
        isValid = false;
    } else if (username.length < 4) { // Username Length
        usernameError.textContent = "Username must be at least 4 characters long.";
        isValid = false;
    }

    // Email Format (Basic Check)
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Simple regex
    if (email === "") {
         emailError.textContent = "Email is required.";
         isValid = false;
    } else if (!emailPattern.test(email)) {
         emailError.textContent = "Please enter a valid email address.";
         isValid = false;
    }

    // Course Selection (value will be "" for the default disabled option)
    if (courseId === "") { // ADDED CHECK
        courseError.textContent = "Please select your course.";
        isValid = false;
    }

    // Password Length
    if (password === "") {
        passwordError.textContent = "Password is required.";
         isValid = false;
    } else if (password.length < 6) { // Use the same minimum length as your server-side check
        passwordError.textContent = "Password must be at least 6 characters long.";
        isValid = false;
    }

    // Confirm Password Check
    if (confirmPassword === "") {
         confirmPasswordError.textContent = "Please confirm your password.";
         isValid = false;
    } else if (password !== confirmPassword) {
        confirmPasswordError.textContent = "Passwords do not match.";
        // Also maybe add error styling to both password fields
        isValid = false;
    }


    // --- Return final validity ---
    return isValid;
}