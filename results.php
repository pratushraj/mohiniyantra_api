<?php 

// $date = date('Y-m-d', strtotime($data['date']));
// $resultCountSql = mysqli_query($conn,"
//     SELECT 
//         MAX(game_type_id) AS game_type_id, 
//         time_slot_id, 
//         MAX(result_date) AS result_date
//     FROM 
//         results
//     WHERE 
//         result_date = '$date'
//         AND game_id = 3
//     GROUP BY 
//         time_slot_id;
// ");
// $events = [];
// if(mysqli_num_rows($resultCountSql) > 0) {
//     while($resultCountRes = mysqli_fetch_assoc($resultCountSql)) {
//         $time_slot_id = $resultCountRes['time_slot_id'];

//         $giftEventsSql = mysqli_query($conn, "SELECT t.time, CONCAT(gt.game_type_code, r.result_number) AS event_code
//             FROM results r
//             LEFT JOIN game_types gt ON gt.game_type_id = r.game_type_id
//             LEFT JOIN time_slots t ON t.time_slot_id = r.time_slot_id
//             WHERE r.time_slot_id = $time_slot_id
//             AND r.result_date = '$date'
//             AND r.game_id = 3");

//             while( $giftEventsRes = mysqli_fetch_assoc($giftEventsSql) ) {
//                 $events[$giftEventsRes['time']][] = $giftEventsRes['event_code'];
//             }
//     }

// }

$events = [
    [
        "17:00:00" => ["RM70", "RG85", "RY85", "RS00"]
    ],
    [
        "17:30:00" => ["RM80", "RG95", "RY55", "RS10"]
    ],
    [
        "18:00:00" => ["RM20", "RG35", "RY15", "RS97"]
    ],
    [
        "18:30:00" => ["RM71", "RG84", "RY81", "RS09"]
    ],
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift Event Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bg-gift-enent {
            background-color: #f5f562;
            padding: 10px;
            display: flex;
            justify-content: end;
        }

        .close-button {
            padding: 4px 15px;
            color: white;
            background-color: red;
            border: none;
            outline: none;
            border-radius: 5px;

        }

        .hopeDate {
            display: flex;
            align-items: center;
            padding-top: 5px;
            justify-content: center;
        }

        /* Table */
        table {
            width: 100%;
            /* border-collapse: collapse; */
            border-collapse: separate;
            /* Important to enable border-spacing */
            border-spacing: 10px;/
        }

        table th,
        table td {
            /* padding: 15px; */
            border: 1px solid black;
            text-align: center;
            background-color: #f5f562;
        }

        /* Styling for the main header */
        .main-header th {
            background-color: #f5f562;
        }

        .header-color-ge {
            color: #f13e3e;
        }

        .upcoming-link a {
            text-decoration: none;
            color: #fff;
        }
        body{
            background: pink;
        }
    </style>
</head>

<body>

    <div class="bg-gift-enent">
        <button class="close-button upcoming-link"><a href="./games.php">Close</a></button>
    </div>
    <div class="">
        <table>
            <thead>
                <tr class="main-header">
                    <!-- First header row -->
                    <th rowspan="1" class="header-color-ge">Gift Event Code</th>
                    <th colspan="4" class="header-color-ge">Rajarani mohini</th>
                </tr>

            </thead>
            <tbody id="insertResponse">
                <?php
                 foreach($events as $event){
                    foreach($event as $key => $val){
                        ?>
                        <tr>
                            <td><?php echo $key; ?></td>
                            <td><?php echo $val[0]; ?></td>
                            <td><?php echo $val[1]; ?></td>
                            <td><?php echo $val[2]; ?></td>
                            <td><?php echo $val[3]; ?></td>
                        </tr>
                        <?php
                    }
                 }
                 ?>
                <!-- <tr>
                    <td>9:15</td>
                    <td>NV61</td>
                    <td>RR58</td>
                    <td>RY99</td>
                    <td>CH06</td>
                </tr> -->

                <!-- Add more rows as needed -->
            </tbody>
        </table>
    </div>
</body>


</html>