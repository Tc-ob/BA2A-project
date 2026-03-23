<?php
include "connection.php";
$sql = "ALTER TABLE PAYMENT ADD COLUMN status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') NOT NULL DEFAULT 'Pending'";
$conn->query($sql);
if ($conn->errno == 1060) {
    echo "Migration: Column 'status' already exists.";
} else if ($conn->error) {
    echo "Migration error: " . $conn->error;
} else {
    echo "Migration successful: status column added to PAYMENT.";
}
?>
