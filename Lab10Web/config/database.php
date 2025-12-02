<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$db_host = 'localhost';
$db_port = '3307';  // Default XAMPP MySQL port is 3306, but using 3307 as per your configuration
$db_name = 'lab9_php_modular';
$db_user = 'root';
$db_pass = '';

// Create connection with specified port
try {
    // Check if MySQL service is running
    if (!@fsockopen('localhost', $db_port, $errno, $errstr, 5)) {
        throw new Exception("MySQL service is not running. Please start MySQL from XAMPP Control Panel.");
    }
    
    // Create connection with port
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error . ". Tried connecting to $db_host:$db_port");
    }
    
    // Set charset to utf8
    if (!$conn->set_charset("utf8")) {
        throw new Exception("Error loading character set utf8: " . $conn->error);
    }
    
} catch (Exception $e) {
    $error_message = "Database connection failed: " . $e->getMessage();
    // Log the error
    error_log($error_message);
    // Display user-friendly error message
    die($error_message . "<br><br>Please check your database configuration and make sure MySQL is running.");
}

// Function to check if database exists
function databaseExists($conn, $dbname) {
    $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
    return $result->num_rows > 0;
}

// Check if database exists, if not create it
if (!databaseExists($conn, $db_name)) {
    try {
        $conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $conn->select_db($db_name);
        
        // Create users table if it doesn't exist
        $create_table = "CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `role` VARCHAR(20) NOT NULL DEFAULT 'user',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if (!$conn->query($create_table)) {
            throw new Exception("Error creating users table: " . $conn->error);
        }
        
        // Add default admin user if no users exist
        $result = $conn->query("SELECT id FROM users LIMIT 1");
        if ($result->num_rows === 0) {
            $hashed_password = password_hash('12345678', PASSWORD_DEFAULT);
            $conn->query("INSERT INTO users (username, password, role) VALUES ('dwiokta', '$hashed_password', 'admin')");
        }
        
    } catch (Exception $e) {
        die("Error setting up database: " . $e->getMessage());
    }
}
?>