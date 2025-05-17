<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Brak dostępu']);
    exit();
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "projekt_www");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Błąd połączenia']);
    exit();
}

// Pobierz bonusy z przedmiotów
$sql = "SELECT i.hp_bonus, i.damage_bonus, i.defense_bonus, i.agility_bonus, i.luck_bonus, i.block_bonus
        FROM inventory inv
        JOIN items i ON inv.item_id = i.id
        WHERE inv.user_id = ? AND inv.status = 'equipped'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$stats = [
    'hp' => 0,
    'damage' => 0,
    'defense' => 0,
    'agility' => 0,
    'luck' => 0,
    'block' => 0,
    'credits' => 0
];

while ($row = $res->fetch_assoc()) {
    $stats['hp'] += (int)$row['hp_bonus'];
    $stats['damage'] += (int)$row['damage_bonus'];
    $stats['defense'] += (int)$row['defense_bonus'];
    $stats['agility'] += (int)$row['agility_bonus'];
    $stats['luck'] += (int)$row['luck_bonus'];
    $stats['block'] += (int)$row['block_bonus'];
}

// Pobierz kredyty użytkownika
$stmt2 = $conn->prepare("SELECT credits FROM users WHERE id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
if ($row = $res2->fetch_assoc()) {
    $stats['credits'] = (int)$row['credits'];
}

echo json_encode(['success' => true, 'stats' => $stats]);

$conn->close();
