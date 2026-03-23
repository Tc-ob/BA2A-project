<?php
include "connection.php";

$items = [
    ['name' => 'Fast Charging Charger Kit 20W', 'price' => 12000, 'cat' => 1, 'img' => '1 Set Colorful Fast Charging Charger Kit, Including 20W PD Wall Charger And 3_3ft_100cm Fast Charging & High Speed Data Sync Colorful Braided Cable, Compatible With IPhone 14 Pro Max_14 Pro_14 Plus_14_13_12_11_XS_.jpg'],
    ['name' => 'Gaming Wireless Earphone', 'price' => 45000, 'cat' => 2, 'img' => '1pc Noise Cancelling Gaming Wireless Earphone - Copy.jpg'],
    ['name' => 'Wireless Bluetooth Headphone', 'price' => 35000, 'cat' => 2, 'img' => '1pc Wireless Headphone Compatible With Bluetooth - Copy.jpg'],
    ['name' => 'Apple AirPods Pro 2 (USB-C)', 'price' => 180000, 'cat' => 2, 'img' => 'Apple AirPods Pro 2 (USB‑C) _ ANC, Adaptive Audio & Spatial.jpg'],
    ['name' => 'Vivo V50 Red Rose', 'price' => 210000, 'cat' => 1, 'img' => 'Buy Vivo V50 Red Rose colour at amazing Discounted Deal - Copy.jpg'],
    ['name' => 'Esportivo Earbuds', 'price' => 25000, 'cat' => 2, 'img' => 'Fone De Ouvido Esportivo - Copy - Copy.jpg'],
    ['name' => 'Samsung Galaxy S25', 'price' => 650000, 'cat' => 1, 'img' => 'Samsung S25_ A Smart Blend of AI Innovation and Premium Performance.jpg'],
    ['name' => 'Infinix Note 40 256GB', 'price' => 150000, 'cat' => 1, 'img' => 'Smartphone Infinix Note 40 256GB - 8GB Cam 108MP - Carregamento sem fio - Copy.jpg'],
    ['name' => 'Premium Android Phone', 'price' => 300000, 'cat' => 1, 'img' => 'The Best Android Phones ~ must read since hubby needs a new phone.jpg'],
    ['name' => 'Dual Port Charger', 'price' => 5000, 'cat' => 1, 'img' => 'USB Wall Charger Adapter FiveBox 5Pack 2_1Amp Fast Dual Port Plug Charging Block Charger Brick Cube.jpg'],
    ['name' => 'Xiaomi Redmi Note 11SE', 'price' => 180000, 'cat' => 1, 'img' => 'Xiaomi Redmi Note 11SE - Copy.jpg']
];

foreach ($items as $item) {
    $stmt = $conn->prepare("SELECT id FROM PRODUCTS WHERE image_path = ?");
    $stmt->bind_param("s", $item['img']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows == 0) {
        $ins = $conn->prepare("INSERT INTO PRODUCTS (name, description, price, quantity, image_path) VALUES (?, 'Newly added premium device.', ?, 50, ?)");
        $ins->bind_param("sds", $item['name'], $item['price'], $item['img']);
        $ins->execute();
        echo "Inserted " . $item['name'] . "<br>";
    }
}
echo "Finished importing new product images.<br>";

// Also update the initial default products (1-6) to ensure they have an image
$updates = [
    1 => 'product_headphones.png',
    2 => 'product_smartwatch.png',
    3 => 'product_tablet.png',
    4 => 'product_headphones.png',
    5 => 'hero_flagship.png',
    6 => 'product_headphones.png'
];
foreach($updates as $id => $img) {
    $conn->query("UPDATE PRODUCTS SET image_path = '$img' WHERE id = $id AND (image_path IS NULL OR image_path = '')");
}
?>
