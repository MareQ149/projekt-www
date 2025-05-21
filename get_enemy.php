<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "projekt_www");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Błąd połączenia z bazą"]);
    exit();
}

// Pobierz statystyki postaci z parametrów GET
$playerStats = [
    'hp' => isset($_GET['hp']) ? (int)$_GET['hp'] : 50,
    'dmg' => isset($_GET['dmg']) ? (int)$_GET['dmg'] : 10,
    'def' => isset($_GET['def']) ? (int)$_GET['def'] : 5,
    'agility' => isset($_GET['agility']) ? (int)$_GET['agility'] : 5,
    'luck' => isset($_GET['luck']) ? (int)$_GET['luck'] : 5,
    'block' => isset($_GET['block']) ? (int)$_GET['block'] : 0,
];

// Pobierz losowego przeciwnika
$result = $conn->query("SELECT id, name, photo FROM enemies ORDER BY RAND() LIMIT 1");
if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Błąd zapytania"]);
    exit();
}

if ($enemy = $result->fetch_assoc()) {
    // Generuj statystyki przeciwnika na podstawie statystyk gracza (wyrównane +- 10%)
    $randomFactor = fn($val) => max(1, (int)($val * (0.9 + mt_rand(0, 20) / 100)));

    $enemyStats = [
        "hp" => $randomFactor($playerStats['hp']),
        "dmg" => $randomFactor($playerStats['dmg']),
        "def" => $randomFactor($playerStats['def']),
        "agility" => $randomFactor($playerStats['agility']),
        "luck" => $randomFactor($playerStats['luck']),
        "block" => $randomFactor($playerStats['block']),
    ];

    echo json_encode(array_merge($enemy, $enemyStats));
} else {
    http_response_code(404);
    echo json_encode(["error" => "Brak przeciwników"]);
}

$conn->close();
