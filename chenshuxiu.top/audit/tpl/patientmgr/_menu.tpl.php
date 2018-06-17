<style>
    .fc-breadcrumb {
        position: relative;
        z-index: 2;
    }

    .content-div {
        min-height: 500px;
        overflow-x: hidden;
    }

    .patient-menu {
        width: 150px;
        float: left;
        background-color: #f1f1f1;
        max-height: 600px;
        margin-top: -10px;
    }

    .patient-menu:before {
        content: "";
        display: block;
        width: 150px;
        position: fixed;
        bottom: 0;
        top: 0;
        z-index: 1;
        background-color: #f1f1f1;
        border: 1px solid #ccc;
        border-width: 0 0 0 1px;
        margin-top: 50px;
    }

    ul.patient-menu-ul {
        padding: 10px 20px;
        z-index: 2;
        position: relative;

    }

    ul.patient-menu-ul > li > ul {
        padding-left: 10px;

    }

    ul.patient-menu-ul li {
        line-height: 2;
    }

    ul.patient-menu-ul a {
        color: #48576a;
    }

    ul.patient-menu-ul li.active a {
        color: #20a0ff;
        background: none;
    }

    p.patient-info {
        padding: 10px 20px;
        position: relative;
        z-index: 2;
        margin-bottom: 0;
        border-bottom: 1px solid #ddd;
    }
</style>
<section class="patient-menu">
    <p class="patient-info"><i class="si si-user"></i> <span class="push-10-l"><?= $patient->name ?></span></p>
    <ul class="list-unstyled patient-menu-ul">
        <li <?php if ($action == 'patientmgr' && $method == 'one') { ?> class="active"<?php } ?>>
            <a href="/patientmgr/one?patientid=<?= $patient->id ?>">总览</a>
        </li>
        <li <?php if ($action == 'patientmgr' && $method == 'modify') { ?> class="active"<?php } ?>>
            <a href="/patientmgr/modify?patientid=<?= $patient->id ?>">基本信息</a>
        </li>
        <li <?php if ($action == 'patientmgr' && $method == 'pipeschart') { ?> class="active"<?php } ?>>
            <a href="/patientmgr/pipeschart?patientid=<?= $patient->id ?>">流类型分布</a>
        </li>
        <li <?php if ($action == 'optaskmgr' && $method == 'listofpatient') { ?> class="active"<?php } ?>>
            <a href="/optaskmgr/listofpatient?patientid=<?= $patient->id ?>">任务</a>
        </li>
        <li <?php if (($action == 'patientmgr' && $method == "drugdetail") || ($action == 'patientmedicinetargetmgr' && $method == "detailofpatient")) { ?> class="active"<?php } ?>>
            <?php if(1 == $patient->diseaseid){ ?>
                <a href="/patientmgr/drugdetail?patientid=<?= $patient->id ?>">用药</a>
            <?php } else { ?>
                <a href="/patientmedicinetargetmgr/detailofpatient?patientid=<?= $patient->id ?>">用药</a>
            <?php } ?>
        </li>
        <li <?php if ($action == 'shopordermgr' && $method = 'listofpatient'){ ?>class="active"<?php } ?>>
            <a href="/shopordermgr/listofpatient?patientid=<?= $patient->id ?>">订单</a>
        </li>
        <li <?php if ($action == 'papermgr' && $method = 'listofpatient'){ ?>class="active"<?php } ?>>
            <a href="/papermgr/listofpatient?patientid=<?= $patient->id ?>">量表</a>
        </li>
    </ul>
</section>
