// validateLogin.js
function validateLogin() {
    let isValid = true;

    // Clear previous errors
    document.getElementById('usernameError').textContent = '';
    document.getElementById('passwordError').textContent = '';
    // Clear role error if you re-add the role dropdown
    // document.getElementById('roleError').textContent = '';

    // Get elements
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    // const roleSelect = document.getElementById('role'); // Uncomment if using role

    // Get error divs
    const usernameError = document.getElementById('usernameError');
    const passwordError = document.getElementById('passwordError');
    // const roleError = document.getElementById('roleError'); // Uncomment if using role

    // Basic check if elements exist
    if (!usernameInput || !passwordInput || !usernameError || !passwordError /* || !roleSelect || !roleError */) {
        console.error("Validation script could not find all required login form elements or error divs.");
        // Display a generic error or just prevent submission
        return false;
    }

    // Get values
    const username = usernameInput.value.trim();
    const password = passwordInput.value; // No trim on password
    // const role = roleSelect ? roleSelect.value : ''; // Uncomment if using role

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

    // Role (Uncomment if using role dropdown)
    /*
    if (role === "") {
        roleError.textContent = "Please select a role.";
        isValid = false;
    }
    */

    return isValid; // True if valid, False if any validation failed
}