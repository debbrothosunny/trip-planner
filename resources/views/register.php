<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tripify | Register</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/vue@3.3.4/dist/vue.global.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>
<style>
body,
html {
    min-height: 100vh;
    /* Use min-height to allow content to expand */
    margin: 0;
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
}

.container {
    width: 90%;
    max-width: 960px;
    background: #0f3460;
    border-radius: 20px;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
    display: flex;
    overflow: hidden;
    margin-bottom: 60px;
    /* Add margin to accommodate the fixed footer */
}

@media (max-width: 768px) {
    .container {
        flex-direction: column;
        width: 95%;
        margin-bottom: 90px;
        /* Adjust for stacked layout */
    }

    .illustration {
        padding: 30px;
    }

    .register-form {
        padding: 30px;
    }
}

.illustration {
    flex: 0 0 40%;
    background: #1e3a8a;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    text-align: center;
    flex-direction: column;
}

.illustration h1 {
    font-size: 2em;
    font-weight: 600;
    margin-bottom: 15px;
}

.illustration p {
    font-size: 1em;
    opacity: 0.85;
}

.register-form {
    flex: 1;
    padding: 30px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: #0f3460;
}

.register-form h2 {
    margin-bottom: 20px;
    font-weight: 600;
    font-size: 1.5em;
    color: #ffffff;
    text-align: center;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 15px;
    border-radius: 8px;
    border: 1px solid #1e3a8a;
    background: #16213e;
    color: #ffffff;
    transition: all 0.3s ease;
    box-sizing: border-box;
    font-size: 0.9em;
}

.form-control:focus {
    border-color: #00a8ff;
    outline: none;
    box-shadow: 0 0 8px rgba(0, 168, 255, 0.6);
}

.btn-primary {
    background-color: #00a8ff;
    border: none;
    padding: 10px;
    border-radius: 8px;
    width: 100%;
    font-weight: 600;
    color: #fff;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9em;
}

.btn-primary:hover {
    background-color: #0080c6;
    transform: translateY(-3px);
}

.password-container {
    position: relative;
}

.password-icon {
    position: absolute;
    right: 10px;
    top: 35%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #00a8ff;
    font-size: 0.9em;
}

footer {
    width: 100%;
    text-align: center;
    padding: 15px;
    background: #16213e;
    color: #00a8ff;
    font-weight: 500;
    position: fixed;
    bottom: 0;
    left: 0;
    font-size: 0.8em;
    box-shadow: 0px -4px 10px rgba(0, 168, 255, 0.3);
}

.form-control-uniform {
    width: 100%;
    box-sizing: border-box;
}

