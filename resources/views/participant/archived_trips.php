<?php
$header_title = "Trip";
$content = __DIR__ . '/dashboard.php'; // Load actual content
include __DIR__ . '/../backend/layouts/app.php';
?>

<div class="container mt-5">
    <h2>Archived Trips</h2>
    <p>These are the trips you either declined.</p>

    <?php if (empty($archivedTrips)): ?>
    <p>No archived trips found.</p>
    <?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Trip Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($archivedTrips as $trip): ?>

            <tr>
                <td><?php echo htmlspecialchars($trip['name']); ?></td>
                <td><?php echo htmlspecialchars($trip['start_date']); ?></td>
                <td><?php echo htmlspecialchars($trip['end_date']); ?></td>
                <td>
                    <?php
                    if ($trip['participant_status'] === 'declined') {
                        echo '<span class="badge bg-warning text-dark">Declined</span>';
                    } elseif ($trip['participant_status'] === 'cancelled') {
                        echo '<span class="badge bg-danger">Cancelled</span>';
                    } else {
                        echo htmlspecialchars($trip['participant_status']);
                    }
                    ?>
                </td>
                <td>
                    <a href="/participant/trip-details/<?php echo htmlspecialchars($trip['id']); ?>"
                        class="btn btn-sm btn-info">View Details</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($totalPagesArchived > 1): ?>
    <nav aria-label="Archived Trips Pagination">
        <ul class="pagination">
            <?php if ($currentArchivePage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?archive_page=<?php echo $currentArchivePage - 1; ?>">Previous</a>
            </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPagesArchived; $i++): ?>
            <li class="page-item <?php echo ($i == $currentArchivePage) ? 'active' : ''; ?>">
                <a class="page-link" href="?archive_page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
            <?php endfor; ?>

            <?php if ($currentArchivePage < $totalPagesArchived): ?>
            <li class="page-item">
                <a class="page-link" href="?archive_page=<?php echo $currentArchivePage + 1; ?>">Next</a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>

    <?php endif; ?>
</div>