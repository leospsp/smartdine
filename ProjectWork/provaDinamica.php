<?php

$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "smartdinedb";   


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}


$sql = "SELECT Nome, Descrizione, ImagePath FROM pietanza";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prodotti</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        .card {
            width: 250px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }
        .card img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<h1>Lista Prodotti</h1>
<div class="container">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='card'>";
            echo "<img src='" . htmlspecialchars($row["ImagePath"]) . "' alt='" . htmlspecialchars($row["Nome"]) . "'>";
            echo "<h2>" . htmlspecialchars($row["Nome"]) . "</h2>";
            echo "<p>" . htmlspecialchars($row["Descrizione"]) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>Nessun prodotto trovato.</p>";
    }
    $conn->close();
    ?>
</div>

</body>
</html>
