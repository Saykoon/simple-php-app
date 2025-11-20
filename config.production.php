<?php
// Production database configuration
define('DB_HOST', '136.114.93.122:8002');
define('DB_NAME', 'DB_NAME_PLACEHOLDER'); // Will be replaced by GitHub Actions
define('DB_USER', 'stud');
define('DB_PASS', 'Uwb123!!');

// Create database connection
function getConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}
?>