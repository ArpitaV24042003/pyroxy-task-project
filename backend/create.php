<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$title = $data['title'] ?? null;
$user_id = $data['user_id'] ?? null;

if (!$title || !$user_id) {
    echo json_encode(["error" => "Missing data"]);
    exit;
}

$query = "INSERT INTO tasks (title, user_id) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "si", $title, $user_id);
mysqli_stmt_execute($stmt);

echo json_encode(["message" => "Task created"]);
?>
