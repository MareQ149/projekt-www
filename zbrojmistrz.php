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
        <div class="bron-1"><img src="items/miecz.png"></div>
        <div class="bron-2"><img src="items/mlot.png"></div>
        <div class="bron-3"><img src="items/maczuga.png"></div>
        <div class="tarcze">Tarcze</div>
        <div class="tarcza-1"><img src="items/tarcza_wiking.png"></div>
        <div class="tarcza-2"><img src="items/tarcza_rycerz.png"></div>
        <div class="tarcza-3"><img src="items/tarcza_zolnierz.png"></div>
        <div class="trinkety">Trinkety</div>
        <div class="trinket-1"><img src="items/znak.png"></div>
        <div class="trinket-2"><img src="items/flacha.png"></div>
        <div class="trinket-3"><img src="items/bombie.png"></div>
        <div class="zbrojmistrz"><img src="items/bombie.png"></div>
    </div>
</body>
</html>