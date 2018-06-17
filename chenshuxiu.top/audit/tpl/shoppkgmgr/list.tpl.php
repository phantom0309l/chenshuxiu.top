<?php
$pagetitle = "列表";
$cssFiles = [
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
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
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <form action="/shoppkgmgr/list" class="form-horizontal">
                <div class="form-group">
                    <div class="col-md-2">
                        <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
                <thead>
                <tr>
                    <td>id</td>
                    <td>创建时间</td>
                    <td>患者</td>
                    <td>所属医生</td>
                    <td>订单号</td>
                    <td>配送单号</td>
                    <td>实际运费</td>
                    <td>是否出库</td>
                    <td>是否发货</td>
                    <td>快递公司</td>
                    <td>快递号</td>
                    <td>出库时间</td>
                    <td>发货时间</td>
                    <td>是否需要推送到erp</td>
                    <td>是否已推送到erp</td>
                    <td>推送到erp的时间</td>
                    <td>状态</td>

                    <td>操作</td>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($shopPkgs as $i => $a) {
                    ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->createtime ?></td>
                        <td><?= $a->patient->name ?></td>
                        <td><?= $a->patient->doctor->name ?></td>
                        <td><?= $a->shoporderid ?></td>
                        <td><?= $a->fangcun_platform_no ?></td>
                        <td><?= $a->getExpress_price_real_yuan() ?></td>
                        <td><?= $a->getIs_goodsoutStr() ?></td>
                        <td><?= $a->getIs_sendoutStr() ?></td>
                        <td><?= $a->express_company ?></td>
                        <td><?= $a->getExpress_noStr() ?></td>
                        <td><?= $a->time_goodsout ?></td>
                        <td><?= $a->time_sendout ?></td>
                        <td><?= $a->getNeed_push_erpStr() ?></td>
                        <td><?= $a->getIs_push_erpStr() ?></td>
                        <td><?= $a->time_push_erp ?></td>
                        <td><?= $a->getStatusStr() ?></td>

                        <td align="center">
                            <a target="_blank" href="/shoppkgmgr/one?shoppkgid=<?= $a->id ?>">查看详情</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?php if ($pagelink) {
            include $dtpl . "/pagelink.ctr.php";
        } ?>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
