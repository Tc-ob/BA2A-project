<?php
include "connection.php";

$updates = [
    'download (16).jpg' => 'Realme Buds Wireless Earbuds',
    'download (17) - Copy.jpg' => 'JBL Tune 500BT Headphones',
    'download (18).jpg' => 'Rose Gold Smartwatch',
    'download (19).jpg' => 'Classic Black Smartwatch',
    'download (20).jpg' => 'Slim Black Android Tablet',
    'download (21).jpg' => 'Space Grey Android Tablet',
    'download (22).jpg' => 'Lavender Purple Android Tablet',
    'download (23).jpg' => 'Huawei MatePad Pro',
    'download (24).jpg' => 'iPhone 14 Pro (Deep Purple)',
    'download (25).jpg' => 'Sony Wireless Neckband Earphones',
    'download (26).jpg' => 'Multi-functional Power Bank'
];

foreach ($updates as $img => $name) {
    $stmt = $conn->prepare("UPDATE PRODUCTS SET name = ? WHERE image_path = ?");
    $stmt->bind_param("ss", $name, $img);
    $stmt->execute();
}

// Ensure 18, 19, 25 are Wearables (category 3), 16, 17, 25 are Audio (category 2), 20-23 are Computers & Tablets (1), 24 is Phones (4), 26 is Chargers (5).
$conn->query("UPDATE PRODUCTS SET category_id = 2 WHERE image_path LIKE '%download (16)%' OR image_path LIKE '%download (17)%' OR image_path LIKE '%download (25)%'");
$conn->query("UPDATE PRODUCTS SET category_id = 3 WHERE image_path LIKE '%download (18)%' OR image_path LIKE '%download (19)%'");
$conn->query("UPDATE PRODUCTS SET category_id = 1 WHERE image_path LIKE '%download (20)%' OR image_path LIKE '%download (21)%' OR image_path LIKE '%download (22)%' OR image_path LIKE '%download (23)%'");
$conn->query("UPDATE PRODUCTS SET category_id = 4 WHERE image_path LIKE '%download (24)%'");
$conn->query("UPDATE PRODUCTS SET category_id = 5 WHERE image_path LIKE '%download (26)%'");


echo "All downloaded generic product images successfully identified and renamed!";
?>
