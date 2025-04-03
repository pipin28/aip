<?php
$conn = new mysqli("localhost", "root", "", "your_database_name");

$user_id = $_GET['user_id'];
$sql = "SELECT login_time FROM tbl_login WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'title' => date("H:i", strtotime($row['login_time'])), // show time
        'start' => $row['login_time'],
        'allDay' => false
    ];
}

echo json_encode($events);
