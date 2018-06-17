<?php
$pagetitle = "医生门诊";
$sideBarMini = true;
$cssFiles = [
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; //填写完整地址
$pageStyle = <<<STYLE
        .laydate_yms {
            -webkit-box-sizing: content-box;
            box-sizing: content-box;
        }

        .laydate_bottom {
            -webkit-box-sizing: content-box;
            box-sizing: content-box;
        }

        .laydate_ym {
            -webkit-box-sizing: initial;
            box-sizing: initial;
        }

        #laydate_box .laydate_y {
            margin-right: 4px;
        }

        .showList p:nth-child(odd) {
            background: #f5f5f5;
        }

        .showList p {
            height: 50px;
            line-height: 50px;
            margin: 0px;
            padding: 0px 10px;
        }

        .showList .btn {
            margin-top: 8px;
        }

        .stop-ops {
            height: 32px;
            width: 90px;
            position: relative;
            border: 1px solid #ddd;
            border-radius: 16px;
            background: #fff;
            margin-top: 10px;
        }

        .stop-ops .ops-inner {
            position: absolute;
            width: 30px;
            height: 30px;
            border: 1px solid #eee;
            background: #fff;
            border-radius: 15px;
        }

        .start-ops {
            height: 32px;
            width: 90px;
            position: relative;
            border: 1px solid #ddd;
            border-radius: 16px;
            background: #5cc26f;
            margin-top: 10px;
        }

        .start-ops .ops-inner {
            position: absolute;
            width: 30px;
            height: 30px;
            border: 1px solid #eee;
            background: #fff;
            border-radius: 15px;
            right: 0px;
        }

        .trOnSeleted td:last-child {
            border-right: 2px solid #20a0ff;
        }
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12 ">
    <?php include_once $tpl . "/doctorconfigmgr/_menu.tpl.php";?>
    <div class="content-div">
    <section class="col-md-6">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>星期</th>
                <th>上午</th>
                <th>下午</th>
                <th>夜间</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($scheduletplTable as $a){ ?>
                <tr>
                    <td><?= $a['weekday']; ?></td>
                    <td><?= $a['am']; ?></td>
                    <td><?= $a['pm']; ?></td>
                    <td><?= $a['night']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
        <div class="tr mt20" style="text-align: right">
            <a class="btn btn-primary" style="margin-bottom: 15px;" data-type="add" data-toggle="modal" data-target="#bulletin-modal"><i class=""></i> 门诊公告</a>
            <a class="btn btn-primary showAddHtml" style="margin-bottom: 15px;" data-doctorid="<?= $doctor->id ?>"><i class="si si-plus"></i> 新建</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
            <thead>
            <tr>
                <th>疾病</th>
                <th>周期</th>
                <th>出诊日期</th>
                <th>星期</th>
                <th>时刻</th>
                <th>类型</th>
                <th>实例</th>
                <th>状态</th>
                <th>加号单</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($scheduletpls as $a) {
                $_ops_class = '';
                $_bgcolor = '';
                $_textcolor = '';

                if ($a->status == 1) {
                    $_ops_class = 'start-ops';
                } else {
                    $_ops_class = 'stop-ops';
                    $_textcolor = 'gray';
                }

                if ($a->opdateIsPass()) {
                    $_bgcolor = 'bggray';
                }

                $scheduleCnt = $a->getScheduleCntGtToday();
                $revisitTktCnt = $a->getRevisitTktCntGtToday();

                ?>
                <tr class='<?= $_bgcolor ?> <?= $_textcolor ?> color_select scheduletpl_<?=$a->id?>'>
                    <td rowspan=2><?= $a->diseaseid == 0 ? "全部疾病" : $a->disease->name ?></td>
                    <td rowspan=2><?= $a->get_op_hz() ?></td>
                    <td><?= $a->getTheDateStr() ?></td>
                    <td><?= $a->get_wday() ?></td>
                    <td><?= $a->get_day_part() . $a->getBegin_hour_str_Str() ?></td>
                    <td><?= $a->get_op_type() ?></td>
                    <td>
                        <a target="_blank" class="red"
                           href="/scheduleMgr/list?scheduletplid=<?= $a->id ?>"><?= $scheduleCnt ?></a>
                    </td>
                    <td>
                        <?php if ($a->status) { ?>
                            <span class="label label-success">已开启</span>
                        <?php } else { ?>
                            <span class="label label-default">已关闭</span>
                        <?php } ?>
                    </td>
                    <td><?= $revisitTktCnt ?></td>
                    <td>
                    <?php if($revisitTktCnt > 0 || $scheduleCnt > 0) { $modifyType = "小修"; $modifyContent = "医生生成了实例，医生可能正在使用，或者患者已经约上了那天的号；这种情况不能改日期时间等，只能修改基本字段，俗称小修";} else {$modifyType = "大修"; $modifyContent="刚创建不久还没生成实例（无患者预约）;这个时候可以随便修改，俗称大修";} ?>
                    <a class="showModifyHtml btn btn-default btn-xs <?php if(!$a->status){?>gray <?php }?> <?php if ($scheduletpl->id == $a->id) {
                            echo 'thescheduletpl';
                    } ?>" href="#" data-scheduletplid="<?= $a->id ?>" data-toggle="popover" data-placement="right" data-content="<?=$modifyContent?>" data-original-title="什么是<?=$modifyType?>？">
                        <i class="fa fa-pencil"></i> <?=$modifyType ?>
                        </a>
                        <?php
                            if (! $a->status) {
                                $scheduleCnt = ScheduleDao::getListByScheduleTpl($a);
                                $cnt = count($scheduleCnt);
                                if ($cnt == 0) {
                                ?>
                            <p></p>
                            <a class="btn btn-danger btn-xs deleteSchedule" data-doctorid="<?=$doctor->id?>" data-href="/scheduletplmgr/deletepost?scheduletplid=<?=$a->id?>"><i class="fa fa-trash-o"></i> 删除</a>
                        <?php } } ?>
                        <p></p>
                        <?php if ($a->status) { ?>
                        <a href="/scheduletplmgr/closePost?scheduletplid=<?= $a->id ?>" class="btn btn-default btn-xs"><i class="fa fa-times"></i> 关闭</a>
                        <?php } else { ?>
                            <a href="/scheduletplmgr/openPost?scheduletplid=<?= $a->id ?>" class="btn btn-default btn-xs gray"><i class="fa fa-check"></i> 开启</a>
                        <?php } ?>
                    </td>
                </tr>
                <tr class='<?= $_bgcolor ?> <?= $_textcolor ?> color_select scheduletpl_<?=$a->id?>'>
                    <td colspan=11 class="text-info" style="text-align: left"><?= $a->content ?> <?= $a->tip ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
    </section>
    <section class="col-md-6 add_page">
        <div id="ScheduleTplBox"></div>
    </section>
    </div>
</div>
<!-- 模态框 -->
<div class="modal" id="bulletin-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button">
                                <i class="si si-close"></i>
                            </button>
                        </li>
                    </ul>
                    <h3 class="block-title">门诊公告</h3>
                </div>
                <div class="block-content">
                    <div class="form-group">
                        <div class="">
                            <textarea class="form-control" id="bulletin" name="bulletin" placeholder="请输入门诊公告" rows="5"><?= $doctor->bulletin ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button class="btn btn-sm btn-primary" type="button" id="submit-bulletin" data-doctorid="<?= $doctor->id ?>">
                    <i class="fa fa-check"></i>
                    保存
                </button>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>

<?php
$footerScript = <<<XXX
    $(function () {
        var tool = {
            getAjaxData: function (option) {
                var defaultOption = {
                    type: "get",
                    url: "",
                    data: "",
                    dataType: "json"
                };
                option = $.extend(defaultOption, option || {});
                return $.ajax(option);
            }
        };

        // app begin
        var app = {
            init: function () {
                var self = this;

                self.submitBulletin();
                self.showAddHtmlOnClick();
                self.showModifyHtmlOnClick();
                self.deleteSchedule();
            },
            submitBulletin: function() {
                $('#submit-bulletin').on('click', function() {
                    var bulletin = $('#bulletin').val();
                    var doctorid = $(this).data('doctorid');
                    $.ajax({
                        "type": "post",
                        "data": {
                            doctorid: doctorid,
                            bulletin: bulletin
                        },
                        "dataType": "json",
                        "url" : "/scheduletplmgr/ajaxmodifybulletin",
                        "success": function (data) {
                            if (data.errno == 0) {
                                window.location.reload();
                            } else {
                                alert(data.errmsg);
                            }
                        },
                        "error": function() {
                            alert("修改失败");
                        }
                    });
                })
            },
            showAddHtmlOnClick: function () {

                $(".showAddHtml").on("click", function () {

                    var me = $(this);
                    var doctorid = me.data("doctorid");

                    $.ajax({
                        "type": "get",
                        "data": {
                            doctorid: doctorid
                        },
                        "dataType": "html",
                        "url" : "/scheduletplmgr/addHtml",
                        "success": function (data) {
                            $(".color_select").removeClass('trOnSeleted');
                            $("#ScheduleTplBox").html(data);
                        }
                    });

                    $(".add_page").show();
                });
            },
            deleteSchedule: function () {

                $(".deleteSchedule").on("click", function (e) {
                    e.preventDefault();
                    var me = $(this);
                    var scheduletplid = me.data("scheduletplid");
                    var doctorid = me.data("doctorid");

                    if (confirm("确定要删除吗？")) {
                        $.ajax({
                            "type": "post",
                            "data": {
                                scheduletplid : scheduletplid
                            },
                            "dataType": "text",
                            "url" : "/scheduletplmgr/deleteScheduleJson",
                            "success": function () {
                                window.location.href = "/scheduletplmgr/listofdoctor?doctorid=" + doctorid;
                            }
                        });
                    }
                });
            },
            showModifyHtmlOnClick: function () {
                $(".showModifyHtml").on("click", function () {

                    var me = $(this);
                    var scheduletplid = me.data("scheduletplid");

                    $(".color_select").removeClass('trOnSeleted');
                    $(".scheduletpl_" + scheduletplid).addClass('trOnSeleted');

                    $.ajax({
                        "type": "get",
                        "data": {
                            scheduletplid : scheduletplid
                        },
                        "dataType": "html",
                        "url" : "/scheduletplmgr/modifyHtml",
                        "success": function (data) {
                            $("#ScheduleTplBox").html(data);
                        }
                    });

                    $(".add_page").show();
                });
            },

        }; //app end
        app.init();

        var node = $(".thescheduletpl");
        node.click();
    });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
