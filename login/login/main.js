
const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");

    
});

const form = document.getElementById('loginForm');
        const inputs = form.querySelectorAll('.input');
        const passwordHint = document.getElementById('passwordHint');
        const usernameHint = document.getElementById('usernameHint');

        // Helper function to validate input
        function validateInput(input) {
            const value = input.value.trim();

            if (input.name === 'password') {
                // Password field length validation
                if (value.length >= 8 && value.length <= 12) {
                    input.classList.add('valid');
                    input.classList.remove('invalid');
                    passwordHint.style.display = 'none'; // Hide password hint when valid
                    return true;
                } else if (value.length === 0) {
                    input.classList.remove('invalid');
                    input.classList.remove('valid');
                    passwordHint.style.display = 'none'; // Hide password hint when empty
                    return true; // Empty password is considered valid here, but we handle this on submit
                } else {
                    input.classList.add('invalid');
                    input.classList.remove('valid');
                    passwordHint.style.display = 'block'; // Show password hint when invalid
                    passwordHint.classList.add('invalid');
                    return false;
                }
            } else if (input.name === 'username') {
                // Username field validation
                if (value.length > 0) {
                    input.classList.add('valid');
                    input.classList.remove('invalid');
                    usernameHint.style.display = 'none'; // Hide username hint when valid
                    return true;
                } else {
                    input.classList.remove('valid');
                    input.classList.remove('invalid');
                    usernameHint.style.display = 'none'; // Hide username hint when empty
                    return true; // Username can stay clear while typing
                }
            }
        }

        // Add input event listener to each input field
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                validateInput(input);
            });
        });

        // Validate on form submission
        form.addEventListener('submit', async (e) => {
            e.preventDefault(); // Prevent the default action initially

            let isFormValid = true;

            // Get username and password values
            const username = form.querySelector('input[name="username"]').value.trim();
            const password = form.querySelector('input[name="password"]').value.trim();

            // Validate Username
            if (username.length === 0) {
                form.querySelector('input[name="username"]').classList.add('invalid');
                usernameHint.style.display = 'block';
                usernameHint.classList.add('invalid');
                usernameHint.textContent = "Username is required."; // Show error message for empty username
                isFormValid = false;
            } else {
                form.querySelector('input[name="username"]').classList.add('valid');
                usernameHint.style.display = 'none'; // Hide message if username is valid
            }

            // Validate Password
            if (password.length === 0) {
                form.querySelector('input[name="password"]').classList.add('invalid');
                passwordHint.style.display = 'block';
                passwordHint.classList.add('invalid');
                passwordHint.textContent = "Password is required."; // Show error message for empty password
                isFormValid = false;
            } else if (password.length < 8 || password.length > 12) {
                form.querySelector('input[name="password"]').classList.add('invalid');
                passwordHint.style.display = 'block'; // Ensure password hint is visible
                passwordHint.classList.add('invalid');
                passwordHint.textContent = "Password must be between 8 and 12 characters."; // Show the correct hint
                isFormValid = false;
            } else {
                form.querySelector('input[name="password"]').classList.add('valid');
                passwordHint.style.display = 'none'; // Hide password hint when valid
            }

            // If form is invalid, prevent submission
            if (!isFormValid) {
                alert('Please ensure all fields are valid before submitting.');
                return;
            }

            // If everything is valid, submit the form
            alert('Form submitted successfully!');
            form.submit();
        });

        const registrationForm = document.getElementById('regForm');
        const formInputs = registrationForm.querySelectorAll('.input');
        const errorMessage = document.getElementById('errorMessage');
        const birthdateHint = document.getElementById('birthdateHint');
        const phoneHint = document.getElementById('phoneHint');
        const confirmPasswordHint = document.getElementById('confirmPasswordHint');

        // Helper function to validate input
        function validateInput(input) {
            const value = input.value.trim();
            let isValid = true;

            // Check for empty fields
            if (value.length === 0) {
                isValid = false;
                input.classList.add('invalid');
                input.classList.remove('valid');
            } else {
                // Specific validations for different fields
                if (input.name === 'password') {
                    // Password validation: between 8 and 12 characters
                    if (value.length < 8 || value.length > 12) {
                        isValid = false;
                        input.classList.add('invalid');
                        input.classList.remove('valid');
                    }
                } else if (input.name === 'confirmPassword') {
                    // Confirm password validation: check if it matches the password
                    const password = document.querySelector('[name="password"]').value;
                    if (value !== password) {
                        isValid = false;
                        input.classList.add('invalid');
                        input.classList.remove('valid');
                        confirmPasswordHint.style.display = 'block'; // Show error message if passwords don't match
                    } else {
                        confirmPasswordHint.style.display = 'none'; // Hide error message if passwords match
                    }
                } else if (input.name === 'phone') {
                    // Phone validation: 12 digits only
                    const phoneRegex = /^\d{12}$/;
                    if (!phoneRegex.test(value)) {
                        isValid = false;
                        input.classList.add('invalid');
                        input.classList.remove('valid');
                        phoneHint.style.display = 'block'; // Show error message if passwords don't match
                    } else {
                        phoneHint.style.display = 'none'; // Hide error message if passwords match
                    }
                    
                } else if (input.name === 'birthdate') {
                    // Birthdate validation: user must be at least 18 years old
                    const birthDate = new Date(value);
                    const age = calculateAge(birthDate);
                    if (age < 18) {
                        isValid = false;
                        input.classList.add('invalid');
                        input.classList.remove('valid');
                        birthdateHint.style.display = 'block';
                    }
                } else {
                    input.classList.add('valid');
                    input.classList.remove('invalid');
                    birthdateHint.style.display = 'none';
                }
            }

            // If valid, turn the input green
            if (isValid) {
                input.classList.add('valid');
                input.classList.remove('invalid');
            }

            return isValid;
        }

        // Calculate the age from birthdate
        function calculateAge(birthDate) {
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const month = today.getMonth();
            if (month < birthDate.getMonth() || (month === birthDate.getMonth() && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return age;
        }

        // Add input event listener to each input field
        formInputs.forEach(input => {
            input.addEventListener('input', () => {
                validateInput(input);
            });
        });

        // Validate on form submission
        registrationForm.addEventListener('submit', (e) => {
            e.preventDefault(); // Prevent the default action initially
            let isFormValid = true;

            // Validate all inputs
            formInputs.forEach(input => {
                if (!validateInput(input)) {
                    isFormValid = false;
                }
            });

            // If the form is invalid, show error message and prevent submission
            if (!isFormValid) {
                errorMessage.style.display = 'block';
                return;
            }

            // Simulate successful form submission
            alert('Form submitted successfully!');
            registrationForm.submit();
        });
        function myFunction() {
            var x = document.getElementById("password");
            var icon = document.querySelector(".checkbox-container i");

            if (x.type === "password") {
                x.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                x.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
        function myFunction(inputId) {
            var x = document.getElementById(inputId); // Get the input element based on the passed id
            var icon = x.nextElementSibling.querySelector("i"); // Get the corresponding icon
        
            if (x.type === "password") {
                x.type = "text";  // Show password
                icon.classList.remove("fa-eye");  // Switch to eye-slash icon
                icon.classList.add("fa-eye-slash");
            } else {
                x.type = "password";  // Hide password
                icon.classList.remove("fa-eye-slash");  // Switch back to eye icon
                icon.classList.add("fa-eye");
            }
        }
        
        
        