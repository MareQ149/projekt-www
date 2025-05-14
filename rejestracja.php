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
    $ranga = 'gracz';

    $stmt = $conn->prepare("SELECT * FROM uzytkownicy WHERE nazwa = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: index.html");
        exit();
    } else {
        $stmt = $conn->prepare("INSERT INTO uzytkownicy (nazwa, haslo, ranga) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user, $pass, $ranga);
        if ($stmt->execute()) {
            header("Location: index.html");
        } else {
            echo "Błąd: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
?>
