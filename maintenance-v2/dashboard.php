<?php
/**
 * MaintenancePro v2.0 - Main Dashboard (Role-Based)
 * Routes users to appropriate dashboard based on role
 */

require_once 'config.php';
requireLogin();

$user = getCurrentUser();
$role = $user['role'];

// Get dashboard data based on role
$db = Database::getInstance();

// Common stats
if ($role === 'PLANNER') {
    $stats = [
        ['label' => 'Open Work Orders', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM work_orders WHERE status = 'NEW'")['count'], 'color' => '#2563eb'],
        ['label' => 'Planned', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM work_orders WHERE status = 'PLANNED'")['count'], 'color' => '#7c3aed'],
        ['label' => 'Scheduled', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM work_orders WHERE status = 'SCHEDULED'")['count'], 'color' => '#0369a1'],
        ['label' => 'Unplanned', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM work_orders WHERE is_unplanned = TRUE")['count'], 'color' => '#b45309'],
        ['label' => 'Overdue', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM maintenance_plans WHERE next_due_date < CURDATE() AND is_active = TRUE")['count'], 'color' => '#dc2626'],
        ['label' => 'Completed', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM work_orders WHERE status = 'CLOSED'")['count'], 'color' => '#047857'],
    ];
} elseif ($role === 'ENGINEER') {
    $stats = [
        ['label' => 'Awaiting Assignment', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM work_orders WHERE status = 'PLANNED'")['count'], 'color' => '#dc2626'],
        ['label' => 'Assigned', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM work_orders WHERE status = 'ASSIGNED'")['count'], 'color' => '#7c3aed'],
        ['label' => 'In Progress', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM work_orders WHERE status = 'IN_PROGRESS'")['count'], 'color' => '#ea580c'],
        ['label' => 'Verification', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM work_orders WHERE status = 'COMPLETED'")['count'], 'color' => '#0369a1'],
        ['label' => 'Verified', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM work_orders WHERE status = 'VERIFIED'")['count'], 'color' => '#059669'],
        ['label' => 'Closed', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM work_orders WHERE status = 'CLOSED'")['count'], 'color' => '#6b7280'],
    ];
} else {
    // Workman
    $stats = [
        ['label' => 'Completed', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM job_cards WHERE assigned_to = ? AND status = 'COMPLETED'", [$user['id']])['count'], 'color' => '#047857'],
        ['label' => 'In Progress', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM job_cards WHERE assigned_to = ? AND status = 'IN_PROGRESS'", [$user['id']])['count'], 'color' => '#ea580c'],
        ['label' => 'Assigned', 'value' => $db->fetchOne("SELECT COUNT(*) as count FROM job_cards WHERE assigned_to = ? AND status = 'ASSIGNED'", [$user['id']])['count'], 'color' => '#2563eb'],
        ['label' => 'Completion Rate', 'value' => '94%', 'color' => '#0369a1'],
    ];
}

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MaintenancePro</title>
    <link rel="stylesheet" href="design-system.css">
</head>
<body>
    <div class="layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">MM</div>
                <div>
                    <div class="sidebar-title">MaintenancePro</div>
                    <div class="sidebar-subtitle">Operations</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-label">NAVIGATION</div>
                    <a href="dashboard.php" class="nav-link active">
                        <span class="nav-icon">📊</span>
                        <span>Dashboard</span>
                    </a>
                    <?php if (hasAnyRole(['PLANNER', 'ENGINEER'])): ?>
                    <a href="work-orders.php" class="nav-link">
                        <span class="nav-icon">📋</span>
                        <span>Work Orders</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasRole('PLANNER')): ?>
                    <a href="planning.php" class="nav-link">
                        <span class="nav-icon">📅</span>
                        <span>Planning</span>
                    </a>
                    <?php endif; ?>
                    <?php if (hasRole('WORKMAN')): ?>
                    <a href="my-jobs.php" class="nav-link">
                        <span class="nav-icon">🔧</span>
                        <span>My Jobs</span>
                    </a>
                    <?php endif; ?>
                </div>
            </nav>

            <div class="user-section">
                <div style="margin-bottom: 12px; padding: 0 8px;">
                    <div style="font-size: 11px; color: #475569; font-weight: 600; margin-bottom: 6px;">Active Role</div>
                    <div style="font-size: 13px; color: white; font-weight: 600;"><?php echo getRoleName($user['role']); ?></div>
                </div>
                <div class="user-info">
                    <div class="user-avatar" style="background: <?php echo getRoleColor($user['role']); ?>;"><?php echo getRoleInitials($user['role']); ?></div>
                    <div class="user-details">
                        <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        <div class="user-role"><?php echo substr($user['role'], 0, 1) . strtolower(substr($user['role'], 1)); ?></div>
                    </div>
                    <a href="logout.php" class="btn-logout" title="Logout">→</a>
                </div>
            </div>
        </aside>

        <!-- Main Container -->
        <div class="main-container">
            <!-- Header -->
            <header class="header">
                <h1 style="color: var(--foreground); font-size: 16px; font-weight: 600; margin: 0;">Dashboard</h1>
                <div class="header-actions">
                    <span style="color: var(--muted-foreground); font-size: 12px;"><?php echo date('l, d F Y'); ?></span>
                    <div class="header-user">
                        <div class="header-avatar" style="background: <?php echo getRoleColor($user['role']); ?>;"><?php echo getRoleInitials($user['role']); ?></div>
                        <div class="header-user-info">
                            <div class="header-user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                            <div class="header-user-role"><?php echo getRoleName($user['role']); ?></div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="main">
                <?php if ($flash): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>" style="margin-bottom: 24px;">
                        <?php echo htmlspecialchars($flash['message']); ?>
                    </div>
                <?php endif; ?>

                <div class="page-header">
                    <div>
                        <h2><?php echo getRoleName($user['role']); ?></h2>
                        <p>Welcome back! Here's your current overview.</p>
                    </div>
                </div>

                <!-- Stat Cards -->
                <div class="stats-grid">
                    <?php foreach ($stats as $stat): ?>
                    <div class="stat-card" style="border-left: 3px solid <?php echo $stat['color']; ?>;">
                        <div class="stat-value" style="color: <?php echo $stat['color']; ?>"><?php echo $stat['value']; ?></div>
                        <div class="stat-label"><?php echo $stat['label']; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                            <?php if (hasRole('PLANNER')): ?>
                            <a href="create-work-order.php" class="btn btn-primary">+ Create Work Order</a>
                            <a href="work-orders.php" class="btn btn-secondary">View All Orders</a>
                            <?php elseif (hasRole('ENGINEER')): ?>
                            <a href="work-orders.php?filter=pending" class="btn btn-primary">Assign Jobs</a>
                            <a href="work-orders.php?filter=verification" class="btn btn-secondary">Verify Work</a>
                            <?php else: ?>
                            <a href="my-jobs.php" class="btn btn-primary">My Jobs</a>
                            <a href="my-jobs.php?filter=completed" class="btn btn-secondary">Completed</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- System Info -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">System Information</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 13px;">
                            <div>
                                <div style="font-weight: 600; color: #0f172a; margin-bottom: 4px;">Application</div>
                                <div style="color: #64748b;">MaintenancePro v2.0</div>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #0f172a; margin-bottom: 4px;">Environment</div>
                                <div style="color: #64748b;"><?php echo APP_ENV; ?></div>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #0f172a; margin-bottom: 4px;">Logged In As</div>
                                <div style="color: #64748b;"><?php echo htmlspecialchars($user['full_name']); ?></div>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #0f172a; margin-bottom: 4px;">Login Time</div>
                                <div style="color: #64748b;"><?php echo date('Y-m-d H:i:s'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
