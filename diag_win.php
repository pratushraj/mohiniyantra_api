<?php
require './be/connection.php';
$q = mysqli_query($conn, "SELECT * FROM user_winnings WHERE game_id = 3 LIMIT 1");
if ($r = mysqli_fetch_assoc($q)) {
    print_r($r);
} else {
    echo "No double winnings found";
}
?>