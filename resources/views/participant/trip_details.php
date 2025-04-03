<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trip Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .card {
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease-in-out;
    }


    .card-header {
        font-weight: bold;
    }

    .amount {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: bold;
        padding: 10px 0;
    }
    </style>
</head>

<body>


    <div class="container my-5">
        <h2 class="text-center mb-4">🌍 Trip Details</h2>
        <p><strong>Total Participants:</strong> <?= $tripDetails['accepted_participants'] ?></p>

        <div class="row g-4">
            <!-- Itinerary Section -->
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">📅 Itinerary</div>
                    <div class="card-body">
                        <?php if (!empty($itinerary)) : ?>
                        <?php foreach ($itinerary as $item): ?>
                        <div class="border-bottom pb-2 mb-2">
                            <h5 class="mb-1"><?= htmlspecialchars($item['day_title']) ?></h5>
                            <p class="text-muted"><?= htmlspecialchars($item['description']) ?></p>
                            <small class="text-secondary"><?= htmlspecialchars($item['itinerary_date']) ?></small>

                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal"
                                data-bs-target="#editRequestModal_<?= $item['id'] ?>">
                                ✏️ Request Edit
                            </button>

                            <div class="modal fade" id="editRequestModal_<?= $item['id'] ?>" tabindex="-1"
                                aria-labelledby="editRequestLabel_<?= $item['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Request Itinerary Edit</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                        <form method="POST" action="/participant/trip/<?= $tripDetails['trip_id'] ?>/itinerary/<?= $item['id'] ?>/request-edit">
                                                <div class="mb-3">
                                                    <label for="edit_reason_<?= $item['id'] ?>"
                                                        class="form-label">Reason for Edit Request</label>
                                                    <textarea class="form-control" id="edit_reason_<?= $item['id'] ?>"
                                                        name="edit_reason" rows="3" required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Submit Request</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <p class="text-muted">No itinerary available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

















            <!-- Accommodations Section -->
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">🏨 Accommodations</div>
                    <div class="card-body">
                        <?php if (!empty($accommodations)): ?>
                        <?php foreach ($accommodations as $acc): ?>
                        <p><strong>Room:</strong> <?= htmlspecialchars($acc['room_description']) ?><br>
                            <strong>Room Type:</strong> <?= htmlspecialchars($acc['room_type']) ?><br>
                            <strong>Dates:</strong> <?= $acc['check_in_date'] ?> to <?= $acc['check_out_date'] ?>
                        </p>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p>No accommodations found for this trip.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>




            <!-- Transportation Section -->
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">🚗 Transportation</div>
                    <div class="card-body">
                        <?php if (!empty($transportation)) : ?>
                        <?php foreach ($transportation as $transport): ?>
                        <div class="border-bottom pb-2 mb-2">
                            <h5 class="mb-1"><?= htmlspecialchars($transport['type']) ?></h5>
                            <p class="text-muted">Departure: <?= htmlspecialchars($transport['departure_location']) ?> |
                                Arrival: <?= htmlspecialchars($transport['arrival_location']) ?> | Comapny Name:
                                <?= htmlspecialchars($transport['company_name']) ?>| Departure Date:
                                <?= htmlspecialchars($transport['departure_date']) ?>| Arrival Date:
                                <?= htmlspecialchars($transport['arrival_date']) ?>| Amount:
                                $<?= htmlspecialchars($transport['amount']) ?></p>
                        </div>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <p class="text-muted">No transportation details available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Expenses Section -->
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">💰 Expenses</div>
                    <div class="card-body">
                        <?php if (!empty($expenses)) : ?>
                        <?php foreach ($expenses as $expense): ?>
                        <div class="border-bottom pb-2 mb-2">
                            <h5 class="mb-1"><?= htmlspecialchars($expense['description']) ?></h5>
                            <p class="text-muted">Date: <?= htmlspecialchars($expense['expense_date']) ?></p>
                            <div class="amount text-danger">$<?= htmlspecialchars($expense['amount']) ?></div>
                        </div>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <p class="text-muted">No expenses recorded.</p>
                        <?php endif; ?>





                    </div>
                </div>
            </div>

            <?php if ($participantStatus === 'accepted') : ?>
            <!-- Trip accepted, show review form -->
            <div class="mt-4">
                <h4>Rate Your Trip</h4>
                <form action="/participant/submitReview/<?= $tripDetails['trip_id'] ?>" method="POST">
                    <div class="form-group">
                        <label for="rating">Rating (1-5):</label>
                        <select name="rating" id="rating" class="form-control" required>
                            <option value="">Select Rating</option>
                            <option value="1">1 - Poor</option>
                            <option value="2">2 - Fair</option>
                            <option value="3">3 - Good</option>
                            <option value="4">4 - Very Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <label for="review_text">Review:</label>
                        <textarea name="review_text" id="review_text" class="form-control" rows="4"
                            placeholder="Write your review..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Submit Review</button>
                </form>
            </div>

            <?php else : ?>
            <!-- Trip not accepted, show the message -->
            <p>You cannot review this trip until you accept it!</p>
            <?php endif; ?>

            <!-- Display all reviews for this trip -->
            <h4>Other Reviews</h4>
            <?php if (!empty($reviews)) : ?>
            <!-- Reviews available -->
            <?php foreach ($reviews as $review) : ?>
            <div class="review">
                <p><strong>Rating:</strong>
                    <?php
                    $rating = $review['rating'];
                    // Display filled stars based on rating
                    for ($i = 1; $i <= 5; $i++) {
                        echo ($i <= $rating) ? '★' : '☆';
                    }
                ?> / 5
                </p>

                <!-- Display Review Text -->
                <p><strong>Review:</strong> <?= htmlspecialchars($review['review_text']) ?></p>

                <!-- Display Review Time -->
                <?php if (isset($review['created_at'])) : ?>
                <p><strong>Reviewed on:</strong> <?= date('F j, Y, g:i a', strtotime($review['created_at'])) ?></p>
                <?php endif; ?>

                <!-- Display Reviewer Name -->
                <?php if (isset($review['name'])) : ?>
                <p><strong>Reviewed by:</strong> <?= htmlspecialchars($review['name']) ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php else : ?>
            <!-- No reviews available -->
            <p class="no-reviews">No reviews yet.</p>
            <?php endif; ?>


        </div>
        <div class="text-center mt-3">
            <a href="/participant/dashboard" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>




    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form[action*="/request-edit"]');

        forms.forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(this);
                const actionUrl = this.getAttribute('action');
                const modalElement = this.closest('.modal');

                fetch(actionUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);

                    if (data.includes("✅ Request stored successfully!")) {
                        const modalBody = modalElement.querySelector('.modal-body');
                        modalBody.innerHTML = '<div class="alert alert-success">Request submitted successfully!</div>';

                        setTimeout(() => {
                            const modal = bootstrap.Modal.getInstance(modalElement);
                            modal.hide();
                            setTimeout(() => {
                                modalBody.innerHTML = `
                                    <div class="mb-3">
                                        <label for="edit_reason_${modalElement.id.split('_')[1]}" class="form-label">Reason for Edit Request</label>
                                        <textarea class="form-control" id="edit_reason_${modalElement.id.split('_')[1]}" name="edit_reason" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit Request</button>
                                `;
                            }, 500);
                        }, 1500);
                    } else {
                        const modalBody = modalElement.querySelector('.modal-body');
                        modalBody.innerHTML += `<div class="alert alert-danger mt-2">${data}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const modalBody = modalElement.querySelector('.modal-body');
                    modalBody.innerHTML += '<div class="alert alert-danger mt-2">An error occurred. Please try again.</div>';
                });
            });
        });
    });
</script>
</body>

</html>