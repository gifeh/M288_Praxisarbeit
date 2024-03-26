<?php
session_start();

$config = parse_ini_file('settings.ini');
$dsn = 'mysql:host=' . $config['server'] . ';dbname=' . $config['dbname'];
$username = $config['dbuser'];
$password = $config['dbpass'];

try {
    $db = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die('Verbindung fehlgeschlagen: ' . $e->getMessage());
}

// Überprüfen, ob ein Benutzer angemeldet ist, sonst zurück zum Login
if (!isset($_SESSION['userName'])) {
    header('Location: index.php');
    exit;
}

// Funktion zur Abfrage des Highscores des aktuellen Benutzers
function getCurrentUserHighscore($db, $userName) {
    $stmt = $db->prepare('SELECT userScore FROM leaderboard WHERE userName = :name');
    $stmt->bindParam(':name', $userName);
    $stmt->execute();
    $highscore = $stmt->fetchColumn();
    return $highscore;
}

// Funktion zum Aktualisieren des Highscores in der Datenbank
function updateHighscore($db, $userName, $newScore) {
    $stmt = $db->prepare('UPDATE leaderboard SET userScore = :score WHERE userName = :name');
    $stmt->bindParam(':name', $userName);
    $stmt->bindParam(':score', $newScore);
    $stmt->execute();
}

// Aktuellen Benutzer und Highscore abrufen
$currentUserName = $_SESSION['userName'];
$currentUserHighscore = getCurrentUserHighscore($db, $currentUserName);

// Überprüfen, ob der aktuelle Score höher als der bisherige Highscore ist
if ($score > $currentUserHighscore) {
    // Den neuen Highscore in der Datenbank aktualisieren
    updateHighscore($db, $currentUserName, $score);
    $currentUserHighscore = $score; // Aktualisierten Highscore speichern
}

// Ausgabe des Benutzernamens und des Highscores
echo "<p>Angemeldeter Benutzer: $currentUserName</p>";
echo "<p>Highscore: $currentUserHighscore</p>";

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
  <p>Press the corresponding number key to continue.</p>
  <div id="score">Score: 0</div>
  <div id="timer" style="display: none;">Time left: 4.0</div>
  <div id="number">?</div>
  <div id="message"></div>
  <div class="button-container">
    <button id="startButton">Start</button>
    <button id="restartButton" style="display: none;">Restart</button>
    <button id="leaderboardButton" onclick="location.href='leaderboard.php';">Leaderboard</button>
  </div>
  <div class="button-container" id="smartphoneInput">
    <button id="numberButton1">1</button>
    <button id="numberButton2">2</button>
    <button id="numberButton3">3</button>
  </div>
  
  <script src="/assets/scripts/script.js"></script>
</body>
</html>
