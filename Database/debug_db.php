<?php
include "connection.php";
$tables = ['PAYMENT', 'PRODUCTS'];
foreach ($tables as $table) {
    echo "--- $table ---\n";
    $res = mysqli_query($conn, "DESCRIBE $table");
    while($row = mysqli_fetch_assoc($res)) {
        print_r($row);
    }
}

echo "--- DATA PREVIEW (PRODUCTS) ---\n";
$res = mysqli_query($conn, "SELECT * FROM PRODUCTS");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

echo "--- DATA PREVIEW (PAYMENT) ---\n";
$res = mysqli_query($conn, "SELECT * FROM PAYMENT LIMIT 5");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
