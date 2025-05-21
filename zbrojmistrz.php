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

$items_data = [];

$sql = "SELECT i.id, i.name, i.photo, i.price, b.hp_bonus, b.damage_bonus, b.defense_bonus, b.agility_bonus, b.luck_bonus, b.block_bonus
        FROM items i
        LEFT JOIN item_bonuses b ON i.id = b.item_id";

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $key = pathinfo($row['photo'], PATHINFO_FILENAME);
        $items_data[$key] = $row;
    }
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kowal</title>
    <link rel="stylesheet" href="zbrojmistrz.css"/>
</head>
<body>
    <nav class="menu-wrapper">
    <button id="menuToggle">☰ Menu</button>
    <div id="dropdownMenu" class="hidden">
    <ul>
        <li><a href="stronka.php">Profil</a></li>
        <li><a href="zbrojmistrz.php">Zbrojmistrz</a></li>
        <li><a href="kowal.php">Kowal</a></li>
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
    <header>
        <h1>Witaj u zbrojmistrza</h1>
    </header>
    <div id="sklep">
        <div class="bronie">Bronie</div>
        <div class="bron-1">
            <img src="items/miecz.png"
            <?php if(isset($items_data['miecz'])): ?>
                data-id="<?= htmlspecialchars($items_data['miecz']['id']) ?>"
                data-price="<?= htmlspecialchars($items_data['miecz']['price']) ?>"
                data-hp_bonus="<?= htmlspecialchars($items_data['miecz']['hp_bonus']) ?>"
                data-damage_bonus="<?= htmlspecialchars($items_data['miecz']['damage_bonus']) ?>"
                data-defense_bonus="<?= htmlspecialchars($items_data['miecz']['defense_bonus']) ?>"
                data-agility_bonus="<?= htmlspecialchars($items_data['miecz']['agility_bonus']) ?>"
                data-luck_bonus="<?= htmlspecialchars($items_data['miecz']['luck_bonus']) ?>"
                data-block_bonus="<?= htmlspecialchars($items_data['miecz']['block_bonus']) ?>"
            <?php endif; ?>
            >
        </div>
        <div class="bron-2">
            <img src="items/mlot.png"
            <?php if(isset($items_data['mlot'])): ?>
                data-id="<?= htmlspecialchars($items_data['mlot']['id']) ?>"
                data-price="<?= htmlspecialchars($items_data['mlot']['price']) ?>"
                data-hp_bonus="<?= htmlspecialchars($items_data['mlot']['hp_bonus']) ?>"
                data-damage_bonus="<?= htmlspecialchars($items_data['mlot']['damage_bonus']) ?>"
                data-defense_bonus="<?= htmlspecialchars($items_data['mlot']['defense_bonus']) ?>"
                data-agility_bonus="<?= htmlspecialchars($items_data['mlot']['agility_bonus']) ?>"
                data-luck_bonus="<?= htmlspecialchars($items_data['mlot']['luck_bonus']) ?>"
                data-block_bonus="<?= htmlspecialchars($items_data['mlot']['block_bonus']) ?>"
            <?php endif; ?>
            >
        </div>
        <div class="bron-3">
            <img src="items/maczuga.png"
            <?php if(isset($items_data['maczuga'])): ?>
                data-id="<?= htmlspecialchars($items_data['maczuga']['id']) ?>"
                data-price="<?= htmlspecialchars($items_data['maczuga']['price']) ?>"
                data-hp_bonus="<?= htmlspecialchars($items_data['maczuga']['hp_bonus']) ?>"
                data-damage_bonus="<?= htmlspecialchars($items_data['maczuga']['damage_bonus']) ?>"
                data-defense_bonus="<?= htmlspecialchars($items_data['maczuga']['defense_bonus']) ?>"
                data-agility_bonus="<?= htmlspecialchars($items_data['maczuga']['agility_bonus']) ?>"
                data-luck_bonus="<?= htmlspecialchars($items_data['maczuga']['luck_bonus']) ?>"
                data-block_bonus="<?= htmlspecialchars($items_data['maczuga']['block_bonus']) ?>"
            <?php endif; ?>
            >
        </div>
        <div class="tarcze">Tarcze</div>
        <div class="tarcza-1">
            <img src="items/tarcza_wiking.png"
            <?php if(isset($items_data['tarcza_wiking'])): ?>
                data-id="<?= htmlspecialchars($items_data['tarcza_wiking']['id']) ?>"
                data-price="<?= htmlspecialchars($items_data['tarcza_wiking']['price']) ?>"
                data-hp_bonus="<?= htmlspecialchars($items_data['tarcza_wiking']['hp_bonus']) ?>"
                data-damage_bonus="<?= htmlspecialchars($items_data['tarcza_wiking']['damage_bonus']) ?>"
                data-defense_bonus="<?= htmlspecialchars($items_data['tarcza_wiking']['defense_bonus']) ?>"
                data-agility_bonus="<?= htmlspecialchars($items_data['tarcza_wiking']['agility_bonus']) ?>"
                data-luck_bonus="<?= htmlspecialchars($items_data['tarcza_wiking']['luck_bonus']) ?>"
                data-block_bonus="<?= htmlspecialchars($items_data['tarcza_wiking']['block_bonus']) ?>"
            <?php endif; ?>
            >
        </div>
        <div class="tarcza-2">
            <img src="items/tarcza_rycerz.png"
            <?php if(isset($items_data['tarcza_rycerz'])): ?>
                data-id="<?= htmlspecialchars($items_data['tarcza_rycerz']['id']) ?>"
                data-price="<?= htmlspecialchars($items_data['tarcza_rycerz']['price']) ?>"
                data-hp_bonus="<?= htmlspecialchars($items_data['tarcza_rycerz']['hp_bonus']) ?>"
                data-damage_bonus="<?= htmlspecialchars($items_data['tarcza_rycerz']['damage_bonus']) ?>"
                data-defense_bonus="<?= htmlspecialchars($items_data['tarcza_rycerz']['defense_bonus']) ?>"
                data-agility_bonus="<?= htmlspecialchars($items_data['tarcza_rycerz']['agility_bonus']) ?>"
                data-luck_bonus="<?= htmlspecialchars($items_data['tarcza_rycerz']['luck_bonus']) ?>"
                data-block_bonus="<?= htmlspecialchars($items_data['tarcza_rycerz']['block_bonus']) ?>"
            <?php endif; ?>
            >
        </div>
        <div class="tarcza-3">
            <img src="items/tarcza_zolnierz.png"
            <?php if(isset($items_data['tarcza_zolnierz'])): ?>
                data-id="<?= htmlspecialchars($items_data['tarcza_zolnierz']['id']) ?>"
                data-price="<?= htmlspecialchars($items_data['tarcza_zolnierz']['price']) ?>"
                data-hp_bonus="<?= htmlspecialchars($items_data['tarcza_zolnierz']['hp_bonus']) ?>"
                data-damage_bonus="<?= htmlspecialchars($items_data['tarcza_zolnierz']['damage_bonus']) ?>"
                data-defense_bonus="<?= htmlspecialchars($items_data['tarcza_zolnierz']['defense_bonus']) ?>"
                data-agility_bonus="<?= htmlspecialchars($items_data['tarcza_zolnierz']['agility_bonus']) ?>"
                data-luck_bonus="<?= htmlspecialchars($items_data['tarcza_zolnierz']['luck_bonus']) ?>"
                data-block_bonus="<?= htmlspecialchars($items_data['tarcza_zolnierz']['block_bonus']) ?>"
            <?php endif; ?>
            >
        </div>
        <div class="trinkety">Trinkety</div>
        <div class="trinket-1">
            <img src="items/znak.png"
            <?php if(isset($items_data['znak'])): ?>
                data-id="<?= htmlspecialchars($items_data['znak']['id']) ?>"
                data-price="<?= htmlspecialchars($items_data['znak']['price']) ?>"
                data-hp_bonus="<?= htmlspecialchars($items_data['znak']['hp_bonus']) ?>"
                data-damage_bonus="<?= htmlspecialchars($items_data['znak']['damage_bonus']) ?>"
                data-defense_bonus="<?= htmlspecialchars($items_data['znak']['defense_bonus']) ?>"
                data-agility_bonus="<?= htmlspecialchars($items_data['znak']['agility_bonus']) ?>"
                data-luck_bonus="<?= htmlspecialchars($items_data['znak']['luck_bonus']) ?>"
                data-block_bonus="<?= htmlspecialchars($items_data['znak']['block_bonus']) ?>"
            <?php endif; ?>
            >
        </div>
        <div class="trinket-2">
            <img src="items/flacha.png"
            <?php if(isset($items_data['flacha'])): ?>
                data-id="<?= htmlspecialchars($items_data['flacha']['id']) ?>"
                data-price="<?= htmlspecialchars($items_data['flacha']['price']) ?>"
                data-hp_bonus="<?= htmlspecialchars($items_data['flacha']['hp_bonus']) ?>"
                data-damage_bonus="<?= htmlspecialchars($items_data['flacha']['damage_bonus']) ?>"
                data-defense_bonus="<?= htmlspecialchars($items_data['flacha']['defense_bonus']) ?>"
                data-agility_bonus="<?= htmlspecialchars($items_data['flacha']['agility_bonus']) ?>"
                data-luck_bonus="<?= htmlspecialchars($items_data['flacha']['luck_bonus']) ?>"
                data-block_bonus="<?= htmlspecialchars($items_data['flacha']['block_bonus']) ?>"
            <?php endif; ?>
            >
        </div>
        <div class="trinket-3">
            <img src="items/bombie.png"
            <?php if(isset($items_data['bombie'])): ?>
                data-id="<?= htmlspecialchars($items_data['bombie']['id']) ?>"
                data-price="<?= htmlspecialchars($items_data['bombie']['price']) ?>"
                data-hp_bonus="<?= htmlspecialchars($items_data['bombie']['hp_bonus']) ?>"
                data-damage_bonus="<?= htmlspecialchars($items_data['bombie']['damage_bonus']) ?>"
                data-defense_bonus="<?= htmlspecialchars($items_data['bombie']['defense_bonus']) ?>"
                data-agility_bonus="<?= htmlspecialchars($items_data['bombie']['agility_bonus']) ?>"
                data-luck_bonus="<?= htmlspecialchars($items_data['bombie']['luck_bonus']) ?>"
                data-block_bonus="<?= htmlspecialchars($items_data['bombie']['block_bonus']) ?>"
            <?php endif; ?>
            >
        </div>
        <div class="zbrojmistrz"><img src="items/bombie.png"></div>
    </div>

<div id="tooltip"></div>

<script src="tooltip_kowal.js"></script>
</body>
</html>