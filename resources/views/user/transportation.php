<?php
$header_title = "Transportation";
$content = __DIR__ . '/dashboard.php'; // Load actual content
include __DIR__ . '/../backend/layouts/app.php';
$transportations_json = json_encode($transportations);

?>



<div id="app" class="container">
    <h3 class="mb-3 text-center">Transportation List</h3>

    <div class="d-flex justify-content-end mb-3">
        <a href="/user/transportation/create" class="btn btn-primary">Add Transportation</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Trip Name</th>
                    <th>Type</th>
                    <th>Company</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Dep. Date</th>
                    <th>Arr. Date</th>
                    <th>Booking Ref</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="transportations.length === 0">
                    <td colspan="11" class="text-center text-muted">No transportation records found.</td>
                </tr>
                <tr v-for="transportation in transportations" :key="transportation.id">
                    <td>{{ transportation.id }}</td>
                    <td>{{ transportation.trip_name }}</td>
                    <td>{{ transportation.type }}</td>
                    <td>{{ transportation.company_name }}</td>
                    <td>{{ transportation.departure_location }}</td>
                    <td>{{ transportation.arrival_location }}</td>
                    <td>{{ formatDate(transportation.departure_date) }}</td>
                    <td>{{ formatDate(transportation.arrival_date) }}</td>
                    <td>{{ transportation.booking_reference }}</td>
                    <td>{{ formatCurrency(transportation.amount) }} USD</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <button @click="editTransportation(transportation)" class="btn btn-warning btn-sm"
                                title="Edit">
                                <i class="bi bi-pencil-square me-1"></i>
                            </button>
                            <button @click="confirmDelete(transportation.id)" class="btn btn-danger btn-sm"
                                title="Delete">
                                <i class="bi bi-trash me-1"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Transportation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-if="editingTransportation">
                        <form @submit.prevent="updateTransportation">
                            <div class="mb-9">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select w-100" id="type" v-model="editingTransportation.type">
                                    <option value="Flight">Flight</option>
                                    <option value="Train">Train</option>
                                    <option value="Bus">Bus</option>
                                    <option value="Car">Car</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="company_name" class="form-label">Company</label>
                                <input type="text" class="form-control" id="company_name"
                                    v-model="editingTransportation.company_name">
                            </div>
                            <div class="mb-3">
                                <label for="departure_location" class="form-label">Departure</label>
                                <input type="text" class="form-control" id="departure_location"
                                    v-model="editingTransportation.departure_location">
                            </div>
                            <div class="mb-3">
                                <label for="arrival_location" class="form-label">Arrival</label>
                                <input type="text" class="form-control" id="arrival_location"
                                    v-model="editingTransportation.arrival_location">
                            </div>
                            <div class="mb-3">
                                <label for="departure_date" class="form-label">Departure Date</label>
                                <input type="date" class="form-control" id="departure_date"
                                    v-model="editingTransportation.departure_date">
                            </div>
                            <div class="mb-3">
                                <label for="arrival_date" class="form-label">Arrival Date</label>
                                <input type="date" class="form-control" id="arrival_date"
                                    v-model="editingTransportation.arrival_date">
                            </div>
                            <div class="mb-3">
                                <label for="booking_reference" class="form-label">Booking Ref</label>
                                <input type="text" class="form-control" id="booking_reference"
                                    v-model="editingTransportation.booking_reference">
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" class="form-control" id="amount"
                                    v-model="editingTransportation.amount">
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const {
    createApp
} = Vue;

const transportations_json = <?php echo json_encode($transportations); ?>;

createApp({
    data() {
        return {
            transportations: <?php echo json_encode($transportations); ?>,
            editingTransportation: null,
            sweetAlert: {
                show: false,
                title: '',
                text: '',
                icon: ''
            },
            successMessage: '<?php echo isset($_SESSION['success']) ? $_SESSION['success'] : ''; ?>',
            errorMessage: '<?php echo isset($_SESSION['error']) ? $_SESSION['error'] : ''; ?>',
        };
    },
    methods: {
        formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            return new Date(dateString).toLocaleDateString(undefined, options);
        },
        formatCurrency(amount) {
            return parseFloat(amount).toFixed(2);
        },
        clearMessages() {
            this.successMessage = '';
            this.errorMessage = '';
        },
        editTransportation(transportation) {
            this.editingTransportation = {
                ...transportation
            };
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        },
        updateTransportation() {
            fetch(`/user/transportation/update/${this.editingTransportation.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.editingTransportation)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const index = this.transportations.findIndex(t => t.id === this
                            .editingTransportation.id);
                        if (index !== -1) {
                            this.transportations.splice(index, 1, this.editingTransportation);
                        }
                        const editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                        editModal.hide();

                        // SweetAlert for successful update
                        Swal.fire({
                            title: 'Updated!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        // SweetAlert for update error
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error updating transportation:', error);
                    // SweetAlert for network error
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while updating.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        },
        confirmDelete(id) {
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
                    this.deleteTransportation(id);
                }
            });
        },
        deleteTransportation(id) {
            fetch(`/user/transportation/delete/${id}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.transportations = this.transportations.filter(t => t.id !== id);
                        Swal.fire({
                            title: 'Deleted!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error deleting transportation:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        }
    },
    mounted() {
        this.clearMessages();
    }
}).mount('#app');
</script>