<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rajarani Mohini</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet"> -->
    <?php require_once './Assets/css.php'; ?>
    <style>
        .parent-div-uc {
            display: flex;
            justify-content: space-between;
        }

        .child-div-uc {
            flex: 1;
            display: flex;
            border: 1px solid black;
            align-items: center;
            height: 35px;
            background-color: lightgray;
        }

        .checkbox-container {
            flex: 0 0 40%;
            /* Fixes width at 40% */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            background: white;
        }

        .time-container {
            flex: 0 0 60%;
            /* Fixes width at 60% */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            background: green;
        }

        /* Toggle button */
        /* Hide the default checkbox */
        .toggle-checkbox {
            display: none;
        }

        /* Container for the toggle switch */
        .toggle-container {
            display: inline-block;
        }

        /* The label that will act as the toggle switch */
        .toggle-label {
            width: 80px;
            /* height: 40px; */
            height: 25px;
            background-color: lightgray;
            border: 2px solid #ccc;
            border-radius: 5px;
            position: relative;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Inner square toggle slider */
        .toggle-label::before {
            content: "";
            width: 35px;
            /* height: 35px; */
            height: 20px;
            background-color: red;
            position: absolute;
            top: 50%;
            left: 2px;
            transform: translateY(-50%);
            transition: left 0.3s ease;
            border-radius: 3px;
        }

        /* Default state for text (None on the right) */
        .toggle-text {
            position: absolute;
            font-size: 10px;
            transition: opacity 0.3s ease;
        }

        /* Position "All" on the left, but hidden by default */
        .toggle-text.all {
            left: 10px;
            opacity: 0;
        }

        /* Position "None" on the right */
        .toggle-text.none {
            right: 10px;
            color: #333;
        }

        /* When checked, move the slider to the right */
        .toggle-checkbox:checked+.toggle-label::before {
            left: 40px;
        }

        /* When checked, change background color and swap text */
        .toggle-checkbox:checked+.toggle-label {
            background-color: green;
        }

        /* Show "All" and hide "None" when checked */
        .toggle-checkbox:checked+.toggle-label .all {
            opacity: 1;
            color: white;
        }

        .toggle-checkbox:checked+.toggle-label .none {
            opacity: 0;
        }

        /* Toggle button */
        .toggle-container-btn {
            display: flex;
            margin-bottom: 5px;
            justify-content: space-between;
        }

        .text-delected-contained {
            background: yellow;
            padding: 0 5px;
            color: blue;
        }
        .close-link a{
            text-decoration: none;
            color: #fff;
        }
    </style>
</head>

<body class="bg-pink">
    <div class="container mt-1 text-end">
        <button type="button" class="btn btn-danger close-link"><a href="./">Close</a></button>
    </div>
    <div class="container">
        <!-- <?php require_once './coomonTabHeader.php'; ?> -->
        <div>
            <h3 class="text-white text-center">Upcoming Events</h3>
        </div>
        <div class="container my-3">
            <div class="toggle-container-btn">
                <div class="text-delected-contained">
                    <span>Selected Draw : <span id="selected-time-count">0</span></span>
                </div>
                <div class="toggle-container">
                    <input type="checkbox" id="toggle" class="toggle-checkbox">
                    <label for="toggle" class="toggle-label">
                        <span class="toggle-text all">All</span>
                        <span class="toggle-text none">None</span>
                    </label>
                </div>

            </div>
            <?php for ($i = 0; $i <= 5; $i++) { ?>
                <div class="parent-div-uc">
                    <div class="child-div-uc">
                        <div class="checkbox-container"><input type="checkbox" name="" class="checkbox-holder"></div>
                        <div class="time-container">8:30</div>
                    </div>
                    <div class="child-div-uc">
                        <div class="checkbox-container"><input type="checkbox" name="" class="checkbox-holder"></div>
                        <div class="time-container">8:30</div>
                    </div>
                    <div class="child-div-uc">
                        <div class="checkbox-container"><input type="checkbox" name="" class="checkbox-holder"></div>
                        <div class="time-container">8:30</div>
                    </div>
                    <div class="child-div-uc">
                        <div class="checkbox-container"><input type="checkbox" name="" class="checkbox-holder"></div>
                        <div class="time-container">8:30</div>
                    </div>
                    <div class="child-div-uc">
                        <div class="checkbox-container"><input type="checkbox" name="" class="checkbox-holder"></div>
                        <div class="time-container">8:30</div>
                    </div>
                    <div class="child-div-uc">
                        <div class="checkbox-container"><input type="checkbox" name="" class="checkbox-holder"></div>
                        <div class="time-container">8:30</div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php
        require_once './YantraModal.php';
        ?>

        <div class="d-flex justify-content-between">
            <button class="btn btn-success btn-sm buttons-footer upcoming-link"><a href="./upcoming.php">UPCOMING</a></button>
            <button class="btn btn-primary btn-sm buttons-footer">BUY</button>
            <button class="btn btn-danger btn-sm buttons-footer">CLEAR</button>
            <button class="btn btn-warning btn-sm buttons-footer">CANCEL</button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                YANTRA
            </button>
            <button class="btn btn-secondary btn-sm buttons-footer">C.DERAILS</button>
            <button class="btn btn-dark btn-sm buttons-footer">P.SUM</button>
        </div>

    </div>
</body>

<?php require_once './Assets/js.php'; ?>
<script>
    const selectedTimeCount = document.querySelector("#selected-time-count");
    const toggleBtn = document.querySelector('.toggle-checkbox');
    const checkboxContainer = document.querySelectorAll('.checkbox-holder');
    let totleChecked = 0;
    checkboxContainer.forEach(function(checkbox){
        checkbox.addEventListener('change',function(){
            if(this.checked){
                totleChecked += 1;
            }
            else{
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
</script>

</html>