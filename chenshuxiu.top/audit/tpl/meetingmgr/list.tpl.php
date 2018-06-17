<?php
$pagetitle = "电话记录列表 Meetings";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE
.trOnSeleted {
	background-color: #CCCCFF;
}

.trOnMouseOver {
	background-color: #CCCCFF;
}

.isclosed_objid {
	background-color: #e6e6e6
}

.isclosed {
	background-color: #eeeeee
}

.objid {
	background-color: #dff8df
}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });

        $('#patient-listcond-word').autoComplete({
            type: 'patient',
            partner: '#patientid',
            change: function (event, ui) {
            },
            select: function (event, ui) {
                // $('#patientid').val(ui.item.id);
            },
            close: function (event, ui) {
            }
        });
    })
</script>

    <div class="col-md-12" id="top">
        <section class="col-md-6 content-left">
        <div class=searchBar>
            <form action="/meetingmgr/list" method="get" class="pr">
                <div class="mt10">
                    <div class="col-md-2" style="width: 55px;margin-top: 6px;padding-right: 0px;">
                        <label for="">医生：</label>

                    </div>
                    <div class="col-md-2">
                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="col-md-6" for="" style="width: 100px;margin-top: 6px;padding-right: 0px;">按患者姓名：</label>
                        <div class="col-md-6">
                            <?php include_once $tpl . '/_search_patient.tpl.php'; ?>
                        </div>
                    </div>
                </div>
                <input type="submit" class="btn btn-success" value="组合刷选" />
            </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th>患者</th>
                        <th>医生</th>
                        <th>最后一次记录时间</th>
                        <th>电话记录数量</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
            <?php foreach ($meeting_group as $a ){ ?>
                <tr onmouseover="over(this)" onmouseout="out(this)">
                        <td><?=Patient::getById($a['patientid'])->getMaskName() ?></td>
                        <td><?=Doctor::getById($a['doctorid'])->name ?></td>
                        <td><?=$a['lastmeetingtime'] ?></td>
                        <td><?=$a['cnt'] ?></td>
                        <td>
                            <a href="#top" data-patientid="<?= $a['patientid'] ?>" class="showMeetingOneHtml">详情</a>
                        </td>
                    </tr>
            <?php } ?>
            <tr>
                        <td colspan="10" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
        <section class="col-md-6 content-right pb10">
            <div id="MeetingHtmlShell" class="contentBoxBox"></div>
        </section>
    </div>
    <div class="clear"></div>


<?php
$footerScript = <<<XXX
    function over(tr){
        var className = $(tr).attr('id');
        $(tr).removeClass(className).addClass('trOnMouseOver');
    }
    function out(tr){
        var className = $(tr).attr('id');
        $(tr).removeClass('trOnMouseOver').addClass(className);
    }

    $(function(){
        $(".showMeetingOneHtml").on("click",function(){
            var className = $(this).parents('tr').attr('id');
            $("tr.trOnSeleted").removeClass("trOnSeleted").removeClass("trOnMouseOver").removeClass(className);
            $(this).parents("tr").addClass("trOnSeleted");
        });
    });

    $(function(){
        $(document).on("click", ".showMeetingOneHtml", function() {
            $(".content-right").show();
            var me = $(this);
            var patientid = me.data("patientid");

            $("#MeetingHtmlShell").html('');

            $.ajax({
                "type" : "get",
                "data" : {
                    patientid : patientid
                },
                "dataType" : "html",
                "url" : "/meetingmgr/oneHtml",
                "success" : function(data) {
                    $("#MeetingHtmlShell").html(data);
                }
            });
        });
    });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
