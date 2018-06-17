<?php
$pagetitle = "节点流向 of {$optasktpl->title} OpNodeFlow [objtype:{$optasktpl->objtype}] [OpTaskTpl_{$optasktpl->code}_{$optasktpl->subcode}]";
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
                <a target="_blank" class="btn btn-sm btn-primary" href="/opnodeflowmgr/genOpTaskTplServiceClass?optasktplid=<?=$optasktpl->id?>">创建OpTaskTplService类</a>
            </div>
            <div class="clear"></div>
        </div>
        <div class="col-md-12" style="overflow-x: auto">
            <table class="table table-bordered">
                <tr>
                    <td></td>
                        <?php foreach ($opnodes as $to_opnode) { ?>
                    <td style="align: left">
                        &gt;&gt; <?=$to_opnode->title?>
                        <br />
                        <span class="gray"><?=$to_opnode->code?></span>
                    </td>
                        <?php } ?>
                </tr>

                <?php foreach ($opnodes as $from_opnode) { ?>
                <tr>
                    <td align="right"><?=$from_opnode->title?> &gt;&gt;
                        <br />
                        <span class="gray"><?=$from_opnode->code?></span>
                        <?php
                    if ($from_opnode->is_hang_up) {
                        echo '<br/><span class="blue">可挂起<span>';
                    }
                    ?>
                    </td>
                    <?php foreach ($opnodes as $to_opnode) { ?>
                    <td>
                        <?php
                        $opnodeflow = OpNodeFlowDao::getByFrom_opnodeTo_opnode($from_opnode, $to_opnode);
                        if ($opnodeflow instanceof OpNodeFlow) {
                            ?>
                        <button class="btn btn-sm btn-primary" data-is_hang_up="<?=$from_opnode->is_hang_up?>" data-opnodeflowid="<?=$opnodeflow->id?>" data-type="<?=$opnodeflow->type?>" data-content="<?=$opnodeflow->content?>" data-from_opnode_title="<?=$from_opnode->title?>"
                            data-from_opnodeid="<?=$from_opnode->id?>" data-to_opnode_title="<?=$to_opnode->title?>" data-to_opnodeid="<?=$to_opnode->id?>" data-toggle="modal" data-target="#opnode-edit" type="button"
                        >
                            <i class="fa fa-edit push-5-r"></i><?=$opnodeflow->getTypeStr()?>
                        </button>
                        <button class="btn btn-xs btn-default opnode-delete" type="button" data-opnodeflowid="<?=$opnodeflow->id?>">
                            <i class="fa fa-times"></i>
                        </button>
                            <?php
                            // 已设置流转
                            if ($opnodeflow->type == 'auto') {
                                echo "现在没用到";
                            }
                        } else {
                            // 未设置流转
                            ?>
                                                <button class="btn btn-sm btn-default" data-is_hang_up="<?=$from_opnode->is_hang_up?>" data-from_opnode_title="<?=$from_opnode->title?>" data-from_opnodeid="<?=$from_opnode->id?>" data-to_opnode_title="<?=$to_opnode->title?>"
                            data-to_opnodeid="<?=$to_opnode->id?>" data-opnodeflowid="0" data-toggle="modal" data-target="#opnode-edit" type="button"
                        >
                            <i class="fa fa-plus push-5-r"></i>
                            关联
                        </button>
                        <?php
                        }
                        ?>
                        </td>
                            <?php } ?>
                    </tr>
                    <?php } ?>
            </table>

<?php
$opnodeflow_can_modify = true;
include_once $tpl . '/opnodeflowmgr/_list.php';
?>
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
                    <h3 class="block-title">节点流</h3>
                </div>
                <div class="block-content">
                    <input type="hidden" id="opnodeflowid" value="">
                    <input type="hidden" id="from_opnodeid" value="">
                    <input type="hidden" id="to_opnodeid" value="">
                    <table class="table table-bordered">
                        <tr>
                            <th>起点</th>
                            <td>
                                <div id="from_opnode_title"></div>
                            </td>
                        </tr>
                        <tr>
                            <th>终点</th>
                            <td>
                                <div id="to_opnode_title"></div>
                            </td>
                        </tr>
                        <tr>
                            <th>类型</th>
                            <td>
                                <div class="col-xs-6" id="flow-type">
                                    <label class="css-input css-radio css-radio-primary push-10-r">
                                        <input type="radio" id="type-timeout" data-type="timeout" name="f_type"><span></span> 挂起超时
                                    </label>
                                    <label class="css-input css-radio css-radio-primary">
                                        <input type="radio" id="type-manual" data-type="manual" name="f_type"><span></span> 手动
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>节点流说明</th>
                            <td>
                                <textarea class="form-control" rows="8" id="content" name="content"></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button class="btn btn-sm btn-primary" type="button" id="submit-edit" data-dismiss="modal">
                    <i class="fa fa-check"></i>
                    <span id="submit_text">提交</span>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<script>
