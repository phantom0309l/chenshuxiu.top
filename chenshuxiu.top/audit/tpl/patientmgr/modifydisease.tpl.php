<?php
$pagetitle = "修改[{$pcard->patient->name}]患者疾病";
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
        <form action="/patientmgr/modifydiseasepost" method="post">
            <input type="hidden" name="pcardid" value="<?= $pcard->id ?>" />
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th width=140>姓名</th>
                        <td><?= $pcard->patient->name?></td>
                    </tr>
                    <tr>
                        <th width=140>医生</th>
                        <td><?= $pcard->doctor->name?></td>
                    </tr>
                    <tr>
                        <th>修改疾病</th>
                        <td>
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseCtrArray(false), 'diseaseid', $pcard->diseaseid, 'f18') ?>
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
