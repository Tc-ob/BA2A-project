<?php
session_start();
include "../Database/connection.php";

// 1. Create CATEGORIES table first (PRODUCTS depends on it)
$sqlcreatetable_cat = "CREATE TABLE IF NOT EXISTS CATEGORIES(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT
)";

if (!mysqli_query($conn, $sqlcreatetable_cat)) {
    die("Error creating CATEGORIES table: " . mysqli_error($conn));
}

// 2. Create PRODUCTS table with category_id as a foreign key to CATEGORIES
//ALTER TABLE PRODUCTS ADD COLUMN image_path VARCHAR(500);
$sqlcreatetable_prod = "CREATE TABLE IF NOT EXISTS PRODUCTS(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT(11) NOT NULL DEFAULT 0,
    image_path VARCHAR(500),
    category_id INT(11),
    FOREIGN KEY (category_id) REFERENCES CATEGORIES(id) ON DELETE SET NULL
)";


if (!mysqli_query($conn, $sqlcreatetable_prod)) {
    die("Error creating PRODUCTS table: " . mysqli_error($conn));
}

// 2.1 Seed Initial Categories
$sql_seed_categories = "INSERT IGNORE INTO CATEGORIES (id, name, description) VALUES 
(1, 'Electronics', 'High-end computing and gadgets'),
(2, 'Audio', 'Premium sound and acoustics'),
(3, 'Wearables', 'Executive timepieces and smart accessories')";
mysqli_query($conn, $sql_seed_categories);

// Seed initial products so the foreign key constraints in CART and PAYMENT don't fail
$sql_seed_products = "INSERT IGNORE INTO PRODUCTS (id, name, description, price, quantity, category_id) VALUES 
(1, 'Aura Sound Pros', 'Premium headphones', 195000, 100, 2),
(2, 'Chrono Timepiece', 'Smart watch', 225000, 100, 3),
(3, 'Vision Pad', 'Tablet', 525000, 100, 1),
(4, 'Aura Sound Lite', 'Headphones', 95000, 100, 2),
(5, 'Aura Book Ultra', 'Laptop', 985000, 100, 1),
(6, 'Nova Earbuds', 'Wireless Earbuds', 130000, 100, 2)";
mysqli_query($conn, $sql_seed_products);

// 3. Create CART table with user_id and product_id as foreign keys
// quantity = how many units of that product the user has in their cart
$sqlcreatetable_cart = "CREATE TABLE IF NOT EXISTS CART(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES USERS(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES PRODUCTS(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id)
)";

if (!mysqli_query($conn, $sqlcreatetable_cart)) {
    die("Error creating CART table: " . mysqli_error($conn));
}

// 4. Create PAYMENT table
$sqlcreatetable_payment = "CREATE TABLE IF NOT EXISTS PAYMENT(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    product_total DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    payment_method ENUM('Mobile Money', 'Orange Money', 'Bank Account') NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES USERS(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES PRODUCTS(id) ON DELETE CASCADE
)";

if (!mysqli_query($conn, $sqlcreatetable_payment)) {
    // Fallback in case column needs to be added to existing table
    $sql_add_col = "ALTER TABLE PAYMENT ADD COLUMN IF NOT EXISTS payment_method ENUM('Mobile Money', 'Orange Money', 'Bank Account') NOT NULL AFTER total_price";
    mysqli_query($conn, $sql_add_col);
}
?>
