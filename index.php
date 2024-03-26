<?php
session_start();

// Datenbankverbindung herstellen
$config = parse_ini_file('settings.ini');
$dsn = 'mysql:host=' . $config['server'] . ';dbname=' . $config['dbname'];
$username = $config['dbuser'];
$password = $config['dbpass'];

try {
    $db = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die('Verbindung fehlgeschlagen: ' . $e->getMessage());
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

// Abmeldung des Benutzers, wenn er auf index.php zurückkommt
unset($_SESSION['userName']);


function updateLeaderboard($db, $userName, $score) {
    try {
        // Zuerst den aktuellen Highscore des Benutzers abrufen
        $stmt = $db->prepare('SELECT userScore FROM leaderboard WHERE userName = :name');
        $stmt->bindParam(':name', $userName);
        $stmt->execute();
        $currentScore = $stmt->fetchColumn();

        // Überprüfen, ob der neue Score größer ist als der aktuelle Highscore
        if ($score > $currentScore || $currentScore === false) {
            // Wenn ja, aktualisieren Sie den Highscore des Benutzers in der Datenbank
            $updateStmt = $db->prepare('UPDATE leaderboard SET userScore = :score, date = NOW() WHERE userName = :name');
            $updateStmt->bindParam(':name', $userName);
            $updateStmt->bindParam(':score', $score);
            $updateStmt->execute();
        }
    } catch (PDOException $e) {
        // Fehlerbehandlung: Protokollieren Sie den Fehler oder geben Sie eine Fehlermeldung aus
        echo "Fehler beim Aktualisieren des Highscores: " . $e->getMessage();
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
    <button type="submit">Login</button>
  </form>
</body>
</html>
