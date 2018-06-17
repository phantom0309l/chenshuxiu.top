<?php
$pagetitle = $doctormedicinepkg->doctor->name . " 的 " . $doctormedicinepkg->name . " 添加项 ";
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
            <form action="/doctormedicinepkgitemmgr/addpost" method="post">
                <input type="hidden" id="doctormedicinepkgid" name="doctormedicinepkgid" value="<?=$doctormedicinepkg->id?>">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width="140">药物:</th>
                        <td>
                            <?php
                            if (false == empty($medicines)) {
                                echo HtmlCtr::getSelectCtrImp(CtrHelper::toMedicineCtrArray($medicines), "medicineid", $medicineid, "medicine");
                            } else {
                                echo "该医生没有关联药物，请先关联药物";
                            }
                            ?>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>给药途径:</th>
                        <td>
                    	<?= $doctormedicineref->medicine->drug_way_arr; ?>
                        </td>
                        <td><a target="_blank" href="/medicinemgr/modify?medicineid=<?=$medicineid ?>">药品信息</a></td>
                    </tr>
                    <?php if( $doctormedicineref instanceof DoctorMedicineRef ){ ?>

                    <tr>
                        <th>用药时机:</th>
                        <td>
                    	<?=$doctormedicineref->drug_timespan_arr;?>
                        </td>
                        <td><a target="_blank" href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?=$doctormedicineref->id ?>">医生定制</a></td>
                    </tr>
                    <tr>
                        <th>标准用法:</th>
                        <td>
                    	<?php
                        echo HtmlCtr::getRadioCtrImp($doctormedicineref->getVvArray('drug_std_dosage_arr'), 'drug_std_dosage', $doctormedicineref->getDefaultDrug_dose(),
                                "");
                        ?>
                        </td>
                        <td><a target="_blank" href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?=$doctormedicineref->id ?>">医生定制</a></td>
                    </tr>
                    <tr>
                        <th>药物剂量:</th>
                        <td>
                    	<?php
                        echo HtmlCtr::getRadioCtrImp($doctormedicineref->getVvArray('drug_dose_arr'), 'drug_dose', $doctormedicineref->getDefaultDrug_dose(),
                                "");
                        ?>
                        </td>
                        <td><a target="_blank" href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?=$doctormedicineref->id ?>">医生定制</a></td>
                    </tr>
                    <tr>
                        <th>用药频率:</th>
                        <td>
                    	<?php
                        echo HtmlCtr::getRadioCtrImp($doctormedicineref->getVvArray('drug_frequency_arr'), 'drug_frequency',
                                $doctormedicineref->getDefaultDrug_frequency(), "");
                        ?>
                        </td>
                        <td><a target="_blank" href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?=$doctormedicineref->id ?>">医生定制</a></td>
                    </tr>
                    <tr>
                        <th>药材成分<br/>（中药才需要填写）</th>
                        <td>
                            <textarea name="herbjson" style="width: 80%; height: 100px;"><?= $doctormedicineref->herbjson?></textarea>
                            填写格式  药材名1=用量1|药材名2=用量2
                        </td>
                        <td><a target="_blank" href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?=$doctormedicineref->id ?>">医生定制</a></td>
                    </tr>
                    <tr>
                        <th>调药规则:</th>
                        <td>
                    	<?php
                        echo HtmlCtr::getRadioCtrImp($doctormedicineref->getVvArray('drug_change_arr'), 'drug_change',
                                $doctormedicineref->getDefaultDrug_change());
                        ?>
                        <a class="resetRadio tab-btn">置空</a>
                        </td>
                        <td><a target="_blank" href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?=$doctormedicineref->id ?>">医生定制</a></td>
                    </tr>
                    <tr>
                        <th>用药注意事项:</th>
                        <td>
                            <?=nl2br($doctormedicineref->doctorremark)?>
                        </td>
                        <td><a target="_blank" href="/doctormedicinerefmgr/modify?doctormedicinerefid=<?=$doctormedicineref->id ?>">医生定制</a></td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
                        </td>
                        <td></td>
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
                var doctormedicinepkgid = $("#doctormedicinepkgid").val();
                var url = medicineid==0 ? location.pathname : location.pathname + '?doctormedicinepkgid=' + doctormedicinepkgid + '&medicineid=' + medicineid;
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