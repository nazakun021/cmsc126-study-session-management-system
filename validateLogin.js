function validateLogin() {
    let isValid = true;

    // Clear previous errors
    document.getElementById('usernameError').textContent = '';
    document.getElementById('passwordError').textContent = '';

    // Get elements
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');

    // Get error divs
    const usernameError = document.getElementById('usernameError');
    const passwordError = document.getElementById('passwordError');

    // Basic check if elements exist
    if (!usernameInput || !passwordInput || !usernameError || !passwordError) {
        console.error("Validation script could not find all required login form elements or error divs.");
        return false;
    }

    // Get values
    const username = usernameInput.value.trim();
    const password = passwordInput.value; // No trim on password

    // --- Validation Checks ---
    // Username
    if (username === "") {
        usernameError.textContent = "Username is required.";
        isValid = false;
    }

    // Password
    if (password === "") {
        passwordError.textContent = "Password is required.";
        isValid = false;
    }

    return isValid; // True if valid, False if any validation failed
}