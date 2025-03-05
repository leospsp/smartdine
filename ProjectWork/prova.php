<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // Permette le richieste da qualsiasi dominio
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Connessione al database
$connection = mysqli_connect("localhost", "root", "", "smartdinedb");
$IDTavolo = 1;

if (!$connection) {
    die("Connessione fallita: " . mysqli_connect_error());
}

// Inizializza l'array degli ordini e il prezzo totale nella sessione se non esistono
if (!isset($_SESSION['arrayOrdini'])) {
    $_SESSION['arrayOrdini'] = [];
}
if (!isset($_SESSION['prezzoTotale'])) {
    $_SESSION['prezzoTotale'] = 0;
}

// Aggiunge una pietanza all'ordine se viene cliccato un bottone
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pietanza'], $_POST['prezzo'])) {
    $nomePietanza = $_POST['pietanza'];
    $prezzoPiet = floatval($_POST['prezzo']); // Converte il valore in numero decimale
    
    $_SESSION['arrayOrdini'][] = $nomePietanza;
    $_SESSION['prezzoTotale'] += $prezzoPiet;
}

// Recupera i dati delle pietanze dal database (unica query)
$query = "SELECT * FROM pietanza";
$result = mysqli_query($connection, $query);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Upload nel DB
if (isset($_POST['upload'])) {
    if (!empty($_SESSION['arrayOrdini'])) {
        // Converte l'array in una stringa separata da virgole
        $arrayToString = implode(", ", $_SESSION['arrayOrdini']);

        // Esegue l'escape della stringa per sicurezza
        $arrayToString = mysqli_real_escape_string($connection, $arrayToString);
        
        // Salva l'ordine nel database con il prezzo totale
        $uploadQuery = "UPDATE tavolo SET arrayOrdine = '$arrayToString' WHERE Tavolo.ID = $IDTavolo;";
        //"INSERT INTO Tavolo (ArrayOrdine) VALUES ('$arrayToString') WHERE Tavolo.ID = $IDTavolo";
        
        if (mysqli_query($connection, $uploadQuery)) {
            echo "<p>Ordine salvato con successo!</p>";
            $_SESSION['arrayOrdini'] = []; // Reset dell'ordine dopo il salvataggio
            $_SESSION['prezzoTotale'] = 0; // Reset del prezzo totale
        } else {
            echo "<p>Errore nel salvataggio dell'ordine: " . mysqli_error($connection) . "</p>";
        }
    } else {
        echo "<p>L'ordine è vuoto!</p>";
    }
}

// Genera il contenuto della tabella con i pulsanti per aggiungere le pietanze

$datiDaInviare = [];


$tableContent = "";
foreach ($rows as $row) {

    $datiDaInviare[$row["ID"]] = [

        "nome" => $row["Nome"],
        "prezzo" => $row["Prezzo"],
        "imagePath" => $row["ImagePath"],
    ];

    $tableContent .= "<tr>
        <td>
            <form method='post'>
                <input type='hidden' name='prezzo' value='{$row["Prezzo"]}'>
                <button type='submit' name='pietanza' value='{$row["Nome"]}'>{$row["Nome"]}</button>
            </form>
        </td>
        <td>{$row["Descrizione"]}</td>
        <td>{$row["Prezzo"]} €</td>
    </tr>";
}

// Mostra l'ordine aggiornato
$ordineDisplay = empty($_SESSION['arrayOrdini']) ? "Nessun ordine ancora." : implode(", ", $_SESSION['arrayOrdini']);
$prezzoTotale = $_SESSION['prezzoTotale'];

echo json_encode($datiDaInviare);


/*$html = "
<h1>Ordine:</h1>
<p><strong>Piatti ordinati:</strong> $ordineDisplay</p>
<p><strong>Prezzo totale:</strong> $prezzoTotale €</p>

<table border='1'>
<thead>
    <tr>
        <th>Nome</th>
        <th>Descrizione</th>
        <th>Prezzo</th>
    </tr>
</thead>
<tbody>
    $tableContent
</tbody>
</table>

<form method='post'>
    <button type='submit' name='upload'>Salva Ordine</button>
</form>
";

echo $html;*/
?>
