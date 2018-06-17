<?php
$pagetitle = "新备注列表 PatientRecord 不良反应";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/v5/page/audit/patientrecordmgr/list.js?v=20180206',
]; //填写完整地址
$pageStyle = <<<STYLE
#main-container {
    background: #f5f5f5 !important;
}
.js-table-sections-header.open > tr {
    background-color: #f7f7f7;
}
.table p {
    margin-bottom: 0px;
}
.text-gray-dark {
    color: #787878;
}

@media screen and (max-width: 2200px) {
    .table .content {
        width: 550px;    
    }
}

@media screen and (max-width: 1919px) {
    .table .content {
        width: 380px;    
    }
}

@media screen and (max-width: 1439px) {
    .table .content {
        width: 305px;
    }
}

@media screen and (max-width: 1365px) {
    .table .content {
        width: 180px;
    }
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12 content-left">
        <div class="block">
            <div class="block-content">
                <div class="table-responsive">
                    <table class="js-table-sections table table-hover">
                        <thead>
                        <tr>
                            <th style="width:30px;"></th>
                            <th>创建</th>
                            <th>最后修改</th>
                            <th class="tc" style="width: 100px;">日期</th>
                            <th>名称</th>
                            <th style="width: 55px" class="tc">程度</th>
                            <th>关联化疗</th>
                            <th>备注</th>
                            <th class="tc" style="width: 55px;">跟进</th>
                            <th class="tc" style="width: 85px;">操作</th>
                        </tr>
                        </thead>
                        <?php foreach ($patientrecords as $a) {
                            $data_arr = [];
                            $data_arr = $a->loadJsonContent();
                            $children = $a->getChildren();
                            ?>
                            <tbody class="js-table-sections-header">
                            <tr>
                                <td class="text-center"><i class="fa fa-angle-right"></i></td>
                                <td>
                                    <p><?= $a->create_auditor->name ?></p>
                                    <p><?= $a->createtime ?></p>
                                </td>
                                <td>
                                    <p><?= $a->modify_auditor->name ?></p>
                                    <p><?= $a->modify_auditor ? $a->updatetime : '' ?></p>
                                </td>
                                <td class="tc"><?= $a->thedate ?></td>
                                <td><span class="label label-success"><?= $data_arr['name'] ?></span></td>
                                <td class="tc"><?= $data_arr['degree'] ?></td>
                                <td>
                                    <?php
                                    $patientrecord_chemo = PatientRecord::getById($data_arr['relate_chemo']);
                                    $desc = "尚未关联";
                                    if ($patientrecord_chemo instanceof PatientRecord) {
                                        $desc = PatientRecordCancer::getShortDesc($patientrecord_chemo);
                                    }
                                    ?>
                                    <?= $desc ?>
                                </td>
                                <td class="content"><?= $a->content ?></td>
                                <td class="tc">
                                    <span class="text-info"><?= count($children) ?></span>
                                </td>
                                <td class="tc">
                                    <div class="btn-group">
                                        <a class="btn btn-xs btn-default"
                                           href="/patientrecordmgr/addchild?parent_patientrecordid=<?= $a->id ?>"
                                           data-toggle="tooltip" title="" data-original-title="添加跟进"><i
                                                    class="fa fa-plus"></i></a>
                                        <a class="btn btn-xs btn-default"
                                           target="_blank"
                                           href="/patientrecordmgr/modify?patientrecordid=<?= $a->id ?>"
                                           data-toggle="tooltip" title="" data-original-title="修改"><i
                                                    class="fa fa-pencil"></i></a>
                                        <a class="btn btn-xs btn-default J_delete" data-patientrecordid="<?= $a->id ?>"
                                           data-toggle="tooltip" title="" data-original-title="删除"
                                           data-href="/patientrecordmgr/deletejson?patientrecordid=<?= $a->id ?>"><i
                                                    class="fa fa-times"></i></a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                            <tbody class="bg-gray-lighter">
                            <?php foreach ($children as $child) {
                                $child_data_arr = [];
                                $child_data_arr = $child->loadJsonContent();
                                ?>
                                <tr>
                                    <td style="width:30px;">-</td>
                                    <td class="text-gray-dark">
                                        <p><?= $child->create_auditor->name ?></p>
                                        <p><?= $child->createtime ?></p>
                                    </td>
                                    <td class="text-gray-dark">
                                        <p><?= $child->modify_auditor->name ?></p>
                                        <p><?= $child->modify_auditor ? $child->updatetime : '' ?></p>
                                    </td>
                                    <td class="text-gray-dark tc"><?= $child->thedate ?></td>
                                    <td class="text-gray-dark"></td>
                                    <td class="text-gray-dark tc"><?= $child_data_arr['degree'] ?></td>
                                    <td class="text-gray-dark">
                                        <?php
                                        $patientrecord_chemo = PatientRecord::getById($child_data_arr['relate_chemo']);
                                        $desc = "尚未关联";
                                        if ($patientrecord_chemo instanceof PatientRecord) {
                                            $desc = PatientRecordCancer::getShortDesc($patientrecord_chemo);
                                        }
                                        ?>
                                        <?= $desc ?>
                                    </td>
                                    <td class="text-gray-dark content"><?= $child->content ?></td>
                                    <td class="text-gray-dark"></td>
                                    <td class="text-gray-dark tc">
                                        <div class="btn-group">
                                            <a class="btn btn-xs btn-default"
                                               target="_blank"
                                               href="/patientrecordmgr/modifychild?patientrecordid=<?= $child->id ?>"
                                               data-toggle="tooltip" title="" data-original-title="修改"><i
                                                        class="fa fa-pencil"></i></a>
                                            <a class="btn btn-xs btn-default J_delete"
                                               data-patientrecordid="<?= $child->id ?>"
                                               data-toggle="tooltip" title="" data-original-title="删除"
                                               data-href="/patientrecordmgr/deletejson?patientrecordid=<?= $child->id ?>"><i
                                                        class="fa fa-times"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="clear"></div>
<script>
    $(function () {
        App.initHelper('table-tools');
    })
</script>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
