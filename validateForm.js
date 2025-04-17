function validateForm() {
    let isValid = true; // Flag to track overall validity

    // --- Clear all previous error messages ---
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

    // --- Get Form Elements ---
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const firstNameInput = document.getElementById('firstName');
    const lastNameInput = document.getElementById('lastName');

    // --- Get Error Display Elements ---
    const usernameError = document.getElementById('usernameError');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const confirmPasswordError = document.getElementById('confirmPasswordError');
    const firstNameError = document.getElementById('firstNameError');
    const lastNameError = document.getElementById('lastNameError');

    // --- Check if elements exist before accessing value (Defensive Coding) ---
    if (!usernameInput || !emailInput || !passwordInput || !confirmPasswordInput || !firstNameInput || !lastNameInput ||
        !usernameError || !emailError || !passwordError || !confirmPasswordError || !firstNameError || !lastNameError) {
        console.error("Validation script could not find all required form elements or error divs.");
        // Optionally display a generic form error to the user
        return false; // Prevent submission if setup is wrong
    }

    // --- Get Values ---
    // (Code to get values as before...)
    const username = usernameInput.value.trim();
    const email = emailInput.value.trim();
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    const firstName = firstNameInput.value.trim();
    const lastName = lastNameInput.value.trim();


    // --- Validation Checks ---
    // (All the validation checks as before...)
     if (username === "") {
        usernameError.textContent = "Username is required.";
        isValid = false;
    } else if (username.length < 4) { // Username Length
        usernameError.textContent = "Username must be at least 4 characters long.";
        isValid = false;
    }

    if (firstName === "") {
       firstNameError.textContent = "First Name is required."; 
       isValid = false; 
    }
    if (lastName === "") {
       lastNameError.textContent = "Last Name is required."; 
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