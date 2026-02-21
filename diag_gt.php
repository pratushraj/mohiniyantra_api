<?php
require_once('./be/connection.php');
$q = mysqli_query($conn, "SELECT * FROM game_types");
while ($r = mysqli_fetch_assoc($q)) {
    print_r($r);
}
?>