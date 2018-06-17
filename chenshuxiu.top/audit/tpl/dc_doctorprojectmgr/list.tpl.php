<?php
$pagetitle = "医生项目列表 Dc_doctorProjects";
$cssFiles = [
    $img_uri . '/static/css/jquery-ui.autocomplete.min.css?v=20180208',
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/static/js/vendor/jquery-ui.autocomplete.min.js?v=20180208',
    $img_uri . '/v5/common/select_doctor.js?v=20180530',
]; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar" style="margin-left: 15px;margin-right: 15px;">
            <form action="/dc_doctorprojectmgr/list" method="get" class="pr form-horizontal">
                <div class="form-group">
                    <label class="col-md-1 control-label" style="width: 70px;">医生 </label>
                    <div class="col-md-2">
                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </div>
                    <div class="col-md-3">
                        <input type="submit" class="btn btn-success btn-minw" value=" 筛选 ">
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12" style="margin-bottom: 10px;">
            <div class="col-sm-1 col-xs-3 success" style="float: left; padding: 0px; line-height: 2.5;">
                <button class="btn btn-sm btn-primary" data-type="add" data-toggle="modal" data-target="#dc_project-add" type="button">
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
                        <th>创建时间</th>
                        <th>项目名称</th>
                        <th>开启日期</th>
                        <th>结束日期</th>
                        <th>医生</th>
                        <th>调查频率</th>
                        <th>周期</th>
                        <th>量表</th>
                        <th>是否自动开启下一次</th>
                        <th>发送消息模板</th>
                        <th>公告</th>
                        <th>创建人</th>
                        <th>备注</th>
                        <th>患者项目</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dc_doctorprojects as $a) { ?>
                        <tr id="dc_project-<?=$a->id?>">
                            <td><?=$a->id?></td>
                            <td><?=$a->createtime?></td>
                            <td id="title_<?=$a->id?>"><?=$a->dc_project->title?></td>
                            <td id="begin_date_<?=$a->id?>"><?=$a->begin_date?></td>
                            <td id="end_date_<?=$a->id?>"><?=$a->end_date?></td>
                            <td id="doctor_<?=$a->id?>"><?=$a->doctor->name?></td>
                            <td id="frequency_<?=$a->id?>"><?=$a->frequency?></td>
                            <td id="period_<?=$a->id?>"><?=$a->period?></td>
                            <td><?=$a->getPaperTplTitleStr();?></td>
                            <td><?=$a->is_auto_open_next == 1 ? '开启' : '关闭'?></td>
                            <td id="send_content_tpl_<?=$a->id?>"><?=$a->send_content_tpl?></td>
                            <td id="bulletin_<?=$a->id?>"><?=$a->bulletin?></td>
                            <td><span class="label label-info"><?=$a->create_auditor->name?></span></td>
                            <td id="content_<?=$a->id?>"><?=$a->content?></td>
                            <td><a target="_blank" href="/dc_patientplanmgr/list?dc_doctorprojectid=<?=$a->id?>"><?=$a->getDc_patientplanCnt()?></a></td>
                            <td><?=$a->getStatusStr();?></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-xs btn-default" data-toggle="modal" data-dc_doctorprojectid="<?=$a->id?>" data-is_auto_open_next="<?=$a->is_auto_open_next?>" data-papertplids="<?=$a->papertplids?>" data-type="modify" data-target="#dc_project-modify" type="button" data-original-title="Edit Client"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-xs btn-default dc_project-delete" type="button" data-dc_doctorprojectid="<?=$a->id?>" ><i class="fa fa-times"></i></button>
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

<!-- 添加 模态框 -->
<div class="modal" id="dc_project-add" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                    <h3 class="block-title">添加医生项目</h3>
                </div>
                <div class="block-content">
                    <div class="form-group">
                        <label class="" for="title">项目</label>
                        <div class="">
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toDc_projectCtrArray($dc_projects,true),"dc_projectid", 0,"f18"); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">医生</label>
                        <div class="">
                            <span id="doctor_add_name"></span>
                            <input type="hidden" id="doctor_add_id" name="doctor_add_id" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">开始日期</label>
                        <div class="parentCls">
                            <input class="form-control calendar" type="text" id="begin_date" name="begin_date" placeholder="请输入开始日期">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">结束日期</label>
                        <div class="parentCls">
                            <input class="form-control calendar" type="text" id="end_date" name="end_date" placeholder="请输入结束日期">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">频次(天/次)</label>
                        <div class="parentCls">
                            <input class="form-control" type="text" id="frequency" name="frequency" placeholder="请输入频次">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">周期(天)</label>
                        <div class="">
                            <input class="form-control" type="text" id="period" name="period" placeholder="请输入周期">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">量表ids</label>
                        <div class="col-md-12" style="padding-left: 0px;padding-right: 0px;margin-bottom: 20px;">
                            <div class="input-group">
                                <input class="form-control" type="text" id="papertplids" name="papertplids" placeholder="量表ids">
                                <span class="input-group-btn">
                                    <a target="_blank" class="btn btn-primary" href="/papertplmgr/list">去量表列表</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">自动开启下一次</label>
                        <div class="">
                            <label class="css-input css-radio css-radio-info push-10-r">
                                <input type="radio" name="is_auto_open_next" value="1" checked=""><span></span> Yes
                            </label>
                            <label class="css-input css-radio css-radio-info">
                                <input type="radio" name="is_auto_open_next" value="0"><span></span> No
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">备注</label>
                        <div class="">
                            <textarea class="form-control" id="content" name="content" rows="4" placeholder="请输入备注"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">消息模板</label>
                        <div class="">
                            <textarea class="form-control" id="send_content_tpl" name="send_content_tpl" rows="4" placeholder="请输入消息模板"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">公告</label>
                        <div class="">
                            <textarea class="form-control" id="bulletin" name="bulletin" rows="4" placeholder="请输入公告"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button class="btn btn-sm btn-primary" type="button" id="submit-add" data-dismiss="modal"><i class="fa fa-check"></i>提交</button>
            </div>
        </div>
    </div>
