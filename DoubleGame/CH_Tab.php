 <!-- RS -->
 <div id="ch-sub-game" class="table table-bordered table-sm text-center game-table tab-pane  fade show" role="tabpanel" aria-labelledby="ch-sub-game-tab">
     <!-- 00-C9 -->
     <div class="wc-96 d-flex">
         <div class="d-flex flex-wrap w-85">
             <?php
                for ($i = 0; $i < 110; $i++) {
                ?>
                 <div class="w-10"> 
                    <input type="text" maxlength="3" <?php if($i<99){ ?>data-next-rg="inputrg<?php echo $i+1; }?>" id="inputrg<?php echo $i;?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');checkAndMove_RG(this);" class="form-control RS_input form-control-sm w-100 RSC_<?php echo $i; ?> RS_<?php echo intdiv($i,10); ?> RSD_<?php echo $i<100 ?  "X" : "Y";?>" placeholder="<?php echo $i<100 ?  str_pad($i,2,'0',STR_PAD_LEFT) : "B ".$i%100;?>" />
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
                            <div  style="font-size:12px;">Qty : <span id="RS_qty">-</span></div>
                            <div  style="font-size:12px;">Pts : <span id="RS_pts">-</span></div>
                        </div>
                    <?php }
                    else{
                ?>
                <div class="w-100"> <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control form-control-sm w-100 double-game-rs" placeholder="<?php echo "A ". $i; ?>" /> </div>
             <?php
                    }
                }

                ?>
             <!-- <div class="text-start">
                 <span>Qty : </span>
             </div>
             <div class="text-start">
                 <span>CH : </span>
             </div> -->
         </div>
     </div>
 </div>
 <!-- RS  -->