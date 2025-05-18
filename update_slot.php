<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Brak sesji']);
    exit;
}

$user_id = $_SESSION['user_id'];

$from_slot = $_POST['from_slot'] ?? '';
$to_slot = $_POST['to_slot'] ?? '';
$item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

if (!$from_slot || !$to_slot || !$item_id) {
    echo json_encode(['success' => false, 'message' => 'Niepoprawne dane wejściowe']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "projekt_www");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych']);
    exit;
}

$equipmentSlots = ['helm', 'napiersnik', 'buty', 'bron', 'tarcza', 'trinket'];

$conn->begin_transaction();

try {
    // 1. Pobierz item_id z from_slot - potwierdzenie, że jest ten przedmiot
    $stmt = $conn->prepare("SELECT item_id FROM inventory WHERE user_id = ? AND slot = ?");
    $stmt->bind_param("is", $user_id, $from_slot);
    $stmt->execute();
    $result = $stmt->get_result();
    $fromRow = $result->fetch_assoc();
    $stmt->close();

    if (!$fromRow || (int)$fromRow['item_id'] !== $item_id) {
        throw new Exception("Przedmiot nie znajduje się w podanym slocie źródłowym");
    }

    // 2. Sprawdź typ przedmiotu, czy pasuje do slotu
    $stmt = $conn->prepare("SELECT slot_type FROM items WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $itemInfo = $result->fetch_assoc();
    $stmt->close();

    if (!$itemInfo) {
        throw new Exception("Nie znaleziono przedmiotu w bazie");
    }

    $itemSlotType = $itemInfo['slot_type'];

    if (in_array($to_slot, $equipmentSlots) && $to_slot !== $itemSlotType) {
        throw new Exception("Przedmiot typu '$itemSlotType' nie pasuje do slotu '$to_slot'");
    }

    // 3. Pobierz item_id z to_slot (może być NULL)
    $stmt = $conn->prepare("SELECT item_id FROM inventory WHERE user_id = ? AND slot = ?");
    $stmt->bind_param("is", $user_id, $to_slot);
    $stmt->execute();
    $result = $stmt->get_result();
    $toRow = $result->fetch_assoc();
    $stmt->close();

    $toSlotItemId = $toRow ? $toRow['item_id'] : null;

    // 4. Zamiana item_id między slotami
    $stmt = $conn->prepare("UPDATE inventory SET item_id = ? WHERE user_id = ? AND slot = ?");
    $stmt->bind_param("iis", $item_id, $user_id, $to_slot);
    $stmt->execute();
    $stmt->close();

    if ($toSlotItemId !== null) {
        $stmt = $conn->prepare("UPDATE inventory SET item_id = ? WHERE user_id = ? AND slot = ?");
        $stmt->bind_param("iis", $toSlotItemId, $user_id, $from_slot);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("UPDATE inventory SET item_id = NULL WHERE user_id = ? AND slot = ?");
        $stmt->bind_param("is", $user_id, $from_slot);
        $stmt->execute();
        $stmt->close();
    }

    // 5. Oblicz statystyki tylko z ekwipunku
    $placeholders = implode(',', array_fill(0, count($equipmentSlots), '?'));

    $sql = "SELECT ib.hp_bonus, ib.damage_bonus, ib.defense_bonus, ib.agility_bonus, ib.luck_bonus, ib.block_bonus
            FROM inventory inv
            JOIN item_bonuses ib ON inv.item_id = ib.item_id
            WHERE inv.user_id = ? AND inv.slot IN ($placeholders) AND inv.item_id IS NOT NULL";

    $stmt = $conn->prepare($sql);
    $types = 'i' . str_repeat('s', count($equipmentSlots));
    $params = array_merge([$user_id], $equipmentSlots);
    $bind_params = [];
    foreach ($params as $key => $value) {
        $bind_params[$key] = &$params[$key];
    }
    array_unshift($bind_params, $types);
    call_user_func_array([$stmt, 'bind_param'], $bind_params);

    $stmt->execute();
    $result = $stmt->get_result();

    $stats = [
        'hp' => 0,
        'damage' => 0,
        'defense' => 0,
        'agility' => 0,
        'luck' => 0,
        'block' => 0
    ];

    while ($row = $result->fetch_assoc()) {
        $stats['hp'] += (int)$row['hp_bonus'];
        $stats['damage'] += (int)$row['damage_bonus'];
        $stats['defense'] += (int)$row['defense_bonus'];
        $stats['agility'] += (int)$row['agility_bonus'];
        $stats['luck'] += (int)$row['luck_bonus'];
        $stats['block'] += (int)$row['block_bonus'];
    }

    $stmt->close();

    $conn->commit();

    $swappedItem = null;
    if ($toSlotItemId !== null) {
        $swappedItem = ['item_id' => (int)$toSlotItemId];
    }

    echo json_encode(['success' => true, 'stats' => $stats, 'swapped_item' => $swappedItem]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Błąd: ' . $e->getMessage()]);
}

$conn->close();
