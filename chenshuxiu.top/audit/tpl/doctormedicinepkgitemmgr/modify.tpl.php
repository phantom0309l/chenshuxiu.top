<?php
$pagetitle = "医生用药修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
        <section class="col-md-12">
        <span style="color: red; background-color: #FFFFCC"><?=$msg ?></span>
            <form action="/doctormedicinepkgitemmgr/modifypost" method="post">
                <input type="hidden" name="doctormedicinepkgitemid" value="<?=$doctormedicinepkgitem->id?>">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width="140">药物:</th>
                        <td>
                        	<?php echo $doctormedicinepkgitem->medicine->name?>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>给药途径:</th>
                        <td>
                        	<?= $doctormedicinepkgitem->getDrug_way_arr();?>
                        </td>
                        <td><a target="_blank" href="/medicinemgr/modify?medicineid=<?=$doctormedicinepkgitem->medicineid ?>">药品信息</a></td>
                    </tr>
                    <tr>
                        <th>用药时机:</th>
                        <td>
                        	<?=$doctormedicinepkgitem->getDrug_timespan_arr();?>
                        </td>
                        <td><a target="_blank" href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?=$doctormedicinepkgitem->getDoctorMedicineRef()->id ?>">医生定制</a></td>
                    </tr>
                    <tr>
                        <th>标准用法:</th>
                        <td>
                        	<?php echo HtmlCtr::getRadioCtrImp($doctormedicinepkgitem->getDoctorMedicineRef()->getVvArray('drug_std_dosage_arr'), 'drug_std_dosage', $doctormedicinepkgitem->drug_std_dosage,"");?>
                        </td>
                        <td><a target="_blank" href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?=$doctormedicinepkgitem->getDoctorMedicineRef()->id ?>">医生定制</a></td>
                    </tr>
                    <tr>
                        <th>药物剂量:</th>
                        <td>
                        	<?php echo HtmlCtr::getRadioCtrImp($doctormedicinepkgitem->getDoctorMedicineRef()->getVvArray('drug_dose_arr'), 'drug_dose', $doctormedicinepkgitem->drug_dose,"");?>
                        </td>
                        <td><a target="_blank" href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?=$doctormedicinepkgitem->getDoctorMedicineRef()->id ?>">医生定制</a></td>
                    </tr>
                    <tr>
                        <th>用药频率:</th>
                        <td>
                        	<?php echo HtmlCtr::getRadioCtrImp($doctormedicinepkgitem->getDoctorMedicineRef()->getVvArray('drug_frequency_arr'), 'drug_frequency', $doctormedicinepkgitem->drug_frequency,"");?>
                        </td>
                        <td><a target="_blank" href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?=$doctormedicinepkgitem->getDoctorMedicineRef()->id ?>">医生定制</a></td>
                    </tr>
                    <tr>
                        <th>调药规则:</th>
                        <td>
                        	<?php echo HtmlCtr::getRadioCtrImp($doctormedicinepkgitem->getDoctorMedicineRef()->getVvArray('drug_change_arr'), 'drug_change', $doctormedicinepkgitem->drug_change);?><br />
                            <a class="resetRadio tab-btn">置空</a>
                        </td>
                        <td><a target="_blank" href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?=$doctormedicinepkgitem->getDoctorMedicineRef()->id ?>">医生定制</a></td>
                    </tr>
                    <tr>
                        <th>药材成分<br/>（中药才需要填写）</th>
                        <td>
                            <textarea name="herbjson" style="width: 80%; height: 100px;"><?= $doctormedicinepkgitem->herbjson?></textarea>
                            填写格式  药材名1=用量1|药材名2=用量2
                        </td>
                        <td><a target="_blank" href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?=$doctormedicinepkgitem->getDoctorMedicineRef()->id ?>">医生定制</a></td>
                    </tr>
                    <tr>
                        <th>用药注意事项:</th>
                        <td>
                        	<?= $doctormedicinepkgitem->getDoctorMedicineRef()->doctorremark?>
                        </td>
                        <td><a target="_blank" href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?=$doctormedicinepkgitem->getDoctorMedicineRef()->id ?>">医生定制</a></td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="" value="提交" />
                        </td>
                        <td></td>
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