<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <main>

        <?php
            $host = 'localhost';
            $user = 'root';
            $password = '';
            $dbname = 'projekt_www';

            $conn = new mysqli($host, $user, $password, $dbname);

            if ($conn->connect_error) {
                die("Błąd połączenia: " . $conn->connect_error);
            }

            $sql = "SELECT name, photo FROM items ORDER BY RAND() LIMIT 1";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<div>";
                echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
                echo "<img src='items/" . htmlspecialchars($row['photo']) . "' alt='" . htmlspecialchars($row['name']) . "' style='max-width:300px;'>";
                echo "</div>";
            } else {
                echo "Brak itemów w bazie.";
            }

            $conn->close();
        ?>




    </main>
</body>
</html>