</div>

<!-- 修改 模态框 -->
<div class="modal" id="dc_project-modify" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                    <h3 class="block-title">修改医生项目</h3>
                </div>
                <div class="block-content">
                    <input type="hidden" id="dc_doctorprojectid" name="dc_doctorprojectid" value="0">
                    <div class="form-group">
                        <label class="" for="title">项目</label>
                        <div class="">
                            <input class="form-control" type="text" id="modify_title" name="modify_title" readonly="readonly">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">医生</label>
                        <div class="">
                            <input class="form-control" type="text" id="modify_doctor" name="modify_doctor" readonly="readonly">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">开始日期</label>
                        <div class="parentCls">
                            <input class="form-control calendar" type="text" id="modify_begin_date" name="modify_begin_date" placeholder="请输入开始日期">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">结束日期</label>
                        <div class="parentCls">
                            <input class="form-control calendar" type="text" id="modify_end_date" name="modify_end_date" placeholder="请输入结束日期">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">频次(天/次)</label>
                        <div class="parentCls">
                            <input class="form-control" type="text" id="modify_frequency" name="modify_frequency" placeholder="请输入频次">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">周期(天)</label>
                        <div class="">
                            <input class="form-control" type="text" id="modify_period" name="modify_period" placeholder="请输入周期">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">量表ids</label>
                        <div class="col-md-12" style="padding-left: 0px;padding-right: 0px;margin-bottom: 20px;">
                            <div class="input-group">
                                <input class="form-control" type="text" id="modify_papertplids" name="modify_papertplids" placeholder="量表ids">
                                <span class="input-group-btn">
                                    <a target="_blank" class="btn btn-primary" href="/papertplmgr/list">去量表列表</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">自动开启下一次</label>
                        <div class="">
                            <label class="css-input css-radio css-radio-info push-10-r">
                                <input type="radio" id="yes_radio" name="modify_is_auto_open_next" value="1"><span></span> Yes
                            </label>
                            <label class="css-input css-radio css-radio-info">
                                <input type="radio" id="no_radio" name="modify_is_auto_open_next" value="0"><span></span> No
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">备注</label>
                        <div class="">
                            <textarea class="form-control" id="modify_content" name="modify_content" rows="4" placeholder="请输入备注"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">消息模板</label>
                        <div class="">
                            <textarea class="form-control" id="modify_send_content_tpl" name="modify_send_content_tpl" rows="4" placeholder="请输入消息模板"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">公告</label>
                        <div class="">
                            <textarea class="form-control" id="modify_bulletin" name="modify_bulletin" rows="4" placeholder="请输入公告"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button class="btn btn-sm btn-primary" type="button" id="submit-modify" data-dismiss="modal"><i class="fa fa-check"></i>提交</button>
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
    $('#dc_project-modify').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        var dc_doctorprojectid = button.data('dc_doctorprojectid');
        var title = $('#title_' + dc_doctorprojectid).text();
        var doctor = $('#doctor_' + dc_doctorprojectid).text();
        var begin_date = $('#begin_date_' + dc_doctorprojectid).text();
        var end_date = $('#end_date_' + dc_doctorprojectid).text();
        var doctor = $('#doctor_' + dc_doctorprojectid).text();
        var frequency = $('#frequency_' + dc_doctorprojectid).text();
        var period = $('#period_' + dc_doctorprojectid).text();
        var is_auto_open_next = button.data('is_auto_open_next');
        var send_content_tpl = $('#send_content_tpl_' + dc_doctorprojectid).text();
        var bulletin = $('#bulletin_' + dc_doctorprojectid).text();
        var content = $('#content_' + dc_doctorprojectid).text();
        var papertplids = button.data('papertplids');

        modal.find('#dc_doctorprojectid').val(dc_doctorprojectid);
        modal.find('#modify_title').val(title);
        modal.find('#modify_doctor').val(doctor);
        modal.find('#modify_begin_date').val(begin_date);
        modal.find('#modify_end_date').val(end_date);
        modal.find('#modify_doctor').val(doctor);
        modal.find('#modify_frequency').val(frequency);
        modal.find('#modify_period').val(period);
        modal.find('#modify_send_content_tpl').val(send_content_tpl);
        modal.find('#modify_bulletin').val(bulletin);
        modal.find('#modify_content').val(content);
        modal.find('#modify_papertplids').val(papertplids);
        if (is_auto_open_next == 1) {
            $('#yes_radio').prop("checked",true);
        } else {
            $('#no_radio').prop("checked",true);
        }

    });

    $('#submit-modify').on('click', function () {
        var dc_doctorprojectid = $('#dc_doctorprojectid').val();
        var begin_date = $('#modify_begin_date').val();
        var end_date = $('#modify_end_date').val();
        var frequency = $('#modify_frequency').val();
        var period = $('#modify_period').val();
        var papertplids = $('#modify_papertplids').val();
        var is_auto_open_next = $("input[name='modify_is_auto_open_next']:checked").val();
        var content = $('#modify_content').val();
        var send_content_tpl = $('#modify_send_content_tpl').val();
        var bulletin = $('#modify_bulletin').val();

//        alert("dc_doctorprojectid:" + dc_doctorprojectid + " begin_date:" + begin_date +
//            " end_date:" + end_date + " frequency:" + frequency + " period:" + period + " papertplids:" + papertplids + " is_auto_open_next:" + is_auto_open_next + " content:" + content + " send_content_tpl:" + send_content_tpl + " bulletin:" + bulletin);

//        return false;

        var flag = 0;
        $.ajax({
            url: '/dc_doctorprojectmgr/modifyjson',
            type: 'post',
            dataType: 'text',
            async: false,
            data: {
                dc_doctorprojectid : dc_doctorprojectid,
                begin_date : begin_date,
                end_date : end_date,
                frequency : frequency,
                period : period,
                papertplids : papertplids,
                is_auto_open_next : is_auto_open_next,
                content : content,
                send_content_tpl : send_content_tpl,
                bulletin : bulletin
            },
            "success": function (data) {
                if (data == 'success') {
                    alert("修改成功");

                    flag = 1;
                } else {
                    alert("修改失败");
                }
            }
        });

        if (flag == 0) {
            return false;
        }

        window.location.href = window.location.href;
    });

    $('#dc_project-add').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        
        var doctorid = $("#doctorid").val();
        var doctor_name = $("#doctor-word").val();

        modal.find('#doctor_add_name').text(doctor_name);
        modal.find('#doctor_add_id').val(doctorid);
    });

    $('#submit-add').on('click', function () {
        var dc_projectid = $('#dc_projectid').val();
        var doctorid = $('#doctor_add_id').val();
        var begin_date = $('#begin_date').val();
        var end_date = $('#end_date').val();
        var frequency = $('#frequency').val();
        var period = $('#period').val();
        var papertplids = $('#papertplids').val();
        var is_auto_open_next = $("input[name='is_auto_open_next']:checked").val();
        var content = $('#content').val();
        var send_content_tpl = $('#send_content_tpl').val();
        var bulletin = $('#bulletin').val();
      
        var flag = 0;
        $.ajax({
            url: '/dc_doctorprojectmgr/addjson',
            type: 'post',
            dataType: 'text',
            async: false,
            data: {
                dc_projectid : dc_projectid,
                doctorid : doctorid,
                begin_date : begin_date,
                end_date : end_date,
                frequency : frequency,
                period : period,
                papertplids : papertplids,
                is_auto_open_next : is_auto_open_next,
                content : content,
                send_content_tpl : send_content_tpl,
                bulletin : bulletin
            },
            "success": function (data) {
                if (data == 'success') {
                    alert("添加成功");

                    flag = 1;
                } else {
                    alert("添加失败");
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
        var dc_doctorprojectid = me.data('dc_doctorprojectid');

        $.ajax({
            url: '/dc_doctorprojectmgr/deletejson',
            type: 'get',
            dataType: 'text',
            data: {
                dc_doctorprojectid: dc_doctorprojectid
            },
            "success": function (data) {
                if (data == 'success') {
                    alert("删除成功");
                } else if (data == 'fail-cnt') {
                    alert("该项目还有患者项目,不能删除！")
                } else if (data == 'fail-not') {
                    alert("删除错误");
                } else {
                    alert("未知错误");
                }
            }
        });

        window.location.href = window.location.href;
    });
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
