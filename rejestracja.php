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

// Prosta walidacja
if (empty($user) || empty($passRaw)) {
    header("Location: index.html");
    exit();
}

// Hashowanie hasła
$pass = password_hash($passRaw, PASSWORD_DEFAULT);

// Sprawdzenie czy użytkownik już istnieje
$stmt = $conn->prepare("SELECT id FROM uzytkownicy WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Użytkownik już istnieje, wracamy na start
    $stmt->close();
    $conn->close();
    header("Location: index.html");
    exit();
}
$stmt->close();

// Dodanie nowego użytkownika
$stmt = $conn->prepare("INSERT INTO uzytkownicy (username, password, rank) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $user, $pass, $rank);

if ($stmt->execute()) {
    $user_id = $conn->insert_id;
    $stmt->close();

    // Dodanie pustych slotów do inventory (item_id NULL)
    $slots = ['helm', 'napiersnik', 'buty', 'bron', 'tarcza', 'trinket'];
    for ($i = 1; $i <= 10; $i++) {
        $slots[] = 'slot' . $i;
    }

    $stmt2 = $conn->prepare("INSERT INTO inventory (user_id, item_id, slot) VALUES (?, NULL, ?)");
    foreach ($slots as $slot) {
        $stmt2->bind_param("is", $user_id, $slot);
        $stmt2->execute();
    }
    $stmt2->close();

    // Domyślne statystyki postaci
    $hp = 100;
    $damage = 10;
    $defense = 5;
    $agility = 5;
    $luck = 3;
    $block = 0;
    $credits = 0;

    $stmt3 = $conn->prepare("INSERT INTO postacie (user_id, hp, damage, defense, agility, luck, block, credits) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt3->bind_param("iiiiiiii", $user_id, $hp, $damage, $defense, $agility, $luck, $block, $credits);
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
