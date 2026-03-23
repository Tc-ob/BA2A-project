<?php
include "connection.php";
$res=$conn->query("SHOW CREATE TABLE USERS");
$row=$res->fetch_assoc();
echo $row['Create Table'];
?> 
