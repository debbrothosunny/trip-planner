<?php
$header_title = "Trip Expenses";
include __DIR__ . '/../backend/layouts/app.php';

// Pass the expenses data to the Vue.js app as JSON
$expenses_json = json_encode($expenses);
?>
<style>
.content {
    margin-left: 270px;
    padding: 20px;
}

.table th,
.table td {
    text-align: center;
}

.add-expense-btn {
    margin-bottom: 15px;
}
</style>
<div id="app" class="content">
    <h2 class="mb-3">Trip Expenses</h2>

    <a href="/user/expense/create" class="btn btn-primary add-expense-btn">
        <i class="fas fa-plus"></i> Add Expense
    </a>

    <div id="app">
        <div v-if="expenses.length > 0" class="card">
            <div class="card-header bg-primary text-white text-center">
                <h5>Expenses for {{ expenses[0].trip_name }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Trip Name</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Currency</th>
                                <th>Description</th>
                                <th>Expense Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="expense in expenses" :key="expense.id" class="text-white">
                                <td>{{ expense.trip_name }}</td>
                                <td>{{ expense.category }}</td>
                                <td>{{ formatCurrency(expense.amount) }}</td>
                                <td>{{ expense.currency }}</td>
                                <td>{{ expense.description }}</td>
                                <td>{{ formatDate(expense.expense_date) }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button @click="openEditModal(expense, $event)" class="btn btn-sm btn-primary"
                                            title="Edit">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button @click="confirmDelete(expense.id)" class="btn btn-danger btn-sm"
                                            title="Delete">
                                            <i class="fas fa-trash me-1"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div v-else class="alert alert-warning text-center">
            No expenses found for this trip.
        </div>

        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="editModalLabel">Edit Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="resetEditForm"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="formErrors.length > 0" class="alert alert-danger">
                            <ul class="mb-0">
                                <li v-for="error in formErrors" :key="error">{{ error }}</li>
                            </ul>
                        </div>
                        <form @submit.prevent="submitEditForm">



                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select v-model="formData.category" class="form-control" id="category" required>
                                    <option value="Accommodation">Accommodation</option>
                                    <option value="Food">Food</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Activities">Activities</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" v-model.number="formData.amount" class="form-control" id="amount"
                                    step="0.01" required>
                            </div>

                            <div class="mb-3">
                                <label for="currency" class="form-label">Currency</label>
                                <select v-model="formData.currency" class="form-control" id="currency" required>
                                    <option value="USD">USD</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea v-model="formData.description" class="form-control"
                                    id="description"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="expense_date" class="form-label">Expense Date</label>
                                <input type="date" v-model="formData.expense_date" class="form-control"
                                    id="expense_date" required>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                    @click="resetEditForm">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update Expense</button>
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

    createApp({
        data() {
            return {
                expenses: <?php echo $expenses_json; ?>,
                trips: [],
                successMessage: '<?php echo isset($_SESSION['success']) ? $_SESSION['success'] : ''; ?>',
                errorMessage: '',
                isEditMode: false, // Still needed to track if we are editing
                formData: {
                    id: null,
                    trip_id: '',
                    category: 'Accommodation',
                    amount: null,
                    currency: 'USD',
                    description: '',
                    expense_date: '',
                },
                formErrors: [],
                modalTriggerElement: null,
            };
        },
        methods: {
            formatCurrency(amount) {
                return parseFloat(amount).toFixed(2);
            },
            formatDate(dateString) {
                const options = {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                };
                return new Date(dateString).toLocaleDateString(undefined, options);
            },
            clearMessages() {
                this.successMessage = '';
                this.errorMessage = '';
            },
            openEditModal(expense, event) {
                this.modalTriggerElement = event.target;
                this.isEditMode = true;
                this.formData = {
                    id: expense.id,
                    trip_id: expense.trip_id,
                    category: expense.category,
                    amount: expense.amount,
                    currency: expense.currency,
                    description: expense.description,
                    expense_date: expense.expense_date ? expense.expense_date.substring(0, 10) : '',
                };
                this.fetchTrips();
                const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
            },
            resetEditForm() {
                this.isEditMode = false;
                this.formData = {
                    id: null,
                    trip_id: '',
                    category: 'Accommodation',
                    amount: null,
                    currency: 'USD',
                    description: '',
                    expense_date: '',
                };
                this.formErrors = [];

                const modalElement = document.getElementById('editModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                    modalElement.addEventListener('hidden.bs.modal', () => {
                        if (this.modalTriggerElement) {
                            this.modalTriggerElement.focus();
                            this.modalTriggerElement = null;
                        }
                    }, {
                        once: true
                    });
                }
            },
            submitEditForm() {
                this.formErrors = [];
                if (!this.formData.trip_id) {
                    this.formErrors.push('Please select a trip.');
                }
                if (!this.formData.amount || isNaN(this.formData.amount) || this.formData.amount <= 0) {
                    this.formErrors.push('Please enter a valid amount.');
                }
                if (!this.formData.expense_date) {
                    this.formErrors.push('Please select the expense date.');
                }

                if (this.formErrors.length === 0 && this.isEditMode) {
                    const url = `/user/expense/update/${this.formData.id}`;
                    const method = 'POST';

                    fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': 'YOUR_CSRF_TOKEN_HERE',
                            },
                            body: JSON.stringify(this.formData),
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(errorData => {
                                    throw new Error(errorData.message ||
                                        'Failed to update expense');
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Update Response:', data);
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: data.message || 'Expense updated successfully!',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    this.fetchExpenses();
                                    this.resetEditForm();
                                });
                            } else {
                                this.formErrors = Array.isArray(data.errors) ? data.errors : [data
                                    .message ||
                                    'Failed to update expense.'
                                ];
                            }
                        })
                        .catch(error => {
                            console.error('Error updating expense:', error);
                            this.formErrors.push(error.message || 'An unexpected error occurred.');
                        });
                } else if (!this.isEditMode) {
                    console.warn("Submit should only happen in edit mode for this form.");
                }
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
                        this.deleteExpense(id);
                    }
                });
            },
            deleteExpense(id) {
                fetch(`/user/expense/delete/${id}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || 'Failed to delete expense');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            this.expenses = this.expenses.filter(expense => expense.id !== id);
                            Swal.fire(
                                'Deleted!',
                                data.message || 'Expense deleted successfully!',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                data.message || 'Failed to delete expense.',
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting expense:', error);
                        Swal.fire(
                            'Error!',
                            'An error occurred while trying to delete the expense.',
                            'error'
                        );
                    });
            },
            fetchExpenses() {
                console.log('Fetching expenses...');
                fetch('/user/expense/data')
                    .then(response => response.json())
                    .then(data => {
                        console.log('Expenses data received:', data);
                        if (data.success) {
                            console.log('Current this.expenses before update:', [...this.expenses]);
                            this.expenses = data.expenses;
                            console.log('Current this.expenses after update:', [...this.expenses]);
                            // Option 2: If the above still doesn't work, try updating the array reactively
                            // this.expenses.splice(0, this.expenses.length, ...data.expenses);
                        } else {
                            this.errorMessage = data.message || 'Failed to fetch expenses.';
                            console.error('Failed to fetch expenses:', this.errorMessage);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching expenses:', error);
                        this.errorMessage = 'Error fetching expenses.';
                    });
            },
            fetchTrips() {
                fetch('/user/expense/data')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.trips = data.trips;
                        } else {
                            this.errorMessage = data.message || 'Failed to fetch trips.';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching trips:', error);
                        this.errorMessage = 'Error fetching trips.';
                    });
            },
        },
        mounted() {
            this.clearMessages();
            if (this.successMessage) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: this.successMessage,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
            this.fetchTrips();
            this.fetchExpenses(); // Fetch expenses on component mount
        },
    }).mount('#app');
    </script>