<?php
$pagetitle = "药品库-疾病定制 ";
if ($mydisease instanceof Disease) {
    $pagetitle .= $mydisease->name;
}
$pagetitle .= " DiseaseMedicineRef";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
]; //填写完整地址

$pageStyle = <<<STYLE
    .label-width{
        width: 80px;
    }
STYLE;

$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <form class="form-horizontal pr" action="/diseasemedicinerefmgr/list" method="get">
                <div class="form-group" style="margin-bottom: 0px;">
                    <label class="col-md-1 control-label label-width" for="">分组名</label>
                    <div class="col-md-2">
                        <?php echo HtmlCtr::getSelectCtrImp($grouplist, 'groupstr', $groupstr, "js-select2 form-control"); ?>
                    </div>
                    <label class="col-md-1 control-label label-width" for="medicine_name">药名</label>
                    <div class="col-md-2">
                        <input class="form-control" type="text" id="medicine_name" name="medicine_name" value="<?=$medicine_name?>" placeholder="请输入药名">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-success" type="submit">组合筛选</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="searchBar">
            <span class="text-primary">商品名，学名，分组是药品公共属性</span><br/>
            <span class="text-warning">剂量,频率,调药规则是药品在<?= $mydisease->name ?>下的定制属性</span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
            <tr>
                <td class="bg-primary">商品名/name<br/>学名</td>
                <td class="bg-primary">分组</td>
                <td class="bg-warning">疾病</td>
                <td class="bg-warning">用药时机</td>
                <td class="bg-warning">标准用法</td>
                <td class="bg-warning">剂量</td>
                <td class="bg-warning">频率</td>
                <td class="bg-warning">调药规则</td>
                <td>关联医生</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
            <?php
            if (empty($diseasemedicinerefs)) {
                echo "在此疾病下没有合适筛选条件的药物";
            }
            foreach ($diseasemedicinerefs as $a) {
                ?>
                <tr>
                    <td>
                        <?= $a->medicine->name ?>
                        <br/><span class="gray"><?= $a->medicine->scientificname ?></span>
                    </td>
                    <td><?= $a->medicine->groupstr === '' ? '(未分类)' : $a->medicine->groupstr ?></td>
                    <td><?= $a->disease->name ?></td>
                    <td><?= empty($a->getArrDrug_timespan()) ? '' : '...' ?></td>
                    <td><?= empty($a->getArrDrug_std_dosage()) ? '' : '...' ?></td>
                    <td><?= empty($a->getArrDrug_dose()) ? '' : '...' ?></td>
                    <td><?= empty($a->getArrDrug_frequency()) ? '' : '...' ?></td>
                    <td><?= empty($a->getArrDrug_change()) ? '' : '...' ?> </td>

                    <td>
                        <a href="/diseasemedicinerefmgr/doctormrefadd?diseasemedicinerefid=<?= $a->id ?>">
                            <?= $a->medicine->getDoctorMedicineRefCnt() ?>
                        </a>
                    </td>
                    <td>
                        <a target="_blank" href="/diseasemedicinerefmgr/modify?diseasemedicinerefid=<?= $a->id ?>">修改</a>
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
    $(function(){
        App.initHelper('select2');
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
