<?php
include "connection.php";

$dir = "../Images/";
$files = scandir($dir);
$inserted = 0;

foreach ($files as $file) {
    if ($file == '.' || $file == '..') continue;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) continue;

    // Check if image already exists in DB
    $stmt = $conn->prepare("SELECT id FROM PRODUCTS WHERE image_path = ?");
    $stmt->bind_param("s", $file);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) continue; // Skip existing

    // Determine category based on filename
    $filename = strtolower($file);
    $cat_id = 1; // Default: Computers & Tablets
    $price = 50000;
    
    if (strpos($filename, 'charg') !== false || strpos($filename, 'station') !== false) {
        $cat_id = 5;
        $price = 15000;
    } elseif (strpos($filename, 'watch') !== false || strpos($filename, 'fitness') !== false || strpos($filename, 'bracelet') !== false) {
        $cat_id = 3;
        $price = 35000;
    } elseif (strpos($filename, 'ear') !== false || strpos($filename, 'headphone') !== false || strpos($filename, 'pod') !== false || strpos($filename, 'audio') !== false || strpos($filename, 'sound') !== false) {
        $cat_id = 2;
        $price = 25000;
    } elseif (strpos($filename, 'phone') !== false || strpos($filename, 'samsung') !== false || strpos($filename, 'vivo') !== false || strpos($filename, 'infinix') !== false || strpos($filename, 'xiaomi') !== false || strpos($filename, 'iphone') !== false || strpos($filename, 'mobile') !== false) {
        $cat_id = 4;
        $price = 150000;
    }

    // Clean up name for display
    $display_name = ucwords(str_replace(['_', '-', '.jpg', '.png', '.jpeg', '.webp'], ' ', $file));
    if (strlen($display_name) > 40) {
        $display_name = substr($display_name, 0, 37) . '...';
    }

    // Insert
    $ins = $conn->prepare("INSERT INTO PRODUCTS (name, description, price, quantity, category_id, image_path) VALUES (?, 'Premium automatically imported product.', ?, 50, ?, ?)");
    $ins->bind_param("sdis", $display_name, $price, $cat_id, $file);
    $ins->execute();
    $inserted++;
}

echo "Successfully scanned Images folder and inserted $inserted new products into their respective categories!";
?>
