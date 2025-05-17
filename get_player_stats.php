<?php
header('Content-Type: application/json');
session_start();

$conn = new mysqli("localhost", "root", "", "projekt_www");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Błąd połączenia z bazą"]);
    exit();
}

// Tu wpisz sposób pobrania user_id z sesji lub innego mechanizmu
$user_id = $_SESSION['user_id'] ?? 1;

$sql = "
SELECT 
  p.hp, p.damage, p.defense, p.agility, p.luck, p.block,
  COALESCE(SUM(ib.hp_bonus), 0) AS hp_bonus,
  COALESCE(SUM(ib.damage_bonus), 0) AS damage_bonus,
  COALESCE(SUM(ib.defense_bonus), 0) AS defense_bonus,
  COALESCE(SUM(ib.agility_bonus), 0) AS agility_bonus,
  COALESCE(SUM(ib.luck_bonus), 0) AS luck_bonus,
  COALESCE(SUM(ib.block_bonus), 0) AS block_bonus
FROM postacie p
LEFT JOIN inventory i ON p.user_id = i.user_id AND i.status = 'equipped'
LEFT JOIN item_bonuses ib ON i.item_id = ib.item_id
WHERE p.user_id = ?
GROUP BY p.user_id, p.hp, p.damage, p.defense, p.agility, p.luck, p.block
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $finalStats = [
        "hp" => (int)$row['hp'] + (int)$row['hp_bonus'],
        "dmg" => (int)$row['damage'] + (int)$row['damage_bonus'],
        "def" => (int)$row['defense'] + (int)$row['defense_bonus'],
        "agility" => (int)$row['agility'] + (int)$row['agility_bonus'],
        "luck" => (int)$row['luck'] + (int)$row['luck_bonus'],
        "block" => (int)$row['block'] + (int)$row['block_bonus'],
    ];

    echo json_encode($finalStats);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Nie znaleziono postaci"]);
}

$conn->close();
