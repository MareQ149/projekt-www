<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projekt_www";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}


$user = $_POST['lusername'];
$pass = $_POST['lpassword'];


$stmt = $conn->prepare("SELECT password FROM uzytkownicy WHERE username = ?");
$stmt->bind_param("s", $user); 
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $hash = $row['password'];

    
    if (password_verify($pass, $hash)) {
        
        echo "<script>
                alert('Udało się zalogować');
                window.location.href = 'stronka.html'; // Przekierowanie na stronę główną
              </script>";
    } else {
        
        echo "<script>
                alert('Błędne hasło');
                window.location.href = 'index.html'; 
              </script>";
    }
} else {
    echo "<script>
            alert('Nie znaleziono użytkownika');
            window.location.href = 'index.html'; 
          </script>";
}


$stmt->close();
$conn->close();
?>
