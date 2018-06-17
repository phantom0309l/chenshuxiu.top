<?php
$pagetitle = "医生信息总览";
$sideBarMini = true;
$breadcrumbs = [
    "/doctormgr/list" => "医生列表",
];
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; //填写完整地址
$pageStyle = <<<STYLE
.block-options > li > a {
    opacity: 1;
}
.searchBar {
    padding: 20px 20px 1px;
    background-color: #f9f9f9;
    border: 1px solid #e9e9e9;
}   
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <?php include_once $tpl . "/doctorconfigmgr/_menu.tpl.php"; ?>
    <div class="content-div">
        <section class="col-md-12 remove-padding">
            <div class="col-md-12">
                <div class="searchBar">
                    <btn class="btn btn-primary push-20" onclick="autoConfig(<?= $doctor->id ?>)">一键快捷配置</btn>
                </div>
            </div>
            <!--Doctor-->
            <div class="col-md-6 col-xs-12">
                <div class="block block-bordered">
                    <div class="block-header bg-gray-lighter">
                        <ul class="block-options">
                            <li>
                                <a href="/doctormgr/modify?doctorid=<?= $doctor->id ?>"><i
                                            class="fa fa-edit text-info"></i></a>
                            </li>
                        </ul>
                        <h3 class="block-title">Doctor</h3>
                    </div>
                    <div class="block-content" style="height:150px">
                        <div class="col-sm-8 remove-padding">
                            <p><i class="si si-user gray"></i><span class="push-10-l"><?= $doctor->name ?></span></p>
                            <p><i class="si si-user text-white-op"></i><span class="push-10-l"><?= $doctor->title ?></span>
                            </p>
                            <p><i class="si si-user text-white-op"></i><span
                                        class="push-10-l"><?= $doctor->hospital->name ?></span></p>
                        </div>
                        <div class="col-sm-4">
                            <?php if ($doctorSuperiors) { ?>
                            <p><i class="fa fa-user-md"></i><span class="push-10-l">主管医生</span></p>
                            <p><i class="si si-user text-white-op"></i>
                                <?php foreach ($doctorSuperiors as $doctorSuperior) { ?>
                                    <span class="push-10-r"><a href="/doctorconfigmgr/overview?doctorid=<?=$doctorSuperior->superior_doctorid?>"><?=$doctorSuperior->superior_doctor->name;?></a></span>
                                <?php } ?>
                            </p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--/Doctor-->
            <!--User-->
            <div class="col-md-6 col-xs-12">
                <div class="block block-bordered">
                    <div class="block-header bg-gray-lighter">
                        <ul class="block-options">
                            <li>
                                <a href="/doctormgr/modify?doctorid=<?= $doctor->id ?>#user"><i
                                            class="fa fa-edit text-info"></i></a>
                            </li>
                        </ul>
                        <h3 class="block-title">User</h3>
                    </div>
                    <div class="block-content" style="height:150px">
                        <p><i class="si si-user gray"></i><span class="push-10-l"><?= $doctor->user->username ?></span>
                        </p>
                        <p><i class="si si-key gray"></i><span class="push-10-l"><?= $doctor->user->sasdrowp ?></span>
                        </p>
                    </div>
                </div>
            </div>
            <!--/User-->
            <!--服务备注-->
            <div class="col-md-12 col-xs-12">
                <div class="block block-bordered">
                    <div class="block-header bg-gray-lighter">
                        <ul class="block-options">
                            <li>
                                <a href="/doctormgr/modify?doctorid=<?= $doctor->id ?>#other"><i
                                            class="fa fa-edit text-info"></i></a>
                            </li>
                        </ul>
                        <h3 class="block-title">服务备注</h3>
                    </div>
                    <div class="block-content">
                        <p><?= $doctor->service_remark ?></p>
                    </div>
                </div>
            </div>
            <!--/服务备注-->
            <!--疾病-->
            <div class="col-md-12 col-xs-12">
                <div class="block block-bordered">
                    <div class="block-header bg-gray-lighter">
                        <ul class="block-options">
                            <li>
                                <a href="/doctormgr/modify?doctorid=<?= $doctor->id ?>#disease"><i
                                            class="fa fa-edit text-info"></i></a>
                            </li>
                        </ul>
                        <h3 class="block-title">疾病</h3>
                    </div>
                    <div class="block-content">
                        <?php foreach ($diseases as $disease) { ?>
                            <i class="si si-star text-warning"></i><span
                                    class="push-5-l push-20-r"><?= $disease->name ?></span>
                        <?php } ?>
                        <p></p>
                    </div>
                </div>
            </div>
            <!--/疾病-->
            <!--二维码-->
            <div class="col-md-12 col-xs-12">
                <div class="block block-bordered">
                    <div class="block-header bg-gray-lighter">
                        <ul class="block-options">
                            <li>
                                <a href="/doctorWxShopRefMgr/modify?doctorid=<?= $doctor->id ?>"><i
                                            class="fa fa-edit text-info"></i></a>
                            </li>
                        </ul>
                        <h3 class="block-title">二维码</h3>
                    </div>
                    <div class="block-content">
                        <?php foreach ($doctorWxShopRefs as $doctorWxShopRef) { ?>
                            <div class="pull-left text-center">
                                <p><img style="width:100px;height:100px;" src="<?= $doctorWxShopRef->getQrUrl() ?>"></p>
                                <p class="text-center"><i class="si si-star text-warning"></i><span
                                            class="push-5-l push-20-r"><?= $doctorWxShopRef->wxshop->name ?></span>
                            </div>
                        <?php } ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!--/二维码-->
            <!--综合业务-->
            <div class="col-md-12 col-xs-12">
                <div class="block block-bordered">
                    <div class="block-header bg-gray-lighter">
                        <h3 class="block-title">综合业务</h3>
                    </div>
                    <div class="block-content">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                            <tr>
                                <th class="text-left"><span class="font-w400">疾病</span></th>
                                <th class="text-center"><span class="font-w400 push-10-r">复诊</span><a
                                            href="/revisittktconfigmgr/one?doctorid=<?= $doctor->id ?>"><i
                                                class="fa fa-edit text-info"></i></a></th>
                                <th class="text-center"><span class="font-w400 push-10-r">住院-治疗</span><a
                                            href="/bedtktconfigmgr/one?doctorid=<?= $doctor->id ?>"><i
                                                class="fa fa-edit text-info"></i></a></th>
                                <th class="text-center"><span class="font-w400 push-10-r">住院-检查</span><a
                                            href="/bedtktconfigmgr/one?doctorid=<?= $doctor->id ?>&typestr=checkup"><i
                                                class="fa fa-edit text-info"></i></a></th>
                                <th class="text-center"><span class="font-w400 push-10-r">数据库</span><a
                                            href="/doctorconfigmgr/fitpage?doctorid=<?= $doctor->id ?>&code=patientbaseinfo"><i
                                                class="fa fa-edit text-info"></i></a></th>
                            </tr>
                            <?php foreach ($diseasesBiz as $key => $a) { ?>
                                <tr>
                                    <td><?= $key ?></td>
                                    <td class="text-center"><?php if ($a[0] == 1) { ?><i
                                                class="si si-check text-success"> </i><?php } ?></td>
                                    <td class="text-center"><?php if ($a[1] == 1) { ?><i
                                                class="si si-check text-success"> </i><?php } ?></td>
                                    <td class="text-center"><?php if ($a[2] == 1) { ?><i
                                                class="si si-check text-success"> </i><?php } ?></td>
                                    <td class="text-center"><?php if ($a[3] == 1) { ?> <i
                                                class="si si-check text-success"> </i><?php } else { ?>默认配置<?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--/综合业务-->
            <!--门诊-->
            <div class="col-md-12 col-xs-12">
                <div class="block block-bordered">
                    <div class="block-header bg-gray-lighter">
                        <ul class="block-options">
                            <li>
                                <a href="/scheduletplmgr/listofdoctor?doctorid=<?= $doctor->id ?>"><i
                                            class="fa fa-edit text-info"></i></a>
                            </li>
                        </ul>
                        <h3 class="block-title">门诊</h3>
                    </div>
                    <div class="block-content">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>星期</th>
                                <th>上午</th>
                                <th>下午</th>
                                <th>夜间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($scheduletplTable as $a) { ?>
                                <tr>
                                    <td><?= $a['weekday']; ?></td>
                                    <td><?= $a['am']; ?></td>
                                    <td><?= $a['pm']; ?></td>
                                    <td><?= $a['night']; ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--/门诊-->
        </section>
    </div>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
function autoConfig(doctorid) {
    if (confirm('确定进行快捷配置？（若已存在配置将被覆盖）')) {
        $.ajax({
            type: "post",
            url: "/doctorconfigmgr/ajaxautoconfig",
            data: {
                doctorid: doctorid,
            },
            dataType: "json",
            success: function (d) {
                console.log(d);
                if (d.errno == 0) {
                    alert('快捷配置成功');
                } else {
                    alert('快捷配置失败');
                }
            }
        });
    }
}
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
