<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tripify|Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://unpkg.com/vue@3.3.4/dist/vue.global.js"></script>
 

<body>
    <div id="app">
        <div class="container">
            <div class="illustration">
                <h1>Tripify</h1>
                <p>Your unique journey, uniquely planned.</p>
            </div>
            <div class="login-form">
                <h2>Welcome Back</h2>
                <div v-if="errorMessage" class="error-message">
                    {{ errorMessage }}
                </div>
                <form @submit.prevent="login">
                    <input type="email" v-model="email" class="form-control" placeholder="Email Address" required>
                    <div class="password-input">
                        <input :type="passwordFieldType" v-model="password" class="form-control" placeholder="Password"
                            required>
                        <span class="password-toggle" @click="togglePasswordVisibility">
                            <i :class="passwordVisible ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </span>
                    </div>
                    <button type="submit" class="btn-primary">Login</button>
                </form>
                <p class="mt-2 text-right text-sm text-gray-400">
                    <a href="/forgot-password" class="text-blue-400 hover:underline">Forgot Password?</a>
                </p>
                <p class="mt-4 text-sm text-center text-gray-400">
                    Don’t have an account?
                    <a href="/register" class="text-blue-400 hover:underline">Register here</a>
                </p>
            </div>
        </div>
        <footer>
            🚀 © 2025 | Designed & Developed by Deb Brotho Nath Sunny |💡
        </footer>
    </div>

    <script>
    const app = Vue.createApp({
        data() {
            return {
                email: '',
                password: '',
                errorMessage: '',
                passwordVisible: false,
            };
        },
        computed: {
            passwordFieldType() {
                return this.passwordVisible ? 'text' : 'password';
            }
        },
        methods: {
            login() {
                if (!this.email || !this.password) {
                    this.errorMessage = 'Please enter both email and password.';
                    return;
                }

                const formData = new FormData();
                formData.append('email', this.email);
                formData.append('password', this.password);

                fetch('/login', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            window.location.href = data.redirect;
                        } else {
                            this.errorMessage = data.message || "Invalid credentials.";
                        }
                    })
                    .catch(() => {
                        this.errorMessage = "Something went wrong. Please try again!";
                    });
            },
            togglePasswordVisibility() {
                this.passwordVisible = !this.passwordVisible;
            }
        }
    });

    app.mount("#app");
    </script>
       <style>
    body,
    html {
        height: 100%;
        margin: 0;
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #1a1a2e, #16213e);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .container {
        width: 100%;
        max-width: 900px;
        background: #0f3460;
        border-radius: 20px;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        display: flex;
        overflow: hidden;
    }

    .illustration {
        flex: 1;
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

    .login-form {
        flex: 1;
        padding: 50px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #0f3460;
    }

    .login-form h2 {
        margin-bottom: 30px;
        font-weight: 600;
        font-size: 1.8em;
        color: #ffffff;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        border: 1px solid #1e3a8a;
        background: #16213e;
        color: #ffffff;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #00a8ff;
        outline: none;
        box-shadow: 0 0 8px rgba(0, 168, 255, 0.6);
    }

    .btn-primary {
        background-color: #00a8ff;
        border: none;
        padding: 12px;
        border-radius: 8px;
        width: 100%;
        font-weight: 600;
        color: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0080c6;
        transform: translateY(-3px);
    }

    .error-message {
        background-color: #ff4d4d;
        color: white;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 20px;
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
        font-size: 14px;
        box-shadow: 0px -4px 10px rgba(0, 168, 255, 0.3);
    }

    .password-input {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        top: 36%;
        right: -18px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #ffffff;
    }
    </style>
</body>

</html>