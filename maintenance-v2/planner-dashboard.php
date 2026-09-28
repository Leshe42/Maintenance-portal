<?php
// Demo mode - simulated data
session_start();

// Check if logged in (demo)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Planner') {
    header('Location: login.php');
    exit;
}

$user = $_SESSION;

// Simulated data
$stats = [
    'open' => 3,
    'planned' => 2,
    'scheduled' => 3,
    'in_progress' => 1,
    'completed' => 12,
    'closed' => 35,
];

$workOrders = [
    ['id' => 1, 'wo' => 'WO-2026-001', 'asset' => 'Pump A-01', 'type' => 'Preventive', 'status' => 'PLANNED', 'date' => '2026-09-25', 'time' => '08:00-10:00'],
    ['id' => 2, 'wo' => 'WO-2026-002', 'asset' => 'Motor B-03', 'type' => 'Preventive', 'status' => 'SCHEDULED', 'date' => '2026-09-26', 'time' => '14:00-16:00'],
    ['id' => 3, 'wo' => 'WO-2026-003', 'asset' => 'Conveyor C', 'type' => 'Corrective', 'status' => 'NEW', 'date' => '2026-09-27', 'time' => '09:00-12:00'],
    ['id' => 4, 'wo' => 'WO-2026-004', 'asset' => 'Compressor A-02', 'type' => 'Preventive', 'status' => 'ASSIGNED', 'date' => '2026-09-25', 'time' => 'Assigned'],
    ['id' => 5, 'wo' => 'WO-2026-005', 'asset' => 'Generator', 'type' => 'Preventive', 'status' => 'IN_PROGRESS', 'date' => '2026-09-24', 'time' => 'In Progress'],
];

