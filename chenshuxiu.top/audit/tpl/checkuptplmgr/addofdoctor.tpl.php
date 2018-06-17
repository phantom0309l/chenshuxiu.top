<?php
$pagetitle = "添加检查报告";
$cssFiles = [
    "{$img_uri}/vendor/oneui/js/plugins/datatables/jquery.dataTables.min.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [

]; //填写完整地址
$pageStyle = <<<STYLE
    .form-group .control-label {
        width: 135px;
    }

    .searchBar {
        background-color: #f9f9f9;
        border: 1px solid #e9e9e9;
    }

//    .paper-box>tbody>tr:first-child>td {
//        border-top: 0;
//    }

    .table.dataTable thead>tr>th {
        padding: 12px 10px;
    }

    .paper-box>tbody>tr {
        cursor: pointer;
    }

    .dataTables_filter>label {
        width: 100%;
    }

    .dataTables_filter>label>.form-control {
        width: 90%;
    }

    .selected {
         background-color: #f9f9f9;
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = true;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <?php include_once $tpl . "/doctorconfigmgr/_menu.tpl.php"; ?>
        <div class="content-div block-content">
            <section class="col-md-6 J_papertpls">
                <div class="block block-bordered">
                    <div class="block-header bg-gray-lighter J_diseases">
                        <?php foreach ($diseases as $disease) { ?>
                            <button class="J_disease_btn btn btn-minw <?= $disease->id == $diseaseid ? 'btn-primary' : 'btn-defualt' ?> btn-default"
                                    type="button"
                                    data-diseaseid="<?= $disease->id ?>"><?= $disease->name ?></button>
                        <?php } ?>
                    </div>
                    <div class="block-content scroll-y">
                        <div class="table-responsive">
                            <table class="table table-bordered paper-box js-dataTable-full">
                                <thead>
                                <tr>
                                    <th>名称</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($checkuptpls as $checkuptpl) { ?>
                                    <tr class="J_papertpl_item"
                                        data-checkuptplid="<?= $checkuptpl->id ?>">
                                        <td><?= $checkuptpl->title ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
            <section class="col-md-6 J_papertpl">
            </section>
        </div>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
    var doctorid = {$doctor->id};
    var diseaseid = {$diseaseid};

    // 列表搜索和排序
    var BaseTableDatatables = function() {
        var initDataTableSimple = function() {
            jQuery('.js-dataTable-full').dataTable({
                paging: false,
                searching: true,
                info: false,
                ordering: false,
                autoWidth: false,
                language: {
                  search: "搜索：_INPUT_",
                  zeroRecords: "没有相关搜索",
                  emptyTable: "无数据",
                },
            });
        };
        var bsDataTables = function() {
            var dt = jQuery.fn.dataTable;
            jQuery.extend(dt.ext.classes, {
                sWrapper: "dataTables_wrapper form-inline dt-bootstrap",
                sFilterInput: "form-control",
            });
        }

        return {
            init: function() {
                // Init Datatables
                bsDataTables();
                initDataTableSimple();
            }
        };
    }();

    $(function() {
        BaseTableDatatables.init();

        $('.J_papertpls .block-content').css('height', (parseInt($('body').css('height')) - parseInt($('.J_diseases').css('height')) - 175) + "px");

        $(document).on('click', '.J_disease_btn', function() {
            var diseaseid = $(this).data('diseaseid');
            window.location.href = "/checkuptplmgr/addofdoctor?doctorid=" + doctorid + "&diseaseid=" + diseaseid;
        })

        $(document).on('click', '.J_papertpl_item', function() {
            App.blocks('#tpl_block', 'state_loading');
            $('.J_papertpl .J_submit').prop('disabled', true);

            var checked = $(this).data('checked');
            $('.selected').removeClass('selected');
            $(this).addClass('selected');
            var checkuptplid = $(this).data('checkuptplid');
            $.ajax({
                type: "get",
                url: "/checkuptplmgr/ajaxcheckuptpl",
                dataType: "html",
                data: {
                    "checkuptplid": checkuptplid,
                    "doctorid": doctorid,
                    "diseaseid": diseaseid,
                },
                success: function (d){
                    $('.J_papertpl').html(d);
                    $('.J_papertpl .block-content').css('height', (parseInt($('body').css('height')) - 289) + "px");
                },
                error: function(e) {
                    $('.J_papertpl').html('加载失败');
                }
            });
        })
    })

STYLE;
?>
    <script src="<?= $img_uri ?>/vendor/oneui/js/plugins/datatables/jquery.dataTables.min.js"></script>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>