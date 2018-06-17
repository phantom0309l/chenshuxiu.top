<?php
$pagetitle = "处方详情 Prescription";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <h5>患者信息</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <td>病历号(patientid)</td>
                    <td>姓名</td>
                    <td>性别</td>
                    <td>生日(年龄)</td>
                    <td>费别</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $prescription->patientid  ?></td>
                    <td><?= $prescription->patient_name ?></td>
                    <td><?= $prescription->getPatient_sexDesc() ?></td>
                    <td><?= $prescription->patient_birthday ?> , <?= $prescription->getPatientAgeStr() ?> 岁</td>
                    <td><?= $prescription->fee_type ?></td>
                </tr>
            </tbody>
        </table>
        </div>
        <h5>处方信息</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
            <tbody>
                <tr>
                    <th width="120">处方ID</th>
                    <td><?= $prescription->id ?></td>
                    <td>
                        <a href="/shopordermgr/one?shoporderid=<?= $prescription->shoporderid ?>">申请单(<?= $prescription->shoporderid ?>)</a>
                    </td>
                </tr>
                <tr>
                    <th>生成时间</th>
                    <td><?= $prescription->createtime ?></td>
                    <td></td>
                </tr>
                <tr>
                    <th>处方类型</th>
                    <td><?= $prescription->getTypeDesc() ?></td>
                    <td></td>
                </tr>
                <tr>
                    <th>状态</th>
                    <td><?= $prescription->getStatusDesc() ?></td>
                    <td></td>
                </tr>
                <tr>
                    <th>医院</th>
                    <td><?= $prescription->hospital_name ?></td>
                </tr>
                <tr>
                    <th>科室</th>
                    <td><?= $prescription->department_name ?></td>
                    <td></td>
                </tr>
                <tr>
                    <th>医师</th>
                    <td><?= $prescription->yishi->name ?></td>
                    <td><?= $prescription->time_confirm ?></td>
                </tr>
                <tr>
                    <th>复核药师</th>
                    <td><?= $prescription->yaoshi_audit->name ?></td>
                    <td><?= $prescription->time_audit ?></td>
                </tr>
                <tr>
                    <th>配药师</th>
                    <td><?= $prescription->yaoshi_send->name ?></td>
                    <td><?= $prescription->time_send ?></td>
                </tr>
                <tr>
                    <th>医师备注</th>
                    <td><?= $prescription->yishi_remark?></td>
                    <td>yishi_remark</td>
                </tr>
                <tr>
                    <th>医生备注</th>
                    <td><?= $prescription->doctor_remark?></td>
                    <td></td>
                </tr>
                <tr>
                    <th>审核备注</th>
                    <td><?= $prescription->audit_remark?></td>
                    <td>audit_remark</td>
                </tr>
                <tr>
                    <th>药方内容汇总</th>
                    <td><?= $prescription->content?></td>
                    <td>content</td>
                </tr>
                <tr>
                    <th>签名摘要</th>
                    <td><?= $prescription->md5str?></td>
                    <td>md5str</td>
                </tr>
                <tr>
                    <th>运营备注</th>
                    <td><?= $prescription->remark?></td>
                    <td>remark</td>
                </tr>
            </tbody>
        </table>
        </div>
        <h5>处方明细</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <td>药品名</td>
                    <td>规格</td>
                    <td>用量</td>
                    <td>方法</td>
                    <td>频次</td>
                    <td>数量</td>
                    <td>单位</td>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($prescriptionitems as $a) {
                ?>
                <tr>
                    <td><?= $a->medicine_title ?></td>
                    <td><?= $a->size_pack ?></td>
                    <td><?= $a->drug_dose ?></td>
                    <td><?= $a->drug_way ?></td>
                    <td><?= $a->drug_frequency ?></td>
                    <td><?= $a->cnt ?></td>
                    <td><?= $a->pack_unit ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
