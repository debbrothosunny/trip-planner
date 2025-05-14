<?php
$header_title = "Trip-Itinerary";
include __DIR__ . '/../backend/layouts/app.php';
?>

<div class="content">
    <div class="container mt-5">
        <h2 class="mb-4 text-white">Trip Itineraries</h2>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="/trip/<?= $trip_id ?>/itinerary/create" class="btn btn-primary shadow-sm"><i
                    class="bi bi-plus-circle me-2"></i> Create Itinerary</a>

        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-dark">
                <thead class="thead-light">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($itineraries)): ?>
                    <?php foreach ($itineraries as $row): ?>
                    <tr>
                        <td class="align-middle text-center">
                            <?php if (!empty($row['image'])): ?>
                            <a href="/image/itinerary_img/<?= htmlspecialchars($row['image']) ?>" target="_blank"
                                class="d-inline-block">
                                <img src="/image/itinerary_img/<?= htmlspecialchars($row['image']) ?>"
                                    alt="<?= htmlspecialchars($row['day_title']) ?>"
                                    style="max-width: 80px; max-height: 80px; border-radius: 4px; object-fit: cover; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5); cursor: pointer;">
                            </a>
                            <?php else: ?>
                            <img src="/image/default_itinerary.jpg" alt="Default Image"
                                style="max-width: 80px; max-height: 80px; border-radius: 4px; object-fit: cover; opacity: 0.7;">
                            <?php endif; ?>
                        </td>
                        <td class="align-middle"><?= htmlspecialchars($row['day_title']) ?></td>
                        <td class="align-middle" title="<?= htmlspecialchars($row['description']) ?>">
                            <?= htmlspecialchars(substr($row['description'], 0, 100)) ?>
                            <?= strlen($row['description']) > 100 ? '...' : '' ?>
                        </td>
                        <td class="align-middle"><?= htmlspecialchars($row['location']) ?></td>
                        <td class="align-middle"><?= htmlspecialchars($row['itinerary_date']) ?></td>
                        <td class="align-middle text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="/trip/<?= $trip_id ?>/itinerary/<?= $row['id'] ?>/edit"
                                    class="btn btn-sm btn-outline-warning shadow-sm" title="Edit">
                                    <i class="bi bi-pen"></i>
                                </a>
                                <a href="/itinerary/<?= htmlspecialchars($row['id']) ?>/delete"
                                    class="btn btn-sm btn-outline-danger shadow-sm" title="Delete"
                                    onclick="return confirm('Are you sure you want to delete this itinerary?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">No itineraries found for this trip.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</html>