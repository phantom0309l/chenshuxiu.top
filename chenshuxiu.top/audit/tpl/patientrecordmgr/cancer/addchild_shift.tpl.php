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
                        <th width=140>转移日期</th>
                        <td>
                        	<div class="col-md-2">
                            	<input type="text" class="calendar" name="<?=$type?>[thedate]" value="<?= $patientrecord_data["thedate"] ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>转移位置</th>
                        <td>
                        	<div class="col-md-2">
                            	<?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('shift_position'),"shift[position]",$patientrecord_data["position"],'form-control position'); ?>
                        		<input type="text" style="display:<?= $patientrecord_data["position"] == '其他' ? 'inherit' : 'none'; ?>" class="form-control" id="position_other" name="shift[position_other]" value="<?= $patientrecord_data["position_other"] ?>">
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
	});
</script>
