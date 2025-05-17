<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Nie zalogowano']);
    exit();
}
$user_id = $_SESSION['user_id'];
$from_slot = $_POST['from_slot'] ?? '';
$to_slot = $_POST['to_slot'] ?? '';
$item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
if (!$from_slot || !$to_slot || !$item_id) {
    echo json_encode(['success' => false, 'message' => 'Niepełne dane']);
    exit();
}
$conn = new mysqli("localhost", "root", "", "projekt_www");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Błąd połączenia']);
    exit();
}
$stmt = $conn->prepare("SELECT slot_type FROM items WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Nie znaleziono itemu']);
    exit();
}
$item = $result->fetch_assoc();
$item_slot_type = $item['slot_type'];
if (strpos($to_slot, 'slot') !== 0) { 
    if ($to_slot !== $item_slot_type) {
        echo json_encode(['success' => false, 'message' => 'Item nie pasuje do tego slotu']);
        exit();
    }
    $new_status = 'equipped';
} else {
    $new_status = 'inventory';
}
$stmt_check = $conn->prepare("SELECT item_id FROM inventory WHERE user_id = ? AND slot = ?");
$stmt_check->bind_param("is", $user_id, $to_slot);
$stmt_check->execute();
$res_check = $stmt_check->get_result();
if ($res_check->num_rows > 0) {
    $row = $res_check->fetch_assoc();
    $item_in_to_slot = $row['item_id'];
    if ($item_in_to_slot !== null) {
        $stmt2 = $conn->prepare("SELECT slot_type FROM items WHERE id = ?");
        $stmt2->bind_param("i", $item_in_to_slot);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        if ($res2->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Nie znaleziono itemu w celu zamiany']);
            exit();
        }
        $item_to_slot = $res2->fetch_assoc();
        $to_slot_type = $item_to_slot['slot_type'];
        $stmt2->close();
        if ($to_slot_type !== $item_slot_type) {
            echo json_encode(['success' => false, 'message' => 'Ten slot jest zajęty innym typem itemu']);
            exit();
        }
        $stmt3 = $conn->prepare("SELECT item_id, status FROM inventory WHERE user_id = ? AND slot = ?");
        $stmt3->bind_param("is", $user_id, $from_slot);
        $stmt3->execute();
        $res3 = $stmt3->get_result();
        if ($res3->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Nie znaleziono itemu w from_slot']);
            exit();
        }
        $from_item = $res3->fetch_assoc();
        $stmt3->close();
        $stmt4 = $conn->prepare("SELECT item_id, status FROM inventory WHERE user_id = ? AND slot = ?");
        $stmt4->bind_param("is", $user_id, $to_slot);
        $stmt4->execute();
        $res4 = $stmt4->get_result();
        if ($res4->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Nie znaleziono itemu w to_slot']);
            exit();
        }
        $to_item = $res4->fetch_assoc();
        $stmt4->close();
        $stmt_update = $conn->prepare("UPDATE inventory SET item_id = ?, status = ? WHERE user_id = ? AND slot = ?");
        $stmt_update->bind_param("siss", $to_item['item_id'], $to_item['status'], $user_id, $from_slot);
        $stmt_update->execute();
        $stmt_update->bind_param("siss", $from_item['item_id'], $new_status, $user_id, $to_slot);
        $stmt_update->execute();
        $stmt_update->close();
        echo json_encode(['success' => true]);
        $conn->close();
        exit();
    }
}
$stmt_clear = $conn->prepare("UPDATE inventory SET item_id = NULL, status = 'empty' WHERE user_id = ? AND slot = ?");
$stmt_clear->bind_param("is", $user_id, $from_slot);
$stmt_clear->execute();
$stmt_clear->close();
$stmt_check = $conn->prepare("SELECT id FROM inventory WHERE user_id = ? AND slot = ?");
$stmt_check->bind_param("is", $user_id, $to_slot);
$stmt_check->execute();
$res_check = $stmt_check->get_result();
if ($res_check->num_rows > 0) {
    $stmt_update = $conn->prepare("UPDATE inventory SET item_id = ?, status = ? WHERE user_id = ? AND slot = ?");
    $stmt_update->bind_param("isis", $item_id, $new_status, $user_id, $to_slot);
    $stmt_update->execute();
    $stmt_update->close();
} else {
    $stmt_insert = $conn->prepare("INSERT INTO inventory (user_id, item_id, slot, status) VALUES (?, ?, ?, ?)");
    $stmt_insert->bind_param("iiss", $user_id, $item_id, $to_slot, $new_status);
    $stmt_insert->execute();
    $stmt_insert->close();
}
$stmt_check->close();
$conn->close();
echo json_encode(['success' => true]);
