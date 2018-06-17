<?php
$pagetitle = "运营工作管理列表";
$cssFiles = [
    $img_uri . "/v3/scale.css",
    $img_uri . "/v5/page/audit/optaskcheckmgr/listofauditor/listofauditor.css?v=2018060901",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.js?v=20170829',
    $img_uri . "/v5/plugin/echarts/echarts.js",
    $img_uri . '/v5/page/audit/optaskmgr/list/pipe.js?v=2018050901',
    $img_uri . '/v5/common/wxvoicemsg_content_modify.js?v=20171208',
    $img_uri . '/v5/common/dealwithtpl.js?v=2018050401',
    $img_uri . "/v5/common/pipelevelfix.js?v=20171222",
    $img_uri . "/v5/common/setMedicineBreakDate.js?v=20171019",
    $img_uri . '/v5/page/audit/optaskmgr/list/listnew.js?v=2018060901',
    $img_uri . '/v5/page/audit/optaskmgr/list/changelevel.js?v=20171226',
    $img_uri . '/v5/page/audit/optaskmgr/list/pgroup.js?v=20171206',
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
    $img_uri . '/static/js/vendor/jquery-ui.autocomplete.min.js?v=20180208',
    $img_uri . '/v5/page/audit/optaskcheckmgr/listofauditor/listofauditor.js?v=2018060901'
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>


<div class="col-md-12">
    <!--  顶部auditor列表  -->
    <section>
        <ul class="nav nav-tabs onepatient-tab topMenu-list" style="color:#5c90d2;">
            <input type="hidden" id="hide_auditor_id" value="<?= $auditor_id?>">
            <?php foreach($auditorGroupRefs as $auditorGroupRef) {?>
                <li class="auditor-item <?= $auditorGroupRef->auditor->id == $auditor_id? 'active-item' :  ''?>" data-auditor-id="<?= $auditorGroupRef->auditor->id?>"><i class="fa fa-user pr10"></i><?= $auditorGroupRef->auditor->name?></li>
            <?php }?>
        </ul>
    </section>

    <!--  工作效率统计  -->
    <section class="col-md-12 content-box clear  pl40 pr40">
        <?php include_once $tpl . "/optaskcheckmgr/_workefficiency.php"; ?>
    </section>

    <!--  工作质量周统计  -->
    <section class="col-md-12 content-boxe mt100  pl40 pr40">
        <?php include_once $tpl . "/optaskcheckmgr/_workquality.php"; ?>
    </section>

</div>
<div class="clear"></div>

<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
