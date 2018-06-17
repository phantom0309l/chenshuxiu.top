<?php
$pagetitle = "【{$patient->name}】 呼吸困难量表填写计划列表";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td>id</td>
                    <td>createtime</td>
                    <td>plan_date</td>
                    <td>发送状态</td>
                    <td>填写状态</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($plan_qdxzs as $a) {
                        ?>
                            <tr>
                                <td><?= $a->id ?></td>
                                <td><?= $a->createtime ?></td>
                                <td><?= $a->plan_date ?></td>
                                <td>
                                    <?php
                                        if ($a->status == 1) {
                                            ?> <span class="label label-success">已发送</span> <?php
                                        } elseif ($a->status == 0) {
                                            ?> <span class="label label-danger">未发送</span> <?php
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        $paper = PaperDao::getByPaperTplObjtypeObjid($papertpl, 'Plan_qdxz', $a->id);
                                        if ($a->status == 1) {
                                            if ($paper instanceof Paper) {
                                                ?> <span class="label label-success">已填写</span> <?php
                                            } else {
                                                ?> <span class="label label-danger">未填写</span> <?php
                                            }
                                        }
                                    ?>
                                </td>
                                <td align="center">
                                    <?php
                                        if ($paper instanceof Paper) {
                                            ?> <a target="_blank" href="/xanswersheetmgr/modify?xanswersheetid=<?=$paper->xanswersheetid ?>">查看量表填写</a> <?php
                                        }
                                    ?>

                                </td>
                            </tr>
                        <?php
                    }
                ?>
                </tbody>
        </table>
        </div>
        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
