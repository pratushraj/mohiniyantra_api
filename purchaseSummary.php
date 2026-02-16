

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

<body class="bg-body-css">
    <div class="p-summary-header-parent">
        <div class="p-summary-header">
            <h2>Purchase Summary</h2>
            <div><button type="button" class="btn btn-danger link-fix"><a href="./games.php">Close</a></button></div>
        </div>
    </div>
    <div>
        <div class="p-summary-dates">
            <?php for($i=9;$i>=0;$i--){ 
                $currentDate = new DateTime();
                $currentDate->modify("-$i day");
                $previousDate = $currentDate->format('d-m-Y');    
            ?>
            <div class="p-summary-div-container mb-2">
                <input type="radio" class="" name="date-chooser" value="<?php echo $previousDate; ?>" onclick="changeDate(this)" id="date_<?php echo $previousDate ?>"  >
                <label for = "date_<?php echo $previousDate ?>"><?php echo $previousDate; ?></label>
            </div>
            <?php } ?>
            <!-- <div class="p-summary-div-container mb-2">
                <input type="radio" class="" name="date-chooser" id="second">
                <label for = 'second'>17-7-2024</label>
            </div>
            <div class="p-summary-div-container mb-2">
                <input type="radio" class="" name="date-chooser" id="third">
                <label for = 'third'>17-7-2024</label>
            </div>
            <div class="p-summary-div-container mb-2">
                <input type="radio" class="" name="date-chooser" id="fourth">
                <label for = 'fourth'>17-7-2024</label>
            </div>
            <div class="p-summary-div-container mb-2">
                <input type="radio" class="" name="date-chooser" id="fifth">
                <label for = 'fifth'>17-7-2024</label>
            </div>
            <div class="p-summary-div-container">
                <input type="radio" class="" name="date-chooser" id="sixth">
                <label for = 'sixth'>17-7-2024</label>
            </div>
            <div class="p-summary-div-container">
                <input type="radio" class="" name="date-chooser" id="seventh">
                <label for = 'seventh'>17-7-2024</label>
            </div>
            <div class="p-summary-div-container">
                <input type="radio" class="" name="date-chooser" id="eighth">
                <label for = 'eighth'>17-7-2024</label>
            </div>
            <div class="p-summary-div-container">
                <input type="radio" class="" name="date-chooser" id="nineth">
                <label for = 'nineth'>17-7-2024</label>
            </div>
            <div class="p-summary-div-container">
                <input type="radio" class="" name="date-chooser" id="tenth">
                <label for = 'tenth'><?php echo date("d-m-Y");?></label>
            </div> -->
        </div>
    </div>
    <div class="date-picker-div">
        <div><input placeholder="DD-MM-YYYY"  id = "from_date" class="form-control"></div>
        <div><input placeholder="DD-MM-YYYY"  id = "to_date" class="form-control"></div>
    </div>
    <div>
        <div id="shhowErrors"></div>
        <div class="sub-btn-end">
            <button type="button" class="btn btn-success" id = "okbtn">OK</button>
        </div>
    </div>
</body>

<?php require_once './Assets/js.php'; ?>
<script>
    const PURCHASE_SUMMARY_URL = 'http://localhost/mohini/public_html/be/purchase-summary.php';
    const okButton = document.querySelector('#okbtn')
    const fromDate = document.querySelector('#from_date')
    const toDate = document.querySelector('#to_date')
    let sDateForCompFrom = null;
    let sDateForCompTo = null;
    $( "#from_date" ).datepicker({
        altFormat: "dd-mm-yy",
        dateFormat: 'dd/mm/yy', 
    });
    $( "#to_date" ).datepicker({
        altFormat: "dd-mm-yy",
        dateFormat: 'dd/mm/yy', 
    });
    let from_date = null;
    let to_date = null;
    $("#from_date").on('change',()=>{
        from_date = fromDate.value
        const today = new Date();
        const [day, month, year] = from_date.split('/').map(Number);
        const selectedDate = new Date(year, month - 1, day); // Create date object (month is 0-indexed)
        sDateForCompFrom = selectedDate
        
        const selectedDate2 = new Date(from_date); 
        const thirtyDaysAgo = new Date(today);
        thirtyDaysAgo.setDate(today.getDate() - 30);
        
        today.setHours(0, 0, 0, 0); // Set time to midnight for comp

        if ((selectedDate > today)) {
            document.getElementById('shhowErrors').textContent = "The selected date cannot be in the future.";
        } 
        else if(selectedDate < thirtyDaysAgo){
            document.getElementById('shhowErrors').textContent = "Please select a date within the last 30 days.";
        }
        else {
            console.log(from_date);
            document.getElementById('shhowErrors').textContent = "";
            }
    })

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
            else if(selectedDate < thirtyDaysAgo){
                document.getElementById('shhowErrors').textContent = "Please select a date within the last 30 days.";
            }
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
        console.log(">><><",x.value);
        toDate.value = x.value
        fromDate.value = x.value
        
        //Updating the dates here
        from_date = x.value
        to_date = x.value
    }
    okButton.addEventListener('click',async ()=>{
        if(from_date==null || to_date==null){
            document.getElementById('shhowErrors').textContent = "Please select both the dates";
            return;
        }
        if(sDateForCompTo<sDateForCompFrom){
            document.getElementById('shhowErrors').textContent = "The 'From' date can't be greater than the 'To' date.";
            return;
        }
        if(sDateForCompTo-sDateForCompFrom>7){
            document.getElementById('shhowErrors').textContent = "You can view up to 7 days of transactions.";
            return;
        }
        const dateObj = {
            "from_date":from_date,
            "to_date":to_date,
            "user_id" : JSON.parse(localStorage.getItem("loginInfo")).id
        }
        try {
           const p_res = await  fetch(PURCHASE_SUMMARY_URL,{
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dateObj)
            });
            const p_resp = await p_res.json();
            console.log('p_resp', p_resp);
            if(p_res.ok){
                localStorage.setItem("purchase_summary",JSON.stringify(p_resp.data))
                window.location = './purchaseSummaryForm.php'
            } else {
                console.log('1');
                alert(p_resp.msg)
            }
        } catch (error) {
            console.log('2');
            alert(error.msg)
        }
        // console.log("Kajal sdfghjk",dateObj);

    })


</script>
</html>