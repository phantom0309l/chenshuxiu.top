<?php
$pagetitle = "量表列表 Papers";
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
        <div class="searchBar">
            <?php
            if ($patient instanceof Patient) {
                ?>
                <label>
                    <?php echo $patient->getMaskName() . " 的量表 "; ?>
                </label>
                <a href="/papermgr/list?patientid=<?= $patient->id ?>">&nbsp;全部量表</a>
                <?php
            } else {
                ?>
                <form>
                    <div class="mt10">
                        <label for="">按量表模板:</label>
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toPaperTplCtrArray($papertpls, true), "papertplid", $papertplid); ?>
                    </div>
                    <div class="mt10">
                        <input type="submit" class="btn btn-success" value="查询">
                    </div>
                </form>
                <?php
            }
            ?>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
            <thead>
            <tr>
                <td>ID</td>
                <td>创建日期</td>
                <td>时刻</td>
                <td>患者</td>
                <td>标题</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
            <?php
                foreach ($papers as $a) {
                    ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td><?= $a->getCreateHi() ?></td>
                        <td>
                            <?php
                            if ($a->patient instanceof Patient) {
                                echo $a->patient->getMaskName();
                            }
                            ?>
                        </td>
                        <td>
                            <a href="/papermgr/list?patientid=<?= $patient->id ?>&papertplid=<?= $a->papertplid ?>">
                                <?= $a->papertpl->title ?>
                                [得分:<?= $a->XAnswerSheet->score ?>]
                            </a>
                        </td>
                        <td>
                            <a target="_blank" href="/xanswersheetmgr/modify?xanswersheetid=<?= $a->xanswersheetid ?>">查看答卷</a>
                        </td>
                    </tr>
                    <?php
                }
            ?>
            <tr>
                <td colspan=100 class="pagelink">
                    <?php include $dtpl . "/pagelink.ctr.php"; ?>
                </td>
            </tr>
            </tbody>
        </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
