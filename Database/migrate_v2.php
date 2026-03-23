<?php
include "connection.php";

$sql = "ALTER TABLE USERS ADD COLUMN profile_color VARCHAR(255) DEFAULT 'linear-gradient(135deg, #f43f5e, #fb7185)'";

if ($conn->query($sql)) {
    echo "Migration successful: profile_color column added.";
} else {
    echo "Migration error or column already exists: " . $conn->error;
}
?>
