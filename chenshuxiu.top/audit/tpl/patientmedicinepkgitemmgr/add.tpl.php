<?php
$pagetitle = $patientmedicinepkg->patient->name . " of 添加用药 ";
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
            <form action="/patientmedicinepkgitemmgr/addpost" method="post">
                <input type="hidden" id="patientmedicinepkgid" name="patientmedicinepkgid" value="<?=$patientmedicinepkg->id?>">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width="140">药物:</th>
                        <td>
                            <?php
                            if (false == empty($notselectedmedicines)) {
                                echo HtmlCtr::getSelectCtrImp(CtrHelper::toMedicineCtrArray($notselectedmedicines), "medicineid", $medicineid, "medicine");
                            } else {
                                echo "该医生没有关联药物，请先关联药物";
                            }
                            ?>
                        </td>
                    </tr>
                    <?php if( $doctormedicineref instanceof DoctorMedicineRef ){ ?>
                    <tr>
                        <th>药物剂量:</th>
                        <td>
                    	<?php
                        echo HtmlCtr::getRadioCtrImp($doctormedicineref->getVvArray('drug_dose_arr'), 'drug_dose', $doctormedicineref->getDefaultDrug_dose(),
                                "");
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <th>用药频率:</th>
                        <td>
                    	<?php
                        echo HtmlCtr::getRadioCtrImp($doctormedicineref->getVvArray('drug_frequency_arr'), 'drug_frequency',
                                $doctormedicineref->getDefaultDrug_frequency(), "");
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <th>调药规则:</th>
                        <td>
                    	<?php
                        echo HtmlCtr::getRadioCtrImp($doctormedicineref->getVvArray('drug_change_arr'), 'drug_change',
                                $doctormedicineref->getDefaultDrug_change());
                        ?>
                        	       		&nbsp;<a class="resetRadio tab-btn">置空</a>
                        </td>
                    </tr>
                    <tr>
                        <th>用药注意事项:</th>
                        <td>
                            <?=nl2br($doctormedicineref->doctorremark)?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
                        </td>
                    </tr>

                    <?php }?>
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
            "change",
            ".medicine",
            function(){
                var medicineid =  $(this).val() ;
                var patientmedicinepkgid = $("#patientmedicinepkgid").val();
                var url = medicineid==0 ? location.pathname : location.pathname + '?patientmedicinepkgid=' + patientmedicinepkgid + '&medicineid=' + medicineid;
                window.location.href = url ;
            });
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