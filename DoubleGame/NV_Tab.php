<!-- RM Start -->
<div id="nv-sub-game" class="table table-bordered table-sm text-center game-table tab-pane fade  active show" role="tabpanel" aria-labelledby="nv-sub-game-tab">
    <!-- 00-C9 -->
    <div class="wc-96 d-flex">
        <div class="d-flex flex-wrap w-85">
            <?php
            for ($i = 0; $i < 110; $i++) {
            ?>
                <div class="w-10"> 
                    <input type="text" maxlength="3" <?php if($i<99){ ?>data-next-rm="inputrm<?php echo $i+1; }?>" id="inputrm<?php echo $i;?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');checkAndMove_RM(this);" class="form-control RM_input form-control-sm w-100 RMC_<?php echo $i; ?> RM_<?php echo intdiv($i,10); ?> RMD_<?php echo $i<100 ?  "X" : "Y"; ?>" placeholder="<?php echo $i<100 ?  str_pad($i,2,'0',STR_PAD_LEFT)  : "B ".$i%100;?>" /> 
                </div>
            <?php
            }

            ?>
        </div>
        <div class="w-15">
            <?php
            for ($i = 0; $i <= 10; $i++) {
                if($i==10){ ?>
                    <div class="w-100">
                        <div  style="font-size:12px;">Qty : <span id="RM_qty">-</span></div>
                        <div  style="font-size:12px;">Pts : <span id="RM_pts">-</span></div>
                    </div>
                <?php }else{
            ?>
                <div class="w-100"> <input type="text" maxlength="3" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control form-control-sm w-100 double-game-rm" placeholder="<?php echo "A ". $i; ?>" /> </div>
            <?php
            }
            }

            ?>
            <!-- <div class="text-start">
                <span>Qty : </span>
            </div>
            <div class="text-start">
                <span>NV : </span>
            </div> -->
        </div>
    </div>
</div>
<!-- RM Closed -->