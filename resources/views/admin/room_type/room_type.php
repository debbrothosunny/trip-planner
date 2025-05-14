<?php
$header_title = "Room Type";
// Include layout (or other necessary files)
include __DIR__ . '/../layouts/app.php';

include __DIR__ . '/../sidebar/sidebar.php';

if (file_exists($sidebarPath)) {
    include $sidebarPath;
}
?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Room Types</h1>
        <div>
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search room types...">
            <a href="/admin/room-type/create" class="btn btn-success mt-2">Add New Room Type</a>
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

    <?php if (!empty($roomTypes)): ?>
    <table class="table table-striped" id="roomTypesTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($roomTypes as $roomType): ?>
            <tr>
                <td><?php echo htmlspecialchars($roomType['id']); ?></td>
                <td><?php echo htmlspecialchars($roomType['name']); ?></td>
                <td>
                    <?php
                    if ($roomType['status'] == 0) {
                        echo '<span class="badge bg-success">Active</span>';
                    } else {
                        echo '<span class="badge bg-warning">Pending</span>';
                    }
                    ?>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary edit-room-type-btn" data-bs-toggle="modal"
                        data-bs-target="#editRoomTypeModal-<?php echo htmlspecialchars($roomType['id']); ?>">
                        Edit
                    </button>
                    <a href="/admin/room-type/delete/<?php echo htmlspecialchars($roomType['id']); ?>"
                        class="btn btn-sm btn-danger delete-room-type-btn"
                        data-room-type-id="<?php echo htmlspecialchars($roomType['id']); ?>">Delete</a>
                </td>
            </tr>

            <div class="modal fade" id="editRoomTypeModal-<?php echo htmlspecialchars($roomType['id']); ?>"
                tabindex="-1" aria-labelledby="editRoomTypeModalLabel-<?php echo htmlspecialchars($roomType['id']); ?>"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="editRoomTypeModalLabel-<?php echo htmlspecialchars($roomType['id']); ?>">Edit Room
                                Type</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="/admin/room-type/update/<?php echo htmlspecialchars($roomType['id']); ?>"
                            method="post">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name-<?php echo htmlspecialchars($roomType['id']); ?>"
                                        class="form-label">Room Type Name:</label>
                                    <input type="text" class="form-control"
                                        id="name-<?php echo htmlspecialchars($roomType['id']); ?>" name="name"
                                        value="<?php echo htmlspecialchars($roomType['name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status"
                                            id="active-<?php echo htmlspecialchars($roomType['id']); ?>" value="0"
                                            <?php echo ($roomType['status'] == 0) ? 'checked' : ''; ?>>
                                        <label class="form-check-label"
                                            for="active-<?php echo htmlspecialchars($roomType['id']); ?>">
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status"
                                            id="pending-<?php echo htmlspecialchars($roomType['id']); ?>" value="1"
                                            <?php echo ($roomType['status'] == 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label"
                                            for="pending-<?php echo htmlspecialchars($roomType['id']); ?>">
                                            Inactive
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Update Room Type</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p class="alert alert-info">No room types found.</p>
    <?php endif; ?>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const roomTypesTable = document.getElementById('roomTypesTable').getElementsByTagName('tbody')[0];
        const tableRows = roomTypesTable.getElementsByTagName('tr');
        const deleteButtons = document.querySelectorAll('.delete-room-type-btn');
        const editButtons = document.querySelectorAll('.edit-room-type-btn');
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
                const roomTypeId = this.dataset.roomTypeId;

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
                        window.location.href = `/admin/room-type/delete/${roomTypeId}`;
                    }
                });
            });
        });

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                // No SweetAlert needed for simply opening the modal
            });
        });
    });
</script>
