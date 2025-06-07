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

// Obsługa sortowania
$allowedSorts = ['hp', 'damage', 'defense', 'agility', 'luck', 'block', 'credits'];
$sort = $_GET['sort'] ?? 'credits';

if (!in_array($sort, $allowedSorts)) {
    $sort = 'credits';
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
    ORDER BY $sort DESC
";

$result = $conn->query($sql);
if (!$result) {
    die("Błąd zapytania: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Tu ocenisz siłę swoich przeciwników">
    <title>HALL OF FAME</title>
    <link rel="stylesheet" href="sala.css"/>
    <link rel="icon" href="photos/logo.jpg" type="image/jpg">
    
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
            <li><a href="sala.php">Sala chwały</a></li>
            <li><a href="logout.php">Wyloguj</a></li>
        </ul>
    </div>
</nav>



<main>
    <h1 id="napisik">Sala Chwały</h1>
    <h2 id="sortuj">SORTUJ WEDŁUG:</h2>
    <div id="guziki">
        <a href="?sort=hp"><button>HP</button></a>
        <a href="?sort=damage"><button>DMG</button></a>
        <a href="?sort=defense"><button>DEF</button></a>
        <a href="?sort=agility"><button>AGI</button></a>
        <a href="?sort=luck"><button>LUCK</button></a>
        <a href="?sort=block"><button>BLOCK</button></a>
        <a href="?sort=credits"><button>CREDITS</button></a>
    </div>

    <section id="gracze">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div>
                <strong><?= htmlspecialchars($row['username']) ?></strong> |
                HP: <?= $row['hp'] ?> |
                DMG: <?= $row['damage'] ?> |
                DEF: <?= $row['defense'] ?> |
                AGI: <?= $row['agility'] ?> |
                LUCK: <?= $row['luck'] ?> |
                BLOCK: <?= $row['block'] ?> |
                CREDITS: <?= $row['credits'] ?>
            </div>
            <hr>
        <?php endwhile; ?>
    </section>
</main>

<script src="walka.js"></script>
</body>
</html>
