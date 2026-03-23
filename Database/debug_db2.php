<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "connection.php";

$res = $conn->query("SELECT * FROM PRODUCTS");
$existing_images = [];
while($row = $res->fetch_assoc()) {
    $existing_images[] = $row['image_path'];
}

$dir = "../Images/";
$files = scandir($dir);
echo "New files not in DB:<br>";
foreach($files as $f) {
    if ($f == '.' || $f == '..') continue;
    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'png', 'webp'])) continue;

    if (!in_array($f, $existing_images)) {
        echo "- " . $f . "<br>";
    }
}
?>
