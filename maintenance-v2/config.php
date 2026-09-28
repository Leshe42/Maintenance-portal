<?php
/**
 * MaintenancePro v2.0 - Configuration & Database Connection
 * Central configuration file for the maintenance management system
 */

// ============================================================================
// DATABASE CONFIGURATION
// ============================================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');  // Change this in production!
define('DB_NAME', 'maintenance_pro');
define('DB_PORT', 3306);

// ============================================================================
// APPLICATION CONFIGURATION
// ============================================================================

define('APP_NAME', 'MaintenancePro');
define('APP_VERSION', '2.0');
define('APP_ENV', 'development');  // development, staging, production
define('APP_DEBUG', APP_ENV === 'development');

// Session configuration
define('SESSION_TIMEOUT', 3600);  // 1 hour
define('SESSION_NAME', 'maintenance_pro_session');

// Paths
define('BASE_PATH', dirname(__FILE__));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('INCLUDE_PATH', BASE_PATH . '/includes');

// Security
define('PASSWORD_ALGORITHM', PASSWORD_BCRYPT);
define('PASSWORD_COST', 10);

// Timezone
date_default_timezone_set('Africa/Lusaka');

// ============================================================================
// DATABASE CONNECTION CLASS
// ============================================================================

class Database {
    private static $instance = null;
    private $connection;
    private $last_error;

    /**
     * Singleton pattern - get database instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor - use getInstance()
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false,
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            if (APP_DEBUG) {
                error_log("Database connected successfully");
            }
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Please check your configuration.");
        }
    }

    /**
     * Get database connection
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Execute query with parameters
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->last_error = $e->getMessage();
            error_log("Query error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch single row
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Fetch all rows
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    /**
     * Get last error
     */
    public function getLastError() {
        return $this->last_error;
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize Database instance");
    }
}

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

/**
 * Get database connection shortcut
 */
function db() {
    return Database::getInstance()->getConnection();
}

/**
 * Hash password for storage
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_ALGORITHM, ['cost' => PASSWORD_COST]);
}

/**
 * Verify password against hash
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Sanitize input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    $db = Database::getInstance();
    $user = $db->fetchOne(
        "SELECT id, username, email, full_name, role FROM users WHERE id = ?",
        [$_SESSION['user_id']]
    );
    return $user;
}

/**
 * Check user role
 */
function hasRole($role) {
    $user = getCurrentUser();
    if (!$user) return false;
    return $user['role'] === $role;
}

/**
 * Check any of multiple roles
 */
function hasAnyRole($roles) {
    $user = getCurrentUser();
    if (!$user) return false;
    return in_array($user['role'], (array)$roles);
}

/**
 * Require authentication
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /login.php");
        exit;
    }
}

/**
 * Require specific role
 */
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header("HTTP/1.0 403 Forbidden");
        die("Access Denied: You do not have permission to access this page.");
    }
}

/**
 * Require any of multiple roles
 */
function requireAnyRole($roles) {
    requireLogin();
    if (!hasAnyRole($roles)) {
        header("HTTP/1.0 403 Forbidden");
        die("Access Denied: You do not have permission to access this page.");
    }
}

/**
 * Log activity
 */
function logActivity($action, $entity_type = null, $entity_id = null, $description = null) {
    if (!isLoggedIn()) return;

    $db = Database::getInstance();
    $user_id = $_SESSION['user_id'];
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $db->query(
        "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent) 
         VALUES (?, ?, ?, ?, ?, ?, ?)",
        [$user_id, $action, $entity_type, $entity_id, $description, $ip, $user_agent]
    );
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'Y-m-d') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Format datetime for display
 */
function formatDateTime($datetime, $format = 'Y-m-d H:i:s') {
    if (empty($datetime)) return '';
    return date($format, strtotime($datetime));
}

/**
 * Get status badge class
 */
function getStatusBadgeClass($status) {
    $classes = [
        'NEW' => 'badge-new',
        'PLANNED' => 'badge-planned',
        'SCHEDULED' => 'badge-scheduled',
        'ASSIGNED' => 'badge-assigned',
        'IN_PROGRESS' => 'badge-in-progress',
        'COMPLETED' => 'badge-completed',
        'VERIFIED' => 'badge-verified',
        'CLOSED' => 'badge-closed',
        'ON_HOLD' => 'badge-new',
        'CANCELLED' => 'badge-closed',
    ];
    return $classes[$status] ?? 'badge';
}

