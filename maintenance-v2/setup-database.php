<?php
/**
 * MaintenancePro v2.0 - Database Setup Script
 * Initialize database, create tables, and load sample data
 * 
 * IMPORTANT: Run this once to set up the database
 * Access via browser: http://localhost/maintenance-v2/setup-database.php
 */

// Direct database connection (before config)
$host = 'localhost';
$user = 'root';
$pass = 'password';
$port = 3306;

try {
    // Connect to MySQL server (no database yet)
    $pdo = new PDO(
        "mysql:host=$host;port=$port;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "<pre style='font-family: monospace; padding: 20px; background: #f5f5f5;'>";
    echo "🔧 <strong>MaintenancePro v2.0 - Database Setup</strong>\n\n";

    // Read schema file
    $schema_file = dirname(__FILE__) . '/database-schema.sql';
    if (!file_exists($schema_file)) {
        die("❌ Error: database-schema.sql not found!\n");
    }

    $schema = file_get_contents($schema_file);
    
    // Split statements (simple approach)
    $statements = array_filter(
        array_map('trim', preg_split('/;[\r\n]+/', $schema)),
        function($s) { return !empty($s) && !preg_match('/^--/', $s); }
    );

    $success_count = 0;
    $error_count = 0;

    foreach ($statements as $statement) {
        try {
            $pdo->exec($statement);
            $success_count++;
            // Show progress
            if (preg_match('/CREATE\s+(TABLE|DATABASE|VIEW|INDEX)/i', $statement)) {
                $match = [];
                preg_match('/(?:TABLE|DATABASE|VIEW|INDEX)\s+(?:IF\s+NOT\s+EXISTS\s+)?(?:`)?(\w+)/i', $statement, $match);
                if (!empty($match[1])) {
                    echo "✓ Created: " . $match[1] . "\n";
                }
            }
        } catch (Exception $e) {
            $error_count++;
            echo "⚠ Error: " . $e->getMessage() . "\n";
        }
    }

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "✓ Setup Complete!\n";
    echo "  - Tables created: $success_count\n";
    echo "  - Errors: $error_count\n";
    echo "\n";
    echo "📝 Demo Credentials:\n";
    echo "  Username: planner01 | Password: password123\n";
    echo "  Username: engineer01 | Password: password123\n";
    echo "  Username: workman01 | Password: password123\n";
    echo "\n";
    echo "🔗 <a href='login.php'>Go to Login →</a>\n";
    echo str_repeat("=", 50) . "\n";
    echo "</pre>";

} catch (Exception $e) {
    echo "<div style='background: #fee2e2; border: 2px solid #dc2626; padding: 20px; border-radius: 8px; font-family: monospace; color: #991b1b;'>";
    echo "<strong>❌ Database Connection Error:</strong>\n\n";
    echo htmlspecialchars($e->getMessage());
    echo "\n\nPlease check your database credentials in config.php";
    echo "</div>";
}
?>
