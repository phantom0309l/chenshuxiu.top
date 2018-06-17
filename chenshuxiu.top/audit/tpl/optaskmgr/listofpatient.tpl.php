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
    <?php include_once $tpl . "/patientmgr/_menu.tpl.php"; ?>
    <div class="content-div">
    <section class="col-md-12">
        <div class="searchBar">
            <label>
                <?php echo $patient->getMaskName() . " 的任务 "; ?>
            </label>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <td>创建日期</td>
                    <td>微信名</td>
                    <td>姓名</td>
                    <td>任务类型</td>
                    <td>内容</td>
                    <td>是否已关闭</td>
                    <td>责任人</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($optasks as $a) {
                    ?>
                    <tr>
                        <td><?= $a->getCreateDay() ?></td>
                        <td><?= $a->wxuser->nickname ?></td>
                        <td><?= $a->patient->name ?></td>
                        <td><?= $a->optasktpl->title ?> </td>
                        <td><?= $a->content ?></td>
                        <td><?= $a->status ? '关闭' : '未关闭'  ?></td>
                        <td><?= $a->auditor->name ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            </table>
        </div>
    </section>
    </div>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