/**
 * Get role display name
 */
function getRoleName($role) {
    $names = [
        'ADMIN' => 'Administrator',
        'PLANNER' => 'Maintenance Planner',
        'ENGINEER' => 'Maintenance Engineer',
        'WORKMAN' => 'Workman',
    ];
    return $names[$role] ?? $role;
}

/**
 * Get role color
 */
function getRoleColor($role) {
    $colors = [
        'ADMIN' => '#ef4444',
        'PLANNER' => '#7c3aed',
        'ENGINEER' => '#0369a1',
        'WORKMAN' => '#047857',
    ];
    return $colors[$role] ?? '#2563eb';
}

/**
 * Get role initials
 */
function getRoleInitials($role) {
    $initials = [
        'ADMIN' => 'AD',
        'PLANNER' => 'MP',
        'ENGINEER' => 'ME',
        'WORKMAN' => 'WM',
    ];
    return $initials[$role] ?? 'US';
}

/**
 * Redirect with message
 */
function redirect($location, $message = null, $type = 'info') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: " . $location);
    exit;
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    $message = $_SESSION['flash_message'] ?? null;
    $type = $_SESSION['flash_type'] ?? 'info';
    
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
    
    if ($message) {
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * Render flash message HTML
 */
function renderFlashMessage() {
    $flash = getFlashMessage();
    if (!$flash) return;

    $class = 'alert-' . $flash['type'];
    echo '<div class="alert ' . htmlspecialchars($class) . '">' . htmlspecialchars($flash['message']) . '</div>';
}

/**
 * Check for schedule conflicts
 */
function checkScheduleConflict($workman_id, $start_time, $end_time, $exclude_id = null) {
    $db = Database::getInstance();

    $sql = "SELECT COUNT(*) as count FROM workman_assignments 
            WHERE assigned_to = ? 
            AND assignment_status IN ('ASSIGNED', 'CONFIRMED')
            AND (
                (scheduled_start < ? AND scheduled_end > ?)
                OR (scheduled_start >= ? AND scheduled_start < ?)
            )";
    
    $params = [$workman_id, $end_time, $start_time, $start_time, $end_time];

    if ($exclude_id) {
        $sql .= " AND id != ?";
        $params[] = $exclude_id;
    }

    $result = $db->fetchOne($sql, $params);
    return $result['count'] > 0;
}

/**
 * Get workman workload summary
 */
function getWorkmanWorkload($workman_id) {
    $db = Database::getInstance();

    return $db->fetchOne(
        "SELECT 
            COUNT(DISTINCT CASE WHEN status = 'ASSIGNED' THEN id END) as assigned_count,
            COUNT(DISTINCT CASE WHEN status = 'IN_PROGRESS' THEN id END) as in_progress_count,
            COUNT(DISTINCT CASE WHEN status = 'COMPLETED' THEN id END) as completed_count,
            SUM(CASE WHEN status IN ('ASSIGNED', 'IN_PROGRESS') THEN TIMESTAMPDIFF(MINUTE, scheduled_start_time, scheduled_end_time) ELSE 0 END) as total_scheduled_minutes
         FROM job_cards 
         WHERE assigned_to = ? AND status NOT IN ('VERIFIED', 'CLOSED')",
        [$workman_id]
    );
}

// ============================================================================
// SESSION CONFIGURATION
// ============================================================================

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
    
    // Check session timeout
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            session_destroy();
            session_start();
            $_SESSION['flash_message'] = 'Session expired. Please log in again.';
            $_SESSION['flash_type'] = 'warning';
            header("Location: /login.php");
            exit;
        }
    }
    $_SESSION['last_activity'] = time();
}

// ============================================================================
// ERROR HANDLING
// ============================================================================

if (APP_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno]: $errstr in $errfile on line $errline");
    if (APP_DEBUG) {
        echo "<div class='alert alert-error'><strong>Error:</strong> $errstr</div>";
    }
});

// ============================================================================
// END OF CONFIG
// ============================================================================
?>
