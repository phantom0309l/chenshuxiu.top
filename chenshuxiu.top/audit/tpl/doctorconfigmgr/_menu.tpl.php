<style>
    .fc-breadcrumb {
        position: relative;
        z-index: 2;
    }

    .content-div {
        min-height: 500px;
        overflow-x: hidden;
    }

    .doctor-menu {
        width: 150px;
        float: left;
        background-color: #f1f1f1;
        max-height: 600px;
        margin-top: -10px;
    }

    .doctor-menu:before {
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

    ul.doctor-menu-ul {
        padding: 10px 20px;
        z-index: 2;
        position: relative;

    }

    ul.doctor-menu-ul > li > ul {
        padding-left: 10px;

    }

    ul.doctor-menu-ul li {
        line-height: 2;
    }

    ul.doctor-menu-ul a {
        color: #48576a;
    }

    ul.doctor-menu-ul li.active a {
        color: #20a0ff;
        background: none;
    }

    p.doctor-info {
        padding: 10px 20px;
        position: relative;
        z-index: 2;
        margin-bottom: 0;
        border-bottom: 1px solid #ddd;
    }
</style>
<section class="doctor-menu">
    <p class="doctor-info"><i class="si si-user"></i> <span class="push-10-l"><?= $doctor->name ?></span></p>
    <ul class="list-unstyled doctor-menu-ul">
        <li <?php if ($action == 'doctorconfigmgr' && $method == 'overview') { ?> class="active"<?php } ?>>
            <a href="/doctorconfigmgr/overview?doctorid=<?= $doctor->id ?>">总览</a>
        </li>
        <li <?php if ($action == 'doctormgr' && $method == 'modify') { ?> class="active"<?php } ?>>
            <a href="/doctormgr/modify?doctorid=<?= $doctor->id ?>">基本信息</a>
        </li>
        <li <?php if ($action == 'doctormgr' && $method == 'treatmentnotice') { ?> class="active"<?php } ?>>
            <a href="/doctormgr/treatmentnotice?doctorid=<?= $doctor->id ?>">就诊须知</a>
        </li>
        <li <?php if ($action == 'doctorwxshoprefmgr' && $method == 'modify') { ?> class="active"<?php } ?>>
            <a href="/doctorWxShopRefMgr/modify?doctorid=<?= $doctor->id ?>">二维码</a>
        </li>
        <li <?php if ($action == 'doctorconfigmgr' && ($method == 'fitpage' || $method == 'addfitpage') && $fitpagetpl->code == "baodao") { ?> class="active"<?php } ?>>
            <a href="/doctorconfigmgr/fitpage?doctorid=<?= $doctor->id ?>">报到</a>
        </li>
        <li>
            <a href="javascript:" class="doctor-menu-folder font-w600"><i
                        class="fa fa-folder<?php if ($action == 'scheduletplmgr' || $action == 'schedulemgr') { ?>-open<?php } ?>-o"></i>
                门诊</a>
            <ul class="list-unstyled <?php if ($action != 'scheduletplmgr' && $action != 'schedulemgr') { ?>collapse<?php } ?>">
                <li <?php if ($action == 'scheduletplmgr' && $method == 'listofdoctor') { ?> class="active"<?php } ?>>
                    <a href="/scheduletplmgr/listofdoctor?doctorid=<?= $doctor->id ?>">门诊表</a>
                </li>
                <li <?php if ($action == 'schedulemgr' && $method = 'listofdoctor'){ ?>class="active"<?php } ?>>
                    <a href="/schedulemgr/list?doctorid=<?= $doctor->id ?>">门诊实例</a>
                </li>
            </ul>
        </li>
        <li <?php if ($action == 'revisittktconfigmgr' && $method = 'one'){ ?>class="active"<?php } ?>>
            <a href="/revisittktconfigmgr/one?doctorid=<?= $doctor->id ?>">复诊</a>
        </li>
        <li <?php if ($action == 'bedtktconfigmgr'){ ?>class="active"<?php } ?>>
            <a href="/bedtktconfigmgr/one?doctorid=<?= $doctor->id ?>">住院</a>
        </li>
        <li>
            <?php
            $isDbOpen = $action == 'doctorconfigmgr' && ($method == 'fitpage' || $method == 'addfitpage') && ($fitpagetpl->code == "patientbaseinfo" || $fitpagetpl->code == "patientpcard" || $fitpagetpl->code == "diseasehistory") || $action == 'checkuptplmgr' && ($method == 'listofdoctor' || $method == 'addofdoctor' || $method == 'modifyofdoctor') || $action == 'checkuptplmenumgr' && ($method == 'listofdoctor' || $method == 'addofdoctor' || $method == 'modifyofdoctor');
            ?>
            <a href="javascript:" class="doctor-menu-folder font-w600"><i
                        class="fa fa-folder<?php if ($isDbOpen) { ?>-open<?php } ?>-o"></i> 数据库</a>
            <ul class="list-unstyled <?php if (!$isDbOpen) { ?>collapse<?php } ?>">
                <li <?php if ($action == 'doctorconfigmgr' && ($method == 'fitpage' || $method == 'addfitpage') && ($fitpagetpl->code == "patientbaseinfo" || $fitpagetpl->code == "patientpcard" || $fitpagetpl->code == "diseasehistory")) { ?> class="active"<?php } ?>>
                    <a href="/doctorconfigmgr/fitpage?doctorid=<?= $doctor->id ?>&code=patientbaseinfo">基本信息</a>
                </li>
                <li <?php if ($action == 'checkuptplmgr' && $method = 'listofdoctor') { ?> class="active"<?php } ?>>
                    <a href="/checkuptplmgr/listofdoctor?doctorid=<?= $doctor->id ?>">检查报告</a>
                </li>
                <li <?php if ($action == 'checkuptplmenumgr' && $method = 'listofdoctor') { ?> class="active"<?php } ?>>
                    <a href="/checkuptplmenumgr/listofdoctor?doctorid=<?= $doctor->id ?>">菜单</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:" class="doctor-menu-folder font-w600"><i
                        class="fa fa-folder<?php if ($action == 'diseasepapertplrefmgr') { ?>-open<?php } ?>-o"></i> 量表</a>
            <ul class="list-unstyled <?php if ($action != 'diseasepapertplrefmgr' && $action != 'diseasepapertplrefmgr') { ?>collapse<?php } ?>">
                <li <?php if ($action == 'diseasepapertplrefmgr' && $method = 'listofdoctor'){ ?>class="active"<?php } ?>>
                    <a href="/diseasepapertplrefmgr/listofdoctor?doctorid=<?= $doctor->id ?>">量表</a>
                </li>
            </ul>
        </li>
    </ul>
</section>
<script>
    $(function () {
        $(document).on('click', '.doctor-menu-folder', function () {
            var ul = $(this).siblings('ul');
            if (ul.is(':hidden')) {
                ul.show();
            } else {
                ul.hide();
            }
            var el = $(this).find('i');
            if (el.attr("class") == "fa fa-folder-o") {
                el.attr("class", "fa fa-folder-open-o");
            } else {
                el.attr("class", "fa fa-folder-o");
            }
        });
    });
</script>
