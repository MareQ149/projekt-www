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

// Pobieranie danych z formularza logowania
$user = $_POST['lusername'];
$pass = $_POST['lpassword'];

// Sprawdzanie, czy użytkownik o podanym loginie istnieje
$stmt = $conn->prepare("SELECT password FROM uzytkownicy WHERE username = ?");
$stmt->bind_param("s", $user); // 's' oznacza typ danych 'string'
$stmt->execute();
$result = $stmt->get_result();

// Jeśli użytkownik istnieje
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $hash = $row['password']; // Pobieramy hasło zapisane w bazie

    // Weryfikacja, czy podane hasło zgadza się z hasłem zapisanym w bazie
    if (password_verify($pass, $hash)) {
        // Jeśli logowanie udane
        echo "<script>
                alert('Udało się zalogować');
                window.location.href = 'index.html'; // Przekierowanie na stronę główną
              </script>";
    } else {
        // Jeśli hasło jest błędne
        echo "<script>
                alert('Błędne hasło');
                window.location.href = 'index.html'; // Przekierowanie na stronę główną
              </script>";
    }
} else {
    // Jeśli użytkownik o podanym loginie nie istnieje
    echo "<script>
            alert('Nie znaleziono użytkownika');
            window.location.href = 'index.html'; // Przekierowanie na stronę główną
          </script>";
}

// Zamknięcie zapytania i połączenia
$stmt->close();
$conn->close();
?>
