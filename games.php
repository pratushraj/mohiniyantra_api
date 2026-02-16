<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rajarani Mohini</title>
    <script>
        if (!localStorage.getItem("loginInfo")) {
            window.location = './Login.php'
        }
    </script>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet"> -->
    <?php require_once './Assets/css.php'; ?>
</head>

<body class="bg-pink">
    <div class="container mt-3">
        <?php require_once './coomonTabHeader.php'; ?>
        <div class="mt-2">
            <?php require_once './topTabList.php'; ?>
        </div>
        <script src="./moveFocus.js"></script>
        <div class="table-responsive mt-2 tab-content">
            <?php require_once './SingleGameATab.php'; ?>
            <?php require_once './SingleGameBTab.php'; ?>
            <?php require_once './DoubleGameTab.php'; ?>
        </div>

        <?php
        require_once './YantraModal.php';
        ?>
        <?php
        require_once './upComingModal.php';
        ?>
        <div class="d-flex justify-content-between">
            <!-- <button class="btn btn-success btn-sm buttons-footer upcoming-link"><a href="./upcoming.php">UPCOMING</a></button> -->
            <button class="btn btn-success btn-sm buttons-footer upcoming-link" data-bs-toggle="modal" data-bs-target="#upcomingModal">UPCOMING</button>
            <button class="btn btn-primary btn-sm buttons-footer" id="index_buy">BUY</button>
            <button class="btn btn-danger btn-sm buttons-footer" id="clear_inputs">CLEAR</button>
            <button class="btn btn-warning btn-sm buttons-footer" id="cancelTicket">CANCEL</button>
            <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                YANTRA
            </button> -->
            <button class="btn btn-secondary btn-sm buttons-footer upcoming-link"><a href="./dateSelect.php">Result</a></button>
            <button class="btn btn-secondary btn-sm buttons-footer upcoming-link" tooltip="Purchase Report"> <a href="./purchaseSummary.php">P. Report</a></button>
            <button class="btn btn-dark btn-sm buttons-footer  upcoming-link"><a href="./WinningReport.php">W.Rep</a></button>
        </div>
        <!-- <div class="d-flex justify-content-end mt-3 text-white">
            <span>Date : 26/07/2024</span>
            <span class="ms-3">Gift Event Code : 08:30</span>
            <span class="ms-3">Countdown : 00:21:56</span>
        </div> -->
    </div>
</body>

<!-- <script type="module" src="./urls.js"></script> -->

