<?php
$pagetitle = "新备注列表 PatientRecord 化疗方案";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/v5/page/audit/patientrecordmgr/list.js?v=20170808',
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
        <section class="col-md-12 content-left">
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <td>创建日期</td>
                        <td>创建人</td>
                        <td>最后修改日期</td>
                        <td>最后修改人</td>
                        <td>分期日期</td>
                        <td>分期类型</td>
                        <td>T</td>
                        <td>N</td>
                        <td>M</td>
                        <td>分期</td>
                        <td>备注</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
        		<?php foreach ($patientrecords as $a ){
                    $data_arr = [];
                    $data_arr = $a->loadJsonContent();
                    ?>
            		<tr>
                        <td><?= $a->createtime ?></td>
                        <td><?= $a->create_auditor->name ?></td>
                        <td><?= $a->updatetime ?></td>
                        <td><?= $a->modify_auditor->name ?></td>
                        <td><?= $data_arr['thedate'] ?></td>
                        <td><?= $data_arr['type'] ?></td>
                        <td><?= $data_arr['T'] ?></td>
                        <td><?= $data_arr['N'] ?></td>
                        <td><?= $data_arr['M'] ?></td>
                        <td><?= $data_arr['stage'] ?></td>
                        <td><?= $a->content ?></td>
                        <td>
                            <a class="btn btn-default btn-xs" href="/patientrecordmgr/modify?patientrecordid=<?= $a->id ?>">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a class="btn btn-danger btn-xs a-delete" href="javascript:" data-href="/patientrecordmgr/deletejson?patientrecordid=<?= $a->id ?>">
                                <i class="fa fa-remove"></i>
                            </a>
                        </td>
                    </tr>
          		<?php } ?>
			</tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
