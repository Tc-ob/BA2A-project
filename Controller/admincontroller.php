<?php
session_start();
include "../Database/connection.php";

// Access Control: Only Admins can manage products
if (!isset($_SESSION['id']) || strtolower($_SESSION['role']) !== 'admin') {
    $_SESSION['payment_error'] = "Unauthorized access.";
    header("Location: ../Pages/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $category_id = (int)$_POST['category_id'];
    $image_path = mysqli_real_escape_string($conn, $_POST['image_path']);

    if (empty($name) || empty($price)) {
        $_SESSION['payment_error'] = "Product name and price are required.";
        header("Location: ../Pages/dashboard.php");
        exit();
    }

    $insert_query = "INSERT INTO PRODUCTS (name, description, price, quantity, category_id, image_path) 
                     VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssdiis", $name, $description, $price, $quantity, $category_id, $image_path);

    if ($stmt->execute()) {
        $_SESSION['payment_success'] = "Product '$name' added successfully!";
    } else {
        $_SESSION['payment_error'] = "Failed to add product: " . $conn->error;
    }

    header("Location: ../Pages/dashboard.php");
    exit();
}

header("Location: ../Pages/dashboard.php");
exit();
?>
