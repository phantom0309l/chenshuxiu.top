<style media="screen">
    .inline-select {
        display: inline;
        width: 146px;
    }

    .patient-name {
        font-size: 15px;
        font-weight: 500;
        text-transform: uppercase;
        line-height: 1.2;
    }

    .check-patient-diagnosis-span {
        float: right;
        width: 30%;
        text-align: right;
    }

    .check-diagnosis-type {
        width: 50%;
        height: 25px;
        line-height: 20px;
    }

    .other-patient-diagnosis-input {
        width: 20%;
        height: 25px;
        line-height: 16px;
        border: 1px solid #b0b0b0;
        display: inline-block;
        text-indent: 0.5em;
    }

    .margin_20_imp {
        margin-right: 20px;
    !important;
    }

    .msg-markform {
        border: 1px solid #eee;
        position: absolute;
        background-color: #fff;
        padding: 15px 0;
        border-radius: 6px;
        display: grid;
        z-index: 10;
        top: 35px;
        left: 72px;
    }

    #todaymark_cancel {
        color: #f5f5f5;
        background-color: #545454;
        border-color: #545454;
    }
</style>
<?php
$patientid = $patient->id;
$patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid("Lilly", $patientid);
?>
<div class="patientBaseBox remove-margin-t" style="background: none;">
    <div class="patientBaseBox-item" style="position:relative; line-height: 1.6; margin-bottom: 20px;">
        <?php if ($patient->is_lose) { ?>
            <span class="label label-danger vertical-align_middle">失访</span>
        <?php } ?>
        <a target='_blank' href='/patientmgr/one?patientid=<?= $patient->id ?>'>
            <?php if ($patient->has_valid_quickpass_service()) {
                echo '<i class="vip_quickpass"></i>';
            } ?>
            <span class="patient-name vertical-align_middle"><?= $patient->name ?></span><i class="fa fa-search"></i></a>
        <span class="vertical-align_middle"><?= $patient->getSexStr() ?></span>
        <span class="vertical-align_middle"><?= $patient->getAgeStr() ?> 岁</span>
        <span class="vertical-align_middle"><?= $patient->getXprovinceStr(); ?> <?= $patient->getXcityStr(); ?></span>

        <!-- 重点用户部分 -->
        <?php if (DiseaseGroup::isCancer($patient->disease->diseasegroupid)) { ?>
            <i class="block-title-user todaymark <?= $patient->isTodayMark() ? 'todaymark_primary' : 'todaymark_default' ?>" id="todaymark"
               style="margin-left: 10px;" data-toggle="popover" data-placement="top"
               data-content="<?= $patient->getTodayMarksStr() ?>">
            </i>
        <?php } ?>


        <div id="msg-markform" class="msg-markform" style="display: none">
            <div id="msg-markitem"></div>
            <div class="col-xs-12 tc">
                <button class="btn btn-sm btn-info" id="todaymark_confirm" type="button">确定</button>
                <button class="btn btn-sm btn-default ml10" id="todaymark_cancel" type="button">取消
                </button>
            </div>
        </div>
        <!-- 重点用户结束 -->
        <?php
        if (Disease::isADHD($patient->diseaseid)) {
            ?>
            <span class="vertical-align_middle check-patient-diagnosis-span">
            诊断：
            <button class="btn" data-toggle="modal" data-target="#patient-diagnosis-modal">请选择</button>
        </span>
        <?php } ?>
    </div>
    <?php
    if (Disease::isADHD($patient->diseaseid)) {
        ?>
        <p>
        <span class="vertical-align_middle" id="patient-diagnosis-str">
            诊断：
            <span>
                <?php
                $patientDiagnosisStr = TagRefDao::getTagNamesStr($patient, 'patientDiagnosis');
                echo $patientDiagnosisStr == ' - ' ? "未知" : $patientDiagnosisStr;
                ?>
            </span>
        </span>
        </p>
    <?php } ?>
    <p class="patientBaseBox-item">
        <span>
            报到:
            <span class="red"><?= $patient->getDayCntFromBaodao() ?>天</span>
        </span>
        <span>
            开药门诊:
            <span class="red"><?= $patient->canIntoMenzhen() ? "开启" : "未开启" ?></span>
        </span>
        <span>
            <a target="_blank" href="/shopaddressmgr/listforpatient?patientid=<?= $patient->id ?>">编辑收货地址</a>
        </span>
    </p>
    <?php if ($patient_hezuo instanceof Patient_hezuo) { ?>
        <p class="patientBaseBox-item">
        <span>
            入组时已服药时长:
            <span class="red"><?= $patient_hezuo->drug_monthcnt_when_create ?>个月</span>
        </span>
        </p>
    <?php } ?>

    <p class="patientBaseBox-item">
        <span> 责任人：</span>
        <span>
            <span class="lock_auditor_name blue">
            <?= $patient->auditor ? $patient->auditor->name : '无' ?>
            </span>
            <span data-patientid="<?= $patient->id ?>" data-auditorid="<?= $patient->auditorid ?>" class="btn btn-default btn-sm lock_span">
                <span class="lock_title"><?= $patient->getLock_titleForAudit($myauditor); ?></span>
            </span>
        </span>
    </p>
    <?php
    if (Disease::isADHD($mydisease->id)) {
        ?>
        <?php
        if ($patient_hezuo instanceof Patient_hezuo && $patient_hezuo->status == 1) {
            $status = $patient_hezuo->status;
            $ptag5 = TagRefDao::getByObjtypeObjidTagid("Patient", $patientid, "141");
            $ptag6 = TagRefDao::getByObjtypeObjidTagid("Patient", $patientid, "142");
            $ptag7 = TagRefDao::getByObjtypeObjidTagid("Patient", $patientid, "143");
            $ptag8 = TagRefDao::getByObjtypeObjidTagid("Patient", $patientid, "144");
            ?>
            <p class="patientHezuo row text-center" style="background: #f9f9f9; padding: 5px; border-radius: 2px">
                <span class="col-lg-6 col-lg-offset-3 font-w700 push-10">Sunflower项目进行中</span>
                <span class="patientHezuoBtn sunflowerOutBtn btn btn-default push-5 col-sm-4" data-status="3"
                      data-patientname="<?= $patient->name ?>">不活跃退出</span>
                <span class="patientHezuoBtn sunflowerOutBtn btn btn-default push-5 col-sm-4 col-sm-offset-4" data-status="4"
                      data-patientname="<?= $patient->name ?>">换／停药退出</span>
                <span class="patientHezuoBtn sunflowerOutBtn btn btn-default push-5 col-sm-4" data-status="5"
                      data-patientname="<?= $patient->name ?>">主动退出</span>
                <span class="patientHezuoBtn sunflowerOutBtn btn btn-default push-5 col-sm-4 col-sm-offset-4 disabled" data-status="6"
                      data-patientname="<?= $patient->name ?>">扫非合作医生退出</span>
            </p>
            <p class="patientHezuo duetoDrugOutBtn-item hidden" style="background: #f9f9f9; padding: 5px; border-radius: 2px">
                换/停药退出原因备注
                <br/>
                <span class="patientTagBtn btn btn-default push-5 <?= $ptag5 instanceof TagRef ? 'btn-primary' : '' ?>" data-tagid="141">疗效不明显</span>
                <span class="patientTagBtn btn btn-default push-5 <?= $ptag6 instanceof TagRef ? 'btn-primary' : '' ?>" data-tagid="142">担心副反应</span>
                <span class="patientTagBtn btn btn-default push-5 <?= $ptag7 instanceof TagRef ? 'btn-primary' : '' ?>" data-tagid="143">买药不便</span>
                <span class="patientTagBtn btn btn-default push-5 <?= $ptag8 instanceof TagRef ? 'btn-primary' : '' ?>" data-tagid="144">其他原因</span>
            </p>
        <?php } ?>
        
        <?php if ($patient_hezuo instanceof Patient_hezuo && $patient_hezuo->status > 1) { ?>
            <p class="patientHezuo row text-center" style="background: #f9f9f9; padding: 5px; border-radius: 2px">
                <span class="col-lg-9 col-lg-offset-2 font-w700 push-10">Sunflower项目已结束(<?= $patient_hezuo->getStatusStr() ?>)</span>
            </p>
        <?php } ?>
    <?php } ?>
    <?php if (Disease::isADHD($patient->diseaseid)) { ?>
        <p class="patientBaseBox-item">
        <span>当前分组:
            <?php $ppgrefs = PatientPgroupRefDao::getListByPatientid($patient->id); ?>
            <?php
            foreach ($ppgrefs as $ppgref) {
                $pgroupid = $ppgref->pgroupid;
                $patientpgrouprefs_pgroup = PatientPgroupRefDao::getListByPatientid($patient->id, " and pgroupid = {$pgroupid}");
                $_cnt = count($patientpgrouprefs_pgroup);
                $colorstr = '';
                if (0 == $ppgref->status) {
                    $colorstr = 'red';
                }
                if (1 == $ppgref->status) {
                    $colorstr = 'green';
                }
                if (2 == $ppgref->status) {
                    $colorstr = 'blue';
                }
                ?>
                <span class="span-<?= $colorstr ?>">
                <?= $ppgref->pgroup->name ?>
                    <sup><?= $_cnt ?></sup>
            </span>
                <?php
            }
            ?>
        </span>
        </p>
    <?php } ?>
    
    <?php if (Disease::isCancer($patient->diseaseid)) { ?>
        <p class="patientBaseBox-item" data-patientid="<?= $patient->id ?>">
            治疗阶段：
            <input type="hidden" id="patientstageid_current_val" value="<?= $patient->patientstageid ?>">
            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getPatientStagesCtrArray(), "patientstageid", $patient->patientstageid, 'form-control select-patientstageid inline-select'); ?>
        </p>
    <?php } ?>
    
    <?php
    $pcard = $patient->getMasterPcard();
    if ($pcard instanceof Pcard && !Disease::isCancer($patient->diseaseid) && !Disease::isADHD($patient->diseaseid)) {
        ?>
        <p class="patientBaseBox-item" data-patientid="<?= $patient->id ?>">
            当前分组：
            <input type="hidden" id="current_val" value="<?= $patient->patientgroupid ?>">
            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getPatientGroupsCtrArray(), "patientgroupid", $patient->patientgroupid, 'js-select2 form-control select-patientgroupid inline-select'); ?>
        </p>
        <?php if (!Disease::isNMO($patient->diseaseid)) { ?>
            <p class="patientBaseBox-item">
                <span>下一次用药核对: <?php echo $pcard->next_pmsheet_time == "0000-00-00 00:00:00" ? "关闭" : substr($pcard->next_pmsheet_time, 0, 10); ?></span>
                <a class="btn btn-success btn-sm" target="_blank" href="/patientmgr/list?keyword=<?= $patient->name ?>">
                    <i class="fa fa-pencil"></i>
                    修改
                </a>
            </p>
        <?php } ?>
        <p class="patientBaseBox-item"><span>距离上次复发时间: <?= $pcard->getDescStrOfLast_incidence_date2Today() ?></span></p>
    <? } ?>
    
    <?php
    $tagnames = $patient->getPatientTagNames();
    if (count($tagnames) > 0) {
        ?>
        <p class="patientBaseBox-item">
        <span>医生标签:
            <?php
            foreach ($tagnames as $tagname) { ?>
                <span class="label label-info"><?= $tagname ?></span>
            <?php } ?>
        </span>
        </p>
    <?php } ?>
    
    <?php
    $pcards = $patient->getPcards();
    $flag = false;
    foreach ($pcards as $a) {
        if (Disease::isCancer($a->diseaseid)) {
            $flag = true;
            break;
        }
    }
    if ($flag) {
        ?>
        <p class="patientBaseBox-item" data-patientid="<?= $patient->id ?>">
            患者分组：
            <input type="hidden" id="current_val" value="<?= $patient->patientgroupid ?>">
            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getPatientGroupsCtrArray(), "patientgroupid", $patient->patientgroupid, 'form-control select-patientgroupid inline-select'); ?>
            (<?php echo $patient->doctor->doctorgroup->title; ?>)
        </p>
        <?php
    }
    ?>
    <a href="/patientmgr/list?keyword=<?= $patient->id ?>" target="_blank">查看详情 >></a>
    <a href="/aepcmgr/list?patientid=<?= $patient->id ?>" target="_blank">添加AEPC >></a>
    <?php include $tpl . "/patientmgr/_patientbase_call.php" ?>
    <?php include $tpl . "/patientmgr/_patientbase_pcard.php" ?>
