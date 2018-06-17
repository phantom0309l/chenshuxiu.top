<!-- 一排按钮 begin-->
<div id="goPatientBase" name="goPatientBase" class="colorBox" style="line-height: 150%">
    <?php
    $patientid = $patient->id;
    ?>
    <div>
        <a href="/wxpicmsgmgr/list?patientid=<?= $patientid ?>" id="showcase" target="_blank" class="btn-default btn">病历图</a>
        <a href="/papermgr/list?patientid=<?= $patientid ?>" id="showAllScales" target="_blank" class="btn btn-default">量表列表</a>
        <a href="/checkupmgr/list?patientid=<?= $patientid ?>" id="showAllScales" target="_blank" class="btn btn-default">检查报告列表</a>
        <a href="/xanswersheetmgr/list?patientid=<?= $patientid ?>" id="showAllScales" target="_blank" class="btn btn-default">答卷列表(无用)</a>
        <a href="/lessonuserrefmgr/list?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default">课程/观察</a>
        <a href="/patientmedicinesheetmgr/list?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default">患者核对用药</a>
        <?php if ($patient->diseaseid != 1) { ?>
            <a href="/pmsideeffectmgr/add?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default">添加药物副反应任务</a>
        <?php } ?>
        <a href="/pmsideeffectmgr/list?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default">[列表]</a>
        <a href="/revisitrecordmgr/list?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default">患者门诊历史</a>
        <a href="/optaskmgr/listnew?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default">任务列表</a>
        <a href="/patientmgr/index?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default">控制台(beta)</a>
        <a href="/patientremarkmgr/list?patientid=<?=$patientid?>" target="_blank" class="btn btn-default">PatientRemark</a>
        <a href="/aepcmgr/list?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default">添加AEPC >></a>
    </div>
    <div class="mt10">
        <?php
            $pcard = $patient->getMasterPcard();
        ?>
        <?php if ($pcard->has_update == 1) { ?>
            <button class="btn btn-primary" id=changeNew data-value=0>去new!</button>
        <?php } else { ?>
            <button class="btn btn-default" id=changeNew data-value=1>加new!</button>
        <?php } ?>
        <button class="btn btn-primary" id="changeDoubt" data-toggle="modal" data-target="#doubtBox">患者类型切换</button>
    </div>
    <div class="mt10">
        <button class="patientStatus-btn btn btn-primary">状态更变</button>
        <span class="red" style="margin-left: 20px;">[<?=$patient->getStatusStr()?>]</span>
        <?php if($mydisease->id == 1){ ?>
            <span class="red f16 ml10">
                开药门诊：<?= $patient->canIntoMenzhen() ? "开启" : "未开启" ?>
            </span>
        <? } ?>
        <div class="patientStatus-box none">
            <p class="bg-warning mt10 p5"><?= $patient->auditremark ?></p>
            <textarea class="form-control patientStatus-auditremark" rows="3" data-patientid="<?=$patient->id?>"></textarea>
            <div>
                标记并添加更变历史：
                <button class="patientStatus-setClose btn btn-danger">拒绝/删除</button>
                <button class="patientStatus-setDead btn btn-danger">死亡</button>
            </div>
        </div>
    </div>
</div>
<?php include_once($tpl . "/_doubtbox.php"); ?>
<script>
    $(function () {
        $("#sendmedicinemsg").on("click", function () {
            var patientid = $(this).data("patientid");

            $.ajax({
                "type": "get",
                "data": {
                    patientid: patientid
                },
                "dataType": "text",
                "url": "/patientmedicinesheetmgr/sendmsgJson",
                "success": function (data) {
                    if (data == 'success') {
                        alert("消息已发送给患者");
                    }
                }
            });
        });
        $(".patientStatus-setClose").on("click", function () {
            if (confirm("确认删除该患者？")) {
                var me = $(this);
                $.ajax({
                    "type" : "get",
                    "data" : {
                        "patientid" : $(".patientStatus-auditremark").data('patientid'),
                        "auditremark" : $(".patientStatus-auditremark").val()
                    },
                    "url" : "/patientmgr/offlineJson",
                    "success" : function(data) {
                        if (data == 'ok') {
                            me.addClass("btn-default").removeClass("btn-danger");
                        }
                    }
                });
            }
        });
        $(".patientStatus-setDead").on("click", function () {
            if (confirm("确认删除该患者？")) {
                var me = $(this);
                $.ajax({
                    "type" : "get",
                    "data" : {
                        "patientid" : $(".patientStatus-auditremark").data('patientid'),
                        "auditremark" : $(".patientStatus-auditremark").val()
                    },
                    "url" : "/patientmgr/deadJson",
                    "success" : function(data) {
                        if (data == 'ok') {
                            me.addClass("btn-default").removeClass("btn-danger");
                        }
                    }
                });
            }
        });
        $(".patientStatus-btn").on("click", function () {
            $(".patientStatus-box").toggle();
        });

    });
</script>
<!-- 一排按钮 end-->
