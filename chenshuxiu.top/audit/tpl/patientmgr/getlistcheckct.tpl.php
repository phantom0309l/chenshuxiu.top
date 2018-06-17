<?php
$pagetitle = "检查项目-肺部CT,加呼吸";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <form action="/patientmgr/getlistcheckct" method="get">
            <label for="">日期:</label>
            从
            <input type="text" class="calendar" style="width: 100px; height: 27px;" name="fromdate" value="<?= $fromdate ?>" />
            至
            <input type="text" class="calendar" style="width: 100px; height: 27px;" name="todate" value="<?= $todate ?>" />
            <input type="submit" class="btn_style3" value="筛选">
        </form>
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th>patientid</th>
                        <th>姓名</th>
                        <th>院内识别ID</th>
                        <th>时间</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($list as $a) {
                    ?>
                    <tr>
                        <td><?= $a['patient']->id ?></td>
                        <td><?= $a['patient']->name ?></td>
                        <td><?= $a['pcard']->getYuanNeiStr('<br />') ?></td>
                        <td><?= $a['createtime'] ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
