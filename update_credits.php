<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Nie jesteś zalogowany']);
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['change'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Brak danych']);
    exit;
}

$change = (int)$_POST['change'];

$conn = new mysqli("localhost", "root", "", "projekt_www");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Błąd połączenia z bazą']);
    exit;
}

// Zabezpieczenie - upewnij się, że kredyty nie spadną poniżej 0
$stmt = $conn->prepare("SELECT credits FROM postacie WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($currentCredits);
$stmt->fetch();
$stmt->close();

$newCredits = max(0, $currentCredits + $change);

$stmt = $conn->prepare("UPDATE postacie SET credits = ? WHERE user_id = ?");
$stmt->bind_param("ii", $newCredits, $user_id);
$stmt->execute();

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Nie udało się zaktualizować kredytów']);
}

$stmt->close();
$conn->close();
?>
