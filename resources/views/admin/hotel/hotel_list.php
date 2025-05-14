<?php
$header_title = "Hotel";
// Include layout (or other necessary files)
include __DIR__ . '/../layouts/app.php';

include __DIR__ . '/../sidebar/sidebar.php';

if (file_exists($sidebarPath)) {
    include $sidebarPath;
}
?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Active Hotels</h2>
        <div>
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search hotels...">
            <a href="/admin/hotel/create" class="btn btn-primary mt-2">Add New Hotel</a>
        </div>
    </div>
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($hotels)): ?>
    <table class="table table-bordered" id="hotelsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Country</th>
                <th>State</th>
                <th>Address</th>
                <th>Description</th>
                <th>Star Rating</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($hotels as $hotel): ?>
            <tr>
                <td><?php echo htmlspecialchars($hotel['id']); ?></td>
                <td><?php echo htmlspecialchars($hotel['name']); ?></td>
                <td><?php echo htmlspecialchars($hotel['country_name']); ?></td>
                <td><?php echo htmlspecialchars($hotel['state_name']); ?></td>
                <td><?php echo htmlspecialchars($hotel['address']); ?></td>
                <td><?php echo htmlspecialchars($hotel['description']); ?></td>
                <td><?php echo htmlspecialchars($hotel['star_rating']); ?></td>
                <td><?php echo htmlspecialchars($hotel['status'] == 0 ? 'Active' : 'Inactive'); ?></td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#editHotelModal-<?php echo htmlspecialchars($hotel['id']); ?>">Edit</button>
                    <a href="/admin/hotel/delete/<?php echo htmlspecialchars($hotel['id']); ?>"
                        class="btn btn-sm btn-danger delete-hotel-btn"
                        data-hotel-id="<?php echo htmlspecialchars($hotel['id']); ?>">Delete</a>
                </td>
            </tr>

            <div class="modal fade" id="editHotelModal-<?php echo htmlspecialchars($hotel['id']); ?>" tabindex="-1"
                aria-labelledby="editHotelModalLabel-<?php echo htmlspecialchars($hotel['id']); ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="editHotelModalLabel-<?php echo htmlspecialchars($hotel['id']); ?>">Edit Hotel
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="/admin/hotel/update/<?php echo htmlspecialchars($hotel['id']); ?>"
                                method="POST">
                                <div class="mb-3">
                                    <label for="edit_country_id-<?php echo htmlspecialchars($hotel['id']); ?>"
                                        class="form-label">Country:</label>
                                    <select class="form-select"
                                        id="edit_country_id-<?php echo htmlspecialchars($hotel['id']); ?>"
                                        name="country_id" required>
                                        <option value="">Select Country</option>
                                        <?php if (!empty($countries)): ?>
                                        <?php foreach ($countries as $country): ?>
                                        <option value="<?php echo htmlspecialchars($country['id']); ?>"
                                            <?php echo ($hotel['country_id'] == $country['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($country['name']); ?></option>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <option value="" disabled>No active countries found</option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_state_id-<?php echo htmlspecialchars($hotel['id']); ?>"
                                        class="form-label">State:</label>
                                    <select class="form-select"
                                        id="edit_state_id-<?php echo htmlspecialchars($hotel['id']); ?>" name="state_id"
                                        required>
                                        <option value="">Select State</option>
                                        <?php if (!empty($states)): ?>
                                        <?php foreach ($states as $state): ?>
                                        <option value="<?php echo htmlspecialchars($state['id']); ?>"
                                            <?php echo ($hotel['state_id'] == $state['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($state['name']); ?></option>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <option value="" disabled>No active states found</option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_name-<?php echo htmlspecialchars($hotel['id']); ?>"
                                        class="form-label">Name:</label>
                                    <input type="text" class="form-control"
                                        id="edit_name-<?php echo htmlspecialchars($hotel['id']); ?>" name="name"
                                        value="<?php echo htmlspecialchars($hotel['name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_address-<?php echo htmlspecialchars($hotel['id']); ?>"
                                        class="form-label">Address:</label>
                                    <textarea class="form-control"
                                        id="edit_address-<?php echo htmlspecialchars($hotel['id']); ?>"
                                        name="address"><?php echo htmlspecialchars($hotel['address']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_description-<?php echo htmlspecialchars($hotel['id']); ?>"
                                        class="form-label">Description:</label>
                                    <textarea class="form-control"
                                        id="edit_description-<?php echo htmlspecialchars($hotel['id']); ?>"
                                        name="description"><?php echo htmlspecialchars($hotel['description']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_star_rating-<?php echo htmlspecialchars($hotel['id']); ?>"
                                        class="form-label">Star Rating:</label>
                                    <select class="form-select"
                                        id="edit_star_rating-<?php echo htmlspecialchars($hotel['id']); ?>"
                                        name="star_rating" required>
                                        <option value="1" <?php echo ($hotel['star_rating'] == 1) ? 'selected' : ''; ?>>
                                            1 Star
                                        </option>
                                        <option value="2" <?php echo ($hotel['star_rating'] == 2) ? 'selected' : ''; ?>>
                                            2 Stars
                                        </option>
                                        <option value="3" <?php echo ($hotel['star_rating'] == 3) ? 'selected' : ''; ?>>
                                            3 Stars
                                        </option>
                                        <option value="4" <?php echo ($hotel['star_rating'] == 4) ? 'selected' : ''; ?>>
                                            4 Stars
                                        </option>
                                        <option value="5" <?php echo ($hotel['star_rating'] == 5) ? 'selected' : ''; ?>>
                                            5 Stars
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_status-<?php echo htmlspecialchars($hotel['id']); ?>"
                                        class="form-label">Status:</label>
                                    <select class="form-select"
                                        id="edit_status-<?php echo htmlspecialchars($hotel['id']); ?>" name="status"
                                        required>
                                        <option value="0" <?php echo ($hotel['status'] == 0) ? 'selected' : ''; ?>>
                                            Active</option>
                                        <option value="1" <?php echo ($hotel['status'] == 1) ? 'selected' : ''; ?>>
                                            Inactive</option>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update Hotel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Hotel Pagination">
        <ul class="pagination">
            <?php if ($currentPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="/admin/hotel?page=<?php echo $currentPage - 1; ?>">Previous</a>
            </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                <a class="page-link" href="/admin/hotel?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="/admin/hotel?page=<?php echo $currentPage + 1; ?>">Next</a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
    <?php else: ?>
    <p>No active hotels found.</p>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const hotelsTable = document.getElementById('hotelsTable').getElementsByTagName('tbody')[0];
    const tableRows = hotelsTable.getElementsByTagName('tr');
    const deleteButtons = document.querySelectorAll('.delete-hotel-btn');
    const urlParams = new URLSearchParams(window.location.search);
    const successMessage = urlParams.get('success');
    const errorMessage = urlParams.get('error');

    if (successMessage) {
        Swal.fire('Success!', successMessage, 'success');
        history.replaceState(null, null, window.location.pathname);
    }

    if (errorMessage) {
        Swal.fire('Error!', errorMessage, 'error');
        history.replaceState(null, null, window.location.pathname);
    }

    searchInput.addEventListener('input', function() {
        const searchTerm = searchInput.value.toLowerCase();

        for (let i = 0; i < tableRows.length; i++) {
            const rowData = tableRows[i].textContent.toLowerCase();
            if (rowData.includes(searchTerm)) {
                tableRows[i].style.display = ''; // Show the row
            } else {
                tableRows[i].style.display = 'none'; // Hide the row
            }
        }
    });

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const hotelId = this.dataset.hotelId;

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
                    window.location.href = `/admin/hotel/delete/${hotelId}`;
                }
            });
        });
    });
});
</script>