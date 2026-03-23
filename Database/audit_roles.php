<?php
include "connection.php";
echo "--- USERS ---\n";
$res = mysqli_query($conn, "SELECT id, name, email, role FROM USERS");
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: {$row['id']} | Name: {$row['name']} | Email: {$row['email']} | Role: {$row['role']}\n";
}

echo "\n--- CATEGORIES ---\n";
$res = mysqli_query($conn, "SELECT id, name FROM CATEGORIES");
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: {$row['id']} | Name: {$row['name']}\n";
}

echo "\n--- PRODUCTS (LATEST 3) ---\n";
$res = mysqli_query($conn, "SELECT id, name, price, quantity, category_id FROM PRODUCTS ORDER BY id DESC LIMIT 3");
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: {$row['id']} | Name: {$row['name']} | Price: {$row['price']} | Qty: {$row['quantity']} | CatID: {$row['category_id']}\n";
}
?>
