<?php
$pagetitle = "ADHD销售报表（医生维度）";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
]; //填写完整地址
$pageStyle = <<<STYLE
table{
    table-layout:fixed;
}
.control-label{
    text-align:left;
    font-weight: 500;
    width: 12%;
}
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <form class="form-horizontal" action="/rptmgr/shoporderofdoctorformarket" method="get">
                    <div class="form-group mt10">
                        <label class="control-label col-md-2" style="text-align:left">年度：</label>
                        <div class="col-md-3">
                        <?= HtmlCtr::getSelectCtrImp(XDateTime::getYearArrToNew('2017'), "year", $year, 'js-select2 form-control'); ?>
                        </div>
                    </div>
                    <div class="form-group mt10">
                        <label class="control-label col-md-2" style="text-align:left">是否开通门诊：</label>
                        <div class="col-md-3">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getSimpleDoctorMenzhenStatusCtrArray(), "menzhen_offset_daycnt", $menzhen_offset_daycnt, 'js-select2 form-control'); ?>
                        </div>
                        <label class="control-label col-md-2" style="text-align:left">大区：</label>
                        <div class="col-md-3">
                        <?= HtmlCtr::getSelectCtrImp($areas, "areaid", $areaid, 'js-select2 form-control'); ?>
                        </div>
                    </div>
                    <div class="form-group mt10">
                        <label class="control-label col-md-2" style="text-align:left">省份：</label>
                        <div class="col-md-3">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getAllXprovinceCtrArray(), "xprovinceid", $xprovinceid, 'js-select2 form-control'); ?>
                        </div>
                        <label class="control-label col-md-2" style="text-align:left">市场负责人：</label>
                        <div class="col-md-3">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getMarketAuditorCtrArray(), "auditorid_market", $auditorid_market, 'js-select2 form-control'); ?>
                        </div>
                    </div>
                    <div class="mt10">
                        <input type="submit" class="btn btn-success" value="筛选">
                    </div>
                </form>
            </div>
            <section class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th width="60px">大区</th>
                        <th width="120px">省份</th>
                        <th width="280px">医院</th>
                        <th width="100px">医生</th>
                        <th width="100px">当前是否开通门诊</th>
                        <?php foreach ($months as $month) { ?>
                            <th width="100px"><?= $month . '交易额' ?></th>
                            <th width="100px"><?= $month . '退款额' ?></th>
                        <?php } ?>
                        <th width="100px" class="red"><?= $year . '总交易额' ?></th>
                        <th width="100px" class="red"><?= $year . '总退款额' ?></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $area => $provinces) { ?>
                            <tr>
                                <td style="width: 60px;" rowspan=<?= $rowspanarr[$area]["cnt"] ?>><?= $area ?></td>
                                <?php foreach ($provinces as $province => $hospitals) { ?>
                                    <tr>
                                        <td style="width: 120px;" rowspan=<?= $rowspanarr[$area][$province]["cnt"] ?>><?= $province ?></td>
                                        <?php foreach ($hospitals as $hospital => $arr) { ?>
                                            <tr>
                                                <td style="width: 100px;" rowspan=<?= $rowspanarr[$area][$province][$hospital]["cnt"] ?>><?= $hospital ?></td>
                                                <?php foreach ($arr as $k => $v) { ?>
                                                    <tr>
                                                        <td><?= $v["医生"] ?></td>
                                                        <td><?= $v["当前是否开通门诊"] ?></td>
                                                        <?php foreach ($months as $month) { ?>
                                                            <td><?= sprintf("%.2f", $v[$month . '交易额']/100) ?></td>
                                                            <td><?= sprintf("%.2f", $v[$month . '退款额']/100) ?></td>
                                                        <?php } ?>
                                                        <td class="red"><?= sprintf("%.2f", $v[$year . '总交易额']/100) ?></td>
                                                        <td class="red"><?= sprintf("%.2f", $v[$year . '总退款额']/100) ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                </div>
            </section>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
$(function() {
    App.initHelper('select2');
});
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
