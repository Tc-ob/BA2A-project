<?php
include "connection.php";

// Delete avatars from products
$conn->query("DELETE FROM PRODUCTS WHERE image_path LIKE '%avatar%'");

// Ensure all watches are in Wearables (category 3)
$conn->query("UPDATE PRODUCTS SET category_id = 3 WHERE name LIKE '%Watch%' OR name LIKE '%Chrono%' OR name LIKE '%Fitness%' OR name LIKE '%bracelet%'");

echo "Cleanup complete! Avatars deleted and watches moved to Wearables.";
?>
