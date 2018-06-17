<?php
$pagetitle = "【{$patient->name}患者】联系人列表";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
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
            <a class="btn btn-success" data-toggle="modal" data-target="#linkman-edit" data-type="add" data-patientid="<?=$patient->id?>" href="#">新建</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>createtime</td>
                        <td>微信昵称</td>
                        <td>联系人姓名</td>
                        <td>关系</td>
                        <td>号码</td>
                        <td>联系人地位</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($linkmans as $i => $a) { ?>
                        <tr id="linkman-<?=$a->id?>">
                            <td><?= $a->id ?></td>
                            <td><?= $a->createtime ?></td>
                            <td><?= $a->wxuser->nickname ?></td>
                            <td><?= $a->name ?></td>
                            <td><?= $a->shipstr ?></td>
                            <td><?= $a->mobile ?></td>
                            <td>
                                <?php
                                    if ($a->is_master == 1) {
                                        echo "<span class=\"label label-success\">主联系人</span>";
                                    } else {
                                        echo "<span class=\"label label-info\">备用联系人</span>";
                                    }
                                ?>

                            </td>
                            <td align="center">
                                <div class="btn-group">
                                    <button class="btn btn-xs btn-default" type="button" data-type="modify" data-patientid="<?=$patient->id?>" data-linkmanid="<?=$a->id?>" data-name="<?=$a->name?>" data-shipstr="<?=$a->shipstr?>" data-mobile="<?=$a->mobile?>" data-is_master="<?=$a->is_master?>" data-toggle="modal" data-target="#linkman-edit" data-original-title="修改联系人"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-xs btn-default linkman-delete" type="button" data-id="<?=$a->id?>" data-original-title="删除联系人"><i class="fa fa-times"></i></button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- 模态框 -->
        <div class="modal" id="linkman-edit" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="block block-themed block-transparent remove-margin-b">
                        <div class="block-header bg-primary">
                            <ul class="block-options">
                                <li>
                                    <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                                </li>
                            </ul>
                            <h3 class="block-title">修改联系人</h3>
                        </div>
                        <div class="block-content">
                            <input type="hidden" id="patientid" value="">
                            <input type="hidden" id="linkmanid" value="">
                            <div class="form-group">
                                <label class="" for="title">姓名</label>
                                <div class="">
                                    <input class="form-control" type="text" id="name" name="name" placeholder="请输入联系人姓名">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="" for="content">关系</label>
                                <div class="">
                                    <select class="form-control" id="shipstr" name="shipstr" size="1" style="width: 126px">

                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="" for="content">号码</label>
                                <div class="parentCls">
                                    <input class="form-control" type="text" id="mobile" name="mobile" placeholder="请输入号码">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="" for="content">主联系人</label>
                                <div class="parentCls">
                                    <input class="form-control" type="hidden" id="is_master" name="is_master" placeholder="请输入号码">
                                    <label class="css-input switch switch-success">
                                        <input type="checkbox" id="modify_is_master"><span></span> <span id="text_is_master">否</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                        <button class="btn btn-sm btn-primary" type="button" id="submit-edit" data-dismiss="modal"><i class="fa fa-check"></i>提交</button>
                    </div>
                </div>
            </div>
        </div>

        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
</div>
<div class="clear"></div>
<script>
    $(function(){
        $('#linkman-edit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            var type = button.data('type');
            var patientid = button.data('patientid');
            $('#patientid').val(patientid);
            if (type == 'add') {
                modal.find('#linkmanid').val(0);
                modal.find('#name').val('');
                modal.find('#mobile').val('');
                modal.find('#is_master').val(0);
                modal.find('.block-title').text('添加联系人');

                // 关系
                $.ajax({
                    url : '/linkmanmgr/getshipstrsjson',
                    type : 'get',
                    dataType : 'json',
                    data : {},
                    success : function (json) {
                        var shipstrs = json.data;
                        var shipstrselect = '';
                        $.each(shipstrs, function (index,info) {
                            shipstrselect += "<option value=\"" + info + "\">" + info + "</option>";
                        });

                        modal.find('#shipstr').html(shipstrselect);
                    }
                });

                // 主联系人
                $('#modify_is_master').prop('checked', false);
                $('#text_is_master').text('否');
            } else if (type == 'modify'){
                var linkmanid = button.data('linkmanid');
                var name = button.data('name');
                var shipstr = button.data('shipstr');
                var mobile = button.data('mobile');
                var is_master = button.data('is_master');

                $.ajax({
                    url : '/linkmanmgr/getshipstrsjson',
                    type : 'get',
                    dataType : 'json',
                    data : {},
                    success : function (json) {
                        var shipstrs = json.data;
                        var shipstrselect = '';
                        $.each(shipstrs, function (index,info) {
                            if (info == shipstr) {
                                shipstrselect += "<option value=\"" + info + "\" selected>" + info + "</option>";
                            } else {
                                shipstrselect += "<option value=\"" + info + "\">" + info + "</option>";
                            }
                        });

                        modal.find('#shipstr').html(shipstrselect);
                    }
                });

                if (is_master == 1) {
                    $('#modify_is_master').prop('checked', true);
                    $('#text_is_master').text('是');
                } else {
                    $('#modify_is_master').prop('checked', false);
                    $('#text_is_master').text('否');
                }

                modal.find('#linkmanid').val(linkmanid);
                modal.find('#name').val(name);
                modal.find('#mobile').val(mobile);
                modal.find('#is_master').val(is_master);
                modal.find('.block-title').text('修改联系人');
            } else {
                alert("操作类型未知！");
                return false;
            }
        });

        $('#modify_is_master').on('click', function () {
            console.log(this);
            console.log($(this).context.checked);
            if ($(this).context.checked == true) {
                $('#is_master').val(1);
                $('#text_is_master').text('是');
            } else {
                $('#is_master').val(0);
                $('#text_is_master').text('否');
            }
        });

        $('#submit-edit').on('click', function () {
            var linkmanid = $('#linkmanid').val();
            var patientid = $('#patientid').val();
            var name = $('#name').val();
            var shipstr = $('#shipstr').val();
            var mobile = $('#mobile').val();
            var is_master = $('#is_master').val();

            // 验证号码
            if (mobile == '') {
                alert("号码不能为空");
                return false;
            }

            var flag = 0;

            $.ajax({
                url: '/linkmanmgr/addormodifyjson',
                type: 'post',
                dataType: 'text',
                async: false,
                data: {
                    linkmanid: linkmanid,
                    patientid: patientid,
                    name: name,
                    shipstr: shipstr,
                    mobile: mobile,
                    is_master: is_master,
                },
                "success": function (data) {
                    if (data == 'success-modify' || data == 'success-add') {
                        if (data == 'success-modify') {
                            alert("修改成功");
                        } else {
                            alert("添加成功");
                        }

                        flag = 1;
                    } else {
                        if ('mobile_already') {
                            alert('号码已存在');
                        }
                    }
                }
            });

            if (flag == 0) {
                return false;
            }

            window.location.href = window.location.href;
        });

        $('.linkman-delete').on('click', function () {
            var me = $(this);

            var linkmanid = me.data('id');

            if (!confirm("确定删除吗?")) {
                return false;
            }

            $.ajax({
                url : '/linkmanmgr/deletejson',
                type : 'get',
                dataType : 'text',
                data : {
                    linkmanid : linkmanid
                },
                success : function (data) {
                    if ('delete-success' == data) {
                        $('#linkman-' + linkmanid).remove();
                        alert("删除成功");
                    } else {
                        alert("删除失败");
                    }
                }
            });
        });
    });
</script>
<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
