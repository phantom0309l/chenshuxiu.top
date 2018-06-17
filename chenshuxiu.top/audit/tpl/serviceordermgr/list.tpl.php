<?php
$pagetitle = "服务类订单列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.searchBar {
    border: 0;
    background-color: #fff;
    padding: 20px 30px;
}
.searchBar .form-group label {
    font-weight: 500;
    width: 9%;
    text-align: left;
}
.padding-left0{
    padding-left: 0px;
}
#main-container {
    background: #f5f5f5 !important;
}
.js-table-sections-header.open > tr:hover {
    background-color: #f1f1f9;
}
.js-table-sections-header.open > tr {
    background-color: #f1f1f9;
}
.text-gray-dark {
    color: #787878;
}
.bg-gray-lighter {
    background-color: #f1f1f9;
}
.pagelink {
    margin-bottom: 20px;
    padding-top: 10px;
    margin-top: -20px;
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="content bg-white border-b">
            <div class="row items-push text-uppercase">
                <div class="col-xs-6 col-sm-3">
                    <div class="font-w700 text-gray-darker animated fadeIn">支付总金额</div>
                    <div class="text-muted animated fadeIn"><small><i class="si si-calendar"></i> All Time</small></div>
                    <a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0);">¥ <?= sprintf("%.2f", $totalAmount / 100) ?></a>
                </div>
                <div class="col-xs-6 col-sm-3">
                    <div class="font-w700 text-gray-darker animated fadeIn">退款总金额</div>
                    <div class="text-muted animated fadeIn"><small><i class="si si-calendar"></i> All Time</small></div>
                    <a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0);">¥ <?= sprintf("%.2f", $totalRefundAmount / 100) ?></a>
                </div>
                <div class="col-xs-6 col-sm-3">
                    <div class="font-w700 text-gray-darker animated fadeIn">本月支付总金额</div>
                    <div class="text-muted animated fadeIn"><small><i class="si si-calendar"></i> All Time</small></div>
                    <a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0);">¥ <?= sprintf("%.2f", $totalAmount_month / 100) ?></a>
                </div>
                <div class="col-xs-6 col-sm-3">
                    <div class="font-w700 text-gray-darker animated fadeIn">今天支付总金额</div>
                    <div class="text-muted animated fadeIn"><small><i class="si si-calendar"></i> All Time</small></div>
                    <a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0);">¥ <?= sprintf("%.2f", $totalAmount_today / 100) ?></a>
                </div>
            </div>
        </div>
        <div class="searchBar">
            <form class="form-horizontal" action="/serviceordermgr/list" method="get">
                <div class="form-group mt10">
                    <label class="control-label col-md-2" style="text-align:left">类型</label>
                    <div class="col-md-3">
                        <select name="type" autocomplete="off" class="form-control">
                            <?php
                            foreach ($types as $key => $value) { ?>
                                <option <?= $key == $type ? 'selected' : '' ?>
                                        value="<?= $key ?>"><?= $value ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <label class="control-label col-md-2" style="text-align:left">状态</label>
                    <div class="col-md-3">
                        <select name="status" autocomplete="off" class="form-control">
                            <?php
                            foreach ($allStatus as $key => $value) { ?>
                                <option <?= $key == $status ? 'selected' : '' ?>
                                        value="<?= $key ?>"><?= $value ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group mt10">
                    <label class="control-label col-md-2" style="text-align:left">日期范围</label>
                    <div class="col-md-3">
                        <input class="form-control" type="text" id="date_range" name="date_range"
                               value="<?= $date_range ?>" placeholder="日期范围">
                    </div>
                </div>
                <div class="mt10">
                    <input type="submit" class="btn btn-success" value="组合筛选"/>
                </div>
            </form>
        </div>
        <div class="table-responsive" style="background-color: #fff;">
            <table class="js-table-sections table table-hover">
                <thead>
                <tr>
                    <th style="width:30px;"></th>
                    <th class="tc">#</th>
                    <th>下单时间</th>
                    <th>患者</th>
                    <th>医生</th>
                    <th>疾病</th>
                    <th>商品</th>
                    <th class="tc">时长</th>
                    <th>金额</th>
                    <th>到期时间</th>
                    <th class="tc">第几单</th>
                    <th class="tc">状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php foreach ($serviceorders as $index => $serviceorder) {
                    $patient = $serviceorder->patient;
                    ?>
                    <tbody class="js-table-sections-header">
                    <tr>
                        <td class="text-center"><i class="fa fa-angle-right"></i></td>
                        <td class="tc"><?= $index + 1 ?></td>
                        <td><?= $serviceorder->createtime ?></td>
                        <td><?= $patient->name ?></td>
                        <td><?= $patient->doctor->name ?></td>
                        <td><?= $patient->disease->name ?></td>
                        <td><?= $serviceorder->serviceproduct->title ?></td>
                        <td class="tc"><?= $serviceorder->serviceproduct->item_cnt ?></td>
                        <td>¥<?= $serviceorder->getAmount_yuan() ?></td>
                        <td><?= $serviceorder->getEndTime() ?></td>
                        <td class="tc">
                            <span class="badge badge-primary"><?= $serviceorder->pos ?></span>
                        </td>
                        <td class="tc"><?= $serviceorder->is_pay == 1 ? '<span class="text-success">已支付</span>' : '<span class="text-danger">未支付</span>' ?></td>
                        <td>
                            <a target="_blank" class="btn btn-sm btn-default"
                               href="/optaskmgr/listnew?patient_name=<?= $patient->name ?>&diseaseid=<?= $patient->diseaseid ?>&status_str=all">
                                查看
                            </a>
                        </td>
                    </tr>
                    </tbody>
                    <tbody class="bg-gray-lighter">
                    <?php $items = $serviceorder->getItems();
                    foreach ($items as $key => $item) {
                        ?>
                        <tr>
                            <td style="width:30px;">-</td>
                            <td class="text-gray-dark tc"><?= $key + 1 ?></td>
                            <td class="text-gray-dark"><?= $item->starttime ?></td>
                            <td class="text-gray-dark"></td>
                            <td class="text-gray-dark"></td>
                            <td class="text-gray-dark"></td>
                            <td class="text-gray-dark"></td>
                            <td class="text-gray-dark"></td>
                            <td class="text-gray-dark">¥<?= $item->getPrice_yuan() ?></td>
                            <td class="text-gray-dark"><?= $item->endtime ?></td>
                            <td class="text-gray-dark"></td>
                            <td class="text-gray-dark tc">
                                <?php
                                if ($item->status == 1) {
                                    if ($item->is_refund == 1) {
                                        if ($item->is_timeout) {
                                            echo '<span class="text-warning">超时退款</span>';
                                        } else {
                                            echo '仅退款';
                                        }
                                    } else {
                                        if ($item->refund_optaskid > 0) {
                                            echo '超时待退款';
                                        } else {
                                            echo '<span class="text-success">正常</span>';
                                        }
                                    }
                                } else {
                                    echo '<span class="text-danger">退订</span>';
                                }
                                ?>
                            </td>
                            <td class="text-gray-dark"></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                <?php } ?>
            </table>
            <div class="pagelink border-t">
                <?php include $dtpl . "/pagelink.ctr.php"; ?>
            </div>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
$(function() {
    App.initHelper('table-tools');
    
    //日期范围选择
    laydate.render({ 
        elem: '#date_range',
        range: '至' //或 range: '~' 来自定义分割字符
    });
})
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
