<?php
require './be/connection.php';
$q = mysqli_query($conn, 'SELECT * FROM games');
while ($r = mysqli_fetch_assoc($q)) {
    echo "Game: " . $r['game_name'] . " (ID: " . $r['game_id'] . ") Price: " . $r['ticket_price'] . "\n";
}
$q2 = mysqli_query($conn, "SELECT * FROM results ORDER BY id DESC LIMIT 5");
while ($r = mysqli_fetch_assoc($q2)) {
    echo "Result: Slot " . $r['time_slot_id'] . " Game " . $r['game_id'] . " No " . $r['result_number'] . "\n";
}
?>