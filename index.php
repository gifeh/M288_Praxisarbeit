<?php
session_start();

// Datenbankverbindung herstellen
$config = parse_ini_file('settings.ini');
$dsn = 'mysql:host=' . $config['server'] . ';dbname=' . $config['dbname'];
$username = $config['dbuser'];
$password = $config['dbpass'];

try {
    $db = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die('Verbindung fehlgeschlagen: ' . $e->getMessage());
}

// Überprüfen, ob ein Benutzer angemeldet ist und Weiterleitung zum Spiel
if (isset($_SESSION['userName'])) {
    header('Location: game.php');
    exit;
}

// Überprüfen, ob das Anmeldeformular gesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userName'])) {
    // Überprüfen, ob der eingegebene Benutzername gültig ist
    $userNames = getUserNames($db);
    $inputUserName = $_POST['userName'];
    if (in_array($inputUserName, $userNames)) {
        // Benutzername ist gültig, speichere ihn in der Sitzung und leite zum Spiel weiter
        $_SESSION['userName'] = $inputUserName;
        header('Location: game.php');
        exit;
    } else {
        $errorMessage = 'Ungültiger Benutzername!';
    }
}

// Funktion zur Abfrage der Benutzernamen aus der Datenbank
function getUserNames($db) {
    $stmt = $db->query('SELECT userName FROM leaderboard');
    $userNames = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return $userNames;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Number Game</title>
  <link rel="stylesheet" href="/assets/style/styles.css">
</head>
<body>
  <h1>Number Game</h1>
  <?php if(isset($errorMessage)) echo "<p>$errorMessage</p>"; ?>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="userName">Select your username:</label>
    <select name="userName" id="userName" style="width: 200px; height: 30px;">
        <?php
        $userNames = getUserNames($db);
        foreach ($userNames as $userName) {
            echo "<option value=\"$userName\">$userName</option>";
        }
        ?>
    </select>
    <button type="submit">Login</button>
  </form>
</body>
</html>
