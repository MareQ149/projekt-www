<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Nie jesteś zalogowany']);
    exit();
}

if (!isset($_POST['item_id']) || empty($_POST['item_id'])) {
    echo json_encode(['success' => false, 'message' => 'Nie podano przedmiotu']);
    exit();
}

$user_id = $_SESSION['user_id'];
$item_id = (int) $_POST['item_id'];

//Połączenie z bd
$conn = new mysqli("localhost", "root", "", "projekt_www");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą']);
    exit();
}

//cena itemu
$stmt = $conn->prepare("SELECT price FROM items WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Przedmiot nie istnieje']);
    exit();
}
$item = $result->fetch_assoc();
$price = (int) $item['price'];
$stmt->close();

//kredytki gracza
$stmt = $conn->prepare("SELECT credits FROM postacie WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Postać nie znaleziona']);
    exit();
}
$user = $result->fetch_assoc();
$credits = (int) $user['credits'];
$stmt->close();

//Czy stac gracza
if ($credits < $price) {
    echo json_encode(['success' => false, 'message' => 'Nie masz wystarczająco kredytów']);
    exit();
}

//Pierwszy pusty slot inv
$stmt = $conn->prepare("
    SELECT slot 
    FROM inventory 
    WHERE user_id = ? AND item_id IS NULL AND slot REGEXP '^slot[0-9]+$'
    ORDER BY CAST(SUBSTRING(slot, 5) AS UNSIGNED)
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$freeSlot = null;
if ($row = $result->fetch_assoc()) {
    $freeSlot = $row['slot'];
}
$stmt->close();


if ($freeSlot === null) {
    echo json_encode(['success' => false, 'message' => 'Brak wolnych slotów w ekwipunku']);
    exit();
}

//item do pustego slotu
$stmt = $conn->prepare("UPDATE inventory SET item_id = ? WHERE user_id = ? AND slot = ?");
$stmt->bind_param("iis", $item_id, $user_id, $freeSlot);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Błąd przy dodawaniu przedmiotu']);
    exit();
}
$stmt->close();

//odejmowanie kredytkow
$newCredits = $credits - $price;
$stmt = $conn->prepare("UPDATE postacie SET credits = ? WHERE user_id = ?");
$stmt->bind_param("ii", $newCredits, $user_id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Błąd przy aktualizacji kredytów']);
    exit();
}
$stmt->close();

$conn->close();

echo json_encode([
    'success' => true,
    'message' => "Przedmiot został zakupiony i dodany do $freeSlot. Pozostałe kredyty: $newCredits"
]);
?>
