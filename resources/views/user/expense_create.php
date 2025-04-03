<?php
$header_title = "Trip Expenses";
include __DIR__ . '/../backend/layouts/app.php';

?>

<div id="app" class="container mt-5">
    <h1 class="mb-4">Create New Expense</h1>

    <div v-if="errors.length > 0" class="alert alert-danger">
        <ul class="mb-0">
            <li v-for="error in errors" :key="error">{{ error }}</li>
        </ul>
    </div>

    <form @submit.prevent="submitForm">

        <div class="mb-3">
            <label for="trip_id" class="form-label">Trip</label>
            <select v-model="formData.trip_id" class="form-select w-100" id="trip_id" required>
                <option value="" disabled selected>Select Trip</option>
                <option v-for="trip in trips" :value="trip.id" :key="trip.id">
                    {{ trip.name }}
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select v-model="formData.category" class="form-control" id="category" required>
                <option value="" disabled selected>Select a category</option>
                <option value="Accommodation">Accommodation</option>
                <option value="Food">Food</option>
                <option value="Transport">Transport</option>
                <option value="Activities">Activities</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" v-model.number="formData.amount" class="form-control" id="amount" step="0.01" required>
        </div>

        <div class="mb-3">
            <label for="currency" class="form-label">Currency</label>
            <select v-model="formData.currency" class="form-control" id="currency" required>
                <option value="USD">USD</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea v-model="formData.description" class="form-control" id="description"></textarea>
        </div>

        <div class="mb-3">
            <label for="expense_date" class="form-label">Expense Date</label>
            <input type="date" v-model="formData.expense_date" class="form-control" id="expense_date" required>
        </div>

        <button type="submit" class="btn btn-primary">Save Expense</button>

    </form>

    <div class="mt-4">
        <a href="/user/expense" class="btn btn-secondary">Back</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const {
    createApp,
    ref
} = Vue;

createApp({
    data() {
        return {
            trips: <?php echo json_encode($trips); ?>,
            formData: {
                trip_id: '',
                category: 'Accommodation',
                amount: null,
                currency: 'USD',
                description: '',
                expense_date: '',
            },
            errors: [],
        };
    },
    methods: {
        submitForm() {
            this.errors = [];
            if (!this.formData.trip_id) {
                this.errors.push('Please select a trip.');
            }
            if (!this.formData.amount || isNaN(this.formData.amount) || this.formData.amount <= 0) {
                this.errors.push('Please enter a valid amount.');
            }
            if (!this.formData.expense_date) {
                this.errors.push('Please select the expense date.');
            }

            if (this.errors.length === 0) {
                fetch('/user/expense/store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': 'YOUR_CSRF_TOKEN_HERE', // Add your CSRF token here
                        },
                        body: JSON.stringify(this.formData),
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || 'Failed to save expense');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message || 'Expense saved successfully!',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = '/user/expense'; // Redirect after success
                            });
                        } else {
                            this.errors = Array.isArray(data.errors) ? data.errors : [data.message ||
                                'Failed to save expense.'
                            ];
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: this.errors.join('<br>') || 'Failed to save expense.',
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error saving expense:', error);
                        this.errors.push(error.message || 'An unexpected error occurred.');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An unexpected error occurred.',
                        });
                    });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error!',
                    html: this.errors.join('<br>'),
                });
            }
        },
    },
}).mount('#app');
</script>