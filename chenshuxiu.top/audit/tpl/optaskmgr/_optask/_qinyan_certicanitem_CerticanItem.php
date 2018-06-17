<div class="optaskOneShell">
	<?php 
	   $certicanitem = $optask->obj;
	?>
	<div class="block-content">
            <table class="table table-bordered">
                <tr>
                    <th class="" style="width: 120px;">日期</th>
					<td><?= $certicanitem->plan_date ?></td>
                </tr>
                <tr>
                    <th class="" style="width: 100px;">化疗天数</th>
					<td>
						<?= $i = (strtotime($certicanitem->plan_date) - strtotime($certicanitem->certican->begin_date)) / (3600 * 24) + 1; ?>
					</td>
                </tr>
                <tr>
                    <th class="" style="width: 100px;">服药剂量</th>
					<td><?= $certicanitem->drug_dose . "mg"; ?></td>
                </tr>
                <tr>
                    <th class="" style="width: 100px;">不良反应</th>
					<td><?= $certicanitem->adverse_content ?></td>
                </tr>
                <tr>
                    <th class="" style="width: 100px;">验血</th>
					<td>
						<?php 
            	           if ($certicanitem->wbc) {
            	               echo "wbc:" . $certicanitem->wbc;
            	           } else {
            	               if ($certicanitem->is_wbc == 1) {
                	               echo "已验";
            	               } else {
            	                   echo "未验";
            	               }
            	           }
                    	?>
					</td>
                </tr>
                <tr>
                    <th class="" style="width: 100px;">注射升白针</th>
					<td>
                    	<?php 
                    	   if ($certicanitem->is_fill == 1) {
                    	       if ($certicanitem->is_white == 1) {
                    	           if ($certicanitem->white_dose) {
                    	               echo "已注射：" . $certicanitem->white_dose . "ml";
                    	           } else {
                    	               echo "已注射";
                    	           }
                    	       } else {
                    	           echo "未注射";
                    	       }
                    	   }
                    	?>
					</td>
                </tr>
                <tr>
                    <th class="" style="width: 100px;">注射升血小板</th>
					<td>
						<?php 
                	       if ($certicanitem->is_platelet == 1) {
                	           if ($certicanitem->platelet_dose) {
                	               echo "已注射：" . $certicanitem->platelet_dose . "ml";
                	           } else {
                	               echo "已注射";
                	           }
                	       } else {
                	           echo "未注射";
                	       }
                    	?>
					</td>
                </tr>
            </table>
        </div>
</div>
