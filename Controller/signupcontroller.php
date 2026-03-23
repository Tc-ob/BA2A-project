<?php
session_start();
// Use correct casing for the directory and file
include "../Database/connection.php";

// Use "signup-submit" because that is the name of the button in your signup.php form
if (isset($_POST["signup-submit"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $terms = isset($_POST["terms"]) ? $_POST["terms"] : null;

    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../Pages/signup.php");
        exit();
    }

    // CREATE TABLE Query without the trailing comma
    $sqlcreatetable = "CREATE TABLE IF NOT EXISTS USERS(
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(255) NOT NULL DEFAULT 'Client',
        profile_color VARCHAR(255) DEFAULT 'linear-gradient(135deg, #f43f5e, #fb7185)'
    )";

    if (mysqli_query($conn, $sqlcreatetable)) {
        // Table created successfully
    }
    else {
        $_SESSION['error'] = "Database error: Failed to create table users.";
        header("Location: ../Pages/signup.php");
        exit();
    }

    // Check if the user's email already exists to prevent duplicates
    $check_email = $conn->prepare("SELECT id FROM USERS WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email is already registered.";
        header("Location: ../Pages/signup.php");
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // INSERT the user securely inside the submit block (removed $class)
    $sqlcreateusers = $conn->prepare("INSERT INTO USERS(name, email, password) VALUES (?, ?, ?)");
    $sqlcreateusers->bind_param("sss", $name, $email, $hashed_password);

    if ($sqlcreateusers->execute()) {
        $_SESSION['success'] = "Account created successfully! Please log in.";
        header("Location: ../Pages/login.php");
        exit();
    }
    else {
        $_SESSION['error'] = "Failed to register account: " . mysqli_error($conn);
        header("Location: ../Pages/signup.php");
        exit();
    }
}
else {
    // Redirect back if the page was accessed without submitting the form
    header("Location: ../Pages/signup.php");
    exit();
}
?>
