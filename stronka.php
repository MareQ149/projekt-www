<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "projekt_www");
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Pobranie statystyk postaci, łącznie z credits
$stmt = $conn->prepare("SELECT hp, damage, defense, agility, luck, block, credits FROM postacie WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc() ?: [
    'hp' => 0, 'damage' => 0, 'defense' => 0,
    'agility' => 0, 'luck' => 0, 'block' => 0, 'credits' => 0
];
$stmt->close();

// Pobranie itemów użytkownika
$sql = "
SELECT inv.slot, i.photo, i.id as item_id, i.name
FROM inventory inv
JOIN items i ON inv.item_id = i.id
WHERE inv.user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$slots = [];
$item_ids = [];
while ($row = $result->fetch_assoc()) {
    $slots[$row['slot']] = [
        'photo' => $row['photo'],
        'item_id' => $row['item_id'],
        'name' => $row['name']
    ];
    if ($row['item_id']) {
        $item_ids[] = $row['item_id'];
    }
}
$stmt->close();

// Pobranie bonusów
$bonuses = [
    'hp_bonus' => 0, 'damage_bonus' => 0, 'defense_bonus' => 0,
    'agility_bonus' => 0, 'luck_bonus' => 0, 'block_bonus' => 0
];

if (!empty($item_ids)) {
    $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
    $types = str_repeat('i', count($item_ids));
    $stmt = $conn->prepare("SELECT hp_bonus, damage_bonus, defense_bonus, agility_bonus, luck_bonus, block_bonus FROM item_bonuses WHERE item_id IN ($placeholders)");
    $stmt->bind_param($types, ...$item_ids);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        foreach ($bonuses as $key => &$value) {
            $value += (int)$row[$key];
        }
    }
    $stmt->close();
}
$conn->close();

// Sumowanie
$stats['hp'] += $bonuses['hp_bonus'];
$stats['damage'] += $bonuses['damage_bonus'];
$stats['defense'] += $bonuses['defense_bonus'];
$stats['agility'] += $bonuses['agility_bonus'];
$stats['luck'] += $bonuses['luck_bonus'];
$stats['block'] += $bonuses['block_bonus'];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gra - Ekwipunek</title>
    <link rel="stylesheet" href="style2.css" />
</head>
<body>
<header>
    <img id="logo" src="photos/logo.jpg" alt="logo" />
    <img id="napis" src="photos/napis.png" alt="yasznog" />
</header>

<nav class="menu-wrapper">
    <button id="menuToggle">☰ Menu</button>
    <div id="dropdownMenu" class="hidden">
        <ul>
            <li><a href="stronka.php">Profil</a></li>
            <li><a href="zbrojmistrz.html">Zbrojmistrz</a></li>
            <li><a href="kowal.html">Kowal</a></li>
            <li><a href="walka.html">Walka</a></li>
            <li><a href="logout.php">Wyloguj</a></li>
        </ul>
    </div>
</nav>

<div id="ekwipunek">
    <section id="lewy">
        <?php foreach (['helm', 'napiersnik', 'buty'] as $slot): ?>
            <div id="<?= $slot ?>" class="slot">
                <?php if (!empty($slots[$slot])): ?>
                    <img src="items/<?= htmlspecialchars($slots[$slot]['photo']) ?>"
                         alt="<?= htmlspecialchars($slots[$slot]['name']) ?>"
                         draggable="true"
                         data-itemid="<?= (int)$slots[$slot]['item_id'] ?>" />
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>

    <section id="glowny">
        <div id="postac">
            <img id="awatar" src="photos/logo.jpg" alt="awatar" />
        </div>
    </section>

    <section id="prawy">
        <?php foreach (['bron', 'tarcza', 'trinket'] as $slot): ?>
            <div id="<?= $slot ?>" class="slot">
                <?php if (!empty($slots[$slot])): ?>
                    <img src="items/<?= htmlspecialchars($slots[$slot]['photo']) ?>"
                         alt="<?= htmlspecialchars($slots[$slot]['name']) ?>"
                         draggable="true"
                         data-itemid="<?= (int)$slots[$slot]['item_id'] ?>" />
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>
</div>

<div id="statystyki">
    <h2>Statystyki postaci</h2>
    <ul>
        <li>HP: <?= $stats['hp'] ?></li>
        <li>Obrażenia: <?= $stats['damage'] ?></li>
        <li>Obrona: <?= $stats['defense'] ?></li>
        <li>Zręczność: <?= $stats['agility'] ?></li>
        <li>Szczęście: <?= $stats['luck'] ?></li>
        <li>Blok: <?= $stats['block'] ?></li>
        <li>Kredytki: <?= $stats['credits'] ?></li>
    </ul>
</div>

<div id="inwentarz">
    <section id="inventory">
        <?php for ($i = 1; $i <= 5; $i++):
            $key = 'slot' . $i;
        ?>
            <div id="<?= $key ?>" class="slot">
                <?php if (!empty($slots[$key])): ?>
                    <img src="items/<?= htmlspecialchars($slots[$key]['photo']) ?>"
                         alt="<?= htmlspecialchars($slots[$key]['name']) ?>"
                         draggable="true"
                         data-itemid="<?= (int)$slots[$key]['item_id'] ?>" />
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </section>
</div>

<footer>
    <p>WSZELKIE PRAWA ZASTRZEŻONE&copy;</p>
</footer>


<script src="skrypt.js"></script>
</body>
</html>
