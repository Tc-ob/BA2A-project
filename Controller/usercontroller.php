<?php
session_start();
include "../Database/connection.php";

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['id'];

if (isset($_POST['action']) && $_POST['action'] === 'update_color') {
    $color = $_POST['color'];
    
    // Validate color (ensure it's one of our premium gradients or a valid CSS color)
    $stmt = $conn->prepare("UPDATE USERS SET profile_color = ? WHERE id = ?");
    $stmt->bind_param("si", $color, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['profile_color'] = $color;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
    exit();
}
?>
