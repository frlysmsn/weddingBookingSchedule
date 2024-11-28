<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Get statistics
$stats = [
    'total_bookings' => $db->query("SELECT COUNT(*) FROM bookings")->fetchColumn(),
    'pending_bookings' => $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn(),
    'approved_bookings' => $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'approved'")->fetchColumn(),
    'pending_documents' => $db->query("SELECT COUNT(*) FROM documents WHERE status = 'pending'")->fetchColumn()
];

// Get recent bookings
$stmt = $db->query("
    SELECT b.*, u.name as client_name 
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    ORDER BY b.created_at DESC 
    LIMIT 5
");
$recent_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_bookings'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['pending_bookings'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approved Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['approved_bookings'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Pending Documents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['pending_documents'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Wedding Date</th>
                            <th>Status</th>
                            <th>Booked On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_bookings as $booking): ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['client_name']) ?></td>
                                <td><?= date('M d, Y', strtotime($booking['wedding_date'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $booking['status'] === 'approved' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($booking['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y h:i A', strtotime($booking['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($recent_bookings)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No recent bookings</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 4px solid var(--primary-color) !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.text-gray-300 { color: #dddfeb !important; }
.text-gray-800 { color: #5a5c69 !important; }
</style> 