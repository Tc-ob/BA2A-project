<?php
include "connection.php";
$res = $conn->query("SELECT id, name, image_path FROM PRODUCTS ORDER BY id DESC");
while($row = $res->fetch_assoc()) {
    echo $row['id'] . " | " . $row['name'] . " | " . $row['image_path'] . "<br>";
}
?>
