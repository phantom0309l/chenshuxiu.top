<?php
$pagetitle = "精简版问卷列表 SimpleSheetTpls";
$cssFiles = [
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="col-md-12">
            <div class="col-sm-1 col-xs-3 success" style="float: left; padding: 0px; line-height: 2.5;margin-bottom: 10px;">
                <button class="btn btn-sm btn-primary" data-type="add" data-toggle="modal" data-target="#simplesheettpl-edit" type="button">
                    <i class="fa fa-plus push-5-r"></i>新建问卷
                </button>
            </div>
        </div>

        <div class="col-md-12"  style="overflow-x:auto">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width:100px">ID</th>
                            <th style="width:160px">创建时间</th>
                            <th>ename</th>
                            <th>问卷名称</th>
                            <th>详细</th>
                            <th>答卷</th>
                            <th>创建人</th>
                            <th>备注</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($simplesheettpls as $a) { ?>
                        <tr>
                            <td><?=$a->id?></td>
                            <td><?=$a->createtime?></td>
                            <td><?=$a->ename?></td>
                            <td><a target="_blank" href="/simplesheettplmgr/oneshow?simplesheettplid=<?=$a->id?>"><?=$a->title?></a></td>
                            <td>
                                <?php
                                    $items = explode("\n", $a->content);
                                    foreach ($items as $item) {
                                        echo $item . "<br>";
                                    }
                                ?>
                            </td>
                            <td><a target="_blank" href="/simplesheetmgr/list?simplesheettplid=<?=$a->id?>"><?=$a->getSimpleSheetCnt()?></a></td>
                            <td><span class="label label-info" id="title-<?=$a->id?>"><?=$a->createauditor->name?></span></td>
                            <td><?=$a->remark?></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-xs btn-default" type="button" data-toggle="modal" data-type="modify" data-id="<?=$a->id?>" data-ename="<?=$a->ename?>" data-title="<?=$a->title?>" data-content="<?=$a->content?>" data-remark="<?=$a->remark?>" data-target="#simplesheettpl-edit" title="" data-original-title="Edit Client"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-xs btn-default" type="button" data-toggle="tooltip" title="" data-original-title="Remove Client"><i class="fa fa-times"></i></button>
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
<div class="modal" id="simplesheettpl-edit" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                    <h3 class="block-title">添加问卷</h3>
                </div>
                <div class="block-content">
                    <input type="hidden" id="simplesheettplid" name="simplesheettplid" value="">

                    <div class="form-group">
                        <label class="" for="title">问卷名称</label>
                        <div class="">
                            <input class="form-control" type="text" id="title" name="title" placeholder="问卷名称">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="title">ename</label>
                        <div class="">
                            <input class="form-control" type="text" id="ename" name="ename" placeholder="唯一ename">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="title">配置</label>
                        <div class="">
                            <textarea class="form-control" id="content" name="content" rows="7" placeholder="Please edit config..."></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="title">备注</label>
                        <div class="">
                            <textarea class="form-control" id="remark" name="remark" rows="4" placeholder="Share your ideas with us.."></textarea>
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
    $('#simplesheettpl-edit').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        var type = button.data('type');
        if (type == 'modify') {
            var id = button.data('id');
            var title = button.data('title');
            var ename = button.data('ename');
            var content = button.data('content');
            var remark = button.data('remark');

            modal.find('#simplesheettplid').val(id);
            modal.find('#title').val(title);
            modal.find('#ename').val(ename);
            modal.find('#content').val(content);
            modal.find('#remark').val(remark);
        }
    });

    $('#submit-edit').on('click', function () {
        var simplesheettplid = $('#simplesheettplid').val();
        var title = $('#title').val();
        var ename = $('#ename').val();
        var content = $('#content').val();
        var remark = $('#remark').val();

        var flag = 0;

        if (title == '') {
            alert("名称不能为空");
        }

        if (ename == '') {
            alert("名称不能为空");
        }

        $.ajax({
            url: '/simplesheettplmgr/addormodifyjson',
            type: 'post',
            dataType: 'text',
            async: false,
            data: {
                simplesheettplid: simplesheettplid,
                title: title,
                ename: ename,
                content: content,
                remark: remark
            },
            "success": function (data) {
                if (data == 'add-success') {
                    alert("添加成功");
                    flag = 1;
                } else if (data == 'modify-success') {
                    alert("修改成功");
                    flag = 1;
                } else if (data == 'ename-already') {
                    alert("ename已存在");
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
