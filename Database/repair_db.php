<?php
include "connection.php";

echo "--- REPAIRING SCHEMA ---\n";

// 1. Ensure CATEGORIES table exists
$sql_cat = "CREATE TABLE IF NOT EXISTS CATEGORIES(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT
)";
if (mysqli_query($conn, $sql_cat)) {
    echo "CATEGORIES table check/create OK\n";
} else {
    echo "Error creating CATEGORIES: " . mysqli_error($conn) . "\n";
}

// 2. Ensure PRODUCTS table exists and has category_id
$sql_prod = "CREATE TABLE IF NOT EXISTS PRODUCTS(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT(11) NOT NULL DEFAULT 0,
    image_path VARCHAR(500),
    category_id INT(11),
    FOREIGN KEY (category_id) REFERENCES CATEGORIES(id) ON DELETE SET NULL
)";
if (mysqli_query($conn, $sql_prod)) {
    echo "PRODUCTS table check/create OK\n";
} else {
    // If table exists but column is missing, try ALTER
    $sql_alter = "ALTER TABLE PRODUCTS ADD COLUMN category_id INT(11) AFTER image_path";
    if (mysqli_query($conn, $sql_alter)) {
        echo "Added category_id to PRODUCTS\n";
        $sql_fk = "ALTER TABLE PRODUCTS ADD FOREIGN KEY (category_id) REFERENCES CATEGORIES(id) ON DELETE SET NULL";
        mysqli_query($conn, $sql_fk);
    } else {
        echo "Column category_id already exists or error: " . mysqli_error($conn) . "\n";
    }
}

// 3. Seed Categories
$sql_seed_cat = "INSERT IGNORE INTO CATEGORIES (id, name, description) VALUES 
(1, 'Electronics', 'High-end computing and gadgets'),
(2, 'Audio', 'Premium sound and acoustics'),
(3, 'Wearables', 'Executive timepieces and smart accessories')";
if (mysqli_query($conn, $sql_seed_cat)) {
    echo "Categories seeded OK\n";
}

echo "--- REPAIR COMPLETE ---\n";
?>
