<?php
$header_title = "Trip";
$content = __DIR__ . '/dashboard.php'; // Load actual content
include __DIR__ . '/../backend/layouts/app.php';
?>




    <div class="container my-5">
        <h2 class="text-center mb-4">🌍 Trip Details</h2>


        <?php
        // Ensure session is started in your controller or a middleware
        // You should have access to $tripDetails and $participantStatus here

        // Get the ID of the currently logged-in user
        $loggedInUserId = $_SESSION['user_id'] ?? null;

        // Check if $participantStatus is set and is 'accepted'
        if ($loggedInUserId && isset($participantStatus) && strtolower(trim($participantStatus)) === 'accepted') :
        ?>
        <div class="mb-3">
            <form action="/participant/generate-invite-link" method="post">
                <input type="hidden" name="trip_id" value="<?= htmlspecialchars($tripDetails['trip_id'] ?? '') ?>">
                <button type="submit" class="btn btn-primary">Invite Friend</button>
            </form>
        </div>
        <?php endif; ?>




        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">📅 Itinerary</div>
                    <div class="card-body">
                        <?php if (!empty($itinerary)) : ?>
                        <?php foreach ($itinerary as $item) : ?>
                        <div class="border-bottom pb-3 mb-3" id="itineraryItem_<?= $item['id'] ?>">
                            <h5 class="mb-1"><?= htmlspecialchars($item['day_title']) ?></h5>
                            <?php
                                    $shortDescription = substr(htmlspecialchars($item['description']), 0, 100);
                                    $fullDescription = htmlspecialchars($item['description']);
                                    $showMore = strlen($fullDescription) > 100;
                                    ?>
                            <p class="full-description d-none" id="fullDescription_<?= $item['id'] ?>">
                                <?= $fullDescription ?>
                            </p>
                            <?php if ($showMore) : ?>
                            <button type="button" class="btn btn-sm btn-outline-info show-details-btn mt-2"
                                data-item-id="<?= $item['id'] ?>">Show Details</button>
                            <?php endif; ?>
                            <?php if (!empty($item['location'])) : ?>
                            - <?= htmlspecialchars($item['location']) ?>
                            <?php endif; ?>
                            <small class="text-secondary"><?= htmlspecialchars($item['itinerary_date']) ?></small>
                        </div>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <p class="itinerary-message">No itinerary available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">🏨 Accommodations</div>
                    <div class="card-body">
                        <?php if (!empty($accommodations)) : ?>
                        <?php foreach ($accommodations as $acc) : ?>
                        <p>
                            <strong>Hotel:</strong> <?= htmlspecialchars($acc['hotel_name']) ?><br>
                            <strong>Country:</strong> <?= htmlspecialchars($acc['country_name']) ?><br>
                            <strong>State:</strong> <?= htmlspecialchars($acc['state_name']) ?><br>
                            <strong>Room Type:</strong> <?= htmlspecialchars($acc['room_type_name']) ?><br>
                            <strong>Capacity:</strong> <?= htmlspecialchars($acc['room_capacity']) ?><br>
                            <strong>Description:</strong> <?= htmlspecialchars($acc['room_description']) ?><br>
                            <strong>Amenities:</strong> <?= htmlspecialchars($acc['room_amenities']) ?><br>
                            <strong>Check-in Date:</strong> <?= $acc['check_in_date'] ?><br>
                            <strong>Check-out Date:</strong> <?= $acc['check_out_date'] ?>
                        </p>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <p>No accommodations found for this trip.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">🚗 Transportation</div>
                    <div class="card-body">
                        <?php if (!empty($transportation)) : ?>
                        <?php foreach ($transportation as $transport) : ?>
                        <div class="border-bottom pb-2 mb-2">
                            <h5 class="mb-1"><?= htmlspecialchars($transport['type']) ?></h5>
                            <p>Departure: <?= htmlspecialchars($transport['departure_location']) ?> |
                                Arrival: <?= htmlspecialchars($transport['arrival_location']) ?> | Comapny Name:
                                <?= htmlspecialchars($transport['company_name']) ?>| Departure Date:
                                <?= htmlspecialchars($transport['departure_date']) ?>| Arrival Date:
                                <?= htmlspecialchars($transport['arrival_date']) ?>| Amount:
                                $<?= htmlspecialchars($transport['amount']) ?></p>
                        </div>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <p>No transportation details available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">💰 Expenses</div>
                    <div class="card-body">
                        <?php if (!empty($expenses)) : ?>
                        <?php foreach ($expenses as $expense) : ?>
                        <div class="border-bottom pb-2 mb-2">
                            <h5 class="mb-1"><?= htmlspecialchars($expense['category']) ?></h5>
                            <p>Date: <?= htmlspecialchars($expense['expense_date']) ?></p>
                            <div class="amount text-danger">$<?= htmlspecialchars($expense['amount']) ?></div>
                            <p class="mb-1"><?= htmlspecialchars($expense['description']) ?></p>
                        </div>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <p>No expenses recorded.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>



            <?php if (isset($participantStatus) && strtolower(trim($participantStatus)) === 'accepted') : ?>
            <div class="col-md-12 mt-4">
                <div class="card shadow">
                    <div class="card-header bg-secondary text-white">🗳️ Create Simple Poll</div>
                    <div class="card-body">
                        <form method="POST" action="/participant/poll/create">
                            <input type="hidden" name="trip_id"
                                value="<?= htmlspecialchars($tripDetails['trip_id'] ?? '') ?>">
                            <div class="mb-3">
                                <label for="itinerary_day" class="form-label">Select Day</label>
                                <select class="form-control" id="itinerary_day" name="itinerary_id" required>
                                    <option value="">Select a Day</option>
                                    <?php if (!empty($itinerary)) : ?>
                                    <?php foreach ($itinerary as $item) : ?>
                                    <option value="<?= htmlspecialchars($item['id']) ?>">
                                        <?= htmlspecialchars($item['day_title']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <?php else : ?>
                                    <option value="" disabled>No itinerary available</option>
                                    <?php endif; ?>
                                </select>
                                <small class="form-text text-muted">Select the day for your poll.</small>
                            </div>
                            <div class="mb-3">
                                <label for="poll_question" class="form-label">Proposed Activity / Question</label>
                                <textarea class="form-control" id="poll_question" name="question" rows="3" required
                                    placeholder="What should we do on this day?"></textarea>
                                <small class="form-text text-muted">Enter your proposed activity or question for the
                                    poll.</small>
                            </div>
                            <button type="submit" class="btn btn-primary">Create Poll</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-4">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">📊 Current Polls</div>
                    <div class="card-body">
                        <?php if (!empty($polls)) : ?>
                        <?php foreach ($polls as $poll) : ?>
                        <div class="mb-4 border p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h5><?= htmlspecialchars($poll['question']) ?></h5>
                                <p class="mb-2 ">Day:
                                    <?php
                                                foreach ($itinerary as $item) {
                                                    if ($item['id'] == $poll['itinerary_id']) {
                                                        echo htmlspecialchars($item['day_title']);
                                                        break;
                                                    }
                                                }
                                                ?>
                                </p>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-success like-btn"
                                        data-poll-id="<?= htmlspecialchars($poll['id']) ?>">
                                        <i class="bi bi-heart-fill"></i> Like (<span
                                            class="like-count"><?= htmlspecialchars($poll['likes']) ?></span>)
                                    </button>
                                    <span class="already-liked-message text-success ms-2" style="display: none;">Already
                                        Liked</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <p>No polls have been created yet for specific days.</p>
                        <?php endif; ?>
                        <p class="mt-3">More liked polls are added on the trip itinerary.</p>
                    </div>
                </div>
            </div>
            <?php else : ?>
            <div class="col-md-12 mt-4">
                <div class="card shadow">
                    <div class="card-body">
                        <p class="text-info">You need to be an accepted participant to create and view polls for this
                            trip.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>


        </div>

        <?php if (isset($participantStatus) && strtolower(trim($participantStatus)) === 'accepted') : ?>
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
        <p>You cannot review this trip until you accept it!</p>
        <?php endif; ?>

        <h4>Other Reviews</h4>
        <?php if (!empty($reviews)) : ?>
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

            <p><strong>Review:</strong> <?= htmlspecialchars($review['review_text']) ?></p>

            <?php if (isset($review['created_at'])) : ?>
            <p><strong>Reviewed on:</strong> <?= date('F j, Y, g:i a', strtotime($review['created_at'])) ?></p>
            <?php endif; ?>

            <?php if (isset($review['name'])) : ?>
            <p><strong>Reviewed by:</strong> <?= htmlspecialchars($review['name']) ?></p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php else : ?>
        <p class="no-reviews">No reviews yet.</p>
        <?php endif; ?>


    </div>
    <div class="text-center mt-3">
        <a href="/participant/trips" class="btn btn-outline-secondary">Back</a>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Functionality for the "Show Details" button in the itinerary
        const showDetailButtons = document.querySelectorAll('.show-details-btn');
        showDetailButtons.forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                const fullDescription = document.getElementById(`fullDescription_${itemId}`);
                if (fullDescription) {
                    fullDescription.classList.toggle('d-none');
                    this.textContent = fullDescription.classList.contains('d-none') ?
                        'Show Details' : 'Show Less';
                }
            });
        });

        // Functionality for the "Like" button on polls
        const likeButtons = document.querySelectorAll('.like-btn');
        likeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const pollId = this.dataset.pollId;
                const likeCountSpan = this.querySelector('.like-count');
                const originalLikeText = this.innerHTML;
                const alreadyLikedMessageSpan = this.nextElementSibling;

                fetch(`/participant/poll/like/${pollId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            console.error('Error liking poll:', response.status);
                            if (response.status === 403) {
                                alert(
                                    'You are not an accepted participant and cannot like polls.'
                                    );
                            }
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);

                        if (data && typeof data.likes !== 'undefined') {
                            likeCountSpan.textContent = data.likes;
                            updateLikeButtonAppearance(button, true);
                            if (alreadyLikedMessageSpan) {
                                alreadyLikedMessageSpan.style.display = 'none';
                            }
                        } else if (data && data.message && data.liked) {
                            console.log(data.message);
                            if (alreadyLikedMessageSpan) {
                                alreadyLikedMessageSpan.style.display = 'inline';
                            }
                            updateLikeButtonAppearance(button, true);
                        } else if (data && data.message) {
                            console.log(data.message);
                            if (alreadyLikedMessageSpan) {
                                alreadyLikedMessageSpan.style.display = 'inline';
                                alreadyLikedMessageSpan.textContent = data.message;
                                setTimeout(() => {
                                    alreadyLikedMessageSpan.style.display = 'none';
                                    alreadyLikedMessageSpan.textContent =
                                        'Already Liked';
                                }, 3000);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error liking poll:', error);
                        button.innerHTML = originalLikeText;
                        updateLikeButtonAppearance(button, false);
                        if (alreadyLikedMessageSpan) {
                            alreadyLikedMessageSpan.style.display = 'none';
                        }
                    });
            });
        });

        // Function to update the visual appearance of the like button
        function updateLikeButtonAppearance(button, liked) {
            if (liked) {
                button.innerHTML = '<i class="bi bi-hand-thumbs-up-fill"></i> Liked';
                button.classList.remove('btn-outline-success');
                button.classList.add('btn-success');
                button.disabled = true;
            } else {
                const likeCount = button.querySelector('.like-count').textContent;
                button.innerHTML = '<i class="bi bi-hand-thumbs-up"></i> Like (<span class="like-count">' +
                    likeCount + '</span>)';
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-success');
                button.disabled = false;
            }
        }

        // Initial check on page load to see if the user has already liked a poll
        likeButtons.forEach(button => {
            const pollId = button.dataset.pollId;
            const alreadyLikedMessageSpan = button.nextElementSibling;
            fetch(`/participant/poll/check-vote/${pollId}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.liked) {
                        updateLikeButtonAppearance(button, true);
                        if (alreadyLikedMessageSpan) {
                            alreadyLikedMessageSpan.style.display = 'inline';
                        }
                    }
                })
                .catch(error => console.error('Error checking vote:', error));
        });
    });
    </script>
