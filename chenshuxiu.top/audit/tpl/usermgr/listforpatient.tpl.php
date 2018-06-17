<?php
$pagetitle = "报到情况(审核 todo:需要修改)";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址

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

    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <form action="/usermgr/listforpatient" method="get" class="pr">
                    <div class="mt10">
                        <label class="col-md-1" for="" style="margin-top: 8px;padding-right: 0px;width: 65px;">医生：</label>
                        <div class="col-md-3">
                            <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                        </div>
                    </div>
                    <div class="mt10">
                        <label class="col-md-1" for="" style="margin-top: 8px;padding-right: 0px;width: 65px;">患者：</label>
                        <div class="col-md-3">
                            <?php include_once $tpl . '/_search_patient.tpl.php'; ?>
                        </div>
                    </div>
                    <input type="submit" class="btn btn-success" value="组合刷选" />
                </form>
            </div>
            <div class="table">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td>userid</td>
                            <td>关注日期</td>
                            <td>扫码进入</td>
                            <td>微信昵称</td>
                            <td>patientid</td>
                            <td>报到日期</td>
                            <td>报到姓名</td>
                            <td>身份证</td>
                            <td>所属医生</td>
                            <td>所属疾病</td>
                            <td>院内ID</td>
                            <td>状态</td>
                            <td>备注</td>
                            <td>审核</td>
                            <td>操作</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $a) { ?>
                            <tr>
                                <td class="gray"><?= $a->id ?></td>
                                <td class="gray"><?= $a->getCreateDayW() ?></td>
                                <td class="gray"><?= $a->getMasterWxUser()->wx_ref_code == ""? "no" : "yes" ?></td>
                                <td class="gray"><?= $a->getMasterWxUser()->nickname ?></td>
                                <td class="gray"><?= $a->patientid ?></td>
                                <td class="gray"><?= $a->patient->createtime ?></td>
                                <td>
                                    <?php
                                        if ($a->patient instanceof Patient) {
                                            echo $a->patient->getMaskName();
                                        }
                                    ?>
                                    <p>
                                        <a href="/patientmgr/changedoctor?patientid=<?= $a->patient->id ?>">更改医生</a>
                                    </p>
                                </td>
                                <?php
                                    $pcard = $a->patient->getMasterPcard();
                                ?>
                                <td><?= $a->patient->prcrid ?></td>
                                <td><?= $pcard->doctor->name ?></td>
                                <td><?= $pcard->disease->name ?></td>
                                <td><?= $pcard->getYuanNeiStr('<br/>') ?></td>
                                <td><?= XConst::status_withcolor($a->patient->status) ?></td>
                                <td name="auditremark_<?= $a->patientid ?>"><?= $a->patient->auditremark ?></td>
                                <td name="audit_<?= $a->patientid ?>"><?= XConst::auditStatus_withcolor($a->patient->auditstatus) ?></td>
                                <td>
                                    <?php if ($a->patient->auditstatus == 0) { ?>
                                        <?php if ($a->patient->has_audited_patient()) { ?>
                                    <a class="btn btn-danger" href="/patientmgr/list4bind?patientid=<?=$a->patient->id ?>">关联患者</a>
                                        <?php } else { ?>
                                    <input class="btn btn-primary Joper only-pass" type='button' data-patientid='<?= $a->patientid ?>' id='passJson' value='通过' />
                                    <input class="btn btn-danger Joper no-pass" type='button' id='refuseJson' data-patientid='<?= $a->patientid ?>' data-userid='<?= $a->id ?>' value='拒绝' data-toggle="modal" data-target="#denyBox" />
                                        <?php } ?>

                                    <?php } ?>
                                    <a href="/patientmgr/list4bind?patientid=<?=$a->patientid?>">同名(<?=$a->patient->getSameNamePatientCnt(); ?>)</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan=20>
                                <?php include $dtpl . "/pagelink.ctr.php"; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    <div class="modal fade" id="denyBox" tabindex="-1" role="dialog" aria-labelledby="denyBoxLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="denyBoxLabel">拒绝患者</h4>
                </div>
                <div class="modal-body">
                    <form class="denyBox">
                        <div class="form-group">
                            <label>拒绝备注</label>
                            <textarea class="form-control denyBox-content" rows="7"></textarea>
                        </div>
                        <p class="denyBox-notice text-success none text-right"></p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default deny-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-default deny-btn deny">狠心拒绝。。。</button>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<SCRIPT
    $(document).ready(function () {
        $(".only-pass").click(function () {
            var me = $(this);
            var patientid = me.data('patientid');
            $.ajax({
                type: 'get',
                url: '/patientmgr/passJson',
                data: {patientid: patientid},
                success: function (data) {
                    if (data == 'ok') {
                        $("td[name='audit_" + patientid + "']").text('通过');
                        me.parents("tr").find(".Joper").hide();
                    }
                }
            });
        });

        $(".no-pass").click(function () {
            var me = $(this);
            var userid = me.data('userid');
            var patientid = me.data('patientid');
            $(".deny-btn").data('userid', userid);
            $(".deny-btn").data('patientid', patientid);
        });

        $(".deny-btn").click(function () {
            var me = $(this);
            var userid = me.data('userid');
            var patientid = me.data('patientid');
            var contentNode = $("#denyBox").find(".denyBox-content");
            var content = $.trim( contentNode.val());

            $.ajax({
                type: 'get',
                url: '/patientmgr/refuseJson',
                data: {
                    userid: userid,
                    patientid: patientid,
                    content: content
                },
                success: function (data) {
                    var txt = "更新失败";

                    if (data == 'ok') {
                        txt = "更新成功";

                        var auditremarkNode = $("td[name='auditremark_" + patientid + "']");
                        $("td[name='audit_" + patientid + "']").text('拒绝');
                        auditremarkNode.text(content);
                        auditremarkNode.parents("tr").find(".Joper").hide();
                    }

                    $(".denyBox-notice").text(txt).fadeIn(0, function() {
                        $(this).fadeOut(1200, function(){
                            contentNode.val("");
                            $(".deny-default").click();
                        });
                    });
                }
            });
        });
    });
SCRIPT;
 ?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
