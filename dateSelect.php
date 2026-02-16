<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rajarani Mohini</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/smoothness/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <script>
        if(!localStorage.getItem("loginInfo")){
            window.location = './Login.php'
        }
    </script>
    <?php require_once './Assets/css.php'; ?>
    <style>
        .p-summary-header{
            width: 80%;
            margin: 10px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }
        .p-summary-header-parent{
            background: #d7d722cf;
        }
        .p-summary-dates{
            width:85%;
            padding: 15px;
            margin: 10px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0, 0, 0, 0.3);
            flex-wrap: wrap ;
        }
        .p-summary-div-container{
            background: white;
            padding: 5px;
            border-radius: 7px;
            font-weight: 600;
            width: 19%;
            text-align: center;
        }
        .date-picker-div{
            width: 80%;
            margin: 10px auto;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }
        .sub-btn-end{
            width: 80%;
            margin: 10px auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .bg-body-css{
            background-color: #f1eeab;
        }
        #shhowErrors{
            display: flex;
            align-items: center;
            justify-content: center;
            color: red;
        }
        .link-fix a{
            color:white;
            text-decoration: none;
        }
    </style>
</head>
<body>
<body class="bg-body-css">
    <div class="p-summary-header-parent">
        <div class="p-summary-header">
            <h2>Select Date</h2>
            <div><button type="button" class="btn btn-danger link-fix"><a href="./games.php">Close</a></button></div>
        </div>
    </div>
    <div>
    </div>
    <div class="date-picker-div">
        <div><input placeholder="DD-MM-YYYY"  id = "to_date" class="form-control"></div>
    </div>
    <div>
        <div id="shhowErrors"></div>
        <div class="sub-btn-end">
            <button type="button" class="btn btn-success" id = "okbtn">OK</button>
        </div>
    </div>
</body>
</body>
<?php require_once './Assets/js.php'; ?>
<script>
    const GIFT_EVENT_CODE_URL = 'http://localhost/mohini/public_html/be/gift_events_code.php';
    const okButton = document.querySelector('#okbtn')
    const toDate = document.querySelector('#to_date')
    let sDateForCompTo = null;

    $( "#to_date" ).datepicker({
        altFormat: "dd-mm-yy",
        dateFormat: 'dd/mm/yy', 
    });
    let to_date = null;


    $("#to_date").on('change',()=>{
        to_date = toDate.value 
        // Validation for future date prevent
            const today = new Date();
            const [day, month, year] = to_date.split('/').map(Number);
            const selectedDate = new Date(year, month - 1, day); // Create date object (month is 0-indexed)
            sDateForCompTo = selectedDate;
            const selectedDate2 = new Date(to_date); 
            const thirtyDaysAgo = new Date(today);
            thirtyDaysAgo.setDate(today.getDate() - 30);
            
            today.setHours(0, 0, 0, 0); // Set time to midnight for comp
            console.log("Selected Date : ",selectedDate);
            console.log("Selected Date : ",today);
            if ((selectedDate > today)) {
                document.getElementById('shhowErrors').textContent = "The selected date cannot be in the future.";
            } 
            // else if(selectedDate < thirtyDaysAgo){
            //     document.getElementById('shhowErrors').textContent = "Please select a date within the last 30 days.";
            // }
            // Extra condition adding
            // else if(selectedDate){

            // }
            // Extra condition ended here
            else {
                console.log(to_date);
                document.getElementById('shhowErrors').textContent = "";
             }
        
    })

    toDate.addEventListener('change',()=>{
        to_date = toDate.value 
        console.log("changed2",to_date);
    })

    function changeDate(x){
        toDate.value = x.value
        
        //Updating the dates here
        to_date = x.value
    }
    okButton.addEventListener('click',async ()=>{
        if(to_date==null){
            document.getElementById('shhowErrors').textContent = "Please select the dates";
            return;
        }

        const dateObj = {
            "date":to_date.replace(/\//g, "-"),
        }
        try {
           const p_res = await  fetch(GIFT_EVENT_CODE_URL,{
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dateObj)
            });
            const p_resp = await p_res.json();
            console.log('>>>>>>>>>',p_resp);
            
            if(p_res.ok){
                localStorage.setItem("giftEventCode",JSON.stringify(p_resp.data))
                window.location = './GiftEventCode.php'
            } else {
                alert(p_resp.msg)
            }
        } catch (error) {
            alert(error.msg)
        }
    })


</script>
</html>