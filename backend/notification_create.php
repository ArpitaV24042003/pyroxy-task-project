<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data['phone_number']) ||
    !isset($data['message_content']) ||
    !isset($data['scheduled_time'])
) {
    echo json_encode(["error" => "Missing fields"]);
    exit();
}

$phone = $data['phone_number'];
$message = $data['message_content'];
$time = $data['scheduled_time'];

$sql = "INSERT INTO notifications (phone_number, message_content, scheduled_time)
        VALUES ('$phone', '$message', '$time')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Notification saved"]);
} else {
    echo json_encode(["error" => $conn->error]);
}
?>
