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





unset($_SESSION['userName']);
session_destroy();


// Funktion zur Abfrage der Benutzernamen aus der Datenbank
function getUserNames($db) {
    $stmt = $db->query('SELECT userName FROM leaderboard');
    $userNames = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return $userNames;
}

// Funktion zur Aktualisierung des Leaderboards mit einem neuen Highscore
function updateLeaderboard($db, $userName, $score) {
    // Zuerst den aktuellen Highscore des Benutzers abrufen
    $stmt = $db->prepare('SELECT userScore FROM leaderboard WHERE userName = :name');
    $stmt->bindParam(':name', $userName);
    $stmt->execute();
    $currentScore = $stmt->fetchColumn();

    // Überprüfen, ob der neue Score größer ist als der aktuelle Highscore
    if ($score > $currentScore) {
        // Wenn ja, aktualisieren Sie den Highscore des Benutzers in der Datenbank
        $stmt = $db->prepare('UPDATE leaderboard SET userScore = :score, date = NOW() WHERE userName = :name');
        $stmt->bindParam(':name', $userName);
        $stmt->bindParam(':score', $score);
        $stmt->execute();
    }
}

// Überprüfen, ob ein Benutzer angemeldet ist
if (isset($_SESSION['userName'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['score'])) {
        // Wenn der Benutzer verloren hat und einen Score übermittelt hat, aktualisieren wir das Leaderboard
        $score = intval($_POST['score']);
        updateLeaderboard($db, $_SESSION['userName'], $score);
        unset($_SESSION['userName']); // Abmelden des Benutzers nach dem Spiel
    }
} else {
    // Benutzer ist nicht angemeldet, zeige das Login-Formular an
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
    <h2>dsdededed</h2>
    <button type="submit">Login</button>
</form>
</body>
</html>

