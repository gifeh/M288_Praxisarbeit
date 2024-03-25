<?php
session_start();

// Überprüfen, ob ein Benutzer angemeldet ist, sonst zurück zum Login
if (!isset($_SESSION['userName'])) {
    header('Location: index.php');
    exit;
}

// Funktion zur Anzeige des aktuellen Benutzers und seines Highscores
function displayUserDetails($db) {
  // Benutzernamen des aktuellen Benutzers abrufen
  $userName = $_SESSION['userName'];

  // Den Highscore des Benutzers aus der Datenbank abrufen
  $stmt = $db->prepare('SELECT userScore FROM leaderboard WHERE userName = :name');
  $stmt->bindParam(':name', $userName);
  $stmt->execute();
  $userScore = $stmt->fetchColumn();

  // Anzeige des Benutzernamens und des Highscores
  echo "<p>Current User: $userName</p>";
  echo "<p>Highscore: $userScore</p>";
}

// Aufruf der Funktion, um Benutzerdetails anzuzeigen
displayUserDetails($db);

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
    function submitScore() {
        // Übermittelte den Score des Spielers an das Spiel-Skript
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "index.php", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Erfolg, zeige die Antwort (z. B. Erfolgsmeldung) an
                console.log(xhr.responseText);
            }
        };
        xhr.send("score=" + score);
    }
  </script>
</body>
</html>
