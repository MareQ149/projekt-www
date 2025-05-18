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

// Pobranie statystyk postaci
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

// 1) Pobierz WSZYSTKIE itemy z inventory użytkownika (do wyświetlania)
$sql_all = "
    SELECT inv.slot, i.photo, i.id as item_id, i.name
    FROM inventory inv
    LEFT JOIN items i ON inv.item_id = i.id
    WHERE inv.user_id = ?
";
$stmt_all = $conn->prepare($sql_all);
$stmt_all->bind_param("i", $user_id);
$stmt_all->execute();
$result_all = $stmt_all->get_result();

$all_slots = [];
while ($row = $result_all->fetch_assoc()) {
    if ($row['item_id']) {
        $all_slots[$row['slot']] = [
            'photo' => $row['photo'],
            'item_id' => $row['item_id'],
            'name' => $row['name']
        ];
    } else {
        $all_slots[$row['slot']] = null;
    }
}
$stmt_all->close();

// 2) Pobierz tylko itemy z wybranych slotów do liczenia statystyk
$equipmentSlots = ['helm', 'napiersnik', 'buty', 'bron', 'tarcza', 'trinket'];

if (count($equipmentSlots) > 0) {
    $placeholders = implode(',', array_fill(0, count($equipmentSlots), '?'));
    $types = str_repeat('s', count($equipmentSlots));
    
    // Budowanie zapytania z parametrami slotów i user_id
    $sql_eq = "
        SELECT inv.slot, i.id as item_id
        FROM inventory inv
        LEFT JOIN items i ON inv.item_id = i.id
        WHERE inv.user_id = ? AND inv.slot IN ($placeholders)
    ";

    $stmt_eq = $conn->prepare($sql_eq);

    // bind_param wymaga referencji i typów: 'i' + sloty jako stringi
    $params = array_merge([$user_id], $equipmentSlots);
    $types_all = 'i' . $types; // user_id jako int + sloty jako stringi

    $bind_params = [];
    foreach ($params as $k => $v) {
        $bind_params[$k] = &$params[$k];
    }
    array_unshift($bind_params, $types_all);
    call_user_func_array([$stmt_eq, 'bind_param'], $bind_params);

    $stmt_eq->execute();
    $result_eq = $stmt_eq->get_result();

    $item_ids_for_stats = [];
    while ($row = $result_eq->fetch_assoc()) {
        if ($row['item_id']) {
            $item_ids_for_stats[] = $row['item_id'];
        }
    }
    $stmt_eq->close();
} else {
    $item_ids_for_stats = [];
}

// 3) Pobranie bonusów tylko dla itemów z wybranych slotów
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

$conn->close();

// Dodanie bonusów do statystyk postaci
foreach ($bonuses as $key => $value) {
    $stat_key = str_replace('_bonus', '', $key);
    $stats[$stat_key] += $value;
}
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
        <div id="helm" class="slot">
            <?php if (!empty($all_slots['helm'])): ?>
                <img src="items/<?php echo htmlspecialchars($all_slots['helm']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($all_slots['helm']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$all_slots['helm']['item_id']; ?>" />
            <?php else: ?>
                <img src="items/helm_slot.png" alt="Pusty slot helm" />
            <?php endif; ?>
        </div>

        <div id="napiersnik" class="slot">
            <?php if (!empty($all_slots['napiersnik'])): ?>
                <img src="items/<?php echo htmlspecialchars($all_slots['napiersnik']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($all_slots['napiersnik']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$all_slots['napiersnik']['item_id']; ?>" />
            <?php else: ?>
                <img src="items/zbroja_slot.png" alt="Pusty slot napiersnik" />
            <?php endif; ?>
        </div>

        <div id="buty" class="slot">
            <?php if (!empty($all_slots['buty'])): ?>
                <img src="items/<?php echo htmlspecialchars($all_slots['buty']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($all_slots['buty']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$all_slots['buty']['item_id']; ?>" />
            <?php else: ?>
                <img src="items/buty_slot.png" alt="Pusty slot buty" />
            <?php endif; ?>
        </div>
    </section>

    <img id="awatar" src="photos/logo.jpg" alt="awatar" />

    <section id="prawy">
        <div id="bron" class="slot">
            <?php if (!empty($all_slots['bron'])): ?>
                <img src="items/<?php echo htmlspecialchars($all_slots['bron']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($all_slots['bron']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$all_slots['bron']['item_id']; ?>" />
            <?php else: ?>
                <img src="items/miecz_slot.png" alt="Pusty slot miecz" />
            <?php endif; ?>
        </div>

        <div id="tarcza" class="slot">
            <?php if (!empty($all_slots['tarcza'])): ?>
                <img src="items/<?php echo htmlspecialchars($all_slots['tarcza']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($all_slots['tarcza']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$all_slots['tarcza']['item_id']; ?>" />
            <?php else: ?>
                <img src="items/tarcza_slot.png" alt="Pusty slot tarcza" />
            <?php endif; ?>
        </div>

        <div id="trinket" class="slot">
            <?php if (!empty($all_slots['trinket'])): ?>
                <img src="items/<?php echo htmlspecialchars($all_slots['trinket']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($all_slots['trinket']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$all_slots['trinket']['item_id']; ?>" />
            <?php else: ?>
                <img src="items/trinket_slot.png" alt="Pusty slot trinket" />
            <?php endif; ?>
        </div>
    </section>
</div>

<div id="staty">
    <?php for ($i = 1; $i <= 10; $i++):
        $key = 'slot' . $i;
    ?>
        <div id="<?php echo $key; ?>" class="slot">
            <?php if (!empty($all_slots[$key])): ?>
                <img src="items/<?php echo htmlspecialchars($all_slots[$key]['photo']); ?>"
                     alt="<?php echo htmlspecialchars($all_slots[$key]['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$all_slots[$key]['item_id']; ?>" />
            <?php endif; ?>
        </div>
    <?php endfor; ?>
    <div id="statystyki">
        <h2>Statystyki postaci</h2>
        <ul>
            <li>HP: <?php echo $stats['hp']; ?></li>
            <li>Obrażenia: <?php echo $stats['damage']; ?></li>
            <li>Obrona: <?php echo $stats['defense']; ?></li>
            <li>Zręczność: <?php echo $stats['agility']; ?></li>
            <li>Szczęście: <?php echo $stats['luck']; ?></li>
            <li>Blok: <?php echo $stats['block']; ?></li>
            <li>Kredytki: <?php echo $stats['credits']; ?></li>
        </ul>
    </div>
</div>

<footer>
    <p>WSZELKIE PRAWA ZASTRZEŻONE&copy;</p>
</footer>

<script src="ekwipunek.js"></script>
<script>
const toggleButton = document.getElementById("menuToggle");
const dropdownMenu = document.getElementById("dropdownMenu");

toggleButton.addEventListener("click", () => {
    dropdownMenu.classList.toggle("show");
});
</script>
</body>
</html>
