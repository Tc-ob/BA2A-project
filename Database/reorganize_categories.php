<?php
include "connection.php";

// Insert new categories 
$conn->query("INSERT IGNORE INTO CATEGORIES (id, name, description) VALUES (4, 'Phones', 'The latest premium smartphones.')");
$conn->query("INSERT IGNORE INTO CATEGORIES (id, name, description) VALUES (5, 'Chargers & Accessories', 'Power up your devices efficiently.')");

// Update existing categories
$conn->query("UPDATE CATEGORIES SET name='Computers & Tablets', description='High-end computing devices and tablets.' WHERE id=1");
$conn->query("UPDATE CATEGORIES SET name='Audio', description='Premium sound and acoustics.' WHERE id=2");
$conn->query("UPDATE CATEGORIES SET name='Wearables', description='Executive timepieces and smart accessories.' WHERE id=3");

// Re-map all products accurately
$conn->query("UPDATE PRODUCTS SET category_id = 4 WHERE name LIKE '%Vivo%' OR name LIKE '%Samsung%' OR name LIKE '%Infinix%' OR name LIKE '%Android Phone%' OR name LIKE '%Xiaomi%'");
$conn->query("UPDATE PRODUCTS SET category_id = 5 WHERE name LIKE '%Charger%' OR name LIKE '%Charging%'");
$conn->query("UPDATE PRODUCTS SET category_id = 2 WHERE name LIKE '%Aura Sound%' OR name LIKE '%Earphone%' OR name LIKE '%Bluetooth%' OR name LIKE '%AirPods%' OR name LIKE '%Earbuds%' OR name LIKE '%Ouvido%' OR name LIKE '%Nova%'");
$conn->query("UPDATE PRODUCTS SET category_id = 3 WHERE name LIKE '%Chrono%' OR name LIKE '%Smart Watch%'");
$conn->query("UPDATE PRODUCTS SET category_id = 1 WHERE name LIKE '%Vision Pad%' OR name LIKE '%Aura Book%'");

echo "Categories reorganized successfully!";
?>
