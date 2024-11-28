<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Get all users except admins
$stmt = $db->query("
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
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Manage Users</h2>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
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
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= $user['total_bookings'] ?></td>
                                <td>
                                    <?= $user['last_booking'] ? date('M d, Y', strtotime($user['last_booking'])) : 'Never' ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $user['is_active'] ? 'success' : 'danger' ?>">
                                        <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" 
                                            onclick="viewUserDetails(<?= $user['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-<?= $user['is_active'] ? 'warning' : 'success' ?>" 
                                            onclick="toggleUserStatus(<?= $user['id'] ?>)">
                                        <i class="fas fa-<?= $user['is_active'] ? 'ban' : 'check' ?>"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info" 
                                            onclick="resetPassword(<?= $user['id'] ?>)">
                                        <i class="fas fa-key"></i>
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

<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 10,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search users..."
        }
    });
});

function viewUserDetails(userId) {
    $.get('../api/get-user-details.php', { user_id: userId })
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
            $.post('../api/toggle-user-status.php', { user_id: userId })
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

function resetPassword(userId) {
    Swal.fire({
        title: 'Reset Password',
        text: 'Are you sure you want to reset this user\'s password?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, reset it!',
        cancelButtonText: 'No, cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../api/reset-user-password.php', { user_id: userId })
                .done(function(response) {
                    Swal.fire('Reset!', 'Password has been reset.', 'success');
                })
                .fail(function() {
                    Swal.fire('Error!', 'Failed to reset password.', 'error');
                });
        }
    });
}
</script> 