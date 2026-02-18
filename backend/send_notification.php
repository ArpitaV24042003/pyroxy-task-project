<?php
date_default_timezone_set("Asia/Kolkata");
include "db.php";

$account_sid = getenv("TWILIO_SID");
$auth_token = getenv("TWILIO_AUTH_TOKEN");
$twilio_number = "whatsapp:+14155238886";

$current_time = date("H:i");

$query = "
SELECT * FROM notifications
WHERE TIME(scheduled_time) >= '$current_time:00'
AND TIME(scheduled_time) < DATE_ADD('$current_time:00', INTERVAL 1 MINUTE)
";


$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {

    $to = "whatsapp:+91" . $row['phone'];
    $message = "Reminder: " . $row['title'];

    $url = "https://api.twilio.com/2010-04-01/Accounts/$account_sid/Messages.json";

    $data = [
        'From' => $twilio_number,
        'To' => $to,
        'Body' => $message
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$account_sid:$auth_token");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_exec($ch);
    curl_close($ch);
}

echo "Checked at $current_time";

?>
