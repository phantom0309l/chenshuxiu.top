<?php
$pagetitle = "修改患者疾病 <span style='color:red'>请谨慎修改(涉及到数据的修复)</span>";
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
            <form action="/pcardmgr/modifydiseasepost" method="post">
            	    <input type="hidden" name="pcardid" value="<?= $pcard->id ?>">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>patientid</th>
						<td><?= $pcard->patientid?></td>
                    </tr>
                    <tr>
                        <th>创建时间</th>
                        <td><?= $pcard->patient->createtime ?></td>
                    </tr>
                    <tr>
                        <th>姓名</th>
                        <td>
                            <?php echo $pcard->patient->name ?>
                        </td>
                    </tr>
                    <tr>
                        <th>选择疾病/医生</th>
                        <td>
                            <?php echo $pcard->doctor->name; ?>
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toDiseaseCtrArray($diseases, false),"diseaseid",$diseaseid ,"f18"); ?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input class="btn btn-success" type="submit" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
