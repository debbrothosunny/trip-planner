<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include layout (or other necessary files)
$content = __DIR__ . '/dashboard.php'; // Load actual content
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
                    <form @submit.prevent="handleSubmit" method="POST" action="/user/create-trip">
                        <div class="mb-3">
                            <label for="name" class="form-label">Trip Name</label>
                            <input v-model="trip.name" type="text" id="name" name="name" class="form-control"
                                placeholder="Enter trip name" required>
                        </div>

                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input v-model="trip.start_date" type="date" id="start_date" name="start_date"
                                class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input v-model="trip.end_date" type="date" id="end_date" name="end_date"
                                class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="budget" class="form-label">Budget</label>
                            <input v-model="trip.budget" type="number" id="budget" name="budget" class="form-control"
                                step="0.01" placeholder="Enter trip budget" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Create Trip</button>
                    </form>


                </div>
            </div>

            <div class="text-center mt-3">
                <a href="/user/view-trip" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>
    </div>
</div>



<!-- Vue.js Integration -->
<script>

export default {
  data() {
    return {
      trip: {
        name: '',
        start_date: '',
        end_date: '',
        budget: ''
      },
    };
  },
  methods: {
    createTrip() {
      const tripData = {
        name: this.trip.name,
        start_date: this.trip.start_date,
        end_date: this.trip.end_date,
        budget: this.trip.budget
      };

      // Send POST request to create a trip
      fetch('/user/create-trip', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(tripData)
      })
        .then(response => response.json())
        .then(data => {
          // Check if the response has a success or error message
          if (data.message) {
            Swal.fire({
              title: '🎉 Success!',
              text: data.message,
              icon: 'success',
              confirmButtonText: 'OK',
              confirmButtonColor: '#4CAF50', // Green button
              background: '#f0f8ff', // Light blue background
              color: '#333', // Dark text color
              timer: 5000, // Auto close after 5 seconds
              showClass: {
                popup: 'animate__animated animate__fadeInDown', // Animation for popup appearance
              },
              hideClass: {
                popup: 'animate__animated animate__fadeOutUp', // Animation for popup disappearance
              },
            });
          } else if (data.error) {
            Swal.fire({
              title: 'Oops!',
              text: data.error,
              icon: 'error',
              confirmButtonText: 'OK',
              confirmButtonColor: '#f44336', // Red button for errors
              background: '#fff0f0', // Light red background
              color: '#333', // Dark text color
              timer: 5000, // Auto close after 5 seconds
              showClass: {
                popup: 'animate__animated animate__fadeInDown',
              },
              hideClass: {
                popup: 'animate__animated animate__fadeOutUp',
              },
            });
          }
        })
        .catch(error => {
          Swal.fire({
            title: 'Error',
            text: 'An error occurred. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#f44336', // Red button for errors
            background: '#fff0f0', // Light red background
            color: '#333', // Dark text color
            timer: 5000, // Auto close after 5 seconds
            showClass: {
              popup: 'animate__animated animate__fadeInDown',
            },
            hideClass: {
              popup: 'animate__animated animate__fadeOutUp',
            },
          });
        });
    }
  }
};
</script>
