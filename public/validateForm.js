function validateForm() {
    let isValid = true; // Flag to track overall validity

    // --- Clear all previous error messages ---
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

    // --- Get Form Elements ---
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const firstNameInput = document.getElementById('first_name');
    const lastNameInput = document.getElementById('last_name');

    // --- Get Error Display Elements ---
    const usernameError = document.getElementById('usernameError');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const confirmPasswordError = document.getElementById('confirmPasswordError');
    // Add error divs for first/last name if you want client-side required check feedback
    // const firstNameError = document.getElementById('firstNameError');
    // const lastNameError = document.getElementById('lastNameError');

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
       // firstNameError.textContent = "First Name is required."; // Optional: Display if you add the div
       isValid = false; // Still mark as invalid even if message isn't shown
    }
    if (lastName === "") {
       // lastNameError.textContent = "Last Name is required."; // Optional: Display if you add the div
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
    // You could add more complex password requirements here (e.g., uppercase, number, symbol)

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