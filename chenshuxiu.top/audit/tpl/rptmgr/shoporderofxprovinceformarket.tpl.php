<?php
$pagetitle = "ADHD销售报表（省份维度）";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
table{
    table-layout:fixed;
}
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <?php
                $yeararr = XDateTime::getYearArrToNew('2017');
                foreach($yeararr as $year_one){ ?>
                <a class="btn <?= $year_one == $year ? 'btn-primary' : 'btn-default' ?>" href="/rptmgr/shoporderofxprovinceformarket?year=<?= $year_one ?>"><?= $year_one ?>年</a>
                <?php } ?>
                <input class="year hidden" value="<?=$year?>">
            </div>
            <section class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered tdcenter">
                    <thead>
                    <tr>
                        <th width="100px">大区</th>
                        <th width="100px">省份</th>
                        <?php foreach ($months as $month) { ?>
                            <th width="100px"><?= $month . '交易额' ?></th>
                            <th width="100px"><?= $month . '退款额' ?></th>
                        <?php } ?>
                        <th width="100px" class="red"><?= $year . '总交易额' ?></th>
                        <th width="100px" class="red"><?= $year . '总退款额' ?></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $area => $arr) { ?>
                            <tr>
                                <td style="width: 100px;" rowspan=<?= count($arr)+1 ?>><?= $area ?></td>
                                <?php foreach ($arr as $k => $v) { ?>
                                    <tr>
                                    <td style="width: 100px;"><?= $v["省份"] ?></td>
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
                    </tbody>
                    </table>
                </div>
            </section>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
