<div id="doubleGame" class="tab-pane fade show " role="tabpanel" aria-labelledby="doubleGame-tab">
    <div>
        <div class="btn-group w-100 cont">
            <button type="button" class="btn btn-bulk1  doubleGame-sub-tab prev_game_res_rm">NV11 || 14:30</button>
            <button type="button" class="btn btn-bulk1  doubleGame-sub-tab prev_game_res_rr" id="">RR51 ||
                14:30</button>
            <button type="button" class="btn btn-bulk1  doubleGame-sub-tab prev_game_res_ry" id="">RY97 ||
                14:30</button>
            <button type="button" class="btn btn-bulk1  doubleGame-sub-tab prev_game_res_ch">CH64 || 14:30</button>
        </div>
        <!-- Header of the DOuble Game -->
        <div class="btn-group w-100 nav " role="tablist">
            <button type="button" class="btn btn-bulk active nav-link doubleGame-sub-tab" id="nv-sub-tab"
                data-bs-toggle="tab" data-bs-target="#nv-sub-game" role="tab" aria-controls="nv-sub-game"
                aria-selected="true">RM</button>
            <button type="button" class="btn btn-bulk nav-link doubleGame-sub-tab" id="rr-sub-tab" data-bs-toggle="tab" data-bs-target="#rr-sub-game" role="tab" aria-controls="rr-sub-game" aria-selected="false">RG</button>
            <button type="button" class="btn btn-bulk nav-link doubleGame-sub-tab" id="ry-sub-tab" data-bs-toggle="tab" data-bs-target="#ry-sub-game" role="tab" aria-controls="ry-sub-game" aria-selected="false">RY</button>
            <button type="button" class="btn btn-bulk nav-link doubleGame-sub-tab" id="ch-sub-tab" data-bs-toggle="tab" data-bs-target="#ch-sub-game" role="tab" aria-controls="ch-sub-game" aria-selected="false">RS</button>
        </div>
        <div class="tab-content">
            <?php require_once './DoubleGame/NV_Tab.php' ?>
            <?php require_once './DoubleGame/RR_Tab.php' ?>
            <?php require_once './DoubleGame/RY_Tab.php' ?>
            <?php require_once './DoubleGame/CH_Tab.php' ?>
        </div>
    </div>
    <!-- <div>
        <div class="btn-group w-100 nav " role="tablist">
            <button type="button" class="btn btn-bulk active nav-link doubleGame-sub-tab" id="nv-sub-tab" data-bs-toggle="tab" data-bs-target="#nv-sub-game" role="tab" aria-controls="nv-sub-game" aria-selected="true">NV</button>
            <button type="button" class="btn btn-bulk nav-link doubleGame-sub-tab" id="rr-sub-tab" data-bs-toggle="tab" data-bs-target="#rr-sub-game" role="tab" aria-controls="rr-sub-game" aria-selected="false">RR</button>
            <button type="button" class="btn btn-bulk nav-link doubleGame-sub-tab" id="ry-sub-tab" data-bs-toggle="tab" data-bs-target="#ry-sub-game" role="tab" aria-controls="ry-sub-game" aria-selected="false">RY</button>
            <button type="button" class="btn btn-bulk nav-link doubleGame-sub-tab" id="ch-sub-tab" data-bs-toggle="tab" data-bs-target="#ch-sub-game" role="tab" aria-controls="ch-sub-game" aria-selected="false">CH</button>
        </div>
        <div class="tab-content">
            <?php require_once './DoubleGame/NV_Tab.php' ?>
            <?php require_once './DoubleGame/RR_Tab.php' ?>
            <?php require_once './DoubleGame/RY_Tab.php' ?>
            <?php require_once './DoubleGame/CH_Tab.php' ?>
        </div>
    </div> -->
</div>