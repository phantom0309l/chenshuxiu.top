<?php
$pagetitle = "药品库-疾病定制 分组列表 DiseaseMedicineRef（group）";
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
                            <td>
                                <a href="/diseasemedicinerefmgr/list?diseaseid=0&groupstr=<?= $groupstr === '' ? '(未分类)' : $groupstr ?>"><?= count(DiseaseMedicineRefDao::getListByDiseaseid($mydisease->id, $groupstr)); ?></a>
                            </td>
                        </tr>
                    <?php } ?>
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
