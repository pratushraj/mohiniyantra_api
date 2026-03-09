<?php
require './be/connection.php';
$date = '2026-03-06';
$user_id = 12;
$q = mysqli_query($conn, "SELECT game_id, selected_number, amount, qty, status FROM tickets WHERE user_id = '$user_id' AND ticket_date = '$date' LIMIT 20");
echo "Tickets for $user_id on $date:\n";
while ($r = mysqli_fetch_assoc($q)) {
    print_r($r);
}
?>