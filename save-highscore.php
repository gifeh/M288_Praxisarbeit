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
