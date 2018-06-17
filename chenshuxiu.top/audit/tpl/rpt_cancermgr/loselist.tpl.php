<?php
$pagetitle = "肿瘤失访患者列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .form-group label {
    font-weight: 500;
    width: 9%;
    text-align: left;
}
.padding-left0{
    padding-left: 0px;
}
td p:last-child {
margin-bottom: 0;
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="content bg-white border-b">
            <div class="row items-push text-uppercase">
                <div class="col-xs-6 col-sm-3">
                    <div class="font-w700 text-gray-darker animated fadeIn">患者数（失访）</div>
                    <div class="text-muted animated fadeIn">
                        <small><i class="si si-calculator"></i> 被标记为失访的患者</small>
                    </div>
                    <a class="h2 font-w300 text-primary animated flipInX"
                       href="javascript:void(0);"><?= $lose_cnt ?></a>
                </div>
                <div class="col-xs-6 col-sm-4">
                    <div class="font-w700 text-gray-darker animated fadeIn">失访率</div>
                    <div class="text-muted animated fadeIn">
                        <small><i class="si si-calculator"></i> 失访患者数/已关注患者数,四舍五入保留两位小数</small>
                    </div>
                    <a class="h2 font-w300 text-success animated flipInX" href="javascript:void(0);">
                        <?php if ($subscribe_patient_cnt > 0) {
                            echo round($lose_cnt / $subscribe_patient_cnt * 100, 2) . "%";
                        } else {
                            echo "0%";
                        } ?>
                    </a>
                    <small> = <?= $lose_cnt ?> / <?= $subscribe_patient_cnt ?></small>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
                <thead>
                <tr>
                    <td>#</td>
                    <td>ID</td>
                    <td>患者</td>
                    <td>医生</td>
                    <td>疾病</td>
                    <td>
                        <div class="tl">失访记录</div>
                    </td>
                    <td>未完成任务数</td>
                    <td>操作</td>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($patients as $key => $patient) {
                    ?>
                    <tr>
                        <td><?= 1 * $pagenum + $key + 1 ?></td>
                        <td><?= $patient->id ?></td>
                        <td><?= $patient->name ?></td>
                        <td><?= $patient->doctor->name ?></td>
                        <td><?= $patient->disease->name ?></td>
                        <td>
                            <?php
                            $patientrecords = PatientRecordDao::getParentListByPatientidCodeType($patient->id, 'common', 'lose');
                            if (empty($patientrecords)) { ?>
                                <p class="tl"><span class="text-muted">系统标注失访</span></p>
                                <?php
                            }
                            foreach ($patientrecords as $patientrecord) {
                                $data = json_decode($patientrecord->json_content, true);
                                ?>
                                <p class="tl">
                                    <span class="text-muted"><?= $patientrecord->createtime ?></span>
                                    <span class="label label-success"><?= $data ? "{$data['reason']}" : '' ?></span>
                                    <span class="text-primary"><?= $patientrecord->content ?></span>
                                    <span class="text-muted"><?= $patientrecord->create_auditor->name ?></span>
                                </p>
                            <?php } ?>

                        </td>
                        <td>
                            <?php
                            $sql = "SELECT count(*) FROM optasks WHERE patientid = :patientid AND status IN (0, 2) ";
                            $bind = [':patientid' => $patient->id];
                            $cnt = Dao::queryValue($sql, $bind);
                            if ($cnt > 0) { ?>
                                <span class="text-primary"><?= $cnt; ?></span>
                            <?php } ?>
                        </td>
                        <td>
                            <a target="_blank" class="label label-primary"
                               href="/optaskmgr/listnew?patient_name=<?= $patient->name ?>&diseaseid=<?= $patient->diseaseid ?>&status_str=all">
                                查看
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan=100 class="pagelink">
                        <?php include $dtpl . "/pagelink.ctr.php"; ?>
                    </td>
                </tr>
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
