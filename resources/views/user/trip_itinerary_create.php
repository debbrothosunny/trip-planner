 <?php
include __DIR__ . '/../backend/layouts/app.php';
?>
 <div id="app">
     <div class="container mt-5">
         <h2>Create New Itinerary for Trip</h2>
         <form @submit.prevent="submitForm" id="createItineraryForm">
             <input type="hidden" v-model="formData.trip_id">
             <div class="mb-3">
                 <label for="day_title" class="form-label">Day</label>
                 <input type="text" v-model="formData.day_title" id="day_title" class="form-control" required>
             </div>
             <div class="mb-3">
                 <label for="description" class="form-label">Description</label>
                 <textarea v-model="formData.description" id="description" class="form-control" required></textarea>
             </div>
             <div class="mb-3">
                 <label for="location" class="form-label">Location</label>
                 <input type="text" v-model="formData.location" id="location" class="form-control" required>
             </div>
             <div class="mb-3">
                 <label for="itinerary_date" class="form-label">Date</label>
                 <input type="date" v-model="formData.itinerary_date" id="itinerary_date" class="form-control" required>
             </div>
             <div class="mb-3">
                 <label for="image" class="form-label">Image (Optional)</label>
                 <input type="file" class="form-control" id="image" @change="handleFileChange" multiple>
             </div>
             <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                 <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status"
                     aria-hidden="true"></span>
                 Create Itinerary
             </button>
             <div v-if="errorMessage" class="alert alert-danger mt-3">{{ errorMessage }}</div>
         </form>
     </div>
 </div>

 <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script>
const {
    createApp,
    ref
} = Vue;

const app = createApp({
    setup() {
        const formData = ref({
            trip_id: window.location.pathname.split('/')[2], // Extract trip_id from URL
            day_title: '',
            description: '',
            location: '',
            itinerary_date: '',
            images: null
        });

        const isSubmitting = ref(false);
        const errorMessage = ref('');

        const submitForm = async () => {
            isSubmitting.value = true;
            errorMessage.value = '';

            const form = new FormData();
            for (const key in formData.value) {
                if (key === 'images' && formData.value.images) {
                    for (let i = 0; i < formData.value.images.length; i++) {
                        form.append('images[]', formData.value.images[i]);
                    }
                } else {
                    form.append(key, formData.value[key]);
                }
            }

            try {
                const response = await fetch(`/trip/${formData.value.trip_id}/itinerary/create`, {
                    method: 'POST',
                    body: form,
                });

                if (!response.ok) {
                    const data = await response.json();
                    errorMessage.value = data.message || 'Failed to create itinerary.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage.value,
                    });
                } else {
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = `/trip/${formData.value.trip_id}/itinerary`;
                        });
                    } else {
                        errorMessage.value = data.message || 'Failed to create itinerary.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage.value,
                        });
                    }
                }
            } catch (error) {
                console.error('Error creating itinerary:', error);
                errorMessage.value = 'An unexpected error occurred.';
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage.value,
                });
            } finally {
                isSubmitting.value = false;
            }
        };

        const handleFileChange = (event) => {
            formData.value.images = event.target.files;
        };

        // Extract trip_id from the URL when the component is created
        formData.value.trip_id = window.location.pathname.split('/')[2];

        return {
            formData,
            isSubmitting,
            errorMessage,
            submitForm,
            handleFileChange,
        };
    }
});

app.mount('#app');
 </script>