.spinner {
    width: 18px;
    height: 18px;
    border: 2.5px solid #00a8ff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-left: 8px;
    display: inline-block;
    vertical-align: middle;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.error-message {
    font-size: 0.85em;
    margin-bottom: 10px;
}

.mt-4 {
    margin-top: 1.5rem;
}

.text-gray-600 {
    color: #adb5bd;
}

.text-blue-500 {
    color: #00a8ff;
}

.text-sm {
    font-size: 0.8em;
}

.register-form p.mt-4 {
    /* Style the "Already have an account?" paragraph */
    margin-top: 20px;
    /* Add some space above it */
    text-align: center;
    color: #adb5bd;
    font-size: 0.9em;
}

.register-form p.mt-4 a {
    color: #00a8ff;
    text-decoration: none;
}

.register-form p.mt-4 a:hover {
    text-decoration: underline;
}
</style>

<body>
    <div class="container" id="app">
        <div class="illustration">
            <h1>Tripify</h1>
            <p>Explore the world, one trip at a time.</p>
        </div>
        <div class="register-form">
            <h2>Create An Account</h2>
            <div v-if="emailError" class="error-message text-danger">{{ emailError }}</div>
            <div v-if="phoneError" class="error-message text-danger">{{ phoneError }}</div>
            <div v-if="generalError && !emailError && !phoneError" class="error-message text-danger">{{ generalError }}
            </div>
            <div v-if="photoError" class="error-message text-danger">{{ photoError }}</div>

            <form id="registerForm" @submit.prevent="register" method="POST" enctype="multipart/form-data">

                <div class="mb-2">
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="Full Name"
                        v-model="name" required>
                </div>

                <div class="mb-2">
                    <input type="email" name="email" class="form-control form-control-sm" placeholder="Email Address"
                        v-model="email" @blur="checkEmail(email)" required>
                </div>

                <div class="password-container mb-2">
                    <input :type="passwordType" name="password" class="form-control form-control-sm"
                        placeholder="Password min-6 characters" v-model="password" required>

                    <span class="password-icon" @click="togglePasswordVisibility">
                        <i v-if="passwordType === 'password'" class="fa fa-eye fa-xs"></i>
                        <i v-else class="fa fa-eye-slash fa-xs"></i>
                    </span>
                </div>

                <div class="password-container mb-2">
                    <input :type="confirmPasswordType" name="confirm_password" class="form-control form-control-sm"
                        placeholder="Confirm Password" v-model="confirmPassword" required>
                    <span class="password-icon" @click="toggleConfirmPasswordVisibility">
                        <i v-if="confirmPasswordType === 'password'" class="fa fa-eye fa-xs"></i>
                        <i v-else class="fa fa-eye-slash fa-xs"></i>
                    </span>
                </div>

                <div class="form-text text-danger" v-if="passwordsMatchError">{{ passwordsMatchError }}</div>
                <div class="mb-2">
                    <input type="tel" name="phone" class="form-control form-control-sm" placeholder="Phone Number"
                        v-model="phone" @blur="checkPhone(phone)" required>
                </div>
                <div class="mb-2">
                    <input type="text" class="form-control form-control-sm" id="country" name="country"
                        v-model="country" placeholder="Your country">
                </div>
                <div class="mb-2">
                    <input type="text" class="form-control form-control-sm" id="city" name="city" v-model="city"
                        placeholder="Your City">
                </div>
                <div class="mb-2">
                    <input type="text" class="form-control form-control-sm" id="language" name="language"
                        v-model="language" placeholder="Your Preferred Language">
                </div>
                <div class="mb-2">
                    <input type="text" class="form-control form-control-sm" id="currency" name="currency"
                        v-model="currency" placeholder="Your Preferred Currency">
                </div>
                <div class="mb-2">
                    <select name="gender" class="form-control form-control-sm" v-model="gender">
                        <option value="" disabled selected>Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label for="profile_photo" class="form-label"><small class="form-text text-muted">Allowed formats:
                            JPEG, PNG. Max size: 2MB.</small></label>
                    <input type="file" class="form-control form-control-sm" id="profile_photo" name="profile_photo"
                        accept="image/*" @change="handlePhotoUpload">
                </div>
                <div class="mb-2">
                    <select name="role" class="form-control form-control-sm" id="role" v-model="role" required>
                        <option value="" disabled selected>Select Account Type</option>
                        <option value="user">Regular User</option>
                        <option value="participant">Trip Participant</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary w-100 py-1 rounded" :disabled="loading">
                    <span v-if="loading">Creating Account...</span>
                    <span v-else>Create Account</span>
                </button>
            </form>
            <p class="mt-4 text-center text-gray-600 text-sm ">
                Already have an account?
                <a href="/" class="text-blue-500">Log in here</a>
            </p>
        </div>
    </div>
    <footer class="mt-5 text-center text-gray-500 text-sm">
        © 2025 | Designed & Developed by Deb Brotho Nath Sunny 🚀💡
    </footer>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script>
    const {
        createApp,
        ref,
        watch,
        onMounted
    } = Vue;

    createApp({
        setup() {
            const name = ref('');
            const email = ref('');
            const password = ref('');
            const confirmPassword = ref(''); // Add confirmPassword ref
            const phone = ref('');
            const role = ref(''); // Set default role
            const country = ref('');
            const city = ref('');
            const language = ref('');
            const currency = ref('');
            const gender = ref('');
            const emailError = ref('');
            const phoneError = ref('');
            const generalError = ref('');
            const loading = ref(false);
            const passwordType = ref('password'); // To toggle password visibility
            const confirmPasswordType = ref('password'); // To toggle confirm password visibility
            const photo = ref(null);
            const photoError = ref('');
            const passwordsMatchError = ref(''); // To display password mismatch error in real-time



            const togglePasswordVisibility = () => {
                passwordType.value = passwordType.value === 'password' ? 'text' : 'password';
            };

            const toggleConfirmPasswordVisibility = () => {
                confirmPasswordType.value = confirmPasswordType.value === 'password' ? 'text' : 'password';
            };

            const checkEmail = async (emailVal) => {
                if (!emailVal) {
                    emailError.value = '';
                    return;
                }
                const formData = new FormData();
                formData.append('email', emailVal);

                try {
                    const response = await fetch('/check-email', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    emailError.value = data.exists ? "This email is already registered." : '';
                } catch (error) {
                    console.error("Error checking email:", error);
                    generalError.value =
                    "An error occurred while checking email."; // Corrected: used generalError directly
                }
            };

            const checkPhone = async (phoneVal) => {
                if (!phoneVal) {
                    phoneError.value = '';
                    return;
                }
                const formData = new FormData();
                formData.append('phone', phoneVal);


                try {
                    const response = await fetch('/check-phone', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    phoneError.value = data.exists ? "This phone number is already registered." : '';
                } catch (error) {
                    console.error("Error checking phone:", error);
                    generalError.value = "An error occurred while checking phone.";
                }
            };

            const handlePhotoUpload = (event) => {
                const file = event.target.files[0];
                photo.value = file;
                photoError.value = ''; // Clear any previous photo errors
            };

            const register = async (event) => {
                loading.value = true;
                emailError.value = '';
                phoneError.value = '';
                generalError.value = '';
                photoError.value = '';
                passwordsMatchError.value = '';

                if (password.value !== confirmPassword.value) {
                    passwordsMatchError.value = "Passwords do not match.";
                    loading.value = false;
                    return;
                }

                const trimmedPhone = phone.value.trim();
                const formData = new FormData(event.target);
                formData.set('phone', trimmedPhone); // Ensure trimmed value is used

                if (photo.value) {
                    formData.append('profile_photo', photo.value);
                }

                try {
                    const response = await fetch('/register', {
                        method: 'POST',
                        body: formData
                    });
                    const responseText = await response.text();

                    loading.value = false;

                    if (responseText.includes("EMAIL_EXISTS")) {
                        emailError.value = "This email is already registered.";
                    } else if (responseText.includes("PHONE_EXISTS")) {
                        phoneError.value = "This phone number is already registered.";
                    } else if (responseText.includes("Invalid profile photo format")) {
                        photoError.value = "Invalid profile photo format. Only JPEG, PNG are allowed.";
                    } else if (responseText.includes("Profile photo size exceeds the limit")) {
                        photoError.value = "Profile photo size exceeds the limit.";
                    } else if (responseText.startsWith('/verify-otp')) {
                        window.location.href = '/verify-otp';
                    } else {
                        generalError.value = responseText || 'Registration failed. Please try again.';
                    }
                } catch (error) {
                    loading.value = false;
                    console.error('Registration error:', error);
                    generalError.value = 'A network error occurred.';
                }
            };

            watch(email, checkEmail);
            watch(phone, checkPhone);
            watch([password, confirmPassword], ([newPassword, newConfirmPassword]) => {
                if (newConfirmPassword && newPassword !== newConfirmPassword) {
                    passwordsMatchError.value = "Passwords do not match.";
                } else {
                    passwordsMatchError.value = "";
                }
            });

            return {
                name,
                email,
                password,
                confirmPassword,
                phone,
                role,
                country,
                city,
                language,
                currency,
                gender,
                emailError,
                phoneError,
                generalError,
                loading,
                passwordType,
                togglePasswordVisibility,
                confirmPasswordType,
                toggleConfirmPasswordVisibility,
                register,
                photoError,
                handlePhotoUpload,

                passwordsMatchError,
                checkEmail,
                checkPhone
            };
        }
    }).mount('#app');
    </script>

</body>


</html>