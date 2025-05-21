<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projekt_www";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych']);
    exit();
}

$user = $_POST['rusername'] ?? '';
$passRaw = $_POST['rpassword'] ?? '';
$rank = 'gracz';

if (empty($user) || empty($passRaw)) {
    echo json_encode(['success' => false, 'message' => 'Proszę podać login i hasło']);
    exit();
}

$pass = password_hash($passRaw, PASSWORD_DEFAULT);

// Sprawdzenie czy użytkownik już istnieje
$stmt = $conn->prepare("SELECT id FROM uzytkownicy WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Użytkownik o takiej nazwie już istnieje']);
    exit();
}
$stmt->close();

// Dodanie użytkownika
$stmt = $conn->prepare("INSERT INTO uzytkownicy (username, password, rank) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $user, $pass, $rank);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Błąd podczas rejestracji: ' . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit();
}
$user_id = $conn->insert_id;
$stmt->close();

// Dodaj 6 slotów ekwipunku
$equipmentSlots = ['helm', 'napiersnik', 'buty', 'bron', 'tarcza', 'trinket'];
foreach ($equipmentSlots as $slot) {
    $stmt = $conn->prepare("INSERT INTO inventory (user_id, slot, item_id) VALUES (?, ?, NULL)");
    $stmt->bind_param("is", $user_id, $slot);
    $stmt->execute();
    $stmt->close();
}

// Dodaj 10 slotów slot1 - slot10
for ($i = 1; $i <= 10; $i++) {
    $slot = 'slot' . $i;
    $stmt = $conn->prepare("INSERT INTO inventory (user_id, slot, item_id) VALUES (?, ?, NULL)");
    $stmt->bind_param("is", $user_id, $slot);
    $stmt->execute();
    $stmt->close();
}

// Statystyki postaci (domyślne)
$stmt = $conn->prepare("INSERT INTO postacie (user_id) VALUES (?)");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

$conn->close();
echo json_encode(['success' => true, 'message' => 'Rejestracja zakończona powodzeniem']);
exit();
