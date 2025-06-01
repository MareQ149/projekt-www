<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Brak sesji']);
    exit;
}

$user_id = $_SESSION['user_id'];
$slot = $_POST['slot'] ?? '';
$item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

if (!$slot || !$item_id) {
    echo json_encode(['success' => false, 'message' => 'Niepoprawne dane wejściowe']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "projekt_www");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych']);
    exit;
}

// Sprawdź czy user ma ten item w tym slocie
$stmt = $conn->prepare("SELECT item_id FROM inventory WHERE user_id = ? AND slot = ?");
$stmt->bind_param("is", $user_id, $slot);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row || (int)$row['item_id'] !== $item_id) {
    echo json_encode(['success' => false, 'message' => 'Przedmiot nie znajduje się w podanym slocie']);
    $conn->close();
    exit;
}

// Usuń item (ustaw na NULL)
$stmt = $conn->prepare("UPDATE inventory SET item_id = NULL WHERE user_id = ? AND slot = ?");
$stmt->bind_param("is", $user_id, $slot);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);

$conn->close();
?>
