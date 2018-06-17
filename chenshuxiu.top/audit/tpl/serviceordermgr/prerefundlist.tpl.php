<?php
$pagetitle = "待退款列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.searchBar {
    background-color: #fff;
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
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="table-responsive" style="background-color: #fff;">
            <table class="js-table-sections table table-hover">
                <thead>
                <tr>
                    <th style="width:30px;"></th>
                    <th>下单时间</th>
                    <th>患者</th>
                    <th>医生</th>
                    <th>疾病</th>
                    <th>商品</th>
                    <th>金额</th>
                    <th>有效期</th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php foreach ($quickpass_serviceitems as $quickpass_serviceitem) {
                    $patient = $quickpass_serviceitem->patient;
                    $serviceorder = $quickpass_serviceitem->serviceorder;
                    ?>
                    <tbody class="js-table-sections-header">
                    <tr>
                        <td class="text-center"><i class="fa fa-angle-right"></i></td>
                        <td><?= $serviceorder->createtime ?></td>
                        <td><?= $patient->name ?></td>
                        <td><?= $patient->doctor->name ?></td>
                        <td><?= $patient->disease->name ?></td>
                        <td><?= $serviceorder->serviceproduct->title ?></td>
                        <td>¥<?= $quickpass_serviceitem->getPrice_yuan() ?></td>
                        <td>
                            <?= $quickpass_serviceitem->starttime ?> 至
                            <?= $quickpass_serviceitem->endtime ?>
                        </td>
                        <td>
                            <a target="_blank" class="btn btn-sm btn-default"
                               href="/optaskmgr/listnew?patient_name=<?= $patient->name ?>&diseaseid=<?= $patient->diseaseid ?>&status_str=all">
                                查看
                            </a>

                            <a target="_blank" class="btn btn-sm btn-danger J_reject"
                               data-serviceitemid="<?= $quickpass_serviceitem->id ?>"
                               data-url="/serviceordermgr/refundreject"
                               href="javascript:void(0);">
                                拒绝
                            </a>

                            <a target="_blank" class="btn btn-sm btn-success J_pass"
                               data-serviceitemid="<?= $quickpass_serviceitem->id ?>"
                               data-url="/serviceordermgr/refundpass"
                               href="javascript:void(0);">
                                通过
                            </a>
                        </td>
                    </tr>
                    </tbody>
                    <tbody class="bg-gray-lighter">
                    <tr>
                        <td style="width:30px;">-</td>
                        <td class="text-gray-dark" colspan="8"><?= $quickpass_serviceitem->remark ?></td>
                    </tr>
                    </tbody>
                <?php } ?>
            </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
$(function() {
    App.initHelper('table-tools');
    
    $('.J_reject, .J_pass').on('click', function() {
        if (!confirm('确定操作吗？')) {
            return false;
        }
        var quickpass_serviceitemid = $(this).data('serviceitemid');
        var tr = $(this).parents('tr');
        $.ajax({
            type: "post",
            url: '/serviceordermgr/ajaxRefundReject',
            data: {
                quickpass_serviceitemid: quickpass_serviceitemid
            },
            dataType: "json",
            success: function(res) {
                if (res.errno == "0") {
                    alert('操作成功');
                } else {
                    alert('操作失败');
                } 
                tr.remove();
            },
            error: function() {
                alert('操作失败');
            }
        })
    })
})
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
