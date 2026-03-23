<?php
session_start();
include "../Database/connection.php";

if (isset($_POST["submit"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and password are required.";
        header("Location: ../Pages/login.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT id, name, password, role, profile_color FROM USERS WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile_color'] = $user['profile_color'];
            header("Location: ../Pages/dashboard.php");
            exit();
        }
        else {
            $_SESSION['error'] = "wrong Credentials entered.";
            header("Location: ../Pages/login.php");
            exit();
        }
    }
    else {
        $_SESSION['error'] = "No account found with that email.";
        header("Location: ../Pages/login.php");
        exit();
    }
}
else {
    header("Location: ../Pages/login.php");
    exit();
}
?>
