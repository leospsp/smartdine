<?php
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "smartdinedb";   

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$editID = null; // ID della pietanza da modificare
$showAddForm = false; // Flag per mostrare la form di aggiunta

// Controllo se il tasto "Modifica" è stato premuto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_id"])) {
    $editID = $_POST["edit_id"];
}

// Controllo se il tasto "Cancella" è stato premuto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_id"])) {
    $deleteID = $_POST["delete_id"];
    $stmt = $conn->prepare("DELETE FROM pietanza WHERE ID = ?");
    $stmt->bind_param("i", $deleteID);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>Pietanza eliminata con successo!</p>";
    } else {
        echo "<p style='color: red;'>Errore durante l'eliminazione!</p>";
    }
}

// Controllo se il tasto "Aggiungi" è stato premuto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["show_add_form"])) {
    $showAddForm = true;
}

// Controllo se il form di aggiornamento è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $id = $_POST["id"];
    $nome = $_POST["nome"];
    $descrizione = $_POST["descrizione"];

    $stmt = $conn->prepare("UPDATE pietanza SET Nome = ?, Descrizione = ? WHERE ID = ?");
    $stmt->bind_param("ssi", $nome, $descrizione, $id);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>Pietanza aggiornata con successo!</p>";
    } else {
        echo "<p style='color: red;'>Errore nell'aggiornamento!</p>";
    }
}

// Controllo se il form di aggiunta è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $nome = $_POST["nome"];
    $descrizione = $_POST["descrizione"];
    $imagePath = $_POST["imagePath"];

    $stmt = $conn->prepare("INSERT INTO pietanza (Nome, Descrizione, ImagePath) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $descrizione, $imagePath);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>Pietanza aggiunta con successo!</p>";
    } else {
        echo "<p style='color: red;'>Errore durante l'aggiunta!</p>";
    }
}

// Recupero i dati delle pietanze
$sql = "SELECT ID, Nome, Descrizione, ImagePath FROM pietanza";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Prodotti</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .container { display: flex; justify-content: center; flex-wrap: wrap; }
        .card { width: 250px; border: 1px solid #ddd; border-radius: 10px; margin: 10px; padding: 15px; text-align: center; box-shadow: 2px 2px 10px rgba(0,0,0,0.1); }
        .card img { max-width: 100%; height: auto; border-radius: 10px; }
        .form-container { margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; display: inline-block; }
    </style>
</head>
<body>

<h1>Gestione Prodotti</h1>

<!-- Tasto per mostrare il form di aggiunta -->
<form method="post">
    <input type="submit" name="show_add_form" value="Aggiungi Nuovo">
</form>

<?php
// Mostra il form di aggiunta se il pulsante "Aggiungi" è stato premuto
if ($showAddForm) {
    ?>
    <div class="form-container">
        <h2>Aggiungi Nuova Pietanza</h2>
        <form method="post">
            <label>Nome: <input type="text" name="nome" required></label><br><br>
            <label>Descrizione: <input type="text" name="descrizione" required></label><br><br>
            <label>URL Immagine: <input type="text" name="imagePath" required></label><br><br>
            <input type="submit" name="add" value="Aggiungi">
        </form>
    </div>
    <?php
}

// Mostra il form di modifica se un piatto è stato selezionato
if ($editID) {
    $sqlEdit = "SELECT * FROM pietanza WHERE ID = $editID";
    $resEdit = $conn->query($sqlEdit);
    if ($resEdit->num_rows > 0) {
        $rowEdit = $resEdit->fetch_assoc();
        ?>
        <div class="form-container">
            <h2>Modifica Pietanza</h2>
            <form method="post">
                <input type="hidden" name="id" value="<?= $rowEdit['ID'] ?>">
                <label>Nome: <input type="text" name="nome" value="<?= htmlspecialchars($rowEdit['Nome']) ?>"></label><br><br>
                <label>Descrizione: <input type="text" name="descrizione" value="<?= htmlspecialchars($rowEdit['Descrizione']) ?>"></label><br><br>
                <input type="submit" name="update" value="Salva Modifiche">
            </form>
        </div>
        <?php
    }
}
?>

<div class="container">
    <?php
    // Ricarichiamo i prodotti dopo eventuale modifica o eliminazione
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='card'>";
            echo "<img src='" . htmlspecialchars($row["ImagePath"]) . "' alt='" . htmlspecialchars($row["Nome"]) . "'>";
            echo "<h2>" . htmlspecialchars($row["Nome"]) . "</h2>";
            echo "<p>" . htmlspecialchars($row["Descrizione"]) . "</p>";

            // Form per richiedere la modifica
            echo "<form method='post'>";
            echo "<input type='hidden' name='edit_id' value='" . $row["ID"] . "'>";
            echo "<input type='submit' value='Modifica'>";
            echo "</form>";

            // Form per cancellare la pietanza
            echo "<form method='post' onsubmit=\"return confirm('Sei sicuro di voler eliminare questa pietanza?');\">";
            echo "<input type='hidden' name='delete_id' value='" . $row["ID"] . "'>";
            echo "<input type='submit' value='Cancella'>";
            echo "</form>";

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
