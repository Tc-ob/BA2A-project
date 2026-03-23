<?php
session_start();
include "../Database/connection.php";

// Must be logged in
if (!isset($_SESSION['id'])) {
    header("Location: ../Pages/login.php");
    exit();
}

$user_id = $_SESSION['id'];
$action  = $_POST['action'] ?? '';

// --- ADD TO CART ---
if ($action === 'add') {
    $product_id = (int)$_POST['product_id'];
    // $product_price = (float)$_POST['product_price'];

    // Insert or increment quantity if same product already in cart
    $sql = "INSERT INTO CART (user_id, product_id, quantity)
            VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE quantity = quantity + 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();

    $_SESSION['cart_success'] = "Product added to cart!";
    header("Location: ../Pages/products.php");
    exit();
}

// --- REMOVE FROM CART ---
if ($action === 'remove') {
    $cart_id = (int)$_POST['cart_id'];

    $sql = "DELETE FROM CART WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();

    header("Location: ../Pages/dashboard.php");
    exit();
}

// --- UPDATE QUANTITY ---
if ($action === 'update') {
    $cart_id  = (int)$_POST['cart_id'];
    $quantity = max(1, (int)$_POST['quantity']);

    $sql = "UPDATE CART SET quantity = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
    $stmt->execute();

    header("Location: ../Pages/dashboard.php");
    exit();
}

// Default fallback
header("Location: ../Pages/products.php");
exit();
?>
