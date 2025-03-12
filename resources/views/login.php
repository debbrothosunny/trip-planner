<!DOCTYPE html>
<html lang="en">

<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

</head>

<body class="bg-light">
    <div id="app">
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class="card p-4" style="width: 100%; max-width: 400px;">
                <h3 class="text-center mb-4">Login</h3>

                <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>

                <!-- Using Vue.js for form handling, without axios for API call -->
                <form @submit.prevent="login">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" v-model="email" placeholder="Enter your email"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" v-model="password" placeholder="Enter your password"
                            required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <p class="mt-3 text-center">Don't have an account? <a href="/register">Register here</a></p>
            </div>
        </div>
    </div>

    

    <script>
    const app = Vue.createApp({
        data() {
            return {
                email: '',
                password: '',
                errorMessage: ''
            };
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
                    .then(response => response.json()) // Convert response to JSON
                    .then(data => {
                        if (data.status === "success") {
                            window.location.href = data.redirect; // Redirect user
                        } else {
                            this.errorMessage = data.message; // Show error message
                        }
                    })
                    .catch(error => {
                        this.errorMessage = "Something went wrong. Please try again!";
                    });
            }
        }
    });

    app.mount("#app");
    </script>
</body>

</html>