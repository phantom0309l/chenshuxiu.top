<?php
$pagetitle = "任务列表 OpTaskTpl";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<script type="text/javascript">
$(function(){

    // 点击批量删除按钮
    $(document).on("click", ".closeOpTasksBtn", function(){
        var me = $(this);
        var optasktplid = me.data('optasktplid');

        if( !confirm("真的要批量关闭任务?") ){
            return;
        }

        $.ajax({
            url: '/optaskmgr/closeOpTasksByOpTaskTplJson',
            type: 'GET',
            dataType: 'text',
            data: {optasktplid : optasktplid},
            success: function(json){
                alert(json);
            }
        });
    });
});
</script>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
            <div class="col-sm-1 col-xs-3 success" style="line-height: 2.5;">
                <a class="btn btn-sm btn-primary" target="_blank" href="/optasktplmgr/add">
                    <i class="fa fa-plus push-5-r"></i>
                    任务新建
                </a>
            </div>
        </div>
        <div class="col-sm-11 col-xs-9">
            <div class="col-sm-5">
                <form class="form-horizontal push-5-t" action="/optasktplmgr/list" method="get">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">状态 :</label>
                        <div class="col-sm-10">
                            <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getOptaskTplStatusCtrArray(),'status', $status, 'css-radio-success status')?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" placeholder="搜索标题" name="title" class="input-search form-inline form-control" value="<?=$title?>">
                            <span class="input-group-btn" style="width: 1%; line-height: 35px;">
                                <button type="submit" class="btn btn-primary search">
                                    <span type="submit" aria-hidden="true" class="glyphicon glyphicon-search"> </span>
                                </button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>id</td>
                            <td width=100>创建时间</td>
                            <td>标题/内容</td>
                            <td width=120>是否可手动创建</td>
                            <td width=120>自动发消息</td>
                            <td width=120>自动进入节点</td>
                            <td>code</td>
                            <td>subcode</td>
                            <td>objtype</td>
                            <td>状态</td>
                            <td width="200">操作</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php

                    foreach ($optasktpls as $i => $a) {
                        $optasktpl_row = $optasktpl_list[$a->id];
                        ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= $a->id ?></td>
                            <td><?=$a->getCreateDay() ?></td>
                            <td>
                                <span class="f16 blue"><?= $a->title ?></span>
                                <span class="f12 gray"><?= $a->diseaseids ?></span>
                                <br />
                                <p class="bg-warning p10">
                                数据: <?= substr($optasktpl_row['min_date'],0,10) ?> 至 <?= substr($optasktpl_row['max_date'],0,10) ?> => <?= $optasktpl_row['cnt'] ?> [<?= $optasktpl_row['cnt_0'] ?>, <?= $optasktpl_row['cnt_1'] ?>, <?= $optasktpl_row['cnt_2'] ?>] 条
                                </p>
                                <p class="bgcolorborderbox">
                                <?= nl2br($a->content) ?>
                                </p>
                            </td>
                            <td>
                                <?php
                                if ($a->is_can_handcreate == 1) {
                                    $checkedstr = 'checked';
                                    $text = 'Yes';
                                } else {
                                    $checkedstr = '';
                                    $text = 'No';
                                }
                                ?>
                                <label class="css-input switch switch-success">
                                    <input type="checkbox" class="modify_is_can_handcreate" data-optasktplid="<?=$a->id?>" <?=$checkedstr?>><span></span> <span id="text_is_can_handcreate-<?=$a->id?>"><?=$text?></span>
                                </label>
                            </td>
                            <td>
                                <?php
                                if ($a->is_auto_send == 1) {
                                    $checkedstr = 'checked';
                                    $text = 'Yes';
                                } else {
                                    $checkedstr = '';
                                    $text = 'No';
                                }
                                ?>
                                <label class="css-input switch switch-success">
                                    <input type="checkbox" class="modify_is_auto_send" data-optasktplid="<?=$a->id?>" <?=$checkedstr?>><span></span> <span id="text_is_auto_send-<?=$a->id?>"><?=$text?></span>
                                </label>
                            </td>
                            <td>
                                <?php
                                if ($a->is_auto_to_opnode == 1) {
                                    $checkedstr = 'checked';
                                    $text = 'Yes';
                                } else {
                                    $checkedstr = '';
                                    $text = 'No';
                                }
                                ?>
                                <label class="css-input switch switch-success">
                                    <input type="checkbox" class="modify_is_auto_to_opnode" data-optasktplid="<?=$a->id?>" <?=$checkedstr?>><span></span> <span id="text_is_auto_to_opnode-<?=$a->id?>"><?=$text?></span>
                                </label>
                            </td>
                            <td><?= $a->code ?></td>
                            <td><?= $a->subcode ?></td>
                            <td><?= $a->objtype ?></td>
                            <td>
                                <?= 1 == $a->status ? '有效' : '<b class="red">无效</b>'?>
                            </td>
                            <td>
                                <a target="_blank" style="<?= $a->is_auto_send == 1 ? '' : 'display: none'; ?>" id="auto_send-<?=$a->id?>" href="/optasktplcronmgr/listofoptasktpl?optasktplid=<?= $a->id ?>">配置自动提醒</a>
                                <br />
                                <a target="_blank" href="/optasktplmgr/modify?optasktplid=<?= $a->id ?>">修改</a>
                                <br />
                                <a target="_blank" href="/opnodemgr/listforoptasktpl?optasktplid=<?= $a->id ?>">节点(<?=$a->getOpNodeCnt();?>个)</a>
                                <br />
                                <span class="btn btn-sm btn-warning closeOpTasksBtn" data-optasktplid="<?= $a->id ?>">批量关闭任务</span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<div class="clear"></div>

<script>
    $(function () {
        $(".status").on("click", function () {
            $(".search").click();
        });

        $('.modify_is_can_handcreate').on('click', function () {
            var me = $(this);

            var optasktplid = me.data('optasktplid');
            var is_can_handcreate = 0;
            if (me.context.checked == true) {
                is_can_handcreate = 1;
            }

            $.ajax({
                url : '/optasktplmgr/changehandcreatejson',
                type : 'get',
                dataType : 'text',
                data : {
                    optasktplid : optasktplid,
                    is_can_handcreate : is_can_handcreate
                },
                success : function (data) {
                    if (data == 'success') {
                        alert("修改成功!");

                        if (me.context.checked == true) {
                            $('#text_is_can_handcreate-' + optasktplid).text('Yes');
                        } else {
                            $('#text_is_can_handcreate-' + optasktplid).text('No');
                        }
                    } else {
                        alert("修改失败!");
                    }
                }
            });
        });

        // is_auto_to_opnode
        $('.modify_is_auto_to_opnode').on('click', function () {
            var me = $(this);

            var is_auto_to_opnode = 0;
            var optasktplid = me.data('optasktplid');
            var is_auto_send = 0;
            if (me.context.checked == true) {
                is_auto_to_opnode = 1;
            }

            $.ajax({
                url : '/optasktplmgr/changeautotoopnodejson',
                type : 'get',
                dataType : 'text',
                data : {
                    optasktplid : optasktplid,
                    is_auto_to_opnode : is_auto_to_opnode
                },
                success : function (data) {
                    if (data == 'success') {
                        alert("修改成功!");

                        if (me.context.checked == true) {
                            $('#text_is_auto_to_opnode-' + optasktplid).text('Yes');
                        } else {
                            $('#text_is_auto_to_opnode-' + optasktplid).text('No');
                        }
                    } else {
                        alert("修改失败!");
                    }
                }
            });
        });

        $('.modify_is_auto_send').on('click', function () {
            var me = $(this);

            var optasktplid = me.data('optasktplid');
            var is_auto_send = 0;
            if (me.context.checked == true) {
                is_auto_send = 1;
            }

            $.ajax({
                url : '/optasktplmgr/changeautosendjson',
                type : 'get',
                dataType : 'text',
                data : {
                    optasktplid : optasktplid,
                    is_auto_send : is_auto_send
                },
                success : function (data) {
                    if (data == 'success') {
                        alert("修改成功!");

                        if (me.context.checked == true) {
                            $('#text_is_auto_send-' + optasktplid).text('Yes');
                            $('#auto_send-' + optasktplid).show();
                        } else {
                            $('#text_is_auto_send-' + optasktplid).text('No');
                            $('#auto_send-' + optasktplid).hide();
                        }
                    } else {
                        alert("修改失败!");
                    }
                }
            });
        });
    })
</script>

<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
