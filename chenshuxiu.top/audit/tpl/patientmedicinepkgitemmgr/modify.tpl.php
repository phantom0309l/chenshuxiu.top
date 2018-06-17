<?php
$pagetitle = $patientmedicinepkgitem->patientmedicinepkg->patient->name . " of 用药修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
        <span style="color: red; background-color: #FFFFCC"><?=$msg ?></span>
            <form action="/patientmedicinepkgitemmgr/modifypost" method="post">
                <input type="hidden" name="patientmedicinepkgitemid" value="<?=$patientmedicinepkgitem->id?>">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width="140">药物:</th>
                        <td><?php echo $patientmedicinepkgitem->medicine->name?>(旧数据)</td>
                        <td width="800">
                        	<?php echo $patientmedicinepkgitem->medicine->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>药物剂量:</th>
                        <td><?php echo $patientmedicinepkgitem->drug_dose;?></td>
                        <td>
                        	<?php echo HtmlCtr::getRadioCtrImp($patientmedicinepkgitem->getDoctorMedicineRef()->getVvArray('drug_dose_arr'), 'drug_dose', $patientmedicinepkgitem->drug_dose,"");?>
                        </td>
                    </tr>
                    <tr>
                        <th>用药频率:</th>
                        <td><?php echo $patientmedicinepkgitem->drug_frequency;?></td>
                        <td>
                        	<?php echo HtmlCtr::getRadioCtrImp($patientmedicinepkgitem->getDoctorMedicineRef()->getVvArray('drug_frequency_arr'), 'drug_frequency', $patientmedicinepkgitem->drug_frequency,"");?>
                        </td>
                    </tr>
                    <tr>
                        <th>调药规则:</th>
                        <td><?php echo $patientmedicinepkgitem->drug_change;?></td>
                        <td>
                        	<?php echo HtmlCtr::getRadioCtrImp($patientmedicinepkgitem->getDoctorMedicineRef()->getVvArray('drug_change_arr'), 'drug_change', $patientmedicinepkgitem->drug_change);?><br />
                            <a class="resetRadio tab-btn">置空</a>
                        </td>
                    </tr>
                    <tr>
                        <th>用药注意事项:</th>
                        <td></td>
                        <td>
                        	<?= $patientmedicinepkgitem->getDoctorMedicineRef()->doctorremark?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td></td>
                        <td>
                            <input type="submit" class="" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function(){
        $(document).on(
			"click",
			".resetRadio",
			function(){
				$("input[name='drug_change']").prop("checked",false);
				return false;
			});
	});
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>