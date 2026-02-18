<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

$conn = mysqli_connect(
    "sql205.infinityfree.com",
    "YOUR_DB_USERNAME",
    "YOUR_DB_PASSWORD",
    "YOUR_DB_NAME"
);

if (!$conn) {
    die(json_encode(["error" => "Database connection failed"]));
}
?>
