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
$stmt = $conn->prepare("SELECT hp, damage, defense, agility, luck, block FROM postacie WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc() ?: [
    'hp' => 0, 'damage' => 0, 'defense' => 0,
    'agility' => 0, 'luck' => 0, 'block' => 0
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

// Pobranie bonusów z item_bonuses dla wszystkich itemów użytkownika
$bonuses = [
    'hp_bonus' => 0, 'damage_bonus' => 0, 'defense_bonus' => 0,
    'agility_bonus' => 0, 'luck_bonus' => 0, 'block_bonus' => 0
];

if (!empty($item_ids)) {
    // Utworzenie zapytania z IN (?,?,...) w zależności od liczby itemów
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

// Sumowanie bonusów z bazowych statystyk
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
<script>
  const toggleButton = document.getElementById("menuToggle");
  const dropdownMenu = document.getElementById("dropdownMenu");

    toggleButton.addEventListener("click", () => {
        dropdownMenu.classList.toggle("show");
  });
</script>

<div id="ekwipunek">
    <section id="lewy">
        <div id="helm" class="slot">
            <?php if (!empty($slots['helm'])): ?>
                <img src="items/<?php echo htmlspecialchars($slots['helm']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($slots['helm']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$slots['helm']['item_id']; ?>" />
            <?php endif; ?>
        </div>
        <div id="napiersnik" class="slot">
            <?php if (!empty($slots['napiersnik'])): ?>
                <img src="items/<?php echo htmlspecialchars($slots['napiersnik']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($slots['napiersnik']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$slots['napiersnik']['item_id']; ?>" />
            <?php endif; ?>
        </div>
        <div id="buty" class="slot">
            <?php if (!empty($slots['buty'])): ?>
                <img src="items/<?php echo htmlspecialchars($slots['buty']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($slots['buty']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$slots['buty']['item_id']; ?>" />
            <?php endif; ?>
        </div>
    </section>
    
    <img id="awatar" src="photos/logo.jpg" alt="awatar" />

    <section id="prawy">
        <div id="bron" class="slot">
            <?php if (!empty($slots['bron'])): ?>
                <img src="items/<?php echo htmlspecialchars($slots['bron']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($slots['bron']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$slots['bron']['item_id']; ?>" />
            <?php endif; ?>
        </div>
        <div id="tarcza" class="slot">
            <?php if (!empty($slots['tarcza'])): ?>
                <img src="items/<?php echo htmlspecialchars($slots['tarcza']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($slots['tarcza']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$slots['tarcza']['item_id']; ?>" />
            <?php endif; ?>
        </div>
        <div id="trinket" class="slot">
            <?php if (!empty($slots['trinket'])): ?>
                <img src="items/<?php echo htmlspecialchars($slots['trinket']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($slots['trinket']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$slots['trinket']['item_id']; ?>" />
            <?php endif; ?>
        </div>
    </section>
</div>
<div id="staty">
    <?php for ($i = 1; $i <= 10; $i++):
        $key = 'slot' . $i;
    ?>
        <div id="<?php echo $key; ?>" class="slot">
            <?php if (!empty($slots[$key])): ?>
                <img src="items/<?php echo htmlspecialchars($slots[$key]['photo']); ?>"
                    alt="<?php echo htmlspecialchars($slots[$key]['name']); ?>"
                    draggable="true"
                    data-itemid="<?php echo (int)$slots[$key]['item_id']; ?>" />
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
        </ul>
    </div>
</div>
<footer>
    <p>WSZELKIE PRAWA ZASTRZEŻONE&copy;</p>
</footer>

<script>
document.querySelectorAll('.slot img').forEach(img => {
    img.addEventListener('dragstart', e => {
        e.dataTransfer.setData('text/plain', JSON.stringify({
            itemId: e.target.dataset.itemid,
            fromSlot: e.target.parentElement.id
        }));
    });
});

document.querySelectorAll('.slot').forEach(slot => {
    slot.addEventListener('dragover', e => {
        e.preventDefault();
        slot.style.outline = '2px solid yellow';
    });
    slot.addEventListener('dragleave', e => {
        slot.style.outline = '';
    });
    slot.addEventListener('drop', e => {
        e.preventDefault();
        slot.style.outline = '';

        const data = JSON.parse(e.dataTransfer.getData('text/plain'));
        const toSlot = slot.id;
        const fromSlot = data.fromSlot;
        const itemId = data.itemId;

        fetch('update_slot.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                from_slot: fromSlot,
                to_slot: toSlot,
                item_id: itemId
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                location.reload();
            } else {
                alert('Błąd podczas przesuwania itemu: ' + data.message);
            }
        })
        .catch(() => alert('Błąd sieci'));
    });
});
</script>

</body>
</html>
