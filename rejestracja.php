<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projekt_www";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$user = $_POST['rusername'];
$pass = password_hash($_POST['rpassword'], PASSWORD_DEFAULT);
$rank = 'gracz';

$stmt = $conn->prepare("SELECT * FROM uzytkownicy WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: index.html");
    exit();
} else {
    $stmt = $conn->prepare("INSERT INTO uzytkownicy (username, password, rank) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user, $pass, $rank);
    
    if ($stmt->execute()) {
        $newUserId = $conn->insert_id;

        $stmt2 = $conn->prepare("INSERT INTO postacie (user_id, hp, damage, defense, agility, luck, block) VALUES (?, 100, 10, 5, 5, 1, 1)");
        $stmt2->bind_param("i", $newUserId);
        $stmt2->execute();
        $stmt2->close();

        header("Location: index.html");
    } else {
        echo "Błąd: " . $stmt->error;
    }
}

$stmt->close();
$conn->close();
?>
