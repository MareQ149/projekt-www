<?php
//polaczenie z baza danych, start sesji, informacja o formacie JSON
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

//czy formularz zostal wypelniony
$user = $_POST['lusername'] ?? '';
$passRaw = $_POST['lpassword'] ?? '';

if (empty($user) || empty($passRaw)) {
    echo json_encode(['success' => false, 'message' => 'Proszę podać login i hasło']);
    exit();
}

//sprawdzenie czy uzytkownik istnieje
$stmt = $conn->prepare("SELECT id, password FROM uzytkownicy WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $hash = $row['password'];

    if (password_verify($passRaw, $hash)) {
        $_SESSION['user_id'] = $row['id'];
        echo json_encode(['success' => true, 'message' => 'Udało się zalogować']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Błędne hasło']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Nie znaleziono użytkownika']);
}

$stmt->close();
$conn->close();
exit();
?>
