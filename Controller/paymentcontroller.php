<?php
session_start();
include "../Database/connection.php";

// Ensure user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: ../Pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $user_id = $_SESSION['id'];
    $payment_method = $_POST['payment_method'] ?? '';

    // Validate payment method
    $allowed_methods = ['Mobile Money', 'Orange Money', 'Bank Account'];
    if (!in_array($payment_method, $allowed_methods)) {
        $_SESSION['payment_error'] = "Invalid payment method selected.";
        header("Location: ../Pages/checkout.php");
        exit();
    }

    // Step 1: Get all items from the user's cart
    $cart_query = "SELECT c.product_id, c.quantity, p.price FROM CART c 
                   JOIN PRODUCTS p ON c.product_id = p.id 
                   WHERE c.user_id = ?";
    $stmt = $conn->prepare($cart_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cart_items = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($cart_items)) {
        $_SESSION['payment_error'] = "Your cart is empty.";
        header("Location: ../Pages/dashboard.php");
        exit();
    }

    // Calculate total price for the entire order
    $total_order_price = 0;
    foreach ($cart_items as $item) {
        $total_order_price += $item['price'] * $item['quantity'];
    }

    // Step 2: Insert each cart item into the PAYMENT table
    $insert_payment = "INSERT INTO PAYMENT (user_id, product_id, quantity, product_total, total_price, payment_method) 
                       VALUES (?, ?, ?, ?, ?, ?)";
    $payment_stmt = $conn->prepare($insert_payment);

    $conn->begin_transaction();

    try {
        foreach ($cart_items as $item) {
            $product_total = $item['price'] * $item['quantity'];
            $payment_stmt->bind_param("iiidds", 
                $user_id, 
                $item['product_id'], 
                $item['quantity'], 
                $product_total, 
                $total_order_price, 
                $payment_method
            );
            $payment_stmt->execute();
        }

        // Step 3: Clear the user's cart
        $clear_cart = "DELETE FROM CART WHERE user_id = ?";
        $clear_stmt = $conn->prepare($clear_cart);
        $clear_stmt->bind_param("i", $user_id);
        $clear_stmt->execute();

        $conn->commit();
        $_SESSION['payment_success'] = "Payment successful! Your order has been placed.";
        header("Location: ../Pages/dashboard.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['payment_error'] = "An error occurred during payment processing: " . $e->getMessage();
        header("Location: ../Pages/checkout.php");
        exit();
    }
} else {
    header("Location: ../Pages/dashboard.php");
    exit();
}
?>
