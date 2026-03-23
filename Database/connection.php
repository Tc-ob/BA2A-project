<?php
$username = "root";
$password = "";
$host = "localhost";
$dbname = "ecommerce";

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed");
}
?>