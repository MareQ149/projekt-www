<?php
//start sesji, polaczenie z db, info o JSON
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
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

//Sprawdzenie czy item jest w slocie
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

//Usuniecie itemu
$stmt = $conn->prepare("UPDATE inventory SET item_id = NULL WHERE user_id = ? AND slot = ?");
$stmt->bind_param("is", $user_id, $slot);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);

$conn->close();
?>
