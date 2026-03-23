<?php
$username = "root";
$password = ""; // Change this in production
$host = "localhost";
$dbname = "ecommerce";

// Enable exception throwing for mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect($host, $username, $password, $dbname);
    
    // Set charset for security and proper encoding
    mysqli_set_charset($conn, "utf8mb4");
} catch (mysqli_sql_exception $e) {
    // In production, log this error to a file instead of displaying it
    error_log("Database Connection Error: " . $e->getMessage());
    
    // Display a user-friendly message
    http_response_code(500);
    die("We are currently experiencing technical difficulties. Please try again later.");
}
?>