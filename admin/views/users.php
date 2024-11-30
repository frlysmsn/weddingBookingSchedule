<?php
// Get clients and admins separately
$stmtClients = $db->query("
    SELECT 
        u.*,
        COALESCE(u.active, 1) as is_active,
        COUNT(b.id) as total_bookings,
        MAX(b.created_at) as last_booking
    FROM users u
    LEFT JOIN bookings b ON u.id = b.user_id
    WHERE u.role = 'client'
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$clients = $stmtClients->fetchAll(PDO::FETCH_ASSOC);

$stmtAdmins = $db->query("
    SELECT 
        u.*,
        COALESCE(u.active, 1) as is_active
    FROM users u
    WHERE u.role = 'admin'
    ORDER BY u.created_at DESC
");
$admins = $stmtAdmins->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <!-- Admins Table -->
    <div class="card shadow mb-4">
        <div class="d-flex justify-content-between align-items-center p-3">
            <h3 class="h4 mb-0">Administrators</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                <i class="fas fa-plus"></i> Add Administrator
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="adminsTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($admins as $admin): ?>
                            <tr>
                                <td><?= htmlspecialchars($admin['name']) ?></td>
                                <td><?= htmlspecialchars($admin['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $admin['is_active'] ? 'success' : 'danger' ?>">
                                        <?= $admin['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($admin['email'] !== 'admin@admin.com'): ?>
                                        <button class="btn btn-sm btn-primary" onclick="viewUserDetails(<?= $admin['id'] ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="toggleUserStatus(<?= $admin['id'] ?>)">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="demoteAdmin(<?= $admin['id'] ?>)">
                                            <i class="fas fa-arrow-down"></i> Demote
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="card shadow">
        <div class="card-header">
            <h3 class="h4 mb-0">Clients</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="clientsTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Total Bookings</th>
                            <th>Last Booking</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($clients as $client): ?>
                            <tr>
                                <td><?= htmlspecialchars($client['name']) ?></td>
                                <td><?= htmlspecialchars($client['email']) ?></td>
                                <td><?= $client['total_bookings'] ?></td>
                                <td>
                                    <?= $client['last_booking'] ? date('M d, Y', strtotime($client['last_booking'])) : 'Never' ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $client['is_active'] ? 'success' : 'danger' ?>">
                                        <?= $client['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewUserDetails(<?= $client['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="toggleUserStatus(<?= $client['id'] ?>)">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $client['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Administrator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAdminForm" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Administrator</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>

function viewUserDetails(userId) {
    $.get('ajax/view-user-details.php', { user_id: userId })
        .done(function(response) {
            Swal.fire({
                title: 'User Details',
                html: response,
                width: '600px'
            });
        });
}

function toggleUserStatus(userId) {
    Swal.fire({
        title: 'Confirm Status Change',
        text: 'Are you sure you want to change this user\'s status?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'No, cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('ajax/toggle-user-status.php', { user_id: userId })
                .done(function(response) {
                    Swal.fire('Updated!', 'User status has been changed.', 'success')
                    .then(() => location.reload());
                })
                .fail(function() {
                    Swal.fire('Error!', 'Failed to update user status.', 'error');
                });
        }
    });
}


function deleteUser(userId) {
    Swal.fire({
        title: 'Delete User',
        text: 'Are you sure you want to delete this user? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('ajax/delete-user.php', { user_id: userId })
                .done(function(response) {
                    Swal.fire('Deleted!', 'User has been deleted.', 'success')
                    .then(() => location.reload());
                })
                .fail(function() {
                    Swal.fire('Error!', 'Failed to delete user.', 'error');
                });
        }
    });
}
$(document).ready(function() {
    $('#addAdminForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
        
        $.ajax({
            url: 'ajax/add_admin.php', // URL to send the request to
            type: 'POST', // Use POST method
            data: $(this).serialize(), // Serialize the form data
            success: function(response) {
                response = JSON.parse(response); // Parse the JSON response
                if (response.success) {
                    Swal.fire(
                        'Success!',
                        'Administrator added successfully.',
                        'success'
                    ).then(() => {
                        $('#addAdminModal').modal('hide'); // Hide the modal
                        location.reload(); // Reload the page to show the new admin
                    });
                } else {
                    Swal.fire(
                        'Error!',
                        response.message,
                        'error'
                    );
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                console.error("AJAX Error: ", status, error);
                Swal.fire(
                    'Error!',
                    'An error occurred while adding the administrator.',
                    'error'
                );
            }
        });
    });
});

</script> 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.all.min.js"></script>
