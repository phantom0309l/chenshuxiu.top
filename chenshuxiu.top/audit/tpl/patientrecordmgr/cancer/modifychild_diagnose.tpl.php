<?php
$pagetitle = "备注修改";
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
            <form>
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <input type="hidden" name="patientrecordid" value="<?= $patientrecord->id ?>" />
                    <?php 
                        $type = $patientrecord->type;
                    ?>
                    <tr>
                        <th width=140>诊断日期</th>
                        <td>
                        	<div class="col-md-2">
                            	<input type="text" class="calendar" name="<?=$type?>[thedate]" value="<?= $patientrecord_data["thedate"] ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>部位</th>
                        <td>
                        	<div class="col-md-2">
                            	<?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('diagnose_position'),"diagnose[position]",$patientrecord_data["position"],'form-control position'); ?>
                        		<input type="text" style="display:<?= $patientrecord_data["position"] == '其他' ? 'inherit' : 'none'; ?>" class="form-control" id="position_other" name="diagnose[position_other]" value="<?= $patientrecord_data["position_other"] ?>">
                        	</div>
                        </td>
                    </tr>
                    <tr>
                        <th>组织起源</th>
                        <td>
                        	<div class="col-md-2">
                        		<?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('diagnose_start'),"diagnose[diagnose_start]",$patientrecord_data["diagnose_start"],'form-control diagnose_start'); ?>
                        		<input type="text" style="display:<?= $patientrecord_data["diagnose_start"] == '其他' ? 'inherit' : 'none'; ?>" class="form-control" id="diagnose_start_other" name="diagnose[diagnose_start_other]" value="<?= $patientrecord_data["diagnose_start_other"] ?>">
                        	</div>
                        </td>
                    </tr>
                    <tr>
                        <th>特殊</th>
                        <td>
                        	<div class="col-md-2">
                            	<?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('diagnose_special'),"diagnose[special]",$patientrecord_data["special"],'form-control special'); ?>
                        		<input type="text" style="display:<?= $patientrecord_data["special"] == '其他' ? 'inherit' : 'none'; ?>" class="form-control" id="special_other" name="diagnose[special_other]" value="<?= $patientrecord_data["special_other"] ?>">
                        	</div>
                        </td>	
                    </tr>
                    <tr>
                        <th>备注</th>
                        <td>
                        	<div class="col-md-2">
                            	<textarea name="content" rows="4" cols="40"><?= $patientrecord->content ?></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>

<script type="text/javascript">
	$(function(){
		$(document).on('change', '.position', function(){
			var me = $(this);
			var option = me.val();
			if (option == '其他') {
				$("#position_other").show();
			} else {
				$("#position_other").hide();
			}
		});
		$(document).on('change', '.diagnose_start', function(){
			var me = $(this);
			var option = me.val();
			if (option == '其他') {
				$("#diagnose_start_other").show();
			} else {
				$("#diagnose_start_other").hide();
			}
		});
		$(document).on('change', '.special', function(){
			var me = $(this);
			var option = me.val();
			if (option == '其他') {
				$("#special_other").show();
			} else {
				$("#special_other").hide();
			}
		});
	});
</script>