<script type="module">
    import {
        TICKET_PURCHAASE_URL,
        UPCOMING_EVENTS_URL,
        CANCEL_TICKETS_URL
    } from './urls.js';
    import {
        WALLET_REQUEST_IN_EVERY_TWO_SEC_URL
    } from './urls.js';
    import {
        TICKET_PRICE_URL
    } from './urls.js';
    var single_game_A_price;
    var single_game_B_price;
    var double_game_price;
    // Show the balance to user wallet
    document.getElementById("totalUserBalance").innerHTML = JSON.parse(localStorage.getItem("loginInfo")).wallet_balance ?? 0

    // update balance in every 2 second
    setInterval(async () => {
        try {
            const wall_req = await fetch(WALLET_REQUEST_IN_EVERY_TWO_SEC_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    'userId': JSON.parse(localStorage.getItem("loginInfo")).id
                })
            });
            const wall_req_json = await wall_req.json();
            if (wall_req.ok) {
                document.getElementById("totalUserBalance").innerHTML = wall_req_json.data;
                const local_data = JSON.parse(localStorage.getItem('loginInfo'));
                local_data.wallet_balance = wall_req_json.data;
                localStorage.setItem("loginInfo", JSON.stringify(local_data))
            } else {

            }
        } catch (error) {

        }
    }, 3000)

    // const URL = 'http://localhost:8080/workspace/techspheresoft/rajaranimohini_gaming';
    const URL = 'http://localhost/mohini/public_html';
    // Single Game A JS Starts here
    const singleGameVal = document.querySelectorAll('.single-game-val');
    const singleGameAll = document.querySelectorAll('.single-game-All');
    const singleGameHorizontalAll = document.querySelectorAll('.single-game-horizontal-all')
    const rajaraniGanga = document.querySelectorAll('.rajarani-ganga')
    const rajaraniYamuna = document.querySelectorAll('.rajarani-yamuna')
    const rajaraniSaraswati = document.querySelectorAll('.rajarani-saraswati')
    const rajaraniMohini = document.querySelectorAll('.rajarani-mohini')
    singleGameAll.forEach((All) => {
        All.addEventListener('change', () => {
            const inputs = All.parentElement.parentElement.querySelectorAll('td input');
            try {
                inputs.forEach(function(inp) {
                    if (inp.classList.contains('single-game-val')) {
                        if (parseInt(Number(All.value) + Number(inp.value))>99){
                            alert("4 Ticket Limit is 99");
                            throw new Error("Ticket Limit is 99")
                        }
                    }
                    // updating the qty in Single Game A
                });

            inputs.forEach(function(inp) {
                if (inp.classList.contains('single-game-val')) {
                    inp.value = parseInt(Number(All.value) + Number(inp.value))
                }
                // updating the qty in Single Game A
            });
            rajaRaniMohiniQtyUpdate();
            rajaRaniSwarasatiQtyUpdate();
            rajaRaniGangaQtyUpdate();
            rajaRaniYumunaQtyUpdate();
        }
            catch (error) {
                console.log(error);
            }
        })
        
    })


    singleGameHorizontalAll.forEach((All, index) => {
        All.addEventListener('change', (inp) => {
            try {
                if(((Number(All.value) + Number(rajaraniGanga[index].value))>99) || ((Number(All.value) + Number(rajaraniYamuna[index].value))>99) || ((Number(All.value) + Number(rajaraniSaraswati[index].value))>99) || ((Number(All.value) + Number(rajaraniMohini[index].value))>99)){
                    alert("1 Ticket Limit is 99")
                    throw new Error("Ticket Limit is 99")
                }

            rajaraniGanga[index].value = Number(All.value) + Number(rajaraniGanga[index].value)
            rajaraniYamuna[index].value = Number(All.value) + Number(rajaraniYamuna[index].value)
            rajaraniSaraswati[index].value = Number(All.value) + Number(rajaraniSaraswati[index].value)
            rajaraniMohini[index].value = Number(All.value) + Number(rajaraniMohini[index].value)
            // update qty 
            rajaRaniMohiniQtyUpdate();
            rajaRaniSwarasatiQtyUpdate();
            rajaRaniGangaQtyUpdate();
            rajaRaniYumunaQtyUpdate();

        } catch (error) {
                console.log(error);
            }
        })
    })
    // Single Game A JS Ends here

    // Update Qty RajaRani Ganga
    rajaraniMohini.forEach((node) => {
        node.addEventListener("change", rajaRaniMohiniQtyUpdate);
    })
    rajaraniGanga.forEach((node) => {
        node.addEventListener("change", rajaRaniGangaQtyUpdate);
    })
    rajaraniYamuna.forEach((node) => {
        node.addEventListener("change", rajaRaniYumunaQtyUpdate);
    })
    rajaraniSaraswati.forEach((node) => {
        node.addEventListener("change", rajaRaniSwarasatiQtyUpdate);
    })

    function rajaRaniMohiniQtyUpdate() {
        let rrm_count = 0
        rajaraniMohini.forEach((singleNode) => {
            rrm_count += Number(singleNode.value)
        })
        document.querySelector("#rajaRaniMohQty").innerHTML = rrm_count || "-"
        document.querySelector("#rajaRaniMohPts").innerHTML = parseFloat(rrm_count * single_game_A_price).toFixed(2) || "-"

        totalFunctionSingleGameA();
    }

    function rajaRaniGangaQtyUpdate() {
        let rrm_count = 0
        rajaraniGanga.forEach((singleNode) => {
            rrm_count += Number(singleNode.value)
        })
        document.querySelector("#rajaRaniGanQty").innerHTML = rrm_count || "-"
        document.querySelector("#rajaRaniGanPts").innerHTML = parseFloat(rrm_count * single_game_A_price).toFixed(2) || "-"
        // rrm_count * single_game_A_price || "-"
        totalFunctionSingleGameA();

    }

    function rajaRaniYumunaQtyUpdate() {
        let rrm_count = 0
        rajaraniYamuna.forEach((singleNode) => {
            rrm_count += Number(singleNode.value)
        })
        document.querySelector("#rajaRaniYumQty").innerHTML = rrm_count || "-"
        document.querySelector("#rajaRaniYumPts").innerHTML = parseFloat(rrm_count * single_game_A_price).toFixed(2) || "-"
        // rrm_count * single_game_A_price || "-"
        totalFunctionSingleGameA();
    }

    function rajaRaniSwarasatiQtyUpdate() {
        let rrm_count = 0
        rajaraniSaraswati.forEach((singleNode) => {
            rrm_count += Number(singleNode.value)
        })
        document.querySelector("#rajaRaniSwarQty").innerHTML = rrm_count || "-"
        document.querySelector("#rajaRaniSwarPts").innerHTML = parseFloat(rrm_count * single_game_A_price).toFixed(2) || "-"
        // rrm_count * single_game_A_price || "-"
        totalFunctionSingleGameA();
    }

    function totalFunctionSingleGameA() {
        const qtym = Number(document.querySelector("#rajaRaniMohQty").innerHTML) || 0
        const qtyg = Number(document.querySelector("#rajaRaniGanQty").innerHTML) || 0
        const qtyy = Number(document.querySelector("#rajaRaniYumQty").innerHTML) || 0
        const qtys = Number(document.querySelector("#rajaRaniSwarQty").innerHTML) || 0
        const total = qtym + qtyg + qtyy + qtys;
        document.querySelector("#totalPtsSingleGameA").innerHTML = parseFloat(total * single_game_A_price).toFixed(2) || "-";
        document.querySelector("#totalqtySingleGameA").innerHTML = total || "-";

    }

    (async function getPts() {
        try {

            const tpr = await fetch(TICKET_PRICE_URL);
            const tpresponse = await tpr.json();
            if (tpr.ok) {
                const result = tpresponse.data.reduce((acc, game) => {
                    acc[game.game_id] = game.ticket_price;
                    return acc;
                }, {});
                single_game_A_price = result[1]
                single_game_B_price = result[2]
                double_game_price = result[3]
            }

        } catch (error) {

        }
    })()

    // Single Game B JS Starts here
    const singleGame_B_Val = document.querySelectorAll('.single-game-b-val');
    const singleGame_B_All = document.querySelectorAll('.single-game-b-All');
    const singleGameHorizontal_B_All = document.querySelectorAll('.single-game-b-horizontal-all')
    singleGame_B_All.forEach((All) => {
        All.addEventListener('change', () => {
            const inputs = All.parentElement.parentElement.querySelectorAll('td input');
            try {
            inputs.forEach(function(inp) {
                if (inp.classList.contains('single-game-b-val')) {
                    if(parseInt(Number(All.value) + Number(inp.value))>99){
                        alert("2 Ticket Limit is 99");
                        throw new Error("Ticket Limit is 99")
                    }
                }
            });

            inputs.forEach(function(inp) {
                if (inp.classList.contains('single-game-b-val')) {
                    inp.value = parseInt(Number(All.value) + Number(inp.value))
                }
            });
            BrajaRaniMohiniQtyUpdate();
            BrajaRaniGangaQtyUpdate();
            BrajaRaniYumunaQtyUpdate();
            BrajaRaniSwarasatiQtyUpdate();
        } catch (error) {
                console.log(error);
            }
        })
    })

    const rajaraniGanga_B = document.querySelectorAll('.rajarani-ganga-b')
    const rajaraniYamuna_B = document.querySelectorAll('.rajarani-yamuna-b')
    const rajaraniSaraswati_B = document.querySelectorAll('.rajarani-saraswati-b')
    const rajaraniMohini_B = document.querySelectorAll('.rajarani-mohini-b')
    singleGameHorizontal_B_All.forEach((All, index) => {
        All.addEventListener('change', (inp) => {
            try {
                if(((Number(All.value) + Number(rajaraniGanga_B[index].value))>99) || (( Number(All.value) + Number(rajaraniYamuna_B[index].value))>99) || ((Number(All.value) + Number(rajaraniSaraswati_B[index].value))>99) || ((Number(All.value) + Number(rajaraniMohini_B[index].value))>99)){
                    alert("3 Ticket Limit is 99")
                    throw new Error("Ticket Limit is 99")
                }
            
            rajaraniGanga_B[index].value = Number(All.value) + Number(rajaraniGanga_B[index].value)
            rajaraniYamuna_B[index].value = Number(All.value) + Number(rajaraniYamuna_B[index].value)
            rajaraniSaraswati_B[index].value = Number(All.value) + Number(rajaraniSaraswati_B[index].value)
            rajaraniMohini_B[index].value = Number(All.value) + Number(rajaraniMohini_B[index].value)
            BrajaRaniMohiniQtyUpdate();
            BrajaRaniGangaQtyUpdate();
            BrajaRaniYumunaQtyUpdate();
            BrajaRaniSwarasatiQtyUpdate();
        } catch (error) {
                console.log("Ticket Limit is 90");
            }
        })
    })

    //Update single game B qty
    function BrajaRaniMohiniQtyUpdate() {
        let rrm_count = 0
        rajaraniMohini_B.forEach((singleNode) => {
            rrm_count += Number(singleNode.value)
        })
        document.querySelector("#BrajaRaniMohQty").innerHTML = rrm_count || "-"
        document.querySelector("#BrajaRaniMohPts").innerHTML = parseFloat(rrm_count * single_game_B_price).toFixed(2) || "-"

        totalFunctionSingleGameB();
    }

    function BrajaRaniGangaQtyUpdate() {
        let rrm_count = 0
        rajaraniGanga_B.forEach((singleNode) => {
            rrm_count += Number(singleNode.value)
        })
        document.querySelector("#BrajaRaniGanQty").innerHTML = rrm_count || "-"
        document.querySelector("#BrajaRaniGanPts").innerHTML = parseFloat(rrm_count * single_game_B_price).toFixed(2) || "-"
        // rrm_count * single_game_B_price || "-"

        totalFunctionSingleGameB();
    }

    function BrajaRaniYumunaQtyUpdate() {
        let rrm_count = 0
        rajaraniYamuna_B.forEach((singleNode) => {
            rrm_count += Number(singleNode.value)
        })
        document.querySelector("#BrajaRaniYumQty").innerHTML = rrm_count || "-"
        document.querySelector("#BrajaRaniYumPts").innerHTML = parseFloat(rrm_count * single_game_B_price).toFixed(2) || "-"
        // rrm_count * single_game_B_price || "-"

        totalFunctionSingleGameB();
    }

    function BrajaRaniSwarasatiQtyUpdate() {
        let rrm_count = 0
        rajaraniSaraswati_B.forEach((singleNode) => {
            rrm_count += Number(singleNode.value)
        })
        document.querySelector("#BrajaRaniSwarQty").innerHTML = rrm_count || "-"
        document.querySelector("#BrajaRaniSwarPts").innerHTML = parseFloat(rrm_count * single_game_B_price).toFixed(2) || "-"
        // rrm_count * single_game_B_price || "-"

        totalFunctionSingleGameB();
    }

    function totalFunctionSingleGameB() {
        const qtym = Number(document.querySelector("#BrajaRaniMohQty").innerHTML) || 0
        const qtyg = Number(document.querySelector("#BrajaRaniGanQty").innerHTML) || 0
        const qtyy = Number(document.querySelector("#BrajaRaniYumQty").innerHTML) || 0
        const qtys = Number(document.querySelector("#BrajaRaniSwarQty").innerHTML) || 0
        const total = qtym + qtyg + qtyy + qtys;
        document.querySelector("#totalPtsSingleGameB").innerHTML = parseFloat(total * single_game_A_price).toFixed(2) || "-";
        document.querySelector("#totalqtySingleGameB").innerHTML = total

    }

    rajaraniMohini_B.forEach((node) => {
        node.addEventListener("change", BrajaRaniMohiniQtyUpdate);
    })
    rajaraniGanga_B.forEach((node) => {
        node.addEventListener("change", BrajaRaniGangaQtyUpdate);
    })
    rajaraniYamuna_B.forEach((node) => {
        node.addEventListener("change", BrajaRaniYumunaQtyUpdate);
    })
    rajaraniSaraswati_B.forEach((node) => {
        node.addEventListener("change", BrajaRaniSwarasatiQtyUpdate);
    })

    // Single Game B JS Ends here

    // Double Game RM starts here 
    const double_Game_RM = document.querySelectorAll('.double-game-rm');
    double_Game_RM.forEach((singleRM, index) => {
        singleRM.addEventListener('change', () => {
            try { 
            const row_RM = document.querySelectorAll(`.RM_${index}`)
            row_RM.forEach((single_row) => {
                if((Number(single_row.value) + Number(singleRM.value))>999){
                    alert("Ticket Limit is 999 only")
                    throw new Error("Ticket Limit is 999 only")
                }
            })
            row_RM.forEach((single_row) => {
                if (isNaN(singleRM.value)) {
                    singleRM.value = 0;
                }
                single_row.value = Number(single_row.value) + Number(singleRM.value)
            })
            RMQtyUpdate();
        } catch (error) {
        
            console.log(error);
        }
        })
    })

    function RMQtyUpdate(){
        let rm_count = 0;
        document.querySelectorAll(".RMD_X").forEach((singleNodeRM)=>{
            rm_count+=Number(singleNodeRM.value)
        })
        document.querySelector("#RM_qty").innerHTML = rm_count || '-'
        document.querySelector("#RM_pts").innerHTML =parseFloat(rm_count * double_game_price).toFixed(2) || '-'
        
    }
    document.querySelectorAll(".RM_input").forEach((node)=>{
        node.addEventListener('change',RMQtyUpdate)
    }) 

    function RGQtyUpdate(){
        let rm_count = 0;
        document.querySelectorAll(".RGD_X").forEach((singleNodeRM)=>{
            rm_count+=Number(singleNodeRM.value)
        })
        document.querySelector("#RG_qty").innerHTML = rm_count || '-'
        document.querySelector("#RG_pts").innerHTML =parseFloat(rm_count * double_game_price).toFixed(2) || '-'
        
    }
    document.querySelectorAll(".RG_input").forEach((node)=>{
        node.addEventListener('change',RGQtyUpdate)
    }) 

    function RYQtyUpdate(){
        let rm_count = 0;
        document.querySelectorAll(".RYD_X").forEach((singleNodeRM)=>{
            rm_count+=Number(singleNodeRM.value)
        })
        document.querySelector("#RY_qty").innerHTML = rm_count || '-'
        document.querySelector("#RY_pts").innerHTML =parseFloat(rm_count * double_game_price).toFixed(2) || '-'
        
    }
    document.querySelectorAll(".RY_input").forEach((node)=>{
        node.addEventListener('change',RYQtyUpdate)
    }) 

    function RSQtyUpdate(){
        let rm_count = 0;
        document.querySelectorAll(".RSD_X").forEach((singleNodeRM)=>{
            rm_count+=Number(singleNodeRM.value)
        })
        document.querySelector("#RS_qty").innerHTML = rm_count || '-'
        document.querySelector("#RS_pts").innerHTML =parseFloat(rm_count * double_game_price).toFixed(2) || '-'
        
    }
    document.querySelectorAll(".RS_input").forEach((node)=>{
        node.addEventListener('change',RSQtyUpdate)
    }) 
    // Note : For the last row means B0-B9 all the class will contain _10
    const last_row_B_RM = document.querySelectorAll('.RM_10')
    last_row_B_RM.forEach((singleRow, index) => {
        singleRow.addEventListener('change', () => {
            try {
                for (let i = 0; i < 10; i++) {
                    if((Number(singleRow.value) + Number(document.querySelector(`.RMC_${10*i+index}`).value))>999){
                        alert("Ticket Limit is 999");
                        throw new Error("Ticket Limit is 999");
                    }
                }

            for (let i = 0; i < 10; i++) {
                // document.querySelector(`.RMC_${10*i+index}`).value += singleRow.value
                document.querySelector(`.RMC_${10*i+index}`).value = Number(singleRow.value) + Number(document.querySelector(`.RMC_${10*i+index}`).value)
            }
            RMQtyUpdate();
        } catch (error) {
                console.log("Ticket Limit is 999");
            }

        })
    })

    // Double Game RM Ends here 

    // Double Game RG starts here
    const double_Game_RG = document.querySelectorAll('.double-game-rg');
    double_Game_RG.forEach((singleRG, index) => {
        singleRG.addEventListener('change', () => {
            try {
            const row_RG = document.querySelectorAll(`.RG_${index}`)
            row_RG.forEach((single_row) => {
                if((Number(single_row.value) + Number(singleRG.value))>999){
                    alert("Ticket Limit is 999")
                    throw new Error("Ticket Limit is 999")
                }
            })
            row_RG.forEach((single_row) => {
                if (isNaN(singleRG.value)) {
                    singleRG.value = 0;
                }
                single_row.value = Number(single_row.value) + Number(singleRG.value)
            })
            RGQtyUpdate();
        } catch (error) {
                console.log(error);
            }
        })
    })

    // Note : For the last row means B0-B9 all the class will contain _10
    const last_row_B_RG = document.querySelectorAll('.RG_10')
    last_row_B_RG.forEach((singleRow, index) => {
        singleRow.addEventListener('change', () => {
            try {
                for (let i = 0; i < 10; i++) {
                    if((Number(singleRow.value) + Number(document.querySelector(`.RGC_${10*i+index}`).value))>999){
                        alert("Ticket Limit is 999")
                        throw new Error("Ticket Limit is 999")
                    }
                }
                
            for (let i = 0; i < 10; i++) {
                document.querySelector(`.RGC_${10*i+index}`).value = Number(singleRow.value) + Number(document.querySelector(`.RGC_${10*i+index}`).value);
            }
            RGQtyUpdate();
        } catch (error) {
                console.log(error);
            }

        })
    })

    // Double Game RG Ends here

    // Double Game RY starts here

    const double_Game_RY = document.querySelectorAll('.double-game-ry');
    double_Game_RY.forEach((singleRG, index) => {
        singleRG.addEventListener('change', () => {
            try {
            const row_RG = document.querySelectorAll(`.RY_${index}`)
            row_RG.forEach((single_row) => {
                if((Number(single_row.value) + Number(singleRG.value))>999){
                    alert("Ticket Limit is 999")
                    throw new Error("Ticket Limit is 999")
                }
            })
            row_RG.forEach((single_row) => {
                if (isNaN(singleRG.value)) {
                    singleRG.value = 0;
                }
                single_row.value = Number(single_row.value) + Number(singleRG.value)
            })
            RYQtyUpdate();
        } catch (error) {
                console.log(error);
            }
        })
    })

    // Note : For the last row means B0-B9 all the class will contain _10
    const last_row_B_RY = document.querySelectorAll('.RY_10')
    last_row_B_RY.forEach((singleRow, index) => {
        singleRow.addEventListener('change', () => {
            try {
                for (let i = 0; i < 10; i++) {
                if((Number(singleRow.value) + Number(document.querySelector(`.RYC_${10*i+index}`).value))>999){
                    alert("Ticket Limit is 999");
                    throw new Error("Ticket Limit is 999")
                }
            }
           
            for (let i = 0; i < 10; i++) {
                document.querySelector(`.RYC_${10*i+index}`).value = Number(singleRow.value) + Number(document.querySelector(`.RYC_${10*i+index}`).value);
            }
            RYQtyUpdate();
        } catch (error) {
                console.log(error);
            }
        })
    })

    // Double Game RY Ends here


    // Double Game RS Starts here

    const double_Game_RS = document.querySelectorAll('.double-game-rs');
    double_Game_RS.forEach((singleRG, index) => {
        singleRG.addEventListener('change', () => {
            try {
            const row_RG = document.querySelectorAll(`.RS_${index}`)
            row_RG.forEach((single_row) => {
                if ((Number(single_row.value) + Number(singleRG.value))>999){
                    alert("Ticket Limit is 999")
                    throw new Error("Ticket Limit is 999")
                }
            })

            row_RG.forEach((single_row) => {
                if (isNaN(singleRG.value)) {
                    singleRG.value = 0;
                }
                single_row.value = Number(single_row.value) + Number(singleRG.value)
            })
            RSQtyUpdate();
                            
        } catch (error) {
                console.log(error);
            }
        })
    })

    // Note : For the last row means B0-B9 all the class will contain _10
    const last_row_B_RS = document.querySelectorAll('.RS_10')
    last_row_B_RS.forEach((singleRow, index) => {
        singleRow.addEventListener('change', () => {
            try {
            for (let i = 0; i < 10; i++) {
                if((Number(singleRow.value) + Number(document.querySelector(`.RSC_${10*i+index}`).value))>999){
                    alert("Ticket Limit is 999")
                    throw new Error("Ticket Limit is 999")
                }
            }
            for (let i = 0; i < 10; i++) {
                document.querySelector(`.RSC_${10*i+index}`).value = Number(singleRow.value) + Number(document.querySelector(`.RSC_${10*i+index}`).value);
            }
            RSQtyUpdate();
        } catch (error) {
                console.log(error);
            }
        })
    })

    // Double Game RS Ends here

    // Prepare the response for the api //
    // single game A id : 1
    // Rajarani Mohini(RM) ( game_type_id ) : 1
    // number : In which box user has entered value
    // qty : How many tickets he is purchased (the value he is entered in number box)  
    // single game B id : 2
    // double game id : 3

    let apiRequest = {
        userId: JSON.parse(localStorage.getItem('loginInfo')).id,
        tickets: []
    }
    const buyBtn = document.querySelector("#index_buy")
    var currentGameTimeSlotId2 = null;
    buyBtn.addEventListener('click', (e) => {
        e.preventDefault()
        const time_slot_id_st = JSON.parse(localStorage.getItem('upcomingSaved'));
        let time_slot_arr = [];
        if (time_slot_id_st && time_slot_id_st.length) {
            time_slot_id_st.forEach((x) => {
                time_slot_arr.push(x.time_slot_id)
            })
        } else {
            time_slot_arr.push(JSON.parse(localStorage.getItem('current_game_details')).timeSlotId)
        }
        apiRequest.tickets = [];
        // rajaraniMohini => 1
        const currentGameDate2 = JSON.parse(localStorage.getItem('current_game_details')).date;
        rajaraniMohini.forEach((x, index) => {
            if (x.value !== "" && x.value != 0) {
                apiRequest.tickets.push({
                    game_id: 1,
                    game_type_id: 1,
                    number: index.toString(),
                    // time_slot_id : JSON.parse(localStorage.getItem('upcomingSaved')),
                    time_slot_id: time_slot_arr,
                    "ticket_date": currentGameDate2,
                    qty: x.value
                });
            }
        })

        // rajaraniGanga => 2
        rajaraniGanga.forEach((x, index) => {
            if (x.value !== "" && x.value != 0) {
                console.log("KAjal");
                apiRequest.tickets.push({
                    game_id: 1,
                    game_type_id: 2,
                    number: index.toString(),
                    // time_slot_id : JSON.parse(localStorage.getItem('upcomingSaved')),
                    time_slot_id: time_slot_arr,
                    "ticket_date": currentGameDate2,
                    qty: x.value
                });
            }
        })

        // rajaraniYamuna => 3
        rajaraniYamuna.forEach((x, index) => {
            if (x.value !== "" && x.value != 0) {
                apiRequest.tickets.push({
                    game_id: 1,
                    game_type_id: 3,
                    number: index.toString(),
                    // time_slot_id : JSON.parse(localStorage.getItem('upcomingSaved')),
                    time_slot_id: time_slot_arr,
                    "ticket_date": currentGameDate2,
                    qty: x.value
                });
            }
        })

        // rajaraniSaraswati => 4
        rajaraniSaraswati.forEach((x, index) => {
            if (x.value !== "" && x.value != 0) {
                apiRequest.tickets.push({
                    game_id: 1,
                    game_type_id: 4,
                    number: index.toString(),
                    // time_slot_id : JSON.parse(localStorage.getItem('upcomingSaved')),
                    time_slot_id: time_slot_arr,
                    "ticket_date": currentGameDate2,
                    qty: x.value
                });
            }
        })

        // Single Game B

        // rajaraniGanga_B
        rajaraniGanga_B.forEach((x, index) => {
            if (x.value !== "" && x.value != 0) {
                apiRequest.tickets.push({
                    game_id: 2,
                    game_type_id: 2,
                    number: index.toString(),
                    // time_slot_id : JSON.parse(localStorage.getItem('upcomingSaved')),
                    time_slot_id: time_slot_arr,
                    "ticket_date": currentGameDate2,
                    qty: x.value
                });
            }
        })

        // rajaraniYamuna_B
        rajaraniYamuna_B.forEach((x, index) => {
            if (x.value !== "" && x.value != 0) {
                apiRequest.tickets.push({
                    game_id: 2,
                    game_type_id: 3,
                    number: index.toString(),
                    // time_slot_id : JSON.parse(localStorage.getItem('upcomingSaved')),
                    time_slot_id: time_slot_arr,
                    "ticket_date": currentGameDate2,
                    qty: x.value
                });
            }
        })

        // rajaraniSaraswati_B
        rajaraniSaraswati_B.forEach((x, index) => {
            if (x.value !== "" && x.value != 0) {
                apiRequest.tickets.push({
                    game_id: 2,
                    game_type_id: 4,
                    number: index.toString(),
                    // time_slot_id : JSON.parse(localStorage.getItem('upcomingSaved')),
                    time_slot_id: time_slot_arr,
                    "ticket_date": currentGameDate2,
                    qty: x.value
                });
            }
        })

        // rajaraniMohini_B
        rajaraniMohini_B.forEach((x, index) => {
            if (x.value !== "" && x.value != 0) {
                apiRequest.tickets.push({
                    game_id: 2,
                    game_type_id: 1,
                    number: index.toString(),
                    // time_slot_id : JSON.parse(localStorage.getItem('upcomingSaved')),
                    time_slot_id: time_slot_arr,
                    "ticket_date": currentGameDate2,
                    qty: x.value
                });
            }
        })

        // Double Game RM
        const RM_input = document.querySelectorAll('.RM_input');
        RM_input.forEach((x, index) => {
            if (x.value !== "" && x.value != 0) {
                let temp_index = null;
                if (index >= 0 && index <= 9) {
                    temp_index = '0' + index;
                }
                apiRequest.tickets.push({
                    game_id: 3,
                    game_type_id: 1,
                    number: temp_index || index.toString(),
                    // time_slot_id : JSON.parse(localStorage.getItem('upcomingSaved')),
                    time_slot_id: time_slot_arr,
                    "ticket_date": currentGameDate2,
                    qty: x.value
                });
            }
        })

        // Double Game RG
        const RG_input = document.querySelectorAll('.RG_input')
        RG_input.forEach((x, index) => {
            if (x.value !== "" && x.value != 0) {
                let temp_index = null;
                if (index >= 0 && index <= 9) {
                    temp_index = '0' + index;
                }
                apiRequest.tickets.push({
                    game_id: 3,
                    game_type_id: 2,
                    number: temp_index || index.toString(),
                    // time_slot_id : JSON.parse(localStorage.getItem('upcomingSaved')),
                    time_slot_id: time_slot_arr,
                    "ticket_date": currentGameDate2,
                    qty: x.value
                });
            }
        })

        // Double Game RY
        const RY_input = document.querySelectorAll('.RY_input');
        RY_input.forEach((x, index) => {
            if (x.value !== "" && x.value != 0) {
                let temp_index = null;
                if (index >= 0 && index <= 9) {
                    temp_index = '0' + index;
                }
                apiRequest.tickets.push({
                    game_id: 3,
                    game_type_id: 3,
                    number: temp_index || index.toString(),
                    // time_slot_id : JSON.parse(localStorage.getItem('upcomingSaved')),
                    time_slot_id: time_slot_arr,
                    "ticket_date": currentGameDate2,
                    qty: x.value
                });
            }
        })

        // Double Game RY
        const RS_input = document.querySelectorAll('.RS_input');
        RS_input.forEach((x, index) => {
            if (x.value !== "" && x.value != 0) {
                let temp_index = null;
                if (index >= 0 && index <= 9) {
                    temp_index = '0' + index;
                }
                apiRequest.tickets.push({
                    game_id: 3,
                    game_type_id: 4,
                    number: temp_index || index.toString(),
                    // time_slot_id : JSON.parse(localStorage.getItem('upcomingSaved')),
                    time_slot_id: time_slot_arr,
                    "ticket_date": currentGameDate2,
                    qty: x.value
                });
            }
        })

        if (apiRequest.tickets.length) {
            localStorage.removeItem('upcomingSaved');
            postData(TICKET_PURCHAASE_URL, apiRequest, "Ticket Purchased Successfully")
            

        } else {
            alert("Please select a ticket first to continue.")
        }
        console.log(apiRequest);
    })


    // Add balance in wallet

    const addBalance_btn = document.getElementById("addBalance_btn")
    addBalance_btn.addEventListener("click", () => {
        const balance = document.querySelector("#addBalance")
        if (balance.value == '' || balance.value <= 0) {
            alert("Invalid Amount")
        } else {
            const balanceObj = {
                userId: JSON.parse(localStorage.getItem("loginInfo")).id,
                "requested-balance": balance.value
            }
            postData(URL + '/be/wallet_request.php', balanceObj, "Balance Added Successfully")
            console.log(balanceObj);
            var modalElement = document.getElementById('exampleModal');
            var modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (!modalInstance) { // If no instance exists, create one
                modalInstance = new bootstrap.Modal(modalElement);
            }

            modalInstance.hide(); // Dynamically close the modal
        }
    })
    // Add balance in wallet

    // Prepare the response for the api ends//

    // Clear all the fields
    document.getElementById("clear_inputs").addEventListener("click", (e) => {
        e.preventDefault();
        singleGameVal.forEach((inp) => {
            inp.value = ""
        })
        singleGame_B_Val.forEach((inp) => {
            inp.value = ""
        })

        singleGameHorizontalAll.forEach((inp) => {
            inp.value = ""
        })
        singleGameHorizontal_B_All.forEach((inp) => {
            inp.value = ""
        })
        singleGameAll.forEach((inp) => {
            inp.value = ""
        })
        singleGame_B_All.forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.RM_input').forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.RG_input').forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.RY_input').forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.RS_input').forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.double-game-rm').forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.double-game-rg').forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.double-game-ry').forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.double-game-rs').forEach((inp) => {
            inp.value = ""
        })
        RSQtyUpdate();
        RGQtyUpdate();
        RYQtyUpdate();
        RMQtyUpdate();
        rajaRaniMohiniQtyUpdate();
        rajaRaniSwarasatiQtyUpdate();
        rajaRaniGangaQtyUpdate();
        rajaRaniYumunaQtyUpdate();
        BrajaRaniMohiniQtyUpdate();
        BrajaRaniGangaQtyUpdate();
        BrajaRaniYumunaQtyUpdate();
        BrajaRaniSwarasatiQtyUpdate();

    })
    // Clear all the fields


    function clearAllInputs(){
        singleGameVal.forEach((inp) => {
            inp.value = ""
        })
        singleGame_B_Val.forEach((inp) => {
            inp.value = ""
        })

        singleGameHorizontalAll.forEach((inp) => {
            inp.value = ""
        })
        singleGameHorizontal_B_All.forEach((inp) => {
            inp.value = ""
        })
        singleGameAll.forEach((inp) => {
            inp.value = ""
        })
        singleGame_B_All.forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.RM_input').forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.RG_input').forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.RY_input').forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.RS_input').forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.double-game-rm').forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.double-game-rg').forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.double-game-ry').forEach((inp) => {
            inp.value = ""
        })
        document.querySelectorAll('.double-game-rs').forEach((inp) => {
            inp.value = ""
        })
        RSQtyUpdate();
        RGQtyUpdate();
        RYQtyUpdate();
        RMQtyUpdate();
        rajaRaniMohiniQtyUpdate();
        rajaRaniSwarasatiQtyUpdate();
        rajaRaniGangaQtyUpdate();
        rajaRaniYumunaQtyUpdate();
        BrajaRaniMohiniQtyUpdate();
        BrajaRaniGangaQtyUpdate();
        BrajaRaniYumunaQtyUpdate();
        BrajaRaniSwarasatiQtyUpdate();
    }

    // Custom function to handle api request
    async function postData(url, data, success_msg) {
        console.log(url);
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
                if(success_msg=='Ticket Purchased Successfully'){
                    clearAllInputs();
                }
                alert(response.msg)
                if(success_msg=='Ticket Purchased Successfully') {
                    location.reload();
                }
            } else {
                alert(response.msg)
                console.log("Error")
            }
        } catch (error) {
            console.log(error);
            alert(response.msg)
        }
    }
    // Custom function to handle api request
