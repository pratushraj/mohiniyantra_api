<style>
    .parent-div-uc {
        /* display: flex;
        justify-content: space-between; */
        display: grid;
        grid-template-columns: repeat(5, 1fr);
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

    .close-link a {
        text-decoration: none;
        color: #fff;
    }
</style>
<div class="modal fade" id="upcomingModal" tabindex="-1" aria-labelledby="upcomingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color:#ED6D85;">
            <div class="modal-header">
                <h3 class=" text-center w-100">Upcoming Events</h3>
            </div>
            <div class="modal-body">
                <div class="container">
                    <!-- <?php require_once './coomonTabHeader.php'; ?> -->
                    <div>
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
                        <?php  ?>
                            <div class="parent-div-uc">
                                <!-- <div class="child-div-uc">
                                    <div class="checkbox-container"><input type="checkbox" value="8:30" name="" class="checkbox-holder"></div>
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
                                </div> -->
                            </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="upcoming_save_btn" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>


<script>
    // const selectedTimeCount = document.querySelector("#selected-time-count");
    // const toggleBtn = document.querySelector('.toggle-checkbox');
    // let totleChecked = 0;
    // if (localStorage.getItem('upcomingSaved')){
    //     const tempArr = JSON.parse(localStorage.getItem('upcomingSaved'));
    //     totleChecked = tempArr.length;
    //     selectedTimeCount.innerHTML = totleChecked;
    //     setTimeout(()=>{
    //         tempArr.forEach((node)=>{
    //             console.log(document.querySelector(`#uch${node.time_slot_id}`));
    //             document.querySelector(`#uch${node.time_slot_id}`) && (document.querySelector(`#uch${node.time_slot_id}`).checked = true);
    //         })
    //     },1000)
        
    // }
    // setTimeout(()=>{
    //     const checkboxContainer = document.querySelectorAll('.checkbox-holder');

    //     checkboxContainer.forEach(function(checkbox) {
    //         console.log("Checked here");
    //         checkbox.addEventListener('change', function() {
    //             if (this.checked) {
    //                 totleChecked += 1;
    //             } else {
    //                 totleChecked -= 1;
    //             }
    //             selectedTimeCount.innerHTML = totleChecked;
    //             console.log(totleChecked);
    //         })
    //     })
    // },1000)
    // toggleBtn.addEventListener('change', function() {
    //     console.log(checkboxContainer.length)
    //     console.log("checked");
    //     if (this.checked) {
    //         totleChecked = checkboxContainer.length;
    //         selectedTimeCount.innerHTML = totleChecked;
    //         checkboxContainer.forEach(checkbox => {
    //             checkbox.checked = true;
    //         })
    //     } else {
    //         checkboxContainer.forEach(checkbox => {
    //             totleChecked = 0;
    //             selectedTimeCount.innerHTML = totleChecked;
    //             checkbox.checked = false;
    //         })
    //     }
    // })
    // document.querySelector("#upcoming_save_btn").addEventListener("click", (e) => {
    //     e.preventDefault();
    //     const upcomingSelected = [];
    //     if (localStorage.getItem('upcomingSaved')) {
    //         localStorage.removeItem("upcomingSaved");
    //     }
    //     document.querySelectorAll('.checkbox-holder').forEach((single) => {
    //         if (single.checked) {
    //             upcomingSelected.push({
    //                 "time_slot_id": single.value
    //             })
    //         }
    //     })
    //     localStorage.setItem("upcomingSaved", JSON.stringify((upcomingSelected)));

    //     console.log(JSON.parse(localStorage.getItem("upcomingSaved")));
    // })
</script>