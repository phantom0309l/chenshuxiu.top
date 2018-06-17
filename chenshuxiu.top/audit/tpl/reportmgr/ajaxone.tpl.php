<?php
$patient = $report->patient;
$doctor = $report->doctor;
$data_json = json_decode($report->data_json, true);
$revisitrecord = $data_json['revisitrecord'];
$patientremarks = $data_json['patientremarks'];
?>
<div class="p10 fb">
    <h5>
        <span>医生：<?= $doctor->name ?></span>
        <span style="margin-left: 15px;">汇报日期：<?= $report->createtime ?></span>
    </h5>
</div>
<div class="block block-bordered">
    <div class="block-header bg-gray-lighter">
        <h3 class="block-title">基本信息</h3>
    </div>
    <div class="block-content">
        <div class="table-responsive">
            <table class="table">
            <thead>
            <tr class="text-info">
                <th style="width: 100px;"><?= $patient->name ?></th>
                <th class="text-left"><?= $patient->getAttrStr() ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $complication = $data_json['pcard']['complication'];
            if (!empty($complication)) { ?>
                <tr>
                    <td>诊断</td>
                    <td><?= $complication ?></td>
                </tr>
            <?php } ?>
            <?php if (!empty($revisitrecord)) { ?>
                <tr>
                    <td>上次就诊</td>
                    <td><?= $revisitrecord['thedate'] ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php if ($report->appeal && '' != trim($report->appeal)) { ?>
    <div class="block block-bordered">
        <div class="block-header bg-gray-lighter">
            <h3 class="block-title">患者诉求</h3>
        </div>
        <div class="block-content">
            <textarea id="appeal" name="appeal"
                      style="padding: 0"
                      class="col-md-12 col-xs-12" rows="4"
                      readonly><?= $report->appeal ?></textarea>
            <div class="clear"></div>
        </div>
    </div>
<?php } ?>
<div class="block block-bordered">
    <div class="block-header bg-gray-lighter">
        <h3 class="block-title">运营备注</h3>
    </div>
    <div class="block-content">
            <textarea id="appeal" name="appeal"
                      style="padding: 0"
                      class="col-md-12 col-xs-12" rows="4"
                      readonly><?= $report->remark ?></textarea>
        <div class="clear"></div>
        <?php if (!empty($reportpictures)) { ?>
            <div style="margin: 0 0 -15px; border-top: 1px solid #e9e9e9;">
                <div class="multipicture" style="width: auto;">
                    <ul>
                        <?php
                        foreach ($reportpictures as $reportpicture) {
                            $arr = JsonPicture::jsonArray($reportpicture->picture, 140, 140, false, true);
                            ?>
                            <li>
                                <p class="setting_thumbimg">
                                    <img width="140" height="140" src="<?= $arr["thumb_url"] ?>"/>
                                </p>
                            </li>
                        <?php } ?>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php
if (!empty($patientremarks)) {
    ?>
    <div class="block block-bordered">
        <div class="block-header bg-gray-lighter">
            <h3 class="block-title">症状体征及不良反应</h3>
        </div>
        <div class="block-content">
            <div style="overflow: auto">
                <div class="table-responsive">
                    <table class="table">
                    <tbody>
                    <?php
                    foreach ($patientremarks as $patientremark) {
                        $content = $patientremark['content'];
                        if ($content == '') {
                            continue;
                        } ?>
                        <tr>
                            <td class="fb"><?= $patientremark['name'] ?></td>
                            <td><?= $patientremark['thedate']; ?></td>
                            <td><?= $content ?></td>
                        </tr>
                        <?php
                    } ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php
$checkuptpls = $data_json['checkuptpls'];
if (!empty($checkuptpls)) { ?>
    <div class="block block-bordered">
        <div class="block-header bg-gray-lighter">
            <h3 class="block-title">检查</h3>
        </div>
        <div class="block-content">
            <div class="text-center">
                <?php foreach ($checkuptpls as $checkuptpl) {
                    ?>
                    <div class="text-primary mt20 mb20 f16 fb">
                        <?= $checkuptpl['title'] ?>
                    </div>
                    <div style="overflow: auto;">
                        <div class="table-responsive">
                            <table class="table table-header-bg table-bordered checkups">
                            <thead>
                            <tr role="row">
                                <th class="text-center">日期</th>
                                <?php
                                $questions = $checkuptpl['questions'];
                                foreach ($questions as $q) {
                                    echo "<th style=\"text-align: center\">{$q}</th>";
                                }
                                ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $checkups = $checkuptpl['checkups'];
                            foreach ($checkups as $checkup) {
                                ?>
                                <tr>
                                    <td><?= $checkup['check_date'] ?></td>
                                    <?php
                                    $answers = $checkup['answers'];
                                    foreach ($answers as $answer) {
                                        echo "<td>{$answer}</td>";
                                    } ?>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                <?php } ?>
                <div class="clear"></div>
            </div>
        </div>
    </div>
<?php } ?>
<?php
$patientmedicinepkg = $data_json['patientmedicinepkg'];
if (!empty($patientmedicinepkg)) {
    $thedate = $patientmedicinepkg['thedate'];
    $patientmedicinepkgitems = $patientmedicinepkg['items']; ?>
    <div class="block block-bordered">
        <div class="block-header bg-gray-lighter">
            <h3 class="block-title">用药</h3>
        </div>
        <div class="block-content">
            <div class="table-responsive">
                <table class="table">
                <thead>
                <tr>
                    <th colspan="2"><?= $thedate ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($patientmedicinepkgitems as $item) {
                    ?>
                    <tr>
                        <td class="fb"><?= $item['medicinename'] ?></td>
                        <td>
                            <div>
                                <?= $item['drug_dose'] ?>
                                <?= $item['drug_frequency'] ?>
                                <?= $item['drug_change'] ?>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
<?php } ?>
<div class="clear"></div>
