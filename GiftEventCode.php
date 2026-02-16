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
    <div class="hopeDate">
        <span><?php echo date('d-m-Y'); ?></span>
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

<script type="module">

            const data =JSON.parse(localStorage.getItem('giftEventCode')); 
            // const data = [
            //     {
            //         "17:00:00" : ["RM70","RG85","RY85","RS00"]
            //     },
            //     {
            //         "17:30:00" : ["RM80","RG95","RY55","RS10"]
            //     },
            //     {
            //         "18:00:00" : ["RM20","RG35","RY15","RS97"]
            //     },
            //     {
            //         "18:30:00" : ["RM71","RG84","RY81","RS09"]
            //     },
            // ];

            data.forEach((item) => {
                const key = Object.keys(item)[0]; // Get the first (and only) key of the object

                const values = item[key]; // Get the values associated with the key
                document.querySelector("#insertResponse").insertAdjacentHTML("beforeend", `
                    <tr>
                        <td>${key}</td>
                        <td>${values[0]}</td>
                        <td>${values[1]}</td>
                        <td>${values[2]}</td>
                        <td>${values[3]}</td>
                    </tr>
                    `)
            });
                // data.forEach(obj => {
                // document.querySelector("#insertResponse").insertAdjacentHTML("beforeend", `
                //     <tr>
                //         <td>${obj.time}</td>
                //         <td>${obj.rm_codes}</td>
                //         <td>${obj.rg_codes}</td>
                //         <td>${obj.ry_codes}</td>
                //         <td>${obj.rs_codes}</td>
                //     </tr>
                //     `);
                // });

</script>

</html>