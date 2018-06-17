<input type="hidden" value="<?= $patient->id ?>" id="patientid"/>
<input type="hidden" value="<?= $patient->name ?>" id="patientName"/>
<input type="hidden" id="doctor_name" value="<?= $patient->doctor->name ?>"/>
<input type="hidden" id="disease_name" value="<?= $patient->disease->name ?>"/>
<div class="tab block">
    <ul class="nav nav-tabs onepatient-tab">
        <li class="active"><a href="javascript:">基本信息</a></li>
        <li><a href="javascript:">服药</a></li>
        <li><a href="javascript:">工具</a></li>
        <li><a href="javascript:">任务</a></li>
        <li><a href="javascript:">运营备注</a></li>
        <?php if ($patient->diseaseid != 1) { ?>
            <li><a href="javascript:">住院</a></li>
            <li><a href="javascript:">复诊</a></li>
        <?php } ?>
        <li><a href="javascript:">量表</a></li>
    </ul>
    <div class="block-content tab-content remove-padding-t" style="overflow:inherit">
        <div class="tab-pane active">
            <?php
            include dirname(__FILE__) . "/_patientBase.php";
            ?>
        </div>
        <div class="tab-pane ">
            <?php
                if ($patient->diseaseid == 1) {
                    include_once $tpl . "/_patient_medicine.php";
                } else {
                    include_once $tpl . "/_patient_notadhd_medicine.php";
                }
            ?>
            <?php if ($patient->diseaseid == 1) { include $tpl . "/_set_medicine_break_date.php"; } ?>
        </div>
        <div class="tab-pane">
            <?php
            include dirname(__FILE__) . "/_tool.php";
            ?>
        </div>
        <div class="tab-pane ">
            <?php
            include dirname(__FILE__) . "/_optask.php";
            ?>
        </div>
        <div class="tab-pane ">
            <?php
            include dirname(__FILE__) . "/_auditorremark.php";
            ?>
        </div>
        <?php if ($patient->diseaseid != 1) { ?>
            <div class="tab-pane ">
                <?php
                include dirname(__FILE__) . "/_optaskbedtkt.php";
                ?>
            </div>
            <div class="tab-pane ">
                <?php
                include dirname(__FILE__) . "/_optaskrevisittkt.php";
                ?>
            </div>
        <?php } ?>
        <div class="tab-pane ">
            <?php
            include dirname(__FILE__) . "/_optaskpaper.php";
            ?>
        </div>
    </div>
