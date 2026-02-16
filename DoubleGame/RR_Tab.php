  <!-- RG Start -->
  <div id="rr-sub-game" class="table table-bordered table-sm text-center game-table tab-pane fade show " role="tabpanel" aria-labelledby="rr-sub-game-tab">
      <div class="wc-96 d-flex">
          <div class="d-flex flex-wrap w-85">
              <?php
                for ($i = 0; $i < 110; $i++) {
                ?>
                  <div class="w-10"> 
                    <input type="text" maxlength="3" <?php if($i<99){ ?>data-next-rs="inputrs<?php echo $i+1; }?>" id="inputrs<?php echo $i;?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');checkAndMove_RS(this);" class="form-control form-control-sm w-100 RG_input RGC_<?php echo $i; ?> RG_<?php echo intdiv($i,10); ?> RGD_<?php echo $i<100 ?  "X" : "Y"; ?>" placeholder="<?php echo $i<100 ?  str_pad($i,2,'0',STR_PAD_LEFT) : "B ".$i%100;?>" /> 
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
                        <div  style="font-size:12px;">Qty : <span id="RG_qty">-</span></div>
                        <div  style="font-size:12px;">Pts : <span id="RG_pts">-</span></div>
                    </div>
                  <?php }
                  else{
                ?>
                  <div class="w-100"> <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control form-control-sm w-100 double-game-rg" placeholder="<?php echo "A ".$i; ?>" /> </div>
              <?php
                }
              }

                ?>
              <!-- <div class="text-start">
                  <span>RR : </span>
              </div>
              <div class="text-start">
                  <span>Pts : </span>
              </div> -->
          </div>
      </div>
  </div>
  <!-- RG Closed -->