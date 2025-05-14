<?php


// Include layout (or other necessary files)
include __DIR__ . '/../backend/layouts/app.php';
?>

<div class="container mt-5" id="app">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3>Create New Trip</h3>
                </div>
                <div class="card-body">
                    <form @submit.prevent="storeTrip" class="row">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Trip Name</label>
                            <input v-model="trip.name" type="text" id="name" name="name" class="form-control"
                                placeholder="Enter trip name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="destination" class="form-label">Destination</label>
                            <input v-model="trip.destination" type="text" id="destination" name="destination"
                                class="form-control" placeholder="Enter destination like Bangladesh" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input v-model="trip.start_date" type="date" id="start_date" name="start_date"
                                class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input v-model="trip.end_date" type="date" id="end_date" name="end_date"
                                class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="budget" class="form-label">Budget</label>
                            <input v-model="trip.budget" type="number" id="budget" name="budget" class="form-control"
                                step="0.01" placeholder="Enter trip budget" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="style" class="form-label">Trip Style</label>
                            <select v-model="trip.style" id="style" name="style" class="form-select" required>
                                <option value="" disabled selected>Select Trip Type</option>
                                <option value="Friends">Trip with Friends</option>
                                <option value="Family">Family Trip</option>
                                <option value="Solo">Solo Trip</option>
                                <option value="Couple">Couple's Getaway</option>
                                <option value="Adventure">Adventure Trip</option>
                                <option value="Relaxation">Relaxation Trip</option>
                                <option value="Cultural">Cultural Trip</option>
                                <option value="Foodie">Foodie Trip</option>
                                <option value="Historical">Historical Trip</option>
                                <option value="Nature">Nature Trip</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100">Create Trip</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="/user/view-trip" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>
    </div>
</div>

<script>
const app = Vue.createApp({
    data() {
        return {
            trip: {
                name: '',
                start_date: '',
                end_date: '',
                budget: '',
                destination: '',
                style: ''
            },
        };
    },
    methods: {
        storeTrip() {
            // ✅ Get CSRF token from hidden input
            const csrfInput = document.querySelector('input[name="csrf_token"]');
            const csrfToken = csrfInput ? csrfInput.value : '';

            if (!csrfToken) {
                Swal.fire('Error', 'CSRF token missing. Please refresh page.', 'error');
                return;
            }

            // ✅ Prepare form data and include CSRF token
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('name', this.trip.name);
            formData.append('start_date', this.trip.start_date);
            formData.append('end_date', this.trip.end_date);
            formData.append('budget', this.trip.budget);
            formData.append('destination', this.trip.destination);
            formData.append('style', this.trip.style);

            // ✅ Make fetch call without custom headers
            fetch('/user/trip/store', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Server error response:', text);
                        throw new Error('Server error with non-JSON response');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.message) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4CAF50',
                        background: '#f0f8ff',
                        color: '#333',
                        timer: 3000,
                        showClass: { popup: 'animate__animated animate__fadeInDown' },
                        hideClass: { popup: 'animate__animated animate__fadeOutUp' },
                    }).then(() => {
                        // Reset form
                        this.trip = { name: '', start_date: '', end_date: '', budget: '', destination: '', style: '' };
                        // Redirect to view page
                        window.location.href = '/user/view-trip';
                    });
                } else if (data.error) {
                    Swal.fire({
                        title: 'Error!',
                        text: data.error,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#f44336',
                        background: '#fff0f0',
                        color: '#333',
                        timer: 5000,
                        showClass: { popup: 'animate__animated animate__fadeInDown' },
                        hideClass: { popup: 'animate__animated animate__fadeOutUp' },
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f44336',
                    background: '#fff0f0',
                    color: '#333',
                    timer: 5000,
                    showClass: { popup: 'animate__animated animate__fadeInDown' },
                    hideClass: { popup: 'animate__animated animate__fadeOutUp' },
                });
                console.error('Fetch error:', error);
            });
        }
    }
});

// Mount the Vue app
app.mount('#app');
</script>



