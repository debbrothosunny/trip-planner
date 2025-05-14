<?php
$header_title = "Trip-Itinerary-Edit";
include __DIR__ . '/../backend/layouts/app.php';
?>
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h4 class="card-title mb-0"><i class="fas fa-edit me-2"></i>Edit Itinerary</h4>
        </div>
        <div class="card-body p-4">
            <form action="/trip/<?= htmlspecialchars($trip_id) ?>/itinerary/<?= htmlspecialchars($data['id']) ?>/update"
                method="POST" enctype="multipart/form-data" class="row g-3">

                <input type="hidden" name="id" value="<?= htmlspecialchars($data['id'] ?? '') ?>">
                <input type="hidden" name="trip_id" value="<?= htmlspecialchars($_GET['trip_id'] ?? '') ?>">

                <div class="col-md-6">
                    <label for="day_title" class="form-label"><i class="fas fa-signature me-1"></i>Name</label>
                    <input type="text" class="form-control" id="day_title" name="day_title"
                        value="<?= htmlspecialchars($data['day_title'] ?? '') ?>" required>
                    <div class="invalid-feedback">Please provide the itinerary name.</div>
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label"><i class="fas fa-file-alt me-1"></i>Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4"
                        required><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
                    <div class="invalid-feedback">Please provide a description.</div>
                </div>

                <div class="col-md-6">
                    <label for="location" class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Location</label>
                    <input type="text" class="form-control" id="location" name="location"
                        value="<?= htmlspecialchars($data['location'] ?? '') ?>" required>
                    <div class="invalid-feedback">Please provide the location.</div>
                </div>

                <div class="col-md-6">
                    <label for="itinerary_date" class="form-label"><i class="fas fa-calendar-alt me-1"></i>Date</label>
                    <input type="date" class="form-control" id="itinerary_date" name="itinerary_date"
                        value="<?= htmlspecialchars($data['itinerary_date'] ?? '') ?>" required>
                    <div class="invalid-feedback">Please select the date.</div>
                </div>

                <div class="col-md-12">
                    <label for="image" class="form-label"><i class="fas fa-image me-1"></i>Image (Optional)</label>
                    <input type="file" class="form-control" id="image" name="image">
                    <?php if (!empty($data['image'])): ?>
                    <div class="mt-2">

                        <img src="/image/itinerary_img/<?= htmlspecialchars($data['image']) ?>" alt="Current Image"
                            style="max-width: 150px; height: auto; border: 1px solid #ddd; border-radius: 4px;">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="remove_image"
                                name="remove_image">
                            <label class="form-check-label" for="remove_image">
                                <i class="fas fa-trash me-1"></i>Remove Current Image
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-12 mt-3">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Update
                            Itinerary</button>
                        <button type="button" class="btn btn-outline-secondary"><i
                                class="fas fa-arrow-left me-2"></i>Back</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-label i {
    color: #007bff;
    /* Bootstrap primary color */
}

.btn-success i {
    color: white;
}
</style>

<script>
// Bootstrap form validation (optional, but good for user experience)
(() => {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }

            form.classList.add('was-validated')
        }, false)
    })
})()

document.addEventListener('DOMContentLoaded', function() {
        const backButton = document.querySelector('.btn-outline-secondary');

        if (backButton) {
            backButton.addEventListener('click', function() {
                window.history.back();
            });
        }
    });
</script>