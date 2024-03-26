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

function updateCurrentUserHighscore($db, $userName, $newScore) {
    $currentHighscore = getCurrentUserHighscore($db, $userName);
    if ($newScore > $currentHighscore) {
        $stmt = $db->prepare('UPDATE leaderboard SET userScore = :score, date = NOW() WHERE userName = :name');
        $stmt->bindParam(':score', $newScore);
        $stmt->bindParam(':name', $userName);
        $stmt->execute();
    }
}

// Aktuellen Benutzer und Highscore abrufen
$currentUserName = $_SESSION['userName'];
$currentUserHighscore = getCurrentUserHighscore($db, $currentUserName);

// Benutzer hat neuen Highscore erreicht
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['score'])) {
    $score = intval($_POST['score']);
    if ($score > $currentUserHighscore) {
        updateCurrentUserHighscore($db, $currentUserName, $score);
    }
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
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('restartButton').addEventListener('click', function() {
        const score = document.getElementById('score').textContent.replace('Score: ', '');
        // Send score to game.php for updating highscore
        fetch('game.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'score=' + score
        });
      });
    });
  </script>
</body>
</html>
