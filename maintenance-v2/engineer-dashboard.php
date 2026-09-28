<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Engineer') {
    header('Location: login.php');
    exit;
}

$user = $_SESSION;

$stats = [
    'awaiting' => 1,
    'assigned' => 2,
    'in_progress' => 1,
    'verification' => 1,
    'verified' => 18,
    'closed' => 35,
];

$jobsAwaiting = [
    ['id' => 1, 'wo' => 'WO-2026-002', 'asset' => 'Motor B-03', 'date' => '2026-09-26', 'time' => '14:00'],
];

$assignedJobs = [
    ['id' => 2, 'wo' => 'WO-2026-004', 'asset' => 'Compressor A-02', 'workman' => 'Peter Workman', 'date' => '2026-09-25', 'status' => 'ASSIGNED'],
    ['id' => 3, 'wo' => 'WO-2026-005', 'asset' => 'Backup Generator', 'workman' => 'Paul Mulenga', 'date' => '2026-09-24', 'status' => 'IN_PROGRESS'],
];

$verificationQueue = [
    ['id' => 5, 'wo' => 'WO-2026-005', 'asset' => 'Backup Generator', 'workman' => 'Paul Mulenga', 'completed' => '2026-09-24 10:30'],
];

$workmen = [
    ['id' => 1, 'name' => 'Peter Workman', 'jobs' => 2, 'completed' => 18],
    ['id' => 2, 'name' => 'Paul Mulenga', 'jobs' => 1, 'completed' => 6],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Engineer - Dashboard</title>
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
                    <div class="nav-label">Engineer</div>
                    <a href="#" class="nav-link active">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                    <a href="#" class="nav-link">
                        <span class="nav-icon">👷</span>
                        Assign Workman
                    </a>
                    <a href="#" class="nav-link">
                        <span class="nav-icon">✓</span>
                        Verify Work
                    </a>
                    <a href="#" class="nav-link">
                        <span class="nav-icon">📋</span>
                        Work Orders
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-label">Overview</div>
                    <a href="#" class="nav-link">
                        <span class="nav-icon">👥</span>
                        Team Performance
                    </a>
                    <a href="#" class="nav-link">
                        <span class="nav-icon">📈</span>
                        Analytics
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
                    <div class="user-avatar">ME</div>
                    <div class="user-details">
                        <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        <div class="user-role">Engineer</div>
                    </div>
                    <button class="btn-logout" title="Logout">→</button>
                </div>
            </div>
        </aside>

        <!-- Header -->
        <header class="header">
            <div class="header-content">
                <div class="header-title">
                    <h1>👷 Maintenance Engineer</h1>
                    <p>Assign work, monitor progress, verify completion</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary">
                        <span>👤</span>
                        <span>Assign Workman</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main">
            <!-- Summary Cards -->
            <div class="cards-grid">
                <div class="card">
                    <div class="card-icon">🔴</div>
                    <div class="card-label">Awaiting Assignment</div>
                    <div class="card-value"><?php echo $stats['awaiting']; ?></div>
                    <div class="card-change">Urgent action needed</div>
                </div>
                <div class="card">
                    <div class="card-icon">👤</div>
                    <div class="card-label">Assigned</div>
                    <div class="card-value"><?php echo $stats['assigned']; ?></div>
                    <div class="card-change">Waiting to start</div>
                </div>
                <div class="card">
                    <div class="card-icon">🔧</div>
                    <div class="card-label">In Progress</div>
                    <div class="card-value"><?php echo $stats['in_progress']; ?></div>
                    <div class="card-change">Being worked on</div>
                </div>
                <div class="card">
                    <div class="card-icon">⭐</div>
                    <div class="card-label">Verification Pending</div>
                    <div class="card-value"><?php echo $stats['verification']; ?></div>
                    <div class="card-change">Needs review</div>
                </div>
                <div class="card">
                    <div class="card-icon">✓</div>
                    <div class="card-label">Verified</div>
                    <div class="card-value"><?php echo $stats['verified']; ?></div>
                    <div class="card-change">Approved work</div>
                </div>
                <div class="card">
                    <div class="card-icon">📦</div>
                    <div class="card-label">Closed</div>
                    <div class="card-value"><?php echo $stats['closed']; ?></div>
                    <div class="card-change">Archived</div>
                </div>
            </div>

            <!-- Jobs Awaiting Assignment -->
            <?php if (count($jobsAwaiting) > 0): ?>
            <div class="section">
                <div class="section-header">
                    <div class="section-title">
                        🔴 Jobs Awaiting Workman Assignment
                    </div>
                    <div class="section-badge"><?php echo count($jobsAwaiting); ?> Urgent</div>
                </div>

                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Work Order</th>
                                <th>Asset</th>
                                <th>Scheduled Date</th>
                                <th>Start Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobsAwaiting as $job): ?>
                            <tr>
                                <td><strong><?php echo $job['wo']; ?></strong></td>
                                <td><?php echo $job['asset']; ?></td>
                                <td><?php echo $job['date']; ?></td>
                                <td><?php echo $job['time']; ?></td>
                                <td>
                                    <button class="btn btn-primary btn-small">ASSIGN ✓</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Assigned & In Progress -->
            <div class="section">
                <div class="section-header">
                    <div class="section-title">
                        👥 Assigned & In Progress Jobs
                    </div>
                    <div class="section-badge"><?php echo count($assignedJobs); ?> Active</div>
                </div>

                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Work Order</th>
                                <th>Asset</th>
                                <th>Assigned To</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assignedJobs as $job): ?>
                            <tr>
                                <td><strong><?php echo $job['wo']; ?></strong></td>
                                <td><?php echo $job['asset']; ?></td>
                                <td><?php echo $job['workman']; ?></td>
                                <td><?php echo $job['date']; ?></td>
                                <td><span class="badge badge-<?php echo strtolower($job['status']); ?>"><?php echo $job['status']; ?></span></td>
                                <td>
                                    <button class="btn btn-secondary btn-small">View</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Verification Queue -->
            <?php if (count($verificationQueue) > 0): ?>
            <div class="section">
                <div class="section-header">
                    <div class="section-title">
                        ⭐ Jobs Awaiting Verification
                    </div>
                    <div class="section-badge"><?php echo count($verificationQueue); ?> To Review</div>
                </div>

                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Work Order</th>
                                <th>Asset</th>
                                <th>Workman</th>
                                <th>Completed</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($verificationQueue as $job): ?>
                            <tr>
                                <td><strong><?php echo $job['wo']; ?></strong></td>
                                <td><?php echo $job['asset']; ?></td>
                                <td><?php echo $job['workman']; ?></td>
                                <td><?php echo $job['completed']; ?></td>
                                <td>
                                    <button class="btn btn-primary btn-small">VERIFY ✓</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Team Performance -->
            <div class="section">
                <div class="section-header">
                    <div class="section-title">👥 Team Workload</div>
                </div>

                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Workman</th>
                                <th>Current Jobs</th>
                                <th>Completed</th>
                                <th>Efficiency</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($workmen as $w): ?>
                            <tr>
                                <td><strong><?php echo $w['name']; ?></strong></td>
                                <td>
                                    <span class="badge" style="background: #fef3c7; color: #92400e;">
                                        <?php echo $w['jobs']; ?> active
                                    </span>
                                </td>
                                <td><?php echo $w['completed']; ?> jobs</td>
                                <td>
                                    <div style="width: 100%; height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden;">
                                        <div style="width: <?php echo ($w['completed'] / 20) * 100; ?>%; height: 100%; background: #059669;"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; 2026 Maintenance Management Module v2.0 • Engineer Dashboard</p>
        </footer>
    </div>
</body>
</html>
