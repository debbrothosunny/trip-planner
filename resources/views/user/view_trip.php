<?php


$header_title = "Trip";
$content = __DIR__ . '/dashboard.php'; // Load actual content (you might need to adjust this path)
include __DIR__ . '/../backend/layouts/app.php'; // Adjust path as needed
?>

<style>
/* Your existing styles remain unchanged */
body {
    display: flex;
}

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
</style>

<div id="app" class="content">
    <div class="container mt-4">
        <?php
        if (isset($_SESSION['success'])) {
            echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>
        
        <h2 class="mb-3">Trip</h2>
        <a href="/user/create-trip" class="btn btn-success mb-3">+ Add New Trip</a>

        <div v-if="trips.length === 0">
            <div class="alert alert-warning">No trips found.</div>
        </div>

        <div v-else>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trip Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Budget</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="trip in trips" :key="trip.id">
                        <td>{{ trip.id }}</td>
                        <td>{{ trip.name }}</td>
                        <td>{{ trip.start_date }}</td>
                        <td>{{ trip.end_date }}</td>
                        <td>${{ parseFloat(trip.budget).toFixed(2) }}</td>
                        <td class="d-flex justify-content-center">
                            <a href="#" @click.prevent="editTrip(trip)" class="btn btn-warning btn-sm me-2">Edit</a>
                            <a :href="'/trip/' + trip.id + '/itinerary'" class="btn btn-success btn-sm me-2">Trip
                                Itinerary</a>
                            <a href="#" @click.prevent="confirmDelete(trip.id)" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div v-if="showModal" class="modal fade show" tabindex="-1" style="display: block;" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Trip</h5>
                    <button type="button" class="btn-close" @click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submitEditTrip">
                        <div class="mb-3">
                            <label for="name" class="form-label">Trip Name</label>
                            <input type="text" id="name" class="form-control" v-model="selectedTrip.name" required>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" id="start_date" class="form-control" v-model="selectedTrip.start_date"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" id="end_date" class="form-control" v-model="selectedTrip.end_date"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="budget" class="form-label">Budget</label>
                            <input type="number" id="budget" class="form-control" v-model="selectedTrip.budget"
                                step="0.01" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const {
    createApp,
    ref
} = Vue;

// Data passed from PHP
const tripsData = <?php echo json_encode($trips); ?>;

createApp({
    data() {
        return {
            trips: tripsData,
            showModal: false,
            selectedTrip: {},
        };
    },
    methods: {
        editTrip(trip) {
            console.log('editTrip called', trip);
            this.selectedTrip = {
                ...trip
            };
            this.showModal = true;
            console.log('showModal:', this.showModal);
        },
        closeModal() {
            this.showModal = false;
            this.selectedTrip = {};
        },
        submitEditTrip() {
            // Implement your logic to send the edited trip data to the server
            console.log('Submitting edited trip:', this.selectedTrip);

            fetch(`/user/trip/update/${this.selectedTrip.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': 'YOUR_CSRF_TOKEN_HERE'
                    },
                    body: JSON.stringify(this.selectedTrip)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success!', data.message, 'success').then(() => {
                            // Find the index of the edited trip in the trips array
                            const index = this.trips.findIndex(trip => trip.id === this.selectedTrip
                                .id);

                            if (index !== -1) {
                                // Update the trip object in the array with the edited data
                                this.trips.splice(index, 1, {
                                    ...this.selectedTrip
                                });
                            }

                            this.closeModal();
                            // Optionally, you can still call fetchTrips to ensure data consistency
                            // this.fetchTrips();
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error updating trip:', error);
                    Swal.fire('Error!', 'Failed to update trip.', 'error');
                });
        },
        confirmDelete(tripId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.deleteTrip(tripId);
                }
            });
        },
        deleteTrip(tripId) {
            const url = `/user/trip/delete/${tripId}`; // Adjust your delete route
            fetch(url, {
                    method: 'GET', // Or 'DELETE' depending on your backend route
                    credentials: 'include',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Deleted!',
                            data.message,
                            'success'
                        ).then(() => {
                            this.trips = this.trips.filter(trip => trip.id !== tripId);
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message,
                            'error'
                        );
                    }
                })
                .catch(error => {
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the trip.',
                        'error'
                    );
                    console.error('Delete Error:', error);
                });
        },
        
        fetchTrips() {
            // Implement a method to fetch the latest trips data from the server
            fetch(`/user/trips-data?t=${Date.now()}`) // Append a timestamp
                .then(response => response.json())
                .then(data => {
                    this.trips = data;
                })
                .catch(error => {
                    console.error('Error fetching trips:', error);
                });
        }
    },
    mounted() {
        this.fetchTrips(); // Always fetch on mount to ensure latest data

    }
}).mount('#app');
</script>