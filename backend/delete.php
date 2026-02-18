<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
include 'db.php';

$data = json_decode(file_get_contents("php://input"));

$id = $data->id;

$conn->query("DELETE FROM tasks WHERE id=$id");

echo json_encode(["message" => "Task deleted"]);
?>
