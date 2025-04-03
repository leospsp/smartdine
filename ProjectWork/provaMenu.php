<?php
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "smartdinedb";   

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$editID = null;
$showAddForm = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_id"])) {
    $editID = $_POST["edit_id"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_id"])) {
    $deleteID = $_POST["delete_id"];
    $stmt = $conn->prepare("DELETE FROM pietanza WHERE ID = ?");
    $stmt->bind_param("i", $deleteID);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>Pietanza eliminata con successo!</p>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["show_add_form"])) {
    $showAddForm = true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $id = $_POST["id"];
    $nome = $_POST["nome"];
    $descrizione = $_POST["descrizione"];

    $stmt = $conn->prepare("UPDATE pietanza SET Nome = ?, Descrizione = ? WHERE ID = ?");
    $stmt->bind_param("ssi", $nome, $descrizione, $id);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>Pietanza aggiornata con successo!</p>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $nome = $_POST["nome"];
    $descrizione = $_POST["descrizione"];
    
    // Gestione dell'upload dell'immagine
    $targetDir = "images/";
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Controlla se è un'immagine reale
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        echo "<p style='color: red;'>Il file non è un'immagine valida.</p>";
        $uploadOk = 0;
    }

    // Controlla estensione (solo JPG, PNG, JPEG)
    if (!in_array($imageFileType, ["jpg", "jpeg", "png"])) {
        echo "<p style='color: red;'>Sono accettati solo file JPG, JPEG, PNG.</p>";
        $uploadOk = 0;
    }

    // Controlla la dimensione (max 2MB)
    if ($_FILES["image"]["size"] > 2 * 1024 * 1024) {
        echo "<p style='color: red;'>Il file è troppo grande. Massimo 2MB.</p>";
        $uploadOk = 0;
    }

    // Se tutto è ok, prova a caricare il file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $stmt = $conn->prepare("INSERT INTO pietanza (Nome, Descrizione, ImagePath) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nome, $descrizione, $targetFilePath);
            if ($stmt->execute()) {
                echo "<p style='color: green;'>Pietanza aggiunta con successo!</p>";
            } else {
                echo "<p style='color: red;'>Errore durante l'aggiunta!</p>";
            }
        } else {
            echo "<p style='color: red;'>Errore nel caricamento dell'immagine.</p>";
        }
    }
}

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
        /* Stile generale */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
    text-align: center;
    color: #333;
}

/* Contenitore principale */
.container {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 20px;
    padding: 20px;
}

/* Card prodotto */
.card {
    width: 280px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    text-align: center;
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: scale(1.05);
}

.card img {
    max-width: 100%;
    border-radius: 10px;
}

.card h2 {
    font-size: 20px;
    margin: 10px 0;
    color: #ff5a5f;
}

.card p {
    font-size: 14px;
    color: #666;
}

/* Bottoni */
button, input[type="submit"] {
    background: #ff5a5f;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
    margin: 5px;
}

button:hover, input[type="submit"]:hover {
    background: #e0484d;
}

/* Form */
.form-container {
    width: 50%;
    background: white;
    padding: 20px;
    margin: 20px auto;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.form-container h2 {
    color: #ff5a5f;
    margin-bottom: 15px;
}

.form-container input[type="text"], 
.form-container input[type="file"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

/* Pulsante fisso per aggiungere */
.add-button {
    display: inline-block;
    margin: 20px;
    font-size: 18px;
    font-weight: bold;
}

/* Effetto sui pulsanti quando premuti */
button:active, input[type="submit"]:active {
    transform: scale(0.95);
}

/* Responsività */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
        align-items: center;
    }
    
    .form-container {
        width: 80%;
    }
}

    </style>
</head>
<body>

<h1>Gestione Prodotti</h1>

<form method="post">
    <input type="submit" name="show_add_form" value="Aggiungi Nuovo">
</form>

<?php if ($showAddForm): ?>
    <div class="form-container">
        <h2>Aggiungi Nuova Pietanza</h2>
        <form method="post" enctype="multipart/form-data">
            <label>Nome: <input type="text" name="nome" required></label><br><br>
            <label>Descrizione: <input type="text" name="descrizione" required></label><br><br>
            <label>Immagine: <input type="file" name="image" accept=".jpg,.jpeg,.png" required></label><br><br>
            <input type="submit" name="add" value="Aggiungi">
        </form>
    </div>
<?php endif; ?>

<?php if ($editID): ?>
    <?php $resEdit = $conn->query("SELECT * FROM pietanza WHERE ID = $editID"); ?>
    <?php if ($resEdit->num_rows > 0): ?>
        <?php $rowEdit = $resEdit->fetch_assoc(); ?>
        <div class="form-container">
            <h2>Modifica Pietanza</h2>
            <form method="post">
                <input type="hidden" name="id" value="<?= $rowEdit['ID'] ?>">
                <label>Nome: <input type="text" name="nome" value="<?= htmlspecialchars($rowEdit['Nome']) ?>"></label><br><br>
                <label>Descrizione: <input type="text" name="descrizione" value="<?= htmlspecialchars($rowEdit['Descrizione']) ?>"></label><br><br>
                <input type="submit" name="update" value="Salva Modifiche">
            </form>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="container">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class='card'>
            <img src='<?= htmlspecialchars($row["ImagePath"]) ?>' alt='<?= htmlspecialchars($row["Nome"]) ?>'>
            <h2><?= htmlspecialchars($row["Nome"]) ?></h2>
            <p><?= htmlspecialchars($row["Descrizione"]) ?></p>
            <form method='post'>
                <input type='hidden' name='edit_id' value='<?= $row["ID"] ?>'>
                <input type='submit' value='Modifica'>
            </form>
            <form method='post' onsubmit="return confirm('Sei sicuro di voler eliminare questa pietanza?');">
                <input type='hidden' name='delete_id' value='<?= $row["ID"] ?>'>
                <input type='submit' value='Cancella'>
            </form>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
