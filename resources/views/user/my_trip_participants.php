<?php
$header_title = "Accomodation";
$content = __DIR__ . '/dashboard.php'; // Load actual content
include __DIR__ . '/../backend/layouts/app.php';
?>
<style>
.content {
    margin-left: 270px;
    padding: 20px;
    width: 100%;
}

.edit-request-item {
    border: 1px solid #444;
    /* Darker border */
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    background-color: #333;
    /* Darker background */
}

.edit-request-item small {
    color: #aaa;
    /* Lighter text for small details */
}

.edit-request-item strong {
    color: #fff;
    /* White text for strong emphasis */
}

.edit-request-item .btn-group {
    margin-top: 10px;
}

.edit-request-item .badge {
    background-color: #ffc107;
    /* Yellow badge for pending */
    color: #333;
    /* Dark text on yellow badge */
}
</style>
<div class="container mt-5 text-white">
    <h2 class="text-center mb-4">Manage My Trips & Participants</h2>

    <?php if (empty($trips)): ?>
    <div class="alert alert-warning text-center">
        You have not created any trips yet.
    </div>
    <?php else: ?>
    <?php foreach ($trips as $trip): ?>
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0"><?= htmlspecialchars($trip['trip_name']); ?></h4>
        </div>
        <div class="card-body">
            <h5 class="mb-3">Accepted Participants:</h5>
            <?php if (isset($participants[$trip['trip_id']]) && !empty($participants[$trip['trip_id']])): ?>
            <table class="table table-striped table-bordered text-white">
                <thead>
                    <tr>
                        <th>Participant Name</th>
                        <th>Email</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participants[$trip['trip_id']] as $participant): ?>
                    <tr class="text-white">
                        <td><?= htmlspecialchars($participant['user_name']); ?></td>
                        <td><?= htmlspecialchars($participant['user_email']); ?></td>
                        <td>
                            <?php if ($participant['payment_status'] == 0): ?>
                            <span class="badge bg-success">Completed</span>
                            <?php elseif ($participant['payment_status'] == 1): ?>
                            <span class="badge bg-warning">Pending</span>
                            <?php else: ?>
                            <span class="badge bg-secondary">No Payment Yet</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="alert alert-info text-center">
                No accepted participants joined this trip yet.
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const acceptForms = document.querySelectorAll('.accept-form');
    const rejectForms = document.querySelectorAll('.reject-form');

    acceptForms.forEach(form => {
        form.addEventListener('submit', handleSubmit);
    });

    rejectForms.forEach(form => {
        form.addEventListener('submit', handleSubmit);
    });

    async function handleSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const actionUrl = form.action;
        const formData = new FormData(form);
        const requestId = actionUrl.split('/').pop(); // Extract requestId from URL
        const status = formData.get('status');

        try {
            const response = await fetch(actionUrl, {
                method: 'POST',
                body: formData,
            });

            const data = await response.json();

            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: (status === 'accept' ? 'Approved!' : 'Rejected!'),
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message,
                });
            }

        } catch (error) {
            console.error('Fetch error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An unexpected error occurred.',
            });
        }
    }
});
</script>