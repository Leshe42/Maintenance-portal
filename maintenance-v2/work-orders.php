<?php
/**
 * MaintenancePro v2.0 - Work Orders List
 * View all work orders with role-based filtering
 */

require_once 'config.php';
requireAnyRole(['PLANNER', 'ENGINEER']);

$db = Database::getInstance();
$user = getCurrentUser();

// Filter parameter
$filter = sanitize($_GET['filter'] ?? 'all');
$status_filter = sanitize($_GET['status'] ?? '');

// Build query based on role and filter
$sql = "SELECT * FROM v_work_orders_summary WHERE 1=1";
$params = [];

if ($user['role'] === 'PLANNER') {
    // Planner sees all
    if ($filter === 'pending') {
        $sql .= " AND status IN ('NEW', 'PLANNED')";
    } elseif ($filter === 'scheduled') {
        $sql .= " AND status = 'SCHEDULED'";
    }
} elseif ($user['role'] === 'ENGINEER') {
    // Engineer sees unplanned and their assigned work
    if ($filter === 'pending') {
        $sql .= " AND status = 'PLANNED'";
    } elseif ($filter === 'verification') {
        $sql .= " AND status = 'COMPLETED'";
    }
}

$sql .= " ORDER BY scheduled_date DESC LIMIT 100";

try {
    $work_orders = $db->fetchAll($sql, $params);
} catch (Exception $e) {
    $work_orders = [];
    error_log("Error fetching work orders: " . $e->getMessage());
}

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Orders - MaintenancePro</title>
    <link rel="stylesheet" href="design-system.css">
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">MM</div>
                <div><div class="sidebar-title">MaintenancePro</div></div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <a href="dashboard.php" class="nav-link"><span class="nav-icon">📊</span><span>Dashboard</span></a>
                    <a href="work-orders.php" class="nav-link active"><span class="nav-icon">📋</span><span>Work Orders</span></a>
                </div>
            </nav>
            <div class="user-section">
                <div class="user-info">
                    <div class="user-avatar" style="background: <?php echo getRoleColor($user['role']); ?>;"><?php echo getRoleInitials($user['role']); ?></div>
                    <div class="user-details">
                        <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        <div class="user-role"><?php echo getRoleName($user['role']); ?></div>
                    </div>
                    <a href="logout.php" class="btn-logout">→</a>
                </div>
            </div>
        </aside>

        <div class="main-container">
            <header class="header">
                <h1 style="color: var(--foreground); font-size: 16px; font-weight: 600; margin: 0;">Work Orders</h1>
                <div class="header-actions">
                    <div class="header-user">
                        <div class="header-avatar" style="background: <?php echo getRoleColor($user['role']); ?>;"><?php echo getRoleInitials($user['role']); ?></div>
                        <div class="header-user-info">
                            <div class="header-user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="main">
                <?php if ($flash): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>" style="margin-bottom: 24px;">
                        <?php echo htmlspecialchars($flash['message']); ?>
                    </div>
                <?php endif; ?>

                <div class="page-header">
                    <div>
                        <h2>Work Orders</h2>
                        <p>Manage maintenance work orders</p>
                    </div>
                    <?php if ($user['role'] === 'PLANNER'): ?>
                    <div class="page-header-actions">
                        <a href="create-work-order.php" class="btn btn-primary">+ Create Work Order</a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Filters -->
                <div style="margin-bottom: 24px; display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="?filter=all" class="btn <?php echo ($filter === 'all') ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
                    <a href="?filter=pending" class="btn <?php echo ($filter === 'pending') ? 'btn-primary' : 'btn-secondary'; ?>">Pending</a>
                    <?php if ($user['role'] === 'PLANNER'): ?>
                    <a href="?filter=scheduled" class="btn <?php echo ($filter === 'scheduled') ? 'btn-primary' : 'btn-secondary'; ?>">Scheduled</a>
                    <?php elseif ($user['role'] === 'ENGINEER'): ?>
                    <a href="?filter=verification" class="btn <?php echo ($filter === 'verification') ? 'btn-primary' : 'btn-secondary'; ?>">Verification</a>
                    <?php endif; ?>
                </div>

                <!-- Work Orders Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Work Orders</h3>
                        <span class="card-badge"><?php echo count($work_orders); ?> Total</span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($work_orders)): ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">📭</div>
                                <div class="empty-state-title">No Work Orders Found</div>
                                <p>There are currently no work orders to display.</p>
                            </div>
                        <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Work Order</th>
                                        <th>Asset</th>
                                        <th>Activity</th>
                                        <th>Scheduled Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($work_orders as $wo): ?>
                                    <tr>
                                        <td class="table-mono"><?php echo htmlspecialchars($wo['work_order_number']); ?></td>
                                        <td style="font-weight: 500;"><?php echo htmlspecialchars($wo['asset_name']); ?></td>
                                        <td style="color: var(--muted-foreground);"><?php echo htmlspecialchars($wo['asset_code'] ?? ''); ?></td>
                                        <td><?php echo formatDate($wo['scheduled_date']); ?></td>
                                        <td class="table-mono"><?php echo $wo['start_time'] ?? '—'; ?></td>
                                        <td><span class="badge <?php echo getStatusBadgeClass($wo['status']); ?>"><?php echo $wo['status']; ?></span></td>
                                        <td>
                                            <a href="view-work-order.php?id=<?php echo $wo['id']; ?>" class="btn btn-secondary btn-small">View</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
