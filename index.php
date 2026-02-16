
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RajaRani Mohini</title>
    <!-- Auth -->
    <script>
        if(!localStorage.getItem("loginInfo")){
            window.location = './Login.php'
        }
    </script>
    <?php require_once './Assets/css.php'; ?>

    <style>
        .waalet-req a {
            text-decoration: none;
            color: #fff;
        }

        .container1 {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .bg-pink {
            background-color: #ED6D85;
        }

        .parent-al {
            display: flex;
            justify-content: space-evenly;
        }

        button {
            padding: 10px;
            border-radius: 15px;
            font-size: 15px;
            width: 200px;
            border: none
        }

        .bg-green {
            background-color: green;
        }

        .bg-red {
            background-color: red;
        }

        .text-white {
            color: white;
        }

        .bg-gray {
            background-color: gray;
        }

        .mrg {
            margin: 10px 0;
        }
    </style>
</head>

<body class="bg-pink">
    <?php
    require_once './YantraModal.php';
    ?>
    <div class="container1">
        <div class="parent-al mrg">
            <div class="d-flex">
                <button class="bg-green text-white waalet-req"><a href="./games.php">RajaRani Mohini</a></button>
            </div>
            <div class="d-flex">

                <button class="bg-gray text-white wall-req-mod" data-bs-toggle="modal" data-bs-target="#exampleModal">Wallet Request</button>
            </div>
        </div>

        <div class="parent-al mrg">
            <?php
              require_once './changePasswordModal.php';  
            ?>
            <div class="">
                <button class="bg-red text-white" id="changePassword" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
            </div>
            <div class="">
                <button class="bg-red text-white " id="logout">Logout</button>
            </div>
        </div>
    </div>
</body>
<?php require_once './Assets/js.php'; ?>
<script type="module" src="./urls.js"></script>

<script type="module">
    import { LOGOUT_URL,WALLET_REQUEST_URL, WALLET_REQUEST_IN_EVERY_TWO_SEC_URL } from './urls.js';
    const addBalance_btn = document.getElementById("addBalance_btn")
    addBalance_btn.addEventListener("click", () => {
        const balance = document.querySelector("#addBalance")
        if (balance.value == '' || balance.value <= 0) {
            alert("Invalid Amount")
        } else {
            const {unique_id,id} = JSON.parse(localStorage.getItem("loginInfo"))
            const balanceObj = {
                userId: id,
                uniqueId : unique_id,
                "requested-balance": balance.value
            }
            // console.log(WALLET_REQUEST_URL);
            postData(WALLET_REQUEST_URL, balanceObj, "Balance Added Successfully")
            addBalance.value="";
            var modalElement = document.getElementById('exampleModal'); 
            var modalInstance = bootstrap.Modal.getInstance(modalElement); 
            if (!modalInstance) { 
                modalInstance = new bootstrap.Modal(modalElement);
            }
            modalInstance.hide();
        }
    })


    async function postData(url, data, success_msg) {
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const response = await res.json();
            if (res.ok) {
                //Api called Successfull
                console.log(success_msg);
                alert(response.msg);
            } else {
                alert(response.msg)
                console.log("Error",response)
            }
        } catch (error) {
            alert(error.msg);
            console.log(error);
        }
    }

    const userInfo = JSON.parse(localStorage.getItem("loginInfo"))
    document.querySelector("#showUniqueName").innerHTML = userInfo.name
    document.querySelector("#showUniqueId").innerHTML = userInfo.unique_id

    // Logout
    document.getElementById("logout").addEventListener("click",async()=>{
                try {
          const res = await fetch(LOGOUT_URL, {
            mode: 'no-cors',
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({id:JSON.parse(localStorage.getItem("loginInfo")).id})
          });
          const response = await res.json();
          console.log('>>>', response);
          
          if (res.ok) {
            // clear local storage
            localStorage.removeItem("loginInfo")
            localStorage.removeItem("upcomingSaved")
            window.location = './'
          } else {
            console.log("Error")
          }
        } catch (error) {
          console.log(error);
        }

    })
    document.querySelector("#currentBlnce").value=JSON.parse(localStorage.getItem('loginInfo')).wallet_balance ?? 0; 

    document.querySelector(".wall-req-mod").addEventListener("click",async()=>{
        try {
            document.querySelector("#currentBlnce").value = "Loading..." 
            const wall_req = await fetch(WALLET_REQUEST_IN_EVERY_TWO_SEC_URL,{
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({'userId':JSON.parse(localStorage.getItem("loginInfo")).id})
            });
            const wall_req_json = await wall_req.json(); 
            if(wall_req.ok){
                // document.getElementById("totalUserBalance").innerHTML = wall_req_json.data;
                const local_data =JSON.parse(localStorage.getItem('loginInfo'));
                local_data.wallet_balance = wall_req_json.data;
                localStorage.setItem("loginInfo",JSON.stringify(local_data))
                document.querySelector("#currentBlnce").value = wall_req_json.data
            }
            else{
                document.querySelector("#currentBlnce").value = JSON.parse(localStorage.getItem('loginInfo')).wallet_balance
            }
        } catch (error) {
            document.querySelector("#currentBlnce").value = JSON.parse(localStorage.getItem('loginInfo')).wallet_balance
            console.log('Error Coming', error);
            
        }
    })



</script>



</html>