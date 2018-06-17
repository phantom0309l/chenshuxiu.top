<div class="tab block">
    <ul class="nav nav-tabs onepatient-tab">
        <li class="active"><a href="javascript:">历史任务</a></li>
        <li><a href="javascript:">运营备注</a></li>
        <?php if(1 == $patient->diseaseid){ ?>
            <li><a target="_blank" href="/patientmgr/drugdetail?patientid=<?=$patient->id?>">服药</a></li>
            <li><a target="_blank" href="/aepcmgr/list?patientid=<?= $patient->id ?>">添加AEPC</a></li>
        <?php } ?>
    </ul>
    <div class="block-content tab-content remove-padding-t" style="overflow:inherit">
        <div class="tab-pane active">
            <?php
            include dirname(__FILE__) . "/_optasktpl_btn.php";
            ?>
        </div>
        <div class="tab-pane ">
            <?php
            include dirname(__FILE__) . "/_auditorremark.php";
            ?>
        </div>
    </div>
</div>
