<?php
$pagetitle = "代您开药订单列表";
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
                    <div class="text-muted animated fadeIn">
                        <small><i class="si si-calendar"></i> All Time</small>
                    </div>
                    <a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0);">¥ <?= sprintf("%.2f", $totalAmount / 100) ?></a>
                </div>
                <div class="col-xs-6 col-sm-3">
                    <div class="font-w700 text-gray-darker animated fadeIn">退款总金额</div>
                    <div class="text-muted animated fadeIn"><small><i class="si si-calendar"></i> All Time</small></div>
                    <a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0);">¥ <?= sprintf("%.2f", $totalRefundAmount / 100) ?></a>
                </div>
                <div class="col-xs-6 col-sm-3">
                    <div class="font-w700 text-gray-darker animated fadeIn">本月支付总金额</div>
                    <div class="text-muted animated fadeIn">
                        <small><i class="si si-calendar"></i> All Time</small>
                    </div>
                    <a class="h2 font-w300 text-primary animated flipInX"
                       href="javascript:void(0);">¥ <?= sprintf("%.2f", $totalAmount_month / 100) ?></a>
                </div>
                <div class="col-xs-6 col-sm-3">
                    <div class="font-w700 text-gray-darker animated fadeIn">今天支付总金额</div>
                    <div class="text-muted animated fadeIn">
                        <small><i class="si si-calendar"></i> All Time</small>
                    </div>
                    <a class="h2 font-w300 text-primary animated flipInX"
                       href="javascript:void(0);">¥ <?= sprintf("%.2f", $totalAmount_today / 100) ?></a>
                </div>
            </div>
        </div>
        <div class="searchBar">
            <form class="form-horizontal" action="/errandordermgr/list" method="get">
                <div class="form-group mt10">
                    <label class="control-label col-md-2" style="text-align:left">日期范围</label>
                    <div class="col-md-3">
                        <input class="form-control" type="text" id="date_range" name="date_range"
                               value="<?= $date_range ?>" placeholder="日期范围">
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
                    <th style="width: 95px;">下单时间</th>
                    <th style="width: 95px;">支付时间</th>
                    <th>患者</th>
                    <th>医生</th>
                    <th>疾病</th>
                    <th>金额</th>
                    <th class="tc">医保卡</th>
                    <th>收货人</th>
                    <th>手机号</th>
                    <th>收货地址</th>
                    <th class="tc">第几单</th>
                    <th class="tc">状态</th>
                    <th class="tc">操作</th>
                </tr>
                </thead>
                <?php foreach ($errandorders as $index => $errandorder) {
                    $patient = $errandorder->patient;
                    $shopAddress = $errandorder->shopaddress;
                    if ($shopAddress instanceof ShopAddress) {
                        $shopAddress_id = $shopAddress->id;
                        $shopAddress_linkman_name = $shopAddress->linkman_name;
                        $shopAddress_linkman_mobile = $shopAddress->linkman_mobile;
                        $shopAddress_detailAddress = $shopAddress->getDetailAddress();
                    } else {
                        $shopAddress_id = '';
                        $shopAddress_linkman_name = '';
                        $shopAddress_linkman_mobile = '';
                        $shopAddress_detailAddress = '';
                    }
                    ?>
                    <tbody class="js-table-sections-header">
                    <tr>
                        <td class="text-center"><i class="fa fa-angle-right"></i></td>
                        <td class="tc"><?= $index + 1 ?></td>
                        <td><?= $errandorder->createtime ?></td>
                        <td><?= $errandorder->time_pay ?></td>
                        <td><?= $patient->name ?></td>
                        <td><?= $patient->doctor->name ?></td>
                        <td><?= $patient->disease->name ?></td>
                        <td>¥<?= $errandorder->getAmount_yuan() ?></td>
                        <td class="tc"><span class="label label-primary"><?= $errandorder->is_use_ybk ? '使用' : '不使用' ?></span></td>
                        <td><?= $shopAddress_linkman_name ?></td>
                        <td><?= $shopAddress_linkman_mobile ?></td>
                        <td><?= $shopAddress_detailAddress ?></td>
                        <td class="tc">
                            <span class="badge badge-primary"><?= $errandorder->pos ?></span>
                        </td>
                        <td class="tc">
                            <?php
                            if ($errandorder->refund_amount > 0) {
                                echo '<span class="text-warning">已退款</span>';
                            } elseif ($errandorder->is_pay == 1) {
                                echo '<span class="text-success">已支付</span>';
                            } else {
                                echo '<span class="text-danger">待支付</span>';
                            }
                            ?>
                            |
                            <?= $errandorder->status == 1 ? '<span class="text-success">有效</span>' : '<span class="text-danger">无效</span>' ?>
                        </td>
                        <td class="tc">
                            <?php if ($errandorder->canRefund()) { ?>
                                <a target="_blank" class="btn btn-sm btn-default J_refund_btn"
                                   href="javascript:void(0);"
                                   data-errandorderid="<?= $errandorder->id ?>">
                                    退款
                                </a>
                            <?php } ?>
                            <a target="_blank" class="btn btn-sm btn-default"
                               href="/optaskmgr/listnew?patient_name=<?= $patient->name ?>&diseaseid=<?= $patient->diseaseid ?>&status_str=all">
                                查看
                            </a>
                        </td>
                    </tr>
                    </tbody>
                    <tbody class="bg-gray-lighter">
                    <?php if ($errandorder->is_use_ybk) { ?>
                        <tr>
                            <td class="text-gray-dark">-</td>
                            <td class="text-gray-dark"></td>
                            <td class="text-gray-dark">患者诉求</td>
                            <td class="text-gray-dark" colspan="12">
                                <p><?= nl2br($errandorder->content) ?></p>
                            </td>
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
<script>
    $(function () {
        $('.J_refund_btn').on('click', function () {
            if (confirm('确认退款吗？')) {
                var errandorderid = $(this).data('errandorderid');
                $.ajax({
                    type: "post",
                    url: "/errandordermgr/ajaxrefundpost",
                    data: {errandorderid: errandorderid},
                    dataType: "json",
                    success: function (response) {
                        if (response.errno === "0") {
                            alert('退款成功');
                            window.location.reload();
                        } else {
                            alert(response.errmsg);
                        }
                    },
                    error: function () {
                        alert('退款失败');
                    }
                });
            }
        });
    })
</script>
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
