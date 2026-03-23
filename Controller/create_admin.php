<?php
include "../Database/connection.php";

$admin_name = "Admin User";
$admin_email = "admin@auroratech.com";
$admin_password = "AdminPassword123";
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
$role = "admin";

// Check if admin already exists
$check = $conn->prepare("SELECT id FROM USERS WHERE email = ?");
$check->bind_param("s", $admin_email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Update existing user to admin
    $update = $conn->prepare("UPDATE USERS SET role = ? WHERE email = ?");
    $update->bind_param("ss", $role, $admin_email);
    if ($update->execute()) {
        echo "Successfully promoted $admin_email to Administrator.\n";
    } else {
        echo "Error promoting user: " . $conn->error . "\n";
    }
} else {
    // Create new admin
    $insert = $conn->prepare("INSERT INTO USERS (name, email, password, role) VALUES (?, ?, ?, ?)");
    $insert->bind_param("ssss", $admin_name, $admin_email, $hashed_password, $role);
    if ($insert->execute()) {
        echo "Successfully created Admin account:\n";
        echo "Email: $admin_email\n";
        echo "Password: $admin_password\n";
    } else {
        echo "Error creating admin: " . $conn->error . "\n";
    }
}
?>
