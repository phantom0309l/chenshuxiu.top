<?php
$pagetitle = "疾病药品快速拷贝列表 DiseaseMedicineRef(copy)";
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
                        <form action="/diseasemedicinerefmgr/copylist" method="get">
                            <div class="mt10">
                                <label for="medicine_name">按药名：</label>
                                <input id="medicine_name" name="medicine_name" value="<?= $medicine_name ?>">
                            </div>
                            <div class="mt10">
                                <input type="submit" class="" value="查找">
                            </div>
                        </form>
                    </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                        <thead>
                        <tr>
                            <td>medicineid</td>
                            <td>商品名/name</td>
                            <td>学名</td>
                            <td>分组</td>
                            <td>剂量</td>
                            <td>频率</td>
                            <td>调药规则</td>
                            <td>关联疾病</td>
                            <td>操作</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (empty($diseasemedicinerefs)) {
                            echo "在此疾病下没有合适筛选条件的药物";
                        }
                        foreach ($diseasemedicinerefs as $a) {
                            $is_this_disease = 0;
                            if ($mydisease->id == $a->diseaseid) {
                                $is_this_disease = 1;
                            }
                            ?>
                            <tr>
                                <td><?= $a->medicine->id ?></td>
                                <td><?= $a->medicine->name ?></td>
                                <td><?= $a->medicine->scientificname ?></td>
                                <td><?= $a->medicine->groupstr === '' ? '(未分类)' : $a->medicine->groupstr ?></td>
                                <td><?= implode('<br/>', $a->getArrDrug_dose()) ?></td>
                                <td><?= implode('<br/>', $a->getArrDrug_frequency()) ?></td>
                                <td><?= implode('<br/>', $a->getArrDrug_change()) ?> </td>
                                <td <?= $is_this_disease == 1 ? "style='color:red;'" : "" ?>><?= $a->disease->name ?></td>
                                <td>
                                    <?php if ($is_this_disease) { ?>
                                        快速拷贝
                                    <?php } else { ?>
                                        <a target="_blank"
                                           href="/diseasemedicinerefmgr/copypost?diseasemedicinerefid=<?= $a->id ?>">快速拷贝</a>
                                    <?php } ?>

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
