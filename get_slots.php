<?php
session_start();
if (!isset($_SESSION['user_id'])) exit;
$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "projekt_www");

$sql = "SELECT inv.slot, i.id AS item_id, i.img_url, i.hp_bonus, i.damage_bonus, i.defense_bonus, i.agility_bonus, i.luck_bonus, i.block_bonus
        FROM inventory inv
        JOIN items i ON inv.item_id = i.id
        WHERE inv.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$slots = [];
while ($row = $res->fetch_assoc()) {
    $slots[$row['slot']] = $row;
}

// Zakładamy, że twoje sloty mają ustalone ID
$allSlots = ['helmet', 'armor', 'weapon', 'boots', 'gloves', 'ring', 'inventory1', 'inventory2', 'inventory3'];

foreach ($allSlots as $slot) {
    echo "<div class='slot' id='$slot'>";
    if (isset($slots[$slot])) {
        $item = $slots[$slot];
        echo "<img 
                src='{$item['img_url']}'
                draggable='true'
                data-itemid='{$item['item_id']}'
                data-hp-bonus='{$item['hp_bonus']}'
                data-damage-bonus='{$item['damage_bonus']}'
                data-defense-bonus='{$item['defense_bonus']}'
                data-agility-bonus='{$item['agility_bonus']}'
                data-luck-bonus='{$item['luck_bonus']}'
                data-block-bonus='{$item['block_bonus']}'
             >";
    }
    echo "</div>";
}