</script>
<?php require_once './Assets/js.php'; ?>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
<script type="module" src="./urls.js"></script>

<script type="module">
    import {
        CANCEL_TICKETS_URL,
        CURRENT_GAME_DETAILS_URL,
        UPCOMING_EVENTS_URL
    } from './urls.js';

    // Current GAme Details
    var currentGameDate = null;
    var currentGameTimeSlotId = null;
    (async function fillGameDetails() {
        try {
            const rd = await fetch(CURRENT_GAME_DETAILS_URL);
            const rds = await rd.json();
            if (rd.ok) {
                // Display the fetched date and time in the respective elements
                document.querySelector("#populateDate").innerHTML = rds.data.date;
                localStorage.setItem('current_game_details', JSON.stringify(rds.data))
                currentGameDate = rds.data.date;
                currentGameTimeSlotId = rds.data.timeSlotId;
                // currentGameTimeSlotId2 = rds.data.timeSlotId;
                document.querySelector("#populateTime").innerHTML = rds.data.end_time;
                const prev_game_res = rds.data.prev_game_results;
                document.querySelectorAll(".prev_game_res_rm").forEach((Node)=>{
                    Node.innerHTML = `${prev_game_res[0].win_code} || ${prev_game_res[0].time ?? '22:00'}`
                })
                document.querySelectorAll(".prev_game_res_rr").forEach((Node)=>{
                    Node.innerHTML = `${prev_game_res[1].win_code} || ${prev_game_res[1].time ?? '22:00'}`
                })
                document.querySelectorAll(".prev_game_res_ry").forEach((Node)=>{
                    Node.innerHTML = `${prev_game_res[2].win_code} || ${prev_game_res[1].time ?? '22:00'}`
                })
                document.querySelectorAll(".prev_game_res_ch").forEach((Node)=>{
                    Node.innerHTML = `${prev_game_res[3].win_code} || ${prev_game_res[1].time ?? '22:00'}`
                })

                // Combine date and time into a single datetime string
                const endTimeString = `${rds.data.date}T${rds.data.end_time}`;
                const endTime = new Date(endTimeString).getTime(); // Convert to timestamp

                // Countdown timer
                const countdownInterval = setInterval(function() {
                    const now = new Date().getTime();
                    let distance = endTime - now;

                    // Calculate hours, minutes, and seconds remaining
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Display the result
                    document.getElementById("countDown").innerHTML =
                        //hours + ":" + minutes + ":" + seconds;
                        String(hours).padStart(2, '0') + ":" +
                        String(minutes).padStart(2, '0') + ":" +
                        String(seconds).padStart(2, '0');

                    // If the countdown is over, stop the interval
                    if (distance < 0) {
                        clearInterval(countdownInterval);
                        // document.getElementById("countDown").innerHTML = "EXPIRED";
                        location.reload();
                    }
                }, 1000); // Update every second

            }
        } catch (error) {
            console.log('>>>1', error);

            // alert(error.msg)
        }
    })()

    let data = [];
    (async function fillDataInUpcoming() {
        try {
            const respo = await fetch(UPCOMING_EVENTS_URL)
            const resul = await respo.json()
            if (respo.ok) {
                data = resul.data;
                data.forEach(obj => {
                    document.querySelector(".parent-div-uc").insertAdjacentHTML("beforeend", `
                        <div class="child-div-uc">
                            <div class="checkbox-container">
                                <input type="checkbox" id = ${"uch"+obj.time_slot_id} value="${obj.time_slot_id}" class="checkbox-holder">
                            </div>
                            <div class="time-container">${obj.time}</div>
                        </div>
                    `);
                });
            }
        } catch (error) {
            console.log('>>>2', error);
            alert(error.message || "An unexpected error occurred.");
        }
    })();

    const selectedTimeCount = document.querySelector("#selected-time-count");
    const toggleBtn = document.querySelector('.toggle-checkbox');
    let totleChecked = 0;
    if (localStorage.getItem('upcomingSaved')) {
        const tempArr = JSON.parse(localStorage.getItem('upcomingSaved'));
        totleChecked = tempArr.length;
        selectedTimeCount.innerHTML = totleChecked;
        setTimeout(() => {
            tempArr.forEach((node) => {
                document.querySelector(`#uch${node.time_slot_id}`) && (document.querySelector(`#uch${node.time_slot_id}`).checked = true);
            })
        }, 1000)

    }

    setTimeout(() => {

        const checkboxContainer = document.querySelectorAll('.checkbox-holder');

        checkboxContainer.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    totleChecked += 1;
                } else {
                    totleChecked -= 1;
                }
                selectedTimeCount.innerHTML = totleChecked;
            })
        })
        toggleBtn.addEventListener('change', function() {
            if (this.checked) {
                totleChecked = checkboxContainer.length;
                selectedTimeCount.innerHTML = totleChecked;

                checkboxContainer.forEach(checkbox => {
                    checkbox.checked = true;
                })
            } else {
                checkboxContainer.forEach(checkbox => {
                    totleChecked = 0;
                    selectedTimeCount.innerHTML = totleChecked;
                    checkbox.checked = false;
                })
            }
        })
    }, 1000)

    document.querySelector("#upcoming_save_btn").addEventListener("click", (e) => {
        e.preventDefault();
        const upcomingSelected = [];
        if (localStorage.getItem('upcomingSaved')) {
            localStorage.removeItem("upcomingSaved");
        }
        document.querySelectorAll('.checkbox-holder').forEach((single) => {
            if (single.checked) {
                upcomingSelected.push({
                    "time_slot_id": single.value
                })
            }
        })
        localStorage.setItem("upcomingSaved", JSON.stringify((upcomingSelected)));

    })

    document.getElementById("cancelTicket").addEventListener("click", async (e) => {
        e.preventDefault();
        try {
            const resp = await fetch(CANCEL_TICKETS_URL, {
                mode: 'no-cors',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    userId: JSON.parse(localStorage.getItem("loginInfo")).id
                })
            })
            const resu = await resp.json();
            if (resp.ok) {
                alert("Ticket Cancelled Successfully")
            } else {
                alert(resu.msg);
            }
        } catch (error) {
            alert(error.msg)
        }
    })

    // Show the the name and id in balance request

    document.getElementById("unique_id_f2").innerHTML = JSON.parse(localStorage.getItem("loginInfo")).unique_id
</script>

</html>