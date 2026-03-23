<?php
include "connection.php";

$conn->query("UPDATE PRODUCTS SET category_id = 3 WHERE image_path LIKE '%download (18)%' OR image_path LIKE '%download (19)%' OR image_path LIKE '%download (25)%'");

echo "Downloads 18, 19, and 25 moved to Wearables!";
?>
