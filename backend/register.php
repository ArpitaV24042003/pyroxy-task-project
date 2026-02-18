<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "No data received"]);
    exit;
}

$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);

// Check if user exists
$check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' OR phone='$phone'");

if (mysqli_num_rows($check) > 0) {
    echo json_encode(["error" => "User already exists"]);
    exit;
}

$sql = "INSERT INTO users (name, email, phone, password) 
        VALUES ('$name', '$email', '$phone', '$password')";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        "success" => true,
        "user" => [
            "id" => mysqli_insert_id($conn),
            "name" => $name,
            "email" => $email,
            "phone" => $phone
        ]
    ]);
} else {
    echo json_encode(["error" => mysqli_error($conn)]);
}
?>
