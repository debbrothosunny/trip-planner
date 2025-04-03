<?php
$header_title = "Create Transportation";
$content = __DIR__ . '/dashboard.php'; // Adjust if needed
include __DIR__ . '/../backend/layouts/app.php';

// Assuming your controller fetches the list of trips
// and makes it available as the $trips variable
// Example (you might need to adjust this based on your framework):
// $tripModel = new TripModel();
// $trips = $tripModel->getAll();
?>



<div id="app" class="container mt-4">
    <h1 class="mb-4">Add New Transportation</h1>

    <form @submit.prevent="submitForm">
        <div class="mb-3">
            <label for="trip_id" class="form-label">Trip Name</label>
            <select class="form-select w-100" id="trip_id" v-model="formData.trip_id" required>
                <option value="" disabled selected>Select Trip</option>
                <?php foreach ($trips as $trip): ?>
                <option value="<?php echo $trip['id']; ?>"><?php echo $trip['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Transportation Type</label>
            <select class="form-select w-100" id="type" v-model="formData.type" required>
                <option value="" disabled selected>Select Type</option>
                <option value="Bus">Bus</option>
                <option value="Train">Train</option>
                <option value="Flight">Flight</option>
                <option value="Ship">Ship</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="company_name" class="form-label">Company Name</label>
            <input type="text" class="form-control" id="company_name" v-model="formData.company_name" required>
        </div>

        <div class="mb-3">
            <label for="departure_location" class="form-label">Departure Location</label>
            <input type="text" class="form-control" id="departure_location" v-model="formData.departure_location"
                required>
        </div>

        <div class="mb-3">
            <label for="arrival_location" class="form-label">Arrival Location</label>
            <input type="text" class="form-control" id="arrival_location" v-model="formData.arrival_location" required>
        </div>

        <div class="mb-3">
            <label for="departure_date" class="form-label">Departure Date</label>
            <input type="date" class="form-control" id="departure_date" v-model="formData.departure_date" required>
        </div>

        <div class="mb-3">
            <label for="arrival_date" class="form-label">Arrival Date</label>
            <input type="date" class="form-control" id="arrival_date" v-model="formData.arrival_date" required>
        </div>

        <div class="mb-3">
            <label for="booking_reference" class="form-label">Booking Reference</label>
            <input type="text" class="form-control" id="booking_reference" v-model="formData.booking_reference"
                required>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" class="form-control" id="amount" v-model="formData.amount" step="0.01" min="0"
                required>
        </div>

        <button type="submit" class="btn btn-success">Add Transportation</button>
        <a href="/user/transportation" class="btn btn-secondary">Cancel</a>
    </form>

</div>

<script>
const {
    createApp,
    ref
} = Vue;

createApp({
    data() {
        return {
            formData: {
                trip_id: '',
                type: '',
                company_name: '',
                departure_location: '',
                arrival_location: '',
                departure_date: '',
                arrival_date: '',
                booking_reference: '',
                amount: ''
            }
        };
    },
    methods: {
        submitForm() {
            console.log('Form Data:', this.formData);

            fetch('/user/transportation/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': 'YOUR_CSRF_TOKEN_HERE', // Important for security
                    },
                    body: JSON.stringify(this.formData),
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Request failed');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message || 'Transportation created successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href =
                            '/user/transportation'; // Redirect after the alert
                        });
                    } else {
                        console.error('Error creating transportation:', data);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message || 'Failed to add transportation.',
                        });
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.message || 'An unexpected error occurred.',
                    });
                });
        }
    }
}).mount('#app');
</script>