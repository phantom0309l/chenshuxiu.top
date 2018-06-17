<?php
$pagetitle = "患者分组列表 PatientGroups";
$cssFiles = [
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="col-md-12">
                <div class="col-sm-1 col-xs-3 success" style="float: left; padding: 0px; line-height: 2.5;">
                    <button class="btn btn-sm btn-primary"  data-type="add"  data-toggle="modal" data-target="#patientgroup-edit" type="button">
                        <i class="fa fa-plus push-5-r"></i>新建分组
                    </button>
                </div>

                <div class="col-sm-11 col-xs-9">
                    <div class="col-sm-3" style="float: right; padding-right: 0px;">
                        <form class="form-horizontal push-5-t" action="/patientgroupmgr/list" method="post">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" placeholder="搜索组名" name="patientgroup_title" class="input-search form-inline form-control" value="<?=$patientgroup_title?>">
                                    <span class="input-group-btn" style="width: 1%; line-height: 35px;">
                                        <button type="submit" class="btn btn-primary">
                                            <span type="submit" aria-hidden="true" class="glyphicon glyphicon-search">
                                            </span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="clear">

                </div>
            </div>
            <div class="col-md-12"  style="overflow-x:auto">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width:190px">创建时间</th>
                            <th style="width:100px">ID</th>
                            <th>序号</th>
                            <th>组名</th>
                            <th>描述</th>
                            <th>组人数</th>
                            <th>创建人</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patientgroups as $a) { ?>
                            <tr id="patientgroup-<?=$a->id?>">
                                <td><?=$a->getCreateDay()?></td>
                                <td><?=$a->id?></td>
                                <td id="pos-<?=$a->id?>"><?=$a->pos?></td>
                                <td id="title-<?=$a->id?>"><?=$a->title?></td>
                                <td id="content-<?=$a->id?>"><?=$a->content?></td>
                                <td>
                                    <a target="_blank" href="/optaskmgr/listnew?patientgroupid=<?=$a->id?>&status_str=all"><?=$a->getPatientCnt();?></a>
                                </td>
                                <td><span class="label label-info"><?=$a->create_auditor->name?></span></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-xs btn-default" id="button-data-<?=$a->id?>" data-toggle="modal" data-type="modify" data-patientgroupid="<?=$a->id?>" data-pos="<?=$a->pos?>" data-title="<?=$a->title?>" data-content="<?=$a->content?>" data-target="#patientgroup-edit" type="button" title="" data-original-title="Edit Client"><i class="fa fa-pencil"></i></button>
                                        <button class="btn btn-xs btn-default patientgroup-delete" type="button" data-patient_cnt="<?=$a->getPatientCnt();?>" data-patientgroupid="<?=$a->id?>" data-title="<?=$a->title?>"><i class="fa fa-times"></i></button>
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
    <div class="modal" id="patientgroup-edit" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="block block-themed block-transparent remove-margin-b">
                    <div class="block-header bg-primary">
                        <ul class="block-options">
                            <li>
                                <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                            </li>
                        </ul>
                        <h3 class="block-title">修改分组</h3>
                    </div>
                    <div class="block-content">
                        <input type="hidden" id="type" value="">
                        <input type="hidden" id="patientgroupid" value="">
                        <input type="hidden" id="old_title" value="">
                        <div class="form-group">
                            <label class="" for="pos">序号</label>
                            <div class="">
                                <input class="form-control" type="text" id="pos" name="pos" placeholder="请输入序号">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="" for="title">组名</label>
                            <div class="">
                                <input class="form-control" type="text" id="title" name="title" placeholder="请输入组名">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="" for="content">描述</label>
                            <div class="">
                                <textarea class="form-control" id="content" name="content" rows="4" placeholder="请输入描述"></textarea>
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
<?php
$footerScript = <<<XXX
$(function() {
    $('#patientgroup-edit').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        var type = button.data('type');

        if (type == 'add') {
            modal.find('#type').val('add');
            modal.find('#patientgroupid').val(0);
            modal.find('.block-title').text('添加分组');

            modal.find('#pos').val('');
            modal.find('#title').val('');
            modal.find('#content').val('');
        } else if (type == 'modify'){
            var patientgroupid = button.data('patientgroupid');
            modal.find('#type').val('modify');
            modal.find('#patientgroupid').val(patientgroupid);
            modal.find('.block-title').text('修改分组');

            var pos = button.data('pos');
            var title = button.data('title');
            var content = button.data('content');

            modal.find('#pos').val(pos);
            modal.find('#title').val(title);
            modal.find('#old_title').val(title);
            modal.find('#content').val(content);
        } else {
            alert("操作类型未知！");
            return false;
        }
    });

    $('#submit-edit').on('click', function () {
        var pos = $('#pos').val();
        var title = $('#title').val();
        var old_title = $('#old_title').val();
        var content = $('#content').val();
        var patientgroupid = $('#patientgroupid').val();

        if (title == '') {
            alert("组名不能为空!");
            return false;
        }

        var flag = 0;

        $.ajax({
            url: '/patientgroupmgr/addormodifyjson',
            type: 'get',
            dataType: 'text',
            async: false,
            data: {
                patientgroupid: patientgroupid,
                pos: pos,
                title: title,
                content : content
            },
            "success": function (data) {
                if (data == 'ok') {
                    if (patientgroupid > 0) {
                        $("#pos-" + patientgroupid).text(pos);
                        $("#title-" + patientgroupid).text(title);
                        $("#content-" + patientgroupid).text(content);

                        $("#button-data-" + patientgroupid).data('pos', pos);
                        $("#button-data-" + patientgroupid).data('title', title);
                        $("#button-data-" + patientgroupid).data('content', content);
                    } else {
                        window.location.href = location.href;
                    }

                    flag = 1;
                } else {
                    alert(title + " 已存在,请重新输入组名!");
                    $('#title').val(old_title);
                }
            }
        });

        if (flag == 0) {
            return false;
        }
        
    });

    $('.patientgroup-delete').on('click', function(){
        var me = $(this);
        var patientgroupid = me.data('patientgroupid');
        var title = me.data('title');
        var patient_cnt = me.data('patient_cnt');

        if (patient_cnt > 0) {
            alert("该组还有患者,不能删除！");
            return false;
        }

        if (false == confirm("确认删除[" + title  + "]吗?")) {
            return false;
        }

        $.ajax({
            url: '/patientgroupmgr/deletejson',
            type: 'get',
            dataType: 'text',
            data: {
                patientgroupid: patientgroupid
            },
            "success": function (data) {
                if (data == 'ok') {
                    $("#patientgroup-" + patientgroupid).remove();
                    alert("删除成功");
                } else if (data == 'fail-notempty') {
                    alert("该组还有患者,不能删除！")
                }
            }
        });
    });
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
