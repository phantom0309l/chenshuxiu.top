<div class="optaskOneShell">
    <?php
    $patientname = $optask->patient->name;
    $pagetitle = "{$patientname}{$optask->optasktpl->title}详情";
    $revisittkt = $optask->obj;

    include $tpl . "/_pagetitle.php"; ?>
<?php if($revisittkt instanceof RevisitTkt){ ?>
    <div class="optaskContent">
        <table class="table  table-bordered">
            <tr>
                <th width=100>预约日期</th>
                <td><?= $revisittkt->thedate?></td>
            </tr>
            <tr>
                <th width=100>预约平台</th>
                <td><?= $revisittkt->getYuyue_platformDesc()?></td>
            </tr>
            <tr>
                <th>预约时间</th>
                <td><?= $revisittkt->schedule->getDaypartStr()?></td>
            </tr>
            <tr>
                <th>预约医生</th>
                <td><?= $revisittkt->doctor->name ?></td>
            </tr>
            <tr>
                <th>门诊地点</th>
                <td><?= $revisittkt->schedule->scheduletpl->getScheduleAddressStr() ?></td>
            </tr>
            <tr>
                <th>患者姓名</th>
                <td><?= $revisittkt->patient->name ?></td>
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
        <div class="tktRemindPanel">
            <input type="hidden" name="revisittktid" class="revisittktid" value="<?= $optask->objid ?>"/>
            <div style="float:right;" class="modifytimePanel">
                <input type="text" name="thedate" class="calendar thedate" value=""/>
                <div class="btn btn-success tktRemindPanelModifyTime">
                    修改日期
                </div>
            </div>
            <div class="clearfix"></div>
            <?php $tkt_status = $revisittkt->status; ?>

                <div class="mt10" style="float:right;">
                    <div class="btn  <?= $tkt_status == 1 ? "btn-success" : "btn-default"?> tktRemindPanelConfirm">
                        确认复诊
                    </div>
                    <div class="btn  <?= $tkt_status == 0 ? "btn-success" : "btn-default"?>  tktRemindPanelCancel">
                        取消复诊
                    </div>
                </div>
                <div class="clearfix"></div>
        </div>
    </div>
<?php } ?>
</div>
