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
            <form action="/patientrecordmgr/modifypost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <input type="hidden" name="patientrecordid" value="<?= $patientrecord->id ?>" />
                    <?php 
                        $type = $patientrecord->type;
                    ?>
                    <tr>
                        <th width=140>分期日期</th>
                        <td>
                        	<div class="col-md-2">
                            	<input type="text" class="calendar" name="<?=$type?>[thedate]" value="<?= $patientrecord_data["thedate"] ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>分期类型</th>
                        <td>
                        	<div class="col-md-2">
                            	<?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('staging_type'),"staging[type]",$patientrecord_data["type"],'form-control'); ?>
                        	</div>
                        </td>
                    </tr>
                    <tr>
                        <th>T</th>
                        <td>
                        	<div class="col-md-2">
                        		<?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('staging_T'),"staging[T]",$patientrecord_data["T"],'form-control'); ?>
                        	</div>
                        </td>
                    </tr>
                    <tr>
                        <th>N</th>
                        <td>
                        	<div class="col-md-2">
                        		<?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('staging_N'),"staging[N]",$patientrecord_data["N"],'form-control'); ?>
                        	</div>
                        </td>
                    </tr>
                    <tr>
                        <th>M</th>
                        <td>
                        	<div class="col-md-2">
                        		<?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('staging_M'),"staging[M]",$patientrecord_data["M"],'form-control'); ?>
                        	</div>
                        </td>
                    </tr>
                    <tr>
                        <th>分期</th>
                        <td>
                        	<div class="col-md-2">
                            	<?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getOptionByCode('staging_stage'),"staging[stage]",$patientrecord_data["stage"],'form-control'); ?>
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