</div>

<!-- 模态框 -->
<div class="modal" id="patient-diagnosis-modal" tabindex="-1" role="dialog" aria-hidden="true" style="">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                    <h3 class="block-title">选择患者诊断类型</h3>
                </div>
                <div class="block-content">
                        <span id="create-checkbox-html">

                        </span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button class="btn btn-sm btn-primary" type="button" id="submit-patient-diagnosis" data-dismiss="modal"><i class="fa fa-check"></i>提交
                </button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(function () {
        $(document).bind("click", function (e) {
            //id为msg-markform的是菜单，id为todaymark的是打开菜单的图标
            if ($(e.target).closest("#msg-markform").length === 0 && $(e.target).closest("#todaymark").length === 0) {
                //点击id为msg-markform之外且id不是不是todaymark，则触发
                $('#msg-markform').hide();
            }
        });

        var marktpls = <?= $patienttodaymarktpls ?>;

        $("#todaymark").on('click', function () {
            $.ajax({
                url: '/patienttodaymarkmgr/gettodaymarkjson',
                type: 'get',
                dataType: 'json',
                data: {
                    patientid: <?= $patient->id ?>,
                },
                success: function (response) {
                    var html = '',
                        selected_tplids = response.data.selected_tplids;
                    $.each(marktpls, function (index, marktpl) {
                        html += '<div class="col-xs-12 mb15">';
                        html += '<label class="css-input css-checkbox css-checkbox-primary" for="todaymarktplid_' + marktpl.id + '">';
                        html += '<input type="checkbox" ';

                        if ($.inArray(marktpl.id + '', selected_tplids) !== -1) {
                            html += ' checked ';
                        }
                        html += 'name="mark-checkbox" id="todaymarktplid_' + marktpl.id + '" data-title=" ' + marktpl.title + '" value="' + marktpl.id + '" >';
                        html += '<span></span> ';
                        html += marktpl.title;
                        html += '</label>';
                        html += '</div>';
                    });

                    $('#msg-markitem').html(html);
                    $("#msg-markform").show();
                }
            });
        });

        $("#todaymark_cancel").on('click', function () {
            $("#msg-markform").hide();
        });

        $("#todaymark_confirm").on('click', function () {
            
            var marktplids = [];
            $("input[name='mark-checkbox']:checked").each(function () {//把所有被选中的复选框的值存入数组
                marktplids.push($(this).val());
            });
            
            var patientid = '<?= $patient->id ?>';

            $.ajax({
                url: '/patienttodaymarkmgr/addpostjson',
                type: 'post',
                dataType: 'json',
                data: {
                    patientid: patientid,
                    marktplids: marktplids,
                },
                success: function (response) {
                    $("#msg-markform").hide();

                    var marksStr = response.data.marksStr,
                        el_todaymark = $('#todaymark'),
                        el_list_todaymark = $('.showPatientOneHtml.patientid-' + patientid).find('.todaymark');

                    el_list_todaymark.show();

                    if (marksStr === '') {
                        el_todaymark.removeClass('todaymark_primary').addClass('todaymark_default');
                        el_todaymark.attr('data-content', '非重点患者');

                        el_list_todaymark.attr('data-content', '非重点患者');
                        el_list_todaymark.removeClass('todaymark_primary').addClass('todaymark_default');
                    } else {
                        el_todaymark.removeClass('todaymark_default').addClass('todaymark_primary');
                        el_todaymark.attr('data-content', response.data.marksStr);

                        el_list_todaymark.attr('data-content', response.data.marksStr);
                        el_list_todaymark.removeClass('todaymark_default').addClass('todaymark_primary');
                    }
                }
            });
        });
    });

    $(function () {
        <?php if ($patient->auditorid > 0 && $patient->auditorid != $myauditor->id ) { ?>
        alert("患者 [<?= $patient->name ?>] 当前负责人 [<?= $patient->auditor->name ?>]");
        <?php }?>

        // 左侧列表, 标记运营责任人
        $('.lock_auditor_name_' +<?=$patient->id ?>).text('<?= $patient->auditor->name ?>');
        var span_lock_patient_cnt = $(".span_lock_patient_cnt");
        span_lock_patient_cnt.text(<?= $myauditor->getLock_patient_cnt() ?>);

        $(".lock_span").on("click", function (event) {
            event.preventDefault();

            var span_lock_patient_cnt = $(".span_lock_patient_cnt");

            var me = $(this);
            var patientid = me.data('patientid');
            var auditorid = me.data('auditorid');

            var url = "";
            var cnt_fix = 0;
            if (auditorid == 0) {
                // 锁定
                url = "/patientmgr/lockjson";
                cnt_fix = 1;
            } else if (auditorid == <?= $myauditor->id ?>) {
                // 解锁
                url = "/patientmgr/unlockjson";
                cnt_fix = -1;
            } else {
                // 抢锁
                url = "/patientmgr/lockjson";
                cnt_fix = 1;
            }

            $.ajax({
                url: url,
                type: 'get',
                dataType: 'json',
                data: {
                    patientid: patientid,
                    auditorid: <?= $myauditor->id ?>
                },
                "success": function (data) {
                    if (data.errno != '0') {
                        alert(data.errmsg);
                        return;
                    }

                    me.data('auditorid', data.data.auditorid);
                    $('.lock_auditor_name').text(data.data.auditor_name);
                    $('.lock_auditor_name_' + patientid).text(data.data.auditor_name);
                    $('.lock_title').text(data.data.lock_title);

                    if (cnt_fix > 0) {
                        span_lock_patient_cnt.text(parseInt(span_lock_patient_cnt.text()) + 1);
                    } else {
                        span_lock_patient_cnt.text(parseInt(span_lock_patient_cnt.text()) - 1);
                    }
                }
            });

        });

        $(".select-patientgroupid").on('change', function (event) {
            event.preventDefault();
            var me = $(this);
            var patientgroupid = me.val();

            if (false == confirm("确定修改分组吗?")) {
                var last = $("#current_val").val();
                me.val(last);

                return false;
            }

            var patientid = me.parents('p').data('patientid');

            $.ajax({
                url: '/patientmgr/modifypatientgroupjson',
                type: 'get',
                dataType: 'text',
                data: {
                    patientid: patientid,
                    patientgroupid: patientgroupid
                },
                "success": function (data) {
                    if (data == 'ok') {
                        if (patientgroupid == 0) {
                            alert("成功移出阶段");
                        } else {
                            alert("修改成功");
                        }
                    }
                }
            });
        });

        $(".select-patientstageid").on('change', function (event) {
            event.preventDefault();
            var me = $(this);
            var patientstageid = me.val();

            if (false == confirm("确定修改阶段吗?")) {
                var last = $("#patientstageid_current_val").val();
                me.val(last);

                return false;
            }

            var patientid = me.parents('p').data('patientid');

            $.ajax({
                url: '/patientmgr/modifypatientstagejson',
                type: 'get',
                dataType: 'text',
                data: {
                    patientid: patientid,
                    patientstageid: patientstageid
                },
                "success": function (data) {
                    if (data == 'ok') {
                        if (patientstageid == 0) {
                            alert("成功移出阶段");
                        } else {
                            alert("修改成功");
                        }
                    }
                }
            });
        });
    });

    $(function () {
        $("#patient-diagnosis-modal").draggable();//为模态对话框添加拖拽
        $('#patient-diagnosis-modal').on('shown.bs.modal', function () {
            $.ajax({
                'type': 'post',
                'url': '/tagmgr/createcheckboxhtml',
                'data': {
                    'patientid': '<?= $patientid?>',
                    'typestr': 'patientDiagnosis',
                    'name': 'patientDiagnosis'
                },
                'datatype': 'html',
                'success': function (data) {
                    $('#create-checkbox-html').html(data)
                    $('#create-checkbox-html').children('.checkbox').children('br').remove()
                    $("#id_other_btn").removeAttr("checked");
                    $('#other-patient-diagnosis-box').html('')
                }
            })
        })

        $("#id_other_btn").on('click', function () {
            if ($(this).is(':checked')) {
                $("#other-patient-diagnosis-box").html('<input class="other-patient-diagnosis-input" type="text" name="other_patientDiagnosis"  autofocus>');
            } else {
                $("#other-patient-diagnosis-box").html('');
            }
        });

        $("#submit-patient-diagnosis").on('click', function () {
            var patientDiagnosisids = [];
            $('input[name="patientDiagnosis"]').each(function () {
                if ($(this).is(':checked')) {
                    var m = $(this);
                    patientDiagnosisids.push(m.val());
                }
            });

            var patientid = <?=$patientid ?>;

            $.ajax({
                type    :   'post',
                url     :   '/tagrefmgr/addordelbypatientdiagnosis',
                data    :   {
                    'patientDiagnosisids'   :   patientDiagnosisids,
                    'patientid'      :   patientid,
                },
                success: function (data) {
                    var jsonData = JSON.parse(data)
                    $("#patient-diagnosis-str").children('span').html(jsonData.tagnameStr)
                }
            });
        });
    });
</script>