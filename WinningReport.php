<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift Event Table</title>
    <script>
        if (!localStorage.getItem("loginInfo")) {
            window.location = './Login.php'
        }
    </script>
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
            /* border-collapse: separate; */
            /* Important to enable border-spacing */
            /* border-spacing: 10px;/ */
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

        body {
            background: pink;
        }
    </style>
</head>

<body>

    <div class="bg-gift-enent">
        <button class="close-button upcoming-link"><a href="./games.php">Close</a></button>
    </div>
    <div class="hopeDate">
        <h3>Winning Report</h3>
    </div>
    <div class="">
        <table>
            <thead>
                <tr class="main-header">
                    <!-- First header row -->
                    <th class="header-color-ge">Sl No</th>
                    <th class="header-color-ge">Name</th>
                    <th class="header-color-ge">Winning Date</th>
                    <th class="header-color-ge">Game Details</th>
                    <th class="header-color-ge">Winning No</th>
                    <th class="header-color-ge">Amount Won</th>
                </tr>

            </thead>
            <tbody id="insertResponse">
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

<script type="module" src="./urls.js"></script>
<script type="module">
    import {
        WINNING_REPORT_URL
    } from './urls.js';

    (async function fetchAndFill() {
        try {
            const r = await fetch(WINNING_REPORT_URL, {
                mode: 'no-cors',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({id:JSON.parse(localStorage.getItem('loginInfo')).id})
            });
            const r_s = await r.json();
            if (r.ok) {
                const data = r_s.data;
                data.forEach((obj, i) => {
                    document.querySelector("#insertResponse").insertAdjacentHTML("beforeend", `
                    <tr>
                        <td>${i+1}</td>
                        <td>${JSON.parse(localStorage.getItem('loginInfo')).name}</td>
                        <td>${obj.winning_date}</td>
                        <td>${obj.game_name} (${obj.game_type_code}) - ${obj.time}</td>
                        <td>${obj.number_won}</td>
                        <td>${obj.amount}</td>
                    </tr>
                    `);
                });

            } else {
                alert(r_s.msg)
            }
        } catch (error) {
            alert(error.msg)
        }
    })()
</script>

</html>