
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rajarani Mohini</title>
    <script>
        if(!localStorage.getItem("loginInfo")){
            window.location = './Login.php'
        }
    </script>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet"> -->
    <?php require_once './Assets/css.php'; ?>
    <style>
        .bg-body-css {
            background-color: #f1eeab;
        }

        .p-form-summ {
            display: flex;
            justify-content: space-around;
        }

        .backg {
            background: #f5f558;
        }

        table {
            background-color: #f1eeab;
        }

        th,
        td {
            border: 2px solid #333;
        }

        th {
            border-left: none;
            /* Remove left border from header cells */
            border-right: none;
            /* Remove right border from header cells */
        }

        .table th,
        .table td {
            border-color: black;
            text-align: center; 
        }

        .table>:not(caption)>*>* {
            padding: 0.2rem 0.5rem;
        }
       .link-fix a{
        color:white;
        text-decoration: none;
       }
       .b-b-b:last-child{
        border-bottom: 2px solid black;
       }
    </style>
</head>

<body class="bg-body-css">
    <div class="backg">
        <div class="p-form-summ pt-2 pb-2">
            <h4>Purchase Summary From <?php echo '17-7-2024'; ?> To <?php echo '17-7-2024'; ?></h4>
            <div><button type="button" class="btn btn-danger link-fix"><a href="./games.php">Close</a></button></div>
        </div>
    </div>
    <div class="d-flex justify-content-between mt-2">
        <!-- <h4>26-7-2024 7:56</h4> -->
        <div class="d-flex gap-5">
            <h3>Opening Points</h3>
            <h3 id="opning_points">0.00</h3>
        </div>
    </div>
    <div class="mt-2 table-responsive">
        <table class="table" cellspacing="5px">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Purchase Points</th>
                    <th>Gift Points</th>
                    <th>Net Points</th>
                    <th>Balance Point</th>
                </tr>
            </thead>
            <tbody id="pur-fill-opp">
                <!-- <tr>
                    <td>17-7-2024</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>6.48</td>
                </tr>
                <tr>
                    <td>18-7-2024</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>6.48</td>
                </tr>
                <tr>
                    <td>19-7-2024</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>6.48</td>
                </tr>
                <tr>
                    <td>20-7-2024</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>6.48</td>
                </tr>
                <tr>
                    <td>21-7-2024</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>6.48</td>
                </tr>
                <tr>
                    <td>22-7-2024</td>
                    <td>52.25</td>
                    <td>0.00</td>
                    <td>52.25</td>
                    <td>-45.77</td>
                </tr>
                <tr>
                    <td>23-7-2024</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>-45.77</td>
                </tr> -->
            </tbody>
        </table>
    </div>

</body>

<?php require_once './Assets/js.php'; ?>
<script type="module" src="./urls.js"></script>
<script tyep="module">
    (function FillPurchaseSummary(){
        // data = [
        //     {
        //         date:"1-1-2024",
        //         purchase_points:"1",
        //         gift_points:"1",
        //         net_points:"1",
        //         balance_points:"1",
        //     },
        //     {
        //         date:"1-1-2024",
        //         purchase_points:"1",
        //         gift_points:"1",
        //         net_points:"1",
        //         balance_points:"1",
        //     },
        //     {
        //         date:"1-1-2024",
        //         purchase_points:"1",
        //         gift_points:"1",
        //         net_points:"1",
        //         balance_points:"1",
        //     },
        //     {
        //         date:"1-1-2024",
        //         purchase_points:"1",
        //         gift_points:"1",
        //         net_points:"1",
        //         balance_points:"1",
        //     },
        //     {
        //         date:"1-1-2024",
        //         purchase_points:"1",
        //         gift_points:"1",
        //         net_points:"1",
        //         balance_points:"1",
        //     },
        //     {
        //         date:"1-1-2024",
        //         purchase_points:"1",
        //         gift_points:"1",
        //         net_points:"1",
        //         balance_points:"1",
        //     },
        // ];
        data =JSON.parse(localStorage.getItem('purchase_summary'))
        document.querySelector("#opning_points").innerHTML = data[0].opening_balance;
        data.forEach(obj => {
                    document.querySelector("#pur-fill-opp").insertAdjacentHTML("beforeend", `
                    <tr class="b-b-b">
                        <td>${obj.date}</td>
                        <td>${obj.purchase_pts}</td>
                        <td>${obj.gift_pts}</td>
                        <td>${obj.net_pts}</td>
                        <td>${obj.balance_pts}</td>
                    </tr>
                    `);
                });
    })()
</script>

</html>