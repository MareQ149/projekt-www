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
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kowal</title>
    <link rel="stylesheet" href="kowal.css" />
    <style>
      /* Minimalny CSS do tooltipa */
      #tooltip {
        position: absolute;
        display: none;
        background: rgba(0,0,0,0.8);
        color: #fff;
        padding: 8px 12px;
        border-radius: 6px;
        pointer-events: none;
        font-size: 14px;
        max-width: 220px;
        z-index: 1000;
        box-shadow: 0 0 6px rgba(0,0,0,0.3);
        line-height: 1.3;
      }
    </style>
</head>
<body>
<nav class="menu-wrapper">
    <button id="menuToggle">☰ Menu</button>
    <div id="dropdownMenu" class="hidden">
    <ul>
        <li><a href="stronka.php">Profil</a></li>
        <li><a href="zbrojmistrz.html">Zbrojmistrz</a></li>
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
    <h1>Witaj u kowala</h1>
</header>

<div id="sklep">
    <div class="buty">Buty</div>
    <div class="buty-1">
      <img src="items/buty_wiking.png"
      <?php if(isset($items_data['buty_wiking'])): ?>
        data-price="<?= htmlspecialchars($items_data['buty_wiking']['price']) ?>"
        data-hp_bonus="<?= htmlspecialchars($items_data['buty_wiking']['hp_bonus']) ?>"
        data-damage_bonus="<?= htmlspecialchars($items_data['buty_wiking']['damage_bonus']) ?>"
        data-defense_bonus="<?= htmlspecialchars($items_data['buty_wiking']['defense_bonus']) ?>"
        data-agility_bonus="<?= htmlspecialchars($items_data['buty_wiking']['agility_bonus']) ?>"
        data-luck_bonus="<?= htmlspecialchars($items_data['buty_wiking']['luck_bonus']) ?>"
        data-block_bonus="<?= htmlspecialchars($items_data['buty_wiking']['block_bonus']) ?>"
      <?php endif; ?>
      >
    </div>
    <div class="buty-2">
      <img src="items/buty_rycerz.png"
      <?php if(isset($items_data['buty_rycerz'])): ?>
        data-price="<?= htmlspecialchars($items_data['buty_rycerz']['price']) ?>"
        data-hp_bonus="<?= htmlspecialchars($items_data['buty_rycerz']['hp_bonus']) ?>"
        data-damage_bonus="<?= htmlspecialchars($items_data['buty_rycerz']['damage_bonus']) ?>"
        data-defense_bonus="<?= htmlspecialchars($items_data['buty_rycerz']['defense_bonus']) ?>"
        data-agility_bonus="<?= htmlspecialchars($items_data['buty_rycerz']['agility_bonus']) ?>"
        data-luck_bonus="<?= htmlspecialchars($items_data['buty_rycerz']['luck_bonus']) ?>"
        data-block_bonus="<?= htmlspecialchars($items_data['buty_rycerz']['block_bonus']) ?>"
      <?php endif; ?>
      >
    </div>
    <div class="buty-3">
      <img src="items/buty_zolnierz.png"
      <?php if(isset($items_data['buty_zolnierz'])): ?>
        data-price="<?= htmlspecialchars($items_data['buty_zolnierz']['price']) ?>"
        data-hp_bonus="<?= htmlspecialchars($items_data['buty_zolnierz']['hp_bonus']) ?>"
        data-damage_bonus="<?= htmlspecialchars($items_data['buty_zolnierz']['damage_bonus']) ?>"
        data-defense_bonus="<?= htmlspecialchars($items_data['buty_zolnierz']['defense_bonus']) ?>"
        data-agility_bonus="<?= htmlspecialchars($items_data['buty_zolnierz']['agility_bonus']) ?>"
        data-luck_bonus="<?= htmlspecialchars($items_data['buty_zolnierz']['luck_bonus']) ?>"
        data-block_bonus="<?= htmlspecialchars($items_data['buty_zolnierz']['block_bonus']) ?>"
      <?php endif; ?>
      >
    </div>

    <div class="zbroje">Zbroje</div>
    <div class="zbroja-1">
      <img src="items/klata_wiking.png"
      <?php if(isset($items_data['klata_wiking'])): ?>
        data-price="<?= htmlspecialchars($items_data['klata_wiking']['price']) ?>"
        data-hp_bonus="<?= htmlspecialchars($items_data['klata_wiking']['hp_bonus']) ?>"
        data-damage_bonus="<?= htmlspecialchars($items_data['klata_wiking']['damage_bonus']) ?>"
        data-defense_bonus="<?= htmlspecialchars($items_data['klata_wiking']['defense_bonus']) ?>"
        data-agility_bonus="<?= htmlspecialchars($items_data['klata_wiking']['agility_bonus']) ?>"
        data-luck_bonus="<?= htmlspecialchars($items_data['klata_wiking']['luck_bonus']) ?>"
        data-block_bonus="<?= htmlspecialchars($items_data['klata_wiking']['block_bonus']) ?>"
      <?php endif; ?>
      >
    </div>
    <div class="zbroja-2">
      <img src="items/klata_rycerz.png"
      <?php if(isset($items_data['klata_rycerz'])): ?>
        data-price="<?= htmlspecialchars($items_data['klata_rycerz']['price']) ?>"
        data-hp_bonus="<?= htmlspecialchars($items_data['klata_rycerz']['hp_bonus']) ?>"
        data-damage_bonus="<?= htmlspecialchars($items_data['klata_rycerz']['damage_bonus']) ?>"
        data-defense_bonus="<?= htmlspecialchars($items_data['klata_rycerz']['defense_bonus']) ?>"
        data-agility_bonus="<?= htmlspecialchars($items_data['klata_rycerz']['agility_bonus']) ?>"
        data-luck_bonus="<?= htmlspecialchars($items_data['klata_rycerz']['luck_bonus']) ?>"
        data-block_bonus="<?= htmlspecialchars($items_data['klata_rycerz']['block_bonus']) ?>"
      <?php endif; ?>
      >
    </div>
    <div class="zbroja-3">
      <img src="items/klata_zolnierz.png"
      <?php if(isset($items_data['klata_zolnierz'])): ?>
        data-price="<?= htmlspecialchars($items_data['klata_zolnierz']['price']) ?>"
        data-hp_bonus="<?= htmlspecialchars($items_data['klata_zolnierz']['hp_bonus']) ?>"
        data-damage_bonus="<?= htmlspecialchars($items_data['klata_zolnierz']['damage_bonus']) ?>"
        data-defense_bonus="<?= htmlspecialchars($items_data['klata_zolnierz']['defense_bonus']) ?>"
        data-agility_bonus="<?= htmlspecialchars($items_data['klata_zolnierz']['agility_bonus']) ?>"
        data-luck_bonus="<?= htmlspecialchars($items_data['klata_zolnierz']['luck_bonus']) ?>"
        data-block_bonus="<?= htmlspecialchars($items_data['klata_zolnierz']['block_bonus']) ?>"
      <?php endif; ?>
      >
    </div>

    <div class="hełmy">Hełmy</div>
    <div class="hełm-1">
      <img src="items/helm_wiking.png"
      <?php if(isset($items_data['helm_wiking'])): ?>
        data-price="<?= htmlspecialchars($items_data['helm_wiking']['price']) ?>"
        data-hp_bonus="<?= htmlspecialchars($items_data['helm_wiking']['hp_bonus']) ?>"
        data-damage_bonus="<?= htmlspecialchars($items_data['helm_wiking']['damage_bonus']) ?>"
        data-defense_bonus="<?= htmlspecialchars($items_data['helm_wiking']['defense_bonus']) ?>"
        data-agility_bonus="<?= htmlspecialchars($items_data['helm_wiking']['agility_bonus']) ?>"
        data-luck_bonus="<?= htmlspecialchars($items_data['helm_wiking']['luck_bonus']) ?>"
        data-block_bonus="<?= htmlspecialchars($items_data['helm_wiking']['block_bonus']) ?>"
      <?php endif; ?>
      >
    </div>
    <div class="hełm-2">
      <img src="items/helm_rycerz.png"
      <?php if(isset($items_data['helm_rycerz'])): ?>
        data-price="<?= htmlspecialchars($items_data['helm_rycerz']['price']) ?>"
        data-hp_bonus="<?= htmlspecialchars($items_data['helm_rycerz']['hp_bonus']) ?>"
        data-damage_bonus="<?= htmlspecialchars($items_data['helm_rycerz']['damage_bonus']) ?>"
        data-defense_bonus="<?= htmlspecialchars($items_data['helm_rycerz']['defense_bonus']) ?>"
        data-agility_bonus="<?= htmlspecialchars($items_data['helm_rycerz']['agility_bonus']) ?>"
        data-luck_bonus="<?= htmlspecialchars($items_data['helm_rycerz']['luck_bonus']) ?>"
        data-block_bonus="<?= htmlspecialchars($items_data['helm_rycerz']['block_bonus']) ?>"
      <?php endif; ?>
      >
    </div>
    <div class="hełm-3">
      <img src="items/helm_zolnierz.png"
      <?php if(isset($items_data['helm_zolnierz'])): ?>
        data-price="<?= htmlspecialchars($items_data['helm_zolnierz']['price']) ?>"
        data-hp_bonus="<?= htmlspecialchars($items_data['helm_zolnierz']['hp_bonus']) ?>"
        data-damage_bonus="<?= htmlspecialchars($items_data['helm_zolnierz']['damage_bonus']) ?>"
        data-defense_bonus="<?= htmlspecialchars($items_data['helm_zolnierz']['defense_bonus']) ?>"
        data-agility_bonus="<?= htmlspecialchars($items_data['helm_zolnierz']['agility_bonus']) ?>"
        data-luck_bonus="<?= htmlspecialchars($items_data['helm_zolnierz']['luck_bonus']) ?>"
        data-block_bonus="<?= htmlspecialchars($items_data['helm_zolnierz']['block_bonus']) ?>"
      <?php endif; ?>
      >
    </div>

    <div class="kowal"><img src="items/bombie.png" alt="Kowal"></div>
</div>


<div id="tooltip"></div>

<script src="tooltip_kowal.js"></script>

</body>
</html>
