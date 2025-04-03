<?php

$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "smartdinedb";   

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$sql = "";
$result = $conn->query($sql);



?>