$upcomingMaintenance = [
    ['asset' => 'Pump A-01', 'plan' => 'Pump A Monthly Service', 'type' => 'Preventive', 'due' => '2026-09-24', 'interval' => '30 days'],
    ['asset' => 'Motor B-03', 'plan' => 'Motor B Quarterly Check', 'type' => 'Preventive', 'due' => '2026-09-24', 'interval' => '90 days'],
    ['asset' => 'Backup Generator', 'plan' => 'Generator Fuel Service', 'type' => 'Preventive', 'due' => '2026-09-28', 'interval' => '30 days'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Planner - Dashboard</title>
    <link rel="stylesheet" href="modern-styles.css">
</head>
<body>
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">⚙️</div>
                <div>
                    <div class="sidebar-title">Maintenance</div>
                    <div class="sidebar-subtitle">Module v2.0</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-label">Planner</div>
                    <a href="#" class="nav-link active">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                    <a href="#" class="nav-link">
                        <span class="nav-icon">➕</span>
                        Create Work Order
                    </a>
                    <a href="#" class="nav-link">
                        <span class="nav-icon">📋</span>
                        Maintenance Plans
                    </a>
                    <a href="#" class="nav-link">
                        <span class="nav-icon">📅</span>
                        Calendar
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-label">Overview</div>
                    <a href="#" class="nav-link">
                        <span class="nav-icon">🏢</span>
                        Assets
                    </a>
                    <a href="#" class="nav-link">
                        <span class="nav-icon">👥</span>
                        Team
                    </a>
                    <a href="#" class="nav-link">
                        <span class="nav-icon">💰</span>
                        Budget
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-label">Other</div>
                    <a href="#" class="nav-link">
                        <span class="nav-icon">⚙️</span>
                        Settings
                    </a>
                    <a href="#" class="nav-link">
                        <span class="nav-icon">❓</span>
                        Help
                    </a>
                </div>
            </nav>

            <div class="user-section">
                <div class="user-info">
                    <div class="user-avatar">JP</div>
                    <div class="user-details">
                        <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        <div class="user-role">Planner</div>
                    </div>
                    <button class="btn-logout" title="Logout">→</button>
                </div>
            </div>
        </aside>

        <!-- Header -->
        <header class="header">
            <div class="header-content">
                <div class="header-title">
                    <h1>📋 Maintenance Planner</h1>
                    <p>Plan, schedule, and track maintenance work</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary">
                        <span>➕</span>
                        <span>New Work Order</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main">
            <!-- Summary Cards -->
            <div class="cards-grid">
                <div class="card">
                    <div class="card-icon">📊</div>
                    <div class="card-label">Open Orders</div>
                    <div class="card-value"><?php echo $stats['open']; ?></div>
                    <div class="card-change">↑ 2 this week</div>
                </div>
                <div class="card">
                    <div class="card-icon">🎯</div>
                    <div class="card-label">Planned</div>
                    <div class="card-value"><?php echo $stats['planned']; ?></div>
                    <div class="card-change">Ready for scheduling</div>
                </div>
                <div class="card">
                    <div class="card-icon">📅</div>
                    <div class="card-label">Scheduled</div>
                    <div class="card-value"><?php echo $stats['scheduled']; ?></div>
                    <div class="card-change">Next 30 days</div>
                </div>
                <div class="card">
                    <div class="card-icon">🔧</div>
                    <div class="card-label">In Progress</div>
                    <div class="card-value"><?php echo $stats['in_progress']; ?></div>
                    <div class="card-change down">Awaiting completion</div>
                </div>
                <div class="card">
                    <div class="card-icon">✓</div>
                    <div class="card-label">Completed</div>
                    <div class="card-value"><?php echo $stats['completed']; ?></div>
                    <div class="card-change">This month</div>
                </div>
                <div class="card">
                    <div class="card-icon">📦</div>
                    <div class="card-label">Closed</div>
                    <div class="card-value"><?php echo $stats['closed']; ?></div>
                    <div class="card-change">Total archived</div>
                </div>
            </div>

            <!-- Work Orders Section -->
            <div class="section">
                <div class="section-header">
                    <div class="section-title">
                        🔴 Work Orders Requiring Attention
                    </div>
                    <div class="section-badge"><?php echo count(array_filter($workOrders, function($wo) { return $wo['status'] === 'NEW'; })); ?> New</div>
                </div>

                <?php 
                $newOrders = array_filter($workOrders, function($wo) { return $wo['status'] === 'NEW'; });
                if (count($newOrders) > 0):
                ?>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Work Order</th>
                                <th>Asset</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($newOrders as $wo): ?>
                            <tr>
                                <td><strong><?php echo $wo['wo']; ?></strong></td>
                                <td><?php echo $wo['asset']; ?></td>
                                <td><?php echo $wo['type']; ?></td>
                                <td><span class="badge badge-new">🆕 NEW</span></td>
                                <td>
                                    <button class="btn btn-primary btn-small">Plan Work</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">✓</div>
                    <div class="empty-state-title">All caught up!</div>
                    <div class="empty-state-text">No new work orders requiring planning</div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Scheduled Maintenance -->
            <div class="section">
                <div class="section-header">
                    <div class="section-title">
                        📅 Scheduled Maintenance (Next 7 Days)
                    </div>
                    <div class="section-badge">3 Scheduled</div>
                </div>

                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Asset</th>
                                <th>Work Order</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($workOrders, 0, 3) as $wo): ?>
                            <tr>
                                <td><?php echo $wo['date']; ?></td>
                                <td><?php echo $wo['time']; ?></td>
                                <td><?php echo $wo['asset']; ?></td>
                                <td><strong><?php echo $wo['wo']; ?></strong></td>
                                <td><span class="badge badge-<?php echo strtolower($wo['status']); ?>"><?php echo $wo['status']; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Upcoming Maintenance from Plans -->
            <div class="section">
                <div class="section-header">
                    <div class="section-title">
                        ⚠️ Assets Requiring Upcoming Maintenance
                    </div>
                    <div class="section-badge"><?php echo count($upcomingMaintenance); ?> Due Soon</div>
                </div>

                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Maintenance Plan</th>
                                <th>Type</th>
                                <th>Next Due</th>
                                <th>Interval</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcomingMaintenance as $plan): ?>
                            <tr>
                                <td><strong><?php echo $plan['asset']; ?></strong></td>
                                <td><?php echo $plan['plan']; ?></td>
                                <td><?php echo $plan['type']; ?></td>
                                <td><?php echo $plan['due']; ?></td>
                                <td><?php echo $plan['interval']; ?></td>
                                <td>
                                    <button class="btn btn-secondary btn-small">Create WO</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="section">
                <div class="section-header">
                    <div class="section-title">📝 Recent Work Orders</div>
                </div>

                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Work Order</th>
                                <th>Asset</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($workOrders as $wo): ?>
                            <tr>
                                <td><strong><?php echo $wo['wo']; ?></strong></td>
                                <td><?php echo $wo['asset']; ?></td>
                                <td><?php echo $wo['type']; ?></td>
                                <td><span class="badge badge-<?php echo strtolower($wo['status']); ?>"><?php echo $wo['status']; ?></span></td>
                                <td><?php echo $wo['date']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; 2026 Maintenance Management Module v2.0 • Planner Dashboard</p>
        </footer>
    </div>
</body>
</html>
