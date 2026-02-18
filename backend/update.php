<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$title = $data['title'] ?? null;
$reminder = $data['reminder_time'] ?? null;

if (!$id || !$title) {
    echo json_encode(["error" => "Missing data"]);
    exit;
}

$query = "UPDATE tasks SET title=?, reminder_time=? WHERE id=?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssi", $title, $reminder, $id);
mysqli_stmt_execute($stmt);

echo json_encode(["message" => "Task updated"]);
?>
