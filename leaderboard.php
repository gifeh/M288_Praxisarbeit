<?php
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

// Funktion zur Abfrage des Leaderboards aus der Datenbank
function getLeaderboard($db) {
    $stmt = $db->query('SELECT userName, userScore, date FROM leaderboard ORDER BY userScore DESC');
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $leaderboard;
}

// Leaderboard aus der Datenbank abrufen
$leaderboard = getLeaderboard($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leaderboard</title>
  <link rel="stylesheet" href="/assets/style/styles.css">
</head>
<body>
  <h1>Leaderboard</h1>
  <table>
    <tr>
      <th>Rank</th>
      <th>User</th>
      <th>Score</th>
      <th>Date</th>
    </tr>
    <?php
    $rank = 1;
    foreach ($leaderboard as $row) {
        echo "<tr>";
        echo "<td>{$rank}</td>";
        echo "<td>{$row['userName']}</td>";
        echo "<td>{$row['userScore']}</td>";
        echo "<td>{$row['date']}</td>";
        echo "</tr>";
        $rank++;
    }
    ?>
  </table>
</body>
</html>
