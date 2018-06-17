<?php
$pagetitle = "药厂医生项目列表 Dc_projects";
$cssFiles = [
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/v5/page/audit/dc_projectmgr/list.js",
]; //填写完整地址
$pageStyle = <<<STYLE
    .out_box{border:1px solid #ccc; background:#fff; font:12px/30px Tahoma;}
    .list_box{border-bottom:1px solid #eee; padding:0 5px; cursor:pointer;}
    .focus_box{background:#646464;}
    .mark_box{color:#c00;}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="col-md-12">
            <div class="col-sm-1 col-xs-3 success" style="float: left; padding: 0px; line-height: 2.5;">
                <button class="btn btn-sm btn-primary" data-type="add" data-toggle="modal" data-target="#dc_project-edit" type="button">
                    <i class="fa fa-plus push-5-r"></i>新建项目
                </button>
            </div>
        </div>
        <div class="col-md-12"  style="overflow-x:auto">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th style="width:100px">ID</th>
                        <th style="width:190px">创建时间</th>
                        <th>项目名称</th>
                        <th>汇报人</th>
                        <th>汇报人邮箱</th>
                        <th>医生项目</th>
                        <th>备注</th>
                        <th>创建人</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dc_projects as $a) { ?>
                        <tr id="dc_project-<?=$a->id?>">
                            <td><?=$a->id?></td>
                            <td><?=$a->createtime?></td>
                            <td><?=$a->title?></td>
                            <td><?=$a->reportor?></td>
                            <td><?=$a->report_email?></td>
                            <td><a target="_blank" href="/dc_doctorprojectmgr/list?dc_projectid=<?=$a->id?>"><?=$a->getCntDoctorProject()?></a></td>
                            <td><?=$a->content?></td>
                            <td><span class="label label-info" id="title-<?=$a->id?>"><?=$a->create_auditor->name?></span></td>
                            <td>
                                <div class="btn-group">
                                <button class="btn btn-xs btn-default" data-toggle="modal" data-type="modify" data-dc_projectid="<?=$a->id?>" data-title="<?=$a->title?>" data-reportor="<?=$a->reportor?>" data-report_email="<?=$a->report_email?>" data-content="<?=$a->content?>" data-target="#dc_project-edit" type="button" data-original-title="Edit Client"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-xs btn-default dc_project-delete" type="button" data-dc_projectid="<?=$a->id?>" data-title="<?=$a->title?>" data-doctorprojectcnt="<?=$a->getCntDoctorProject();?>"><i class="fa fa-times"></i></button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<!-- 模态框 -->
<div class="modal" id="dc_project-edit" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                    <h3 class="block-title">添加项目</h3>
                </div>
                <div class="block-content">
                    <input type="hidden" id="dc_projectid" value="">
                    <div class="form-group">
                        <label class="" for="title">项目名称</label>
                        <div class="">
                            <input class="form-control" type="text" id="title" name="title" placeholder="请输入项目名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">汇报人</label>
                        <div class="">
                            <input class="form-control" type="text" id="reportor" name="reportor" placeholder="请输入汇报人">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">汇报人邮箱</label>
                        <div class="parentCls">
                            <input class="form-control" type="text" id="report_email" name="report_email" placeholder="请输入汇报人邮箱">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">备注</label>
                        <div class="">
                            <textarea class="form-control" id="content" name="content" rows="4" placeholder="请输入备注"></textarea>
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

<div class="clear"></div>

<script>

</script>

<?php
$footerScript = <<<XXX
$(function() {
    $("#report_email").mailAutoComplete({
        boxClass: "out_box", //外部box样式
        listClass: "list_box", //默认的列表样式
        focusClass: "focus_box", //列表选样式中
        markCalss: "mark_box", //高亮样式
        autoClass: false,
        textHint: true //提示文字自动隐藏
    });

    $('#dc_project-edit').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        var type = button.data('type');

        if (type == 'add') {
            modal.find('#dc_projectid').val(0);
            modal.find('.block-title').text('添加项目');
        } else if (type == 'modify'){
            var dc_projectid = button.data('dc_projectid');
            var title = button.data('title');
            var reportor = button.data('reportor');
            var report_email  = button.data('report_email');
            var content = button.data('content');

            modal.find('#title').val();
            modal.find('#dc_projectid').val(dc_projectid);
            modal.find('#title').val(title);
            modal.find('#reportor').val(reportor);
            modal.find('#report_email').val(report_email);
            modal.find('#content').text(content);
            modal.find('.block-title').text('修改项目');
        } else {
            alert("操作类型未知！");
            return false;
        }
    });

    $('#submit-edit').on('click', function () {
        var title = $('#title').val();
        var reportor = $('#reportor').val();
        var report_email = $('#report_email').val();
        var content = $('#content').val();
        var dc_projectid = $('#dc_projectid').val();

        // 验证项目名称
        if (title == '') {
            alert("项目名称不能为空");
            return false;
        }

        // 验证汇报人
        if (reportor == '') {
            alert("汇报人不能为空");
            return false;
        }

        // 验证汇报人邮箱
        if(report_email == ''){
            alert("邮箱不能为空");
            return false;
        } else {
            if(!report_email.match(/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/)) {
                alert("电子邮件地址格式不正确！请重新输入");
                $("#report_email").focus();

                return false;
            }
        }

        var flag = 0;

        $.ajax({
            url: '/dc_projectmgr/addormodifyjson',
            type: 'post',
            dataType: 'text',
            async: false,
            data: {
                dc_projectid: dc_projectid,
                title: title,
                reportor: reportor,
                report_email: report_email,
                content : content
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
                    alert(data);
                }
            }
        });

        if (flag == 0) {
            return false;
        }

        window.location.href = window.location.href;
    });

    $('.dc_project-delete').on('click', function(){
        var me = $(this);
        var dc_projectid = me.data('dc_projectid');
        var title = me.data('title');
        var doctorprojectcnt = me.data('doctorprojectcnt');

        if (doctorprojectcnt > 0) {
            alert("该组还有患者,不能删除！");
            return false;
        }

        if (false == confirm("确认删除[" + title  + "]吗?")) {
            return false;
        }

        $.ajax({
            url: '/dc_projectmgr/deletejson',
            type: 'get',
            dataType: 'text',
            data: {
                dc_projectid: dc_projectid
            },
            "success": function (data) {
                if (data == 'success') {
                    $("#dc_project-" + dc_projectid).remove();
                    alert("删除成功");
                } else if (data == 'fail') {
                    alert("该项目还有医生项目,不能删除！")
                }
            }
        });
    });
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
