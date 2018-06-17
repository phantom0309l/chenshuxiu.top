<?php
$pagetitle = "汇报历史";
$cssFiles = [
    "{$img_uri}/m/css/uploadify.css"
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
textarea {
    border: 0;
    resize:none;
}

.table-header-bg > thead > tr > th, .table-header-bg > thead > tr > td {
    background-color: #f9f9f9;
    color: #333;
}

.table > tbody > tr:first-child > td, .table > tbody > tr:first-child > th {
    border-top: 0;
}

.table {
    white-space: nowrap;
    margin-bottom: 0;
}

.block-content .block-content {
    overflow-x: auto;
    padding: 15px;
}

.modal-content > .block-header {
    background-color: #438eb9;
}

.reports {
    margin-bottom: 15px;
}

a {
    cursor: pointer;
}

.js-table-sections-header.open > tr {
    background-color: #f7f7f7;
}

.table-hover > .sub-body > tr:hover {
    background-color: unset;
}

STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="overflow-x: auto;">
            <div class="searchBar">
                <h5 class="pt10 pb10">患者：<?= $patient->name ?></h5>
            </div>
            <div class="table-responsive">
                <table class="js-table-sections table table-hover reports">
                    <thead>
                    <tr>
                        <th style="width:30px;"></th>
                        <th class="tc" style="width: 120px;">日期</th>
                        <th class="tc" style="width: 120px;">时间</th>
                        <th class="tc" style="width: 120px;">医生</th>
                        <th>批复</th>
                        <th class="tc" style="width: 70px;">操作</th>
                    </tr>
                    </thead>
                    <?php foreach ($reports as $report) {
                        $doctorComments = $report->getDoctorComments();
                        ?>
                        <tbody class="js-table-sections-header">
                        <tr>
                            <td class="text-center"><i class="fa fa-angle-right"></i></td>
                            <td class="tc"><?= date('Y-m-d', strtotime($report->createtime)) ?></td>
                            <td class="tc"><?= date('H:i:s', strtotime($report->createtime)) ?></td>
                            <td class="tc"><?= $report->doctor->name ?></td>
                            <td class="text-gray-dark">有 <span class="text-primary"><?= count($doctorComments) ?></span> 条医生批复，点击展开查看</td>
                            <td class="tc">
                                <button class="btn btn-xs btn-default one_J" data-toggle="modal"
                                        data-target="#modal-report"
                                        type="button" title="查看"
                                        data-reportid="<?= $report->id ?>"
                                        data-original-title="查看"><i class="fa fa-search"></i></button>
                            </td>
                        </tr>
                        </tbody>
                        <tbody class="sub-body" style="background-color: #edf6fd;color: #43a3e5;-webkit-box-shadow: 0 2px #d6ebfa;box-shadow: 0 2px #d6ebfa;">
                        <?php
                        $doctorComments = $report->getDoctorComments();
                        foreach ($doctorComments as $doctorComment) { ?>
                            <tr>
                                <td style="width:30px;">-</td>
                                <td class="tc text-gray-dark"></td>
                                <td class="tc text-gray-dark"></td>
                                <td class="tc text-gray-dark"><?= $doctorComment->doctor->name ?></td>
                                <td class="tl" style="white-space: normal;"><?= $doctorComment->content ?></td>
                                <td class="tc">
                                    <i class="si si-info text-info" data-toggle="popover" data-placement="left" data-content="<?= $doctorComment->createtime ?>" data-original-title="批复时间"></i>
<!--                                    <button class="btn btn-xs btn-default"-->
<!--                                            data-toggle="popover"-->
<!--                                            title=""-->
<!--                                            data-placement="left"-->
<!--                                            data-content="--><?//= $doctorComment->createtime ?><!--"-->
<!--                                            type="button"-->
<!--                                            data-original-title="批复时间"><i class="si si-info"></i></button>-->
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    <?php } ?>
                </table>
            </div>
        </div>
    </section>
</div>
<div class="modal fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                    <h3 class="block-title">汇报详情</h3>
                </div>
                <div class="block-content" style="max-height: 500px; overflow: auto;">

                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function() {
    App.initHelper('table-tools');

    var reportid = '{$reportid}';

    $('.one_J').on('click', function () {
        reportid = $(this).data('reportid');
        loadModalData();
    })

    function loadModalData() {
        $('#modal-report .block-content').html('<div class="text-center p20">'+
                                                   '<i class="si si-refresh fa fa-spin"></i>'+
                                                   '<span class="ml5">loading...</span>'+
                                               '</div>');
        var data = {"reportid": reportid};
        $.ajax({
            "type": "get",
            "url": "/reportmgr/ajaxone",
            dataType: "html",
            data: data,
            "success": function (d) {
                try {
                    var response = eval('('+d+')');
                    if (response.errno) {
                    $('#modal-report .block-content').html('<div class="text-center p20">'+
                                                               '<span class="text-danger">' + response.errmsg + '</span>'+
                                                           '</div>');
                    } else {
                        $('#modal-report .block-content').html(d);
                    }
                } catch(e) {
                    $('#modal-report .block-content').html(d);
                }
            },
            "error": function(d) {
                $('#modal-report .block-content').html('<div class="text-center p20">'+
                                                           '<span class="text-danger mr5">加载失败 </span>'+
                                                           '<button id="modal-refresh" class="btn btn-sm btn-danger" type="button"><i class="fa fa-refresh"></i> 重试</button>'+
                                                       '</div>');
            }
        });
    }

    $(document).on('click', '#modal-refresh', function(e) {
        loadModalData();
    })

    // MARK: - URL上带过来的reportid，直接弹出modal
    if (reportid != '0') {
        $('#modal-report').modal('show');
        loadModalData();
    }
})
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
