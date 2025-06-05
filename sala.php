<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "projekt_www");
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$sql = "
    SELECT 
        u.username,
        p.hp + COALESCE(SUM(ib.hp_bonus), 0) AS hp,
        p.damage + COALESCE(SUM(ib.damage_bonus), 0) AS damage,
        p.defense + COALESCE(SUM(ib.defense_bonus), 0) AS defense,
        p.agility + COALESCE(SUM(ib.agility_bonus), 0) AS agility,
        p.luck + COALESCE(SUM(ib.luck_bonus), 0) AS luck,
        p.block + COALESCE(SUM(ib.block_bonus), 0) AS block,
        p.credits
    FROM uzytkownicy u
    JOIN postacie p ON p.user_id = u.id
    LEFT JOIN inventory i ON i.user_id = u.id 
        AND i.slot IN ('helm', 'napiersnik', 'buty', 'bron', 'tarcza', 'trinket')
    LEFT JOIN items it ON it.id = i.item_id
    LEFT JOIN item_bonuses ib ON ib.item_id = it.id
    GROUP BY u.id
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Błąd przygotowania zapytania: " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Błąd pobierania wyników: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HALL OF FAME</title>
    <link rel="stylesheet" href="sala.css"/>
</head>
<body>
    <nav class="menu-wrapper">
    <button id="menuToggle">☰ Menu</button>
    <div id="dropdownMenu" class="hidden">
    <ul>
        <li><a href="stronka.php">Profil</a></li>
        <li><a href="zbrojmistrz.php">Zbrojmistrz</a></li>
        <li><a href="kowal.php">Kowal</a></li>
        <li><a href="walka.php">Walka</a></li>
        <li><a href="sala.php">HALL OF FAME</a></li>
        <li><a href="logout.php">Wyloguj</a></li>
    </ul>
    </div>
</nav>
    <header>
        <h1 id="napisik">HALL OF FAME</h1>
    </header>
    <container>
        <?php
            while ($row = $result->fetch_assoc()) {
                echo $row['username'] . ", " 
                . $row['hp'] . ", " 
                . $row['damage'] . ", "
                . $row['defense'] . ", "
                . $row['agility'] . ", "
                . $row['luck'] . ", "
                . $row['block'] . ", "
                . $row['credits'] . "<br><hr>";
            }
        ?>
    </container>

    <script src="walka.js"></script>
</body>
</html>