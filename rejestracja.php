<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projekt_www";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$user = $_POST['rusername'] ?? '';
$passRaw = $_POST['rpassword'] ?? '';
$rank = 'gracz';

if (empty($user) || empty($passRaw)) {
    header("Location: index.html");
    exit();
}

$pass = password_hash($passRaw, PASSWORD_DEFAULT);

// Sprawdź, czy użytkownik już istnieje
$stmt = $conn->prepare("SELECT id FROM uzytkownicy WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt->close();
    $conn->close();
    header("Location: index.html");
    exit();
}
$stmt->close();

// Dodaj użytkownika
$stmt = $conn->prepare("INSERT INTO uzytkownicy (username, password, rank) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $user, $pass, $rank);

if ($stmt->execute()) {
    $user_id = $conn->insert_id;
    $stmt->close();

    // Inicjalizacja pustych slotów
    $slots = ['helm', 'napiersnik', 'buty', 'bron', 'tarcza', 'trinket'];
    for ($i = 1; $i <= 10; $i++) {
        $slots[] = 'slot' . $i;
    }

    // Poprawiony INSERT do inventory z kolejnością: user_id, slot, item_id
    $stmt2 = $conn->prepare("INSERT INTO inventory (user_id, slot, item_id) VALUES (?, ?, NULL)");
    foreach ($slots as $slot) {
        $stmt2->bind_param("is", $user_id, $slot);
        $stmt2->execute();
    }
    $stmt2->close();

    // Wstaw statystyki postaci – tylko user_id, reszta z DEFAULT
    $stmt3 = $conn->prepare("INSERT INTO postacie (user_id) VALUES (?)");
    $stmt3->bind_param("i", $user_id);
    $stmt3->execute();
    $stmt3->close();

    $conn->close();
    header("Location: index.html");
    exit();

} else {
    echo "Błąd podczas rejestracji: " . $stmt->error;
    $stmt->close();
    $conn->close();
}
?>
