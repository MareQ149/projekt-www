<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "projekt_www";

    // Połączenie z bazą danych
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Sprawdzenie, czy połączenie z bazą danych jest udane
    if ($conn->connect_error) {
        die("Błąd połączenia: " . $conn->connect_error);
    }

    // Pobieranie danych z formularza rejestracji
    $user = $_POST['rusername'];
    $pass = password_hash($_POST['rpassword'], PASSWORD_DEFAULT);
    $rank = 'gracz';  // domyślnie 'gracz' jako rank

    // Sprawdzanie, czy użytkownik o podanej nazwie już istnieje w bazie danych
    $stmt = $conn->prepare("SELECT * FROM uzytkownicy WHERE username = ?");
    $stmt->bind_param("s", $user);  // 's' oznacza typ danych 'string'
    $stmt->execute();
    $result = $stmt->get_result();

    // Jeśli użytkownik już istnieje, przekierowanie na stronę główną
    if ($result->num_rows > 0) {
        header("Location: index.html");
        exit();
    } else {
        // Jeśli użytkownik nie istnieje, dodanie nowego użytkownika do bazy danych
        $stmt = $conn->prepare("INSERT INTO uzytkownicy (username, password, rank) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user, $pass, $rank);  // 'sss' oznacza 3 argumenty typu 'string'
        if ($stmt->execute()) {
            header("Location: index.html");
        } else {
            echo "Błąd: " . $stmt->error;
        }
    }

    // Zamknięcie zapytania i połączenia
    $stmt->close();
    $conn->close();
?>
