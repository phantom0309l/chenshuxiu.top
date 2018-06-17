<?php
$pagetitle = "依维莫司临床实验项目填写记录 CerticanItems [{$certican->title}] [{$certican->patient->name}] ";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="col-md-12" style="overflow-x: auto">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 100px">id</th>
                        <th style="width: 190px">日期</th>
                        <th>化疗天数</th>
                        <th>服药剂量</th>
                        <th>不良反应</th>
                        <th>验血</th>
                        <th>注射升白针</th>
                        <th>注射升血小板</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $i = 0;
                        foreach ($certicanitems as $a) { ?>
                    	<tr>
                            <td><?=$a->id?></td>
                            <td><?=$a->plan_date?></td>
                            <td><?=++$i?></td>
                            <td><?= $a->is_fill == 1 ? $a->drug_dose . "mg" : ''?></td>
                            <td style="width: 300px">
                            	<?= $a->is_fill == 1 ? $a->adverse_content : ''?>
                           	</td>
                            <td>
                            	<?php 
                            	   if ($a->is_fill == 1) {
                        	           if ($a->wbc) {
                        	               echo $a->wbc;
                        	           } else {
                        	               if ($a->is_wbc == 1) {
                            	               echo "已验";
                        	               } else {
                        	                   echo "未验";
                        	               }
                        	           }
                            	   }
                            	?>
                        	</td>
                            <td>
                            	<?php 
                            	   if ($a->is_fill == 1) {
                            	       if ($a->is_white == 1) {
                            	           if ($a->white_dose) {
                            	               echo "已注射：" . $a->white_dose . "ml";
                            	           } else {
                            	               echo "已注射";
                            	           }
                            	       } else {
                            	           echo "未注射";
                            	       }
                            	   }
                            	?>
                            </td>
                            <td>
                            	<?php 
                            	   if ($a->is_fill == 1) {
                            	       if ($a->is_platelet == 1) {
                            	           if ($a->platelet_dose) {
                            	               echo "已注射：" . $a->platelet_dose . "ml";
                            	           } else {
                            	               echo "已注射";
                            	           }
                            	       } else {
                            	           echo "未注射";
                            	       }
                            	   }
                            	?>
                            <td>
                            	<?php 
                            	   if ($a->is_fill == 0) {
                            	       $str = "未填写";
                            	       $str_color = "red";
                            	       $fill_time = "";
                            	   } else {
                            	       $str = "已填写";
                            	       $str_color = "green";
                            	       $fill_time = "({$a->fill_time})";
                            	   }
                            	?>
                                <span style="color: <?=$str_color?>"><?=$str?> <?=$fill_time?></span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
