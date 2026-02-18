<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

if (!$email || !$password) {
    echo json_encode(["error" => "Missing credentials"]);
    exit;
}

$query = "SELECT * FROM users WHERE email=?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo json_encode(["error" => "User not found"]);
    exit;
}

$row = mysqli_fetch_assoc($result);

if (!password_verify($password, $row['PASSWORD'])) {
    echo json_encode(["error" => "Wrong password"]);
    exit;
}

echo json_encode([
    "id" => $row['id'],        // 🔥 IMPORTANT
    "name" => $row['name'],
    "email" => $row['email'],
    "phone" => $row['phone']
]);
?>
