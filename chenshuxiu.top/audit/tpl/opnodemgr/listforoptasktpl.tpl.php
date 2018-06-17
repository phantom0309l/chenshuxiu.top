<?php
$pagetitle = "节点列表 of {$optasktpl->title}  OpNode [objtype:{$optasktpl->objtype}] [OpTaskTpl_{$optasktpl->code}_{$optasktpl->subcode}]";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <input type="hidden" id="optasktplid" value="<?=$optasktpl->id?>">
        <div class="col-md-12">
            <div class="col-sm-3 col-xs-2 success" style="float: left; padding: 0px; line-height: 2.5;">
                <button class="btn btn-sm btn-primary" data-type="add" data-toggle="modal" data-target="#opnode-edit" type="button">
                    <i class="fa fa-plus push-5-r"></i>
                    新建节点
                </button>
                <a target="_blank" class="btn btn-sm btn-primary" href="/opnodeflowmgr/list?optasktplid=<?=$optasktpl->id?>">节点流向</a>
                <a class="btn btn-sm btn-primary" href="/opnodemgr/createCommonOpNode?optasktplid=<?=$optasktpl->id?>">一键创建通用节点</a>
            </div>
            <div class="col-sm-9 col-xs-8">
                <div class="col-sm-3" style="float: right; padding-right: 0px;">
                    <form class="form-horizontal push-5-t" action="/opnodemgr/listforoptasktpl" method="post">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="hidden" name="optasktplid" value="<?=$optasktpl->id?>">
                                <input type="text" placeholder="搜索节点名" name="title" class="input-search form-inline form-control" value="<?=$opnode_title?>">
                                <span class="input-group-btn" style="width: 1%; line-height: 35px;">
                                    <button type="submit" class="btn btn-primary">
                                        <span type="submit" aria-hidden="true" class="glyphicon glyphicon-search"> </span>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="col-md-12" style="overflow-x: auto">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 100px">ID</th>
                        <th style="width: 190px">创建时间</th>
                        <th>节点名</th>
                        <th>code</th>
                        <th>挂起功能</th>
                        <th>日期框</th>
                        <th>使用数</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                        <?php foreach ($opnodes as $a) { ?>
                            <tr id="opnode-<?=$a->id?>">
                        <td><?=$a->id?></td>
                        <td><?=$a->createtime?></td>
                        <td id="title-<?=$a->id?>"><?=$a->title?></td>
                        <td id="code-<?=$a->id?>"><?=$a->code?></td>
                        <td id="is_hang_up-<?=$a->id?>">
                            <?php
                            if ($a->is_hang_up == 1) {
                                $checked = "checked";
                                $showstr = "能挂起";
                            } else {
                                $checked = "";
                                $showstr = "不能挂起";
                            }
                            ?>
                            <label class="css-input switch switch-info">
                                <input type="checkbox" <?=$checked?> class="is_hang_up" data-is_hang_up="<?=$a->is_hang_up?>" data-opnodeid="<?=$a->id?>">
                                <span></span>
                                <span id="title_is_hang_up-<?=$a->id?>"><?=$showstr?></span>
                            </label>
                        </td>
                        <td><?=($a->is_show_next_plantime == 1)?'显示':''?></td>
                        <td id="opnodecnt-<?=$a->id?>"><?=$a->getOpNodeFlowCnt()?></td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-xs btn-default" id="button-data-<?=$a->id?>" data-toggle="modal" data-type="modify" data-content="<?=$a->content?>" data-code="<?=$a->code?>" data-title="<?=$a->title?>" data-opnodeid="<?=$a->id?>"
                                    data-is_show_next_plantime="<?=$a->is_show_next_plantime?>" data-target="#opnode-edit" type="button" title="" data-original-title="Edit Client"
                                >
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-xs btn-default opnode-delete" type="button" data-opnodecnt="<?=$a->getOpNodeFlowCnt();?>" data-title="<?=$a->title?>" data-opnodeid="<?=$a->id?>">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                        <?php } ?>
                    </tbody>
            </table>
        </div>
        <div class="col-md-12 border-top">
            <a target="_blank" class="blue fb" href="/opnodeflowmgr/list?optasktplid=<?=$optasktpl->id?>">节点流向</a>
            <?php include_once $tpl . '/opnodeflowmgr/_list.php'; ?>
        </div>
    </section>
</div>
<!-- 模态框 -->
<div class="modal" id="opnode-edit" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
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
                    <h3 class="block-title">修改节点</h3>
                </div>
                <div class="block-content">
                    <form class="modal-dialog-form">
                        <input type="hidden" name="optasktplid" value="<?=$optasktpl->id ?>">
                        <input type="hidden" id="type" value="">
                        <input type="hidden" name="opnodeid" id="opnodeid" value="">
                        <input type="hidden" name="old_code" id="old_code" value="">
                        <div class="form-group">
                            <label class="" for="code">code</label>
                            <div class="">
                                <input class="form-control" type="text" id="code" name="code" placeholder="请输入节点名">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="" for="title">节点名</label>
                            <div class="">
                                <input class="form-control" type="text" id="title" name="title" placeholder="请输入节点名">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="" for="title">是否显示日期框</label>
                            <div class="">
                                <input id="is_show_next_plantime_0" type="radio" name="is_show_next_plantime" value="0">
                                <label for="is_show_next_plantime_0">否</label>
                                <input id="is_show_next_plantime_1" type="radio" name="is_show_next_plantime" value="1">
                                <label for="is_show_next_plantime_1">是</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="" for="content">节点说明</label>
                            <div class="">
                                <textarea class="form-control" id="content" name="content" placeholder="请输入节点说明"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button class="btn btn-sm btn-primary" type="button" id="submit-edit" data-dismiss="modal">
                    <i class="fa fa-check"></i>
                    提交
                </button>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function() {
    $('.is_hang_up').on('click', function(){
        var me = $(this);

        var is_hang_up = me.data('is_hang_up');
        var opnodeid = me.data('opnodeid');

        var showstr = "";
        if (is_hang_up == 1) {
            is_hang_up = 0;
            showstr = "不能挂起";
        } else if(is_hang_up == 0) {
            is_hang_up = 1;
            showstr = "能挂起";
        } else {
            alert("数据错误");
            return false;
        }

        $.ajax({
            url: '/opnodemgr/changeis_hang_upJson',
            type: 'get',
            dataType: 'text',
            async: false,
            data: {
                opnodeid: opnodeid,
                is_hang_up: is_hang_up
            },
            "success": function (data) {
                if (data == 'ok') {
                    me.data('is_hang_up', is_hang_up);
                    $("#title_is_hang_up-" + opnodeid).text(showstr);
//                     alert("修改成功!");
                }
            }
        });
    });

    $('#opnode-edit').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        var type = button.data('type');

        if (type == 'add') {
            modal.find('#type').val('add');
            modal.find('#opnodeid').val(0);
            modal.find('.block-title').text('添加节点');

            modal.find('#code').val('');
            modal.find('#title').val('');
            modal.find('#is_show_next_plantime').val('');
            modal.find('#content').val('');

        } else if (type == 'modify'){
            var opnodeid = button.data('opnodeid');
            modal.find('#type').val('modify');
            modal.find('#opnodeid').val(opnodeid);
            modal.find('.block-title').text('修改节点');

            var code = button.data('code');
            var title = button.data('title');
            var is_show_next_plantime = button.data('is_show_next_plantime');
            var content = button.data('content');

            modal.find('#old_code').val(code);
            modal.find('#code').val(code);
            modal.find('#title').val(title);

            modal.find('#is_show_next_plantime_'+is_show_next_plantime).attr('checked','checked');
            modal.find('#content').val(content);
        } else {
            alert("操作类型未知！");
            return false;
        }
    });

    $('#submit-edit').on('click', function () {
        var old_code = $('#old_code').val();
        var code = $('#code').val();
        var title = $('#title').val();

        if (title == '') {
            alert("节点不能为空!");
            return false;
        }

        if (code == '') {
            alert("code不能为空!");
            return false;
        }

        $.ajax({
            url: '/opnodemgr/addormodifyjson',
            type: 'post',
            dataType: 'text',
            async: false,
            data: $('.modal-dialog-form').serialize(),
            "success": function (data) {
                if (data == 'ok') {
                    window.location.href = location.href;
                } else {
                    alert(data);
                    $('#code').val(old_code);
                }
            }
        });
    });

    $('.opnode-delete').on('click', function(){
        var me = $(this);
        var opnodeid = me.data('opnodeid');
        var title = me.data('title');

        var opnodecnt = me.data('opnodecnt');

        if (opnodecnt > 0) {
            alert("该节点还在使用,不能删除!");
            return false;
        }

        if (false == confirm("确认删除节点[ " + title  + " ]吗?")) {
            return false;
        }

        $.ajax({
            url: '/opnodemgr/deletejson',
            type: 'get',
            dataType: 'text',
            data: {
                opnodeid: opnodeid
            },
            "success": function (data) {
                if (data == 'ok') {
                    $("#opnode-" + opnodeid).remove();
                    alert("删除成功");
                } else if (data == 'fail-notempty') {
                    alert("该节点还有使用,不能删除！");
                }
            }
        });
    });
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
