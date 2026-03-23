<?php
include "connection.php";

// Map existing products to their proper categories
$conn->query("UPDATE PRODUCTS SET category_id = 1 WHERE name LIKE '%Vivo%' OR name LIKE '%Samsung%' OR name LIKE '%Infinix%' OR name LIKE '%Android%' OR name LIKE '%Xiaomi%' OR name LIKE '%Charger%'");
$conn->query("UPDATE PRODUCTS SET category_id = 2 WHERE name LIKE '%Aura Sound%' OR name LIKE '%Earphone%' OR name LIKE '%Bluetooth%' OR name LIKE '%AirPods%' OR name LIKE '%Earbuds%' OR name LIKE '%Ouvido%'");
$conn->query("UPDATE PRODUCTS SET category_id = 3 WHERE name LIKE '%Chrono%' OR name LIKE '%Smart Watch%'");
$conn->query("UPDATE PRODUCTS SET category_id = 1 WHERE name LIKE '%Vision Pad%' OR name LIKE '%Aura Book%'");
$conn->query("UPDATE PRODUCTS SET category_id = 2 WHERE name LIKE '%Nova%'");

echo "Products are now successfully linked to their categories!";
?>
