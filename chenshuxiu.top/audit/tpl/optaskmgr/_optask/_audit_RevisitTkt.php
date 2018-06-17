<div class="optaskOneShell">
    <?php
    $patientname = $optask->patient->name;
    $pagetitle = "{$patientname}{$optask->optasktpl->title}详情";
    $revisittkt = $optask->obj;
    include $tpl . "/_pagetitle.php"; ?>
    <?php if ($revisittkt) { ?>
    <div class="optaskContent">
        <table class="table  table-bordered">
            <tr>
                <th width=100>预约日期</th>
                <td><?= $revisittkt->thedate?></td>
            </tr>
            <tr>
                <th>预约时间</th>
                <?php if ($revisittkt->schedule) { ?>
                <td><?= $revisittkt->schedule->getDaypartStr()?></td>
                <?php } else { ?>
                <td>无预约时间</td>
                <?php } ?>
            </tr>
            <tr>
                <th>预约医生</th>
                <td><?= $revisittkt->doctor->name ?></td>
            </tr>
            <tr>
                <th>门诊地点</th>
                <td>
                    <?php
                        if ($revisittkt->schedule->scheduletpl instanceof ScheduleTpl) {
                            echo $revisittkt->schedule->scheduletpl->getScheduleAddressStr();
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <th>患者姓名</th>
                <td><?= $revisittkt->patient->name ?></td>
            </tr>

            <?php
                $_masterPcard = $revisittkt->patient->getPcardByDoctorOrMasterPcard($revisittkt->doctor);
                ?>
            <tr>
                <th>院内病历号</th>
                <td><?= $revisittkt->out_case_no ?> （现在储存的为 <?= $_masterPcard->out_case_no; ?>）</td>
            </tr>
            <tr>
                <th>院内就诊卡号</th>
                <td><?= $revisittkt->patientcardno ?> （现在储存的为 <?= $_masterPcard->patientcardno; ?>）</td>
            </tr>
            <tr>
                <th>院内患者ID</th>
                <td><?= $revisittkt->patientcard_id ?> （现在储存的为 <?= $_masterPcard->patientcard_id; ?>）</td>
            </tr>
            <tr>
                <th>院内病案号</th>
                <td><?= $revisittkt->bingan_no ?> （现在储存的为 <?= $_masterPcard->bingan_no; ?>）</td>
            </tr>

            <tr>
                <th>诉求</th>
                <td><?= $revisittkt->patient_content ?></td>
            </tr>
            <tr>
                <th>审核状态</th>
                <td><?= $revisittkt->getDescStep()?></td>
            </tr>

        </table>
        <div class="tktAuditPanel">
            <input type="hidden" name="revisittktid" class="revisittktid" value="<?= $optask->objid ?>"/>
            <div style="float:right;" class="modifytimePanel">
                <input type="text" name="thedate" class="calendar thedate" value=""/>
                <div class="btn btn-success tktAuditPanelModifyTime">
                    修改日期
                </div>
            </div>
            <div class="clearfix"></div>
            <?php if( 0 == $revisittkt->auditstatus  ){ ?>
                <div class="mt10" style="float:right;">
                    <div class="btn btn-success tktAuditPanelPass">
                        通过
                    </div>
                    <div class="btn btn-success tktAuditPanelRefuse">
                        不通过
                    </div>
                </div>
                <div class="clearfix"></div>
            <?php }?>
        </div>
    </div>
<?php } ?>
</div>
