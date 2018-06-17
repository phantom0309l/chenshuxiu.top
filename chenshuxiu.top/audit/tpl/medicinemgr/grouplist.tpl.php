<?php
$pagetitle = "药品分组列表 Medicines（group）";
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
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>分组名</td>
                        <td>药物数</td>
                    </tr>
                </thead>
                <tbody>
<?php
foreach ($groupArr as $groupstr) {
    ?>
                    <tr>
                        <td><?= $groupstr === '' ? '(未分类)' : $groupstr ?></td>
                        <td><a href="/medicinemgr/list?diseaseid=0&groupstr=<?=$groupstr === '' ? '(未分类)' : $groupstr?>"><?= count(MedicineDao::getListByGroupstr($groupstr)); ?></a></td>
                    </tr>
<?php } ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
