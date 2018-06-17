<?php
$pagetitle = "疾病量表关联修改 DiseasePaperTplRef";
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
        <form action="/diseasepapertplrefmgr/modifypost" method="post">
            <input type="hidden" name="diseasepapertplrefid" value="<?= $diseasepapertplref->id ?>"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width='140'>id</th>
                    <td><?= $diseasepapertplref->id ?></td>
                </tr>
                <tr>
                    <th>创建时间</th>
                    <td><?= $diseasepapertplref->createtime ?></td>
                </tr>
                <tr>
                    <th>疾病</th>
                    <td><?= $diseasepapertplref->disease->name ?></td>
                </tr>
                <tr>
                    <th>
                        医生
                    </th>
                    <td>
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDoctorCtrArray($mydisease->id), "doctorid", $diseasepapertplref->doctorid); ?>
                    </td>
                </tr>
                <tr>
                    <th>量表名</th>
                    <td><?= $diseasepapertplref->papertpl->title ?></td>
                </tr>
                <tr>
                    <th>是否展示在运营端 (填1 为是， 填0 为否)</th>
                    <td>
                        <input type="text" name="show_in_audit" value="<?= $diseasepapertplref->show_in_audit ?>"/>
                    </td>

                </tr>
                <tr>
                    <th>是否展示在患者端 (填1 为是， 填0 为否)</th>
                    <td>
                        <input type="text" name="show_in_wx" value="<?= $diseasepapertplref->show_in_wx ?>"/>
                    </td>

                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" value="提交"/>
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

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
