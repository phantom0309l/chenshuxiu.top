<?php
$pagetitle = "患者收集计划列表 Dc_patientPlans";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
]; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar" style="margin-left: 15px;margin-right: 15px;">
            <form action="/dc_patientplanmgr/list" method="get" class="pr form-horizontal">
                <div class="form-group">
                    <label class="col-md-2 control-label" style="width: 90px;">医生项目 </label>
                    <div class="col-md-2">
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toDc_doctorProjectCtrArray($dc_doctorprojects,true),"dc_doctorprojectid",$dc_doctorprojectid,"f18 form-control js-select2"); ?>
                    </div>
                    <div class="col-md-3">
                        <input type="submit" class="btn btn-success btn-minw" value=" 筛选 ">
                    </div>
                </div>
            </form>
        </div>

        <?php if ($patient instanceof Patient) { ?>
            <div class="col-md-12">
                <div class="col-sm-1 col-xs-3 success" style="float: left; padding: 0px; line-height: 2.5;">
                    <button class="btn btn-sm btn-primary" data-type="add" data-toggle="modal" data-target="#dc_patientplan-edit" type="button">
                        <i class="fa fa-plus push-5-r"></i>新建项目
                    </button>
                </div>
            </div>
        <?php } ?>
        <div class="col-md-12"  style="overflow-x:auto">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th style="width:100px">ID</th>
                        <th>患者</th>
                        <th>项目名称</th>
                        <th style="width:160px">创建时间</th>
                        <th>开始时间</th>
                        <th>结束时间</th>
                        <th>状态</th>
                        <th>量表</th>
                        <th>填写详细</th>
                        <th>创建人</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dc_patientplans as $a) { ?>
                        <tr id="dc_project-<?=$a->id?>">
                            <td><?=$a->id?></td>
                            <td><a href="/dc_patientplanmgr/list?dc_doctorprojectid=<?=$dc_doctorprojectid?>&patientid=<?=$a->patient->id?>"><?=$a->patient->name?></a></td>
                            <td><?=$a->title?></td>
                            <td><?=$a->createtime?></td>
                            <td><?=$a->begin_date?></td>
                            <td><?=$a->end_date?></td>
                            <td><?=$a->getDc_patientplan_statusStr()?></td>
                            <td><?=$a->dc_doctorproject->getPaperTplTitleStr()?></td>
                            <td><a target="_blank" href="/dc_patientplanitemmgr/list?dc_patientplanid=<?=$a->id?>"><?=$a->getItemCnt()?></a></td>
                            <td><span class="label label-info" id="title-<?=$a->id?>"><?=$a->create_auditor->name?></span></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<!-- 模态框 -->
<div class="modal" id="dc_patientplan-edit" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
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
                    <input type="hidden" id="patientid" name="patientid" value="<?=$patient->id?>">
                    <div class="form-group">
                        <label class="" for="title">项目名称</label>
                        <div class="">
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toDc_patientplanCtrArray($dc_doctorprojects,true),"add_dc_doctorprojectid", 0,"f18"); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">患者项目开始日期</label>
                        <div class="">
                            <input class="form-control calendar" type="text" id="begin_date" name="begin_date" placeholder="患者项目开始日期">
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
    App.initHelper('select2');

    $('#dc_patientplan-edit').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
    });

    $('#submit-edit').on('click', function () {
        var dc_doctorprojectid = $('#add_dc_doctorprojectid').val();
        var begin_date = $('#begin_date').val();
        var patientid = $('#patientid').val();

        var flag = 0;

        $.ajax({
            url: '/dc_patientplanmgr/addjson',
            type: 'post',
            dataType: 'text',
            async: false,
            data: {
                dc_doctorprojectid: dc_doctorprojectid,
                begin_date: begin_date,
                patientid: patientid
            },
            "success": function (data) {
                if (data == 'success') {
                    alert("添加成功");
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
