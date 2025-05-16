<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "projekt_www");
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$sql = "
SELECT inv.slot, i.photo, i.id as item_id, i.name
FROM inventory inv
JOIN items i ON inv.item_id = i.id
WHERE inv.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$slots = [];
while ($row = $result->fetch_assoc()) {
    $slots[$row['slot']] = [
        'photo' => $row['photo'],
        'item_id' => $row['item_id'],
        'name' => $row['name']
    ];
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gra - Ekwipunek</title>
    <link rel="stylesheet" href="style2.css" />
</head>
<body>
<header>
    <img id="logo" src="photos/logo.jpg" alt="logo" />
    <img id="napis" src="photos/napis.png" alt="yasznog" />
</header>

<nav class="menu-wrapper">
    <button id="menuToggle">☰ Menu</button>
    <div id="dropdownMenu" class="hidden">
    <ul>
        <li><a href="stronka.php">Profil</a></li>
        <li><a href="zbrojmistrz.html">Zbrojmistrz</a></li>
        <li><a href="kowal.html">Kowal</a></li>
        <li><a href="walka.html">Walka</a></li>
        <li><a href="index.html">Wyloguj</a></li>
    </ul>
    </div>
</nav>
<script>
  const toggleButton = document.getElementById("menuToggle");
  const dropdownMenu = document.getElementById("dropdownMenu");

    toggleButton.addEventListener("click", () => {
        dropdownMenu.classList.toggle("show");
  });
</script>

<div id="ekwipunek">
    <section id="lewy">
        <div id="helm" class="slot">
            <?php if (!empty($slots['helm'])): ?>
                <img src="items/<?php echo htmlspecialchars($slots['helm']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($slots['helm']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$slots['helm']['item_id']; ?>" />
            <?php endif; ?>
        </div>
        <div id="napiersnik" class="slot">
            <?php if (!empty($slots['napiersnik'])): ?>
                <img src="items/<?php echo htmlspecialchars($slots['napiersnik']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($slots['napiersnik']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$slots['napiersnik']['item_id']; ?>" />
            <?php endif; ?>
        </div>
        <div id="buty" class="slot">
            <?php if (!empty($slots['buty'])): ?>
                <img src="items/<?php echo htmlspecialchars($slots['buty']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($slots['buty']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$slots['buty']['item_id']; ?>" />
            <?php endif; ?>
        </div>
    </section>

    <section id="glowny">
        <div id="postac">
            <img id="awatar" src="photos/logo.jpg" alt="awatar" />
        </div>
    </section>

    <section id="prawy">
        <div id="bron" class="slot">
            <?php if (!empty($slots['bron'])): ?>
                <img src="items/<?php echo htmlspecialchars($slots['bron']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($slots['bron']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$slots['bron']['item_id']; ?>" />
            <?php endif; ?>
        </div>
        <div id="tarcza" class="slot">
            <?php if (!empty($slots['tarcza'])): ?>
                <img src="items/<?php echo htmlspecialchars($slots['tarcza']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($slots['tarcza']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$slots['tarcza']['item_id']; ?>" />
            <?php endif; ?>
        </div>
        <div id="trinket" class="slot">
            <?php if (!empty($slots['trinket'])): ?>
                <img src="items/<?php echo htmlspecialchars($slots['trinket']['photo']); ?>"
                     alt="<?php echo htmlspecialchars($slots['trinket']['name']); ?>"
                     draggable="true"
                     data-itemid="<?php echo (int)$slots['trinket']['item_id']; ?>" />
            <?php endif; ?>
        </div>
    </section>
</div>

<div id="inwentarz">
    <section id="inventory">
        <?php for ($i = 1; $i <= 5; $i++): 
            $key = 'slot' . $i;
        ?>
            <div id="<?php echo $key; ?>" class="slot">
                <?php if (!empty($slots[$key])): ?>
                    <img src="items/<?php echo htmlspecialchars($slots[$key]['photo']); ?>"
                         alt="<?php echo htmlspecialchars($slots[$key]['name']); ?>"
                         draggable="true"
                         data-itemid="<?php echo (int)$slots[$key]['item_id']; ?>" />
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </section>
</div>

<footer>
    <p>WSZELKIE PRAWA ZASTRZEŻONE&copy;</p>
</footer>

<script>
document.querySelectorAll('.slot img').forEach(img => {
    img.addEventListener('dragstart', e => {
        e.dataTransfer.setData('text/plain', JSON.stringify({
            itemId: e.target.dataset.itemid,
            fromSlot: e.target.parentElement.id
        }));
    });
});

document.querySelectorAll('.slot').forEach(slot => {
    slot.addEventListener('dragover', e => {
        e.preventDefault();
        slot.style.outline = '2px solid yellow';
    });
    slot.addEventListener('dragleave', e => {
        slot.style.outline = '';
    });
    slot.addEventListener('drop', e => {
        e.preventDefault();
        slot.style.outline = '';

        const data = JSON.parse(e.dataTransfer.getData('text/plain'));
        const toSlot = slot.id;
        const fromSlot = data.fromSlot;
        const itemId = data.itemId;

        
        fetch('update_slot.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                from_slot: fromSlot,
                to_slot: toSlot,
                item_id: itemId
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
               
                location.reload();
            } else {
                alert('Błąd podczas przesuwania itemu: ' + data.message);
            }
        })
        .catch(() => alert('Błąd sieci'));
    });
});
</script>

</body>
</html>