$(function() {
    function init (modal) {
        modal.find('#opnodeflowid').val(0);

        modal.find('#from_opnodeid').val(0);
        modal.find('#to_opnodeid').val(0);

        modal.find('#from_opnode_title').text('');
        modal.find('#to_opnode_title').text('');

        modal.find('#content').text('');
    }

    $('#opnode-edit').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        init(modal);

        var opnodeflowid = button.data('opnodeflowid');
        var type = button.data('type');
        var from_opnodeid = button.data('from_opnodeid');
        var to_opnodeid = button.data('to_opnodeid');
        var from_opnode_title = button.data('from_opnode_title');
        var to_opnode_title = button.data('to_opnode_title');
        var content = button.data('content');

        var is_hang_up = button.data('is_hang_up');
        if (is_hang_up == 1) {
            // 能挂起，所有有挂起超时类型
            var htmlstr = "                                    <label class=\"css-input css-radio css-radio-primary push-10-r\">\n" +
                "                                        <input type=\"radio\" id=\"type-timeout\" data-type=\"timeout\" name=\"f_type\"><span></span> 挂起超时\n" +
                "                                    </label>\n" +
                "                                    <label class=\"css-input css-radio css-radio-primary\">\n" +
                "                                        <input type=\"radio\" id=\"type-manual\" data-type=\"manual\" name=\"f_type\"><span></span> 手动\n" +
                "                                    </label>";
            $('#flow-type').html(htmlstr);
        } else if (is_hang_up == 0) {
            // 不能能挂起，所有没有挂起超时类型
            var htmlstr = "                                    <label class=\"css-input css-radio css-radio-primary\">\n" +
                "                                        <input type=\"radio\" id=\"type-manual\" data-type=\"manual\" name=\"f_type\"><span></span> 手动\n" +
                "                                    </label>";

            $('#flow-type').html(htmlstr);
        }

        if (opnodeflowid != 0) {
            modal.find('#opnodeflowid').val(opnodeflowid);

            modal.find('#from_opnodeid').val(from_opnodeid);
            modal.find('#to_opnodeid').val(to_opnodeid);

            modal.find('#from_opnode_title').text(from_opnode_title);
            modal.find('#to_opnode_title').text(to_opnode_title);

            modal.find('#content').val(content);

            if (type == 'timeout') {
                $('#type-timeout').prop("checked", true);
                $('#type-manual').prop("checked", false);
            } else if (type == 'manual') {
                $('#type-timeout').prop("checked", false);
                $('#type-manual').prop("checked", true);
            } else {
                alert('类型未知错误!');
                return;
            }

            modal.find('.block-title').text('修改节点流');
            modal.find('#submit_text').text('修改');
        } else {
            modal.find('#from_opnodeid').val(from_opnodeid);
            modal.find('#to_opnodeid').val(to_opnodeid);

            modal.find('#from_opnode_title').text(from_opnode_title);
            modal.find('#to_opnode_title').text(to_opnode_title);

            modal.find('#content').val('');

            $("input[name='type']").each(function(index, el) {
                $(this).prop("checked", false);
            });

            modal.find('.block-title').text('添加节点流');
            modal.find('#submit_text').text('关联');
        }

    });

    $('#submit-edit').on('click', function () {
        var opnodeflowid = $('#opnodeflowid').val();
        var from_opnodeid = $('#from_opnodeid').val();
        var to_opnodeid = $('#to_opnodeid').val();
        var type = $("input[name='f_type']:checked").data('type');
        var content = $("#content").val();

        if (type == undefined) {
            alert("类型不能为空!");
            return false;
        }

        $.ajax({
            url: '/opnodeflowmgr/addormodifyjson',
            type: 'get',
            dataType: 'text',
            data: {
                opnodeflowid: opnodeflowid,
                from_opnodeid: from_opnodeid,
                to_opnodeid: to_opnodeid,
                type: type,
                content : content
            },
            "success": function (data) {
                if (data == 'ok') {
                    window.location.href = location.href;
                } else {
                    alert("操作失败");
                }
            }
        });
    });

    $('.opnode-delete').on('click', function(){
        if (! confirm("确定解除关联吗?")) {
            return false;
        }

        var opnodeflowid = $(this).data('opnodeflowid');

        $.ajax({
            url: '/opnodeflowmgr/deletejson',
            type: 'get',
            dataType: 'text',
            data: {
                opnodeflowid: opnodeflowid
            },
            "success": function (data) {
                if (data == 'ok') {
                    alert("解除关联成功");
                    window.location.href = location.href;
                } else {
                    alert("解除关联失败");
                }
            }
        });
    });
});
</script>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
