<?php
//sesja i polaczenie z db
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

//pobieranie stat postaci
$stmt = $conn->prepare("SELECT hp, damage, defense, agility, luck, block, credits FROM postacie WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc() ?: [
    'hp' => 0, 'damage' => 0, 'defense' => 0,
    'agility' => 0, 'luck' => 0, 'block' => 0,
    'credits' => 0
];
$stmt->close();

//pobieranie item_id z wybranych slotow
$equipmentSlots = ['helm', 'napiersnik', 'buty', 'bron', 'tarcza', 'trinket'];

$item_ids_for_stats = [];
if (count($equipmentSlots) > 0) {
    $placeholders = implode(',', array_fill(0, count($equipmentSlots), '?'));
    $types = str_repeat('s', count($equipmentSlots));

    $sql_eq = "
        SELECT inv.slot, i.id as item_id
        FROM inventory inv
        LEFT JOIN items i ON inv.item_id = i.id
        WHERE inv.user_id = ? AND inv.slot IN ($placeholders)
    ";

    $stmt_eq = $conn->prepare($sql_eq);
    $params = array_merge([$user_id], $equipmentSlots);
    $types_all = 'i' . $types;

    $bind_params = [];
    foreach ($params as $k => $v) {
        $bind_params[$k] = &$params[$k];
    }
    array_unshift($bind_params, $types_all);
    call_user_func_array([$stmt_eq, 'bind_param'], $bind_params);

    $stmt_eq->execute();
    $result_eq = $stmt_eq->get_result();

    while ($row = $result_eq->fetch_assoc()) {
        if ($row['item_id']) {
            $item_ids_for_stats[] = $row['item_id'];
        }
    }
    $stmt_eq->close();
}

//bonusy z itemow + staty
$bonuses = [
    'hp_bonus' => 0, 'damage_bonus' => 0, 'defense_bonus' => 0,
    'agility_bonus' => 0, 'luck_bonus' => 0, 'block_bonus' => 0
];

if (!empty($item_ids_for_stats)) {
    $placeholders_items = implode(',', array_fill(0, count($item_ids_for_stats), '?'));
    $types_items = str_repeat('i', count($item_ids_for_stats));
    $stmt_bonus = $conn->prepare("SELECT hp_bonus, damage_bonus, defense_bonus, agility_bonus, luck_bonus, block_bonus FROM item_bonuses WHERE item_id IN ($placeholders_items)");
    $stmt_bonus->bind_param($types_items, ...$item_ids_for_stats);
    $stmt_bonus->execute();
    $result_bonus = $stmt_bonus->get_result();

    while ($row = $result_bonus->fetch_assoc()) {
        foreach ($bonuses as $key => &$value) {
            $value += (int)$row[$key];
        }
    }
    $stmt_bonus->close();
}

foreach ($bonuses as $key => $value) {
    $stat_key = str_replace('_bonus', '', $key);
    $stats[$stat_key] += $value;
}

$conn->close();

// Generowanie statystyk przeciwnika na podstawie gracza (±20%)
function losujZakres($wartosc) {
    $min = floor($wartosc * 0.8);
    $max = ceil($wartosc * 1.2);
    return rand($min, max($min, $max)); // aby uniknąć błędu, gdy $wartosc = 0
}

$enemy_stats = [
    'hp' => losujZakres($stats['hp']),
    'damage' => losujZakres($stats['damage']),
    'defense' => losujZakres($stats['defense']),
    'agility' => losujZakres($stats['agility']),
    'luck' => losujZakres($stats['luck']),
    'block' => losujZakres($stats['block']),
    'credits' => losujZakres($stats['credits']),
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="walka.css">
    <title>Walka</title>
</head>
<body>
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
    <div id="napis">
        <h1>WITAJ NA ARENIE</h1>
        <h3>Tutaj udowodnisz swoją wartość i pokażesz na co cię stać</h3>
    </div>
    <button id="przycisk_walka">Szukaj przeciwnika</button>
    <div id="walka" class="hidden">
        <img src="photos/logo.jpg" id="postac">
        <img src="photos/bpp.jpg" id="wrog">
        <div id="statystyki_postac">
        <h2>Statystyki postaci</h2>
            <ul>
                <li>HP: <?= $stats['hp'] ?></li>
                <li>Obrażenia: <?= $stats['damage'] ?></li>
                <li>Obrona: <?= $stats['defense'] ?></li>
                <li>Zręczność: <?= $stats['agility'] ?></li>
                <li>Szczęście: <?= $stats['luck'] ?></li>
                <li>Blok: <?= $stats['block'] ?></li>
            </ul>

        </div>
        <div id="statystyki_wrog">
        <h2>Statystyki wroga</h2>
            <ul>
            <li>HP: <?= $enemy_stats['hp'] ?></li>
            <li>Obrażenia: <?= $enemy_stats['damage'] ?></li>
            <li>Obrona: <?= $enemy_stats['defense'] ?></li>
            <li>Zręczność: <?= $enemy_stats['agility'] ?></li>
            <li>Szczęście: <?= $enemy_stats['luck'] ?></li>
            <li>Blok: <?= $enemy_stats['block'] ?></li>
        </ul>

        </div>
        <div id="panel_glowny">
            be be beng
        </div>
    </div>
    <script src="walka.js"></script>
</body>
</html>