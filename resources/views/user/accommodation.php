<?php
$header_title = "Accommodation";
$content = __DIR__ . '/dashboard.php'; // Load actual content
include __DIR__ . '/../backend/layouts/app.php';

// Pass the accommodation data to the Vue.js app as JSON
$accommodations_json = json_encode($accommodations);
?>

<style>
.sidebar {
    width: 250px;
    background: #2c3e50;
    color: white;
    height: 100vh;
    position: fixed;
    padding-top: 20px;
}

.sidebar a {
    color: white;
    display: flex;
    align-items: center;
    padding: 12px;
    text-decoration: none;
    transition: 0.3s;
}

.sidebar a i {
    margin-right: 10px;
}

.sidebar a:hover,
.sidebar a.active {
    background: #34495e;
}

.content {
    margin-left: 270px;
    padding: 20px;
    width: 100%;
}

.table th,
.table td {
    vertical-align: middle;
    text-align: center;
}

.badge {
    font-size: 0.875rem;
}
</style>

<div id="app" class="container mt-4">
    <h2 class="text-center mb-4">Accommodation List</h2>

    <div class="d-flex justify-content-end mb-3">
        <a href="/user/accommodation/create" class="btn btn-primary">Add New Accommodation</a>
    </div>

    <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ successMessage }}
        <button type="button" class="btn-close" @click="clearMessages" aria-label="Close"></button>
    </div>
    <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ errorMessage }}
        <button type="button" class="btn-close" @click="clearMessages" aria-label="Close"></button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Hotel Name</th>
                    <th>Room Type</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Total Price</th>
                    <th>Total Rooms</th>
                    <th>Available Rooms</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="accommodations.length === 0">
                    <td colspan="11" class="text-center">No accommodations found.</td>
                </tr>
                <tr v-for="(accommodation, index) in accommodations" :key="accommodation.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ accommodation.hotel_name }}</td>
                    <td>{{ accommodation.room_type || 'N/A' }}</td>
                    <td>{{ formatDate(accommodation.check_in_date) }}</td>
                    <td>{{ formatDate(accommodation.check_out_date) }}</td>
                    <td>${{ formatCurrency(calculateTotalPrice(accommodation)) }}</td>
                    <td>{{ accommodation.total_rooms }}</td>
                    <td>{{ accommodation.available_rooms }}</td>
                    <td>{{ accommodation.description || 'N/A' }}</td>
                    <td>
                        <span
                            :class="{'badge bg-success': accommodation.status == 0, 'badge bg-danger': accommodation.status == 1}">
                            {{ accommodation.status == 0 ? 'Pending' : 'Confirmed' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-2">

                            <button @click="confirmDelete(accommodation.id)" class="btn btn-sm btn-danger"><i
                                    class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<script>
const {
    createApp,
    ref
} = Vue;

createApp({
    data() {
        return {
            accommodations: <?php echo $accommodations_json; ?>,
            successMessage: '',
            errorMessage: '',
        };
    },
    methods: {
        formatDate(dateTimeString) {
            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            };
            return new Date(dateTimeString).toLocaleDateString(undefined, options);
        },
        formatCurrency(amount) {
            return parseFloat(amount).toFixed(2);
        },
        calculateTotalPrice(accommodation) {
            const checkIn = new Date(accommodation.check_in_date);
            const checkOut = new Date(accommodation.check_out_date);
            const timeDiff = Math.abs(checkOut.getTime() - checkIn.getTime());
            const days = Math.ceil(timeDiff / (1000 * 3600 * 24));
            return days * accommodation.price;
        },
        clearMessages() {
            this.successMessage = '';
            this.errorMessage = '';
        },
        confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You cannot revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.deleteAccommodation(id);
                }
            });
        },
        deleteAccommodation(id) {
            fetch(`/user/accommodation/delete/${id}`, {
                    method: 'GET', // Or 'DELETE' depending on your backend route
                    headers: {
                        'Content-Type': 'application/json',
                        // Add any necessary headers like CSRF token
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Failed to delete accommodation');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        this.accommodations = this.accommodations.filter(acc => acc.id !== id);
                        Swal.fire(
                            'Deleted!',
                            data.message || 'Accommodation deleted successfully!',
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message || 'Failed to delete accommodation.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error deleting accommodation:', error);
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the accommodation.',
                        'error'
                    );
                });
        }
    },
    mounted() {
        this.clearMessages();
        console.log('Accommodations data:', this.accommodations);
    },
}).mount('#app');

function confirmDelete(id) {
    // This function is now called from the Vue methods
}
</script>