<div class="optaskOneShell">
    <?php
    $patientname = $optask->patient->name;
    $bedtkt = $optask->obj;
    $typestr = '';
    if ($bedtkt instanceof BedTkt) {
        $typestr = $bedtkt->getTypestrDesc();
    }
    $pagetitle = "{$patientname}{$optask->optasktpl->title}（{$typestr}）详情";
    include $tpl . "/_pagetitle.php"; ?>
    <div class="mt10">
        <a target="_blank" href="/bedtktmgr/showhtml?bedtktid=<?=$bedtkt->id?>" class="btn btn-success show_bedtkt_audit">查看</a>
    </div>
    <div class="optaskContent">
        <table class="table  table-bordered">
            <tr>
                <th width=100>患者预约日期</th>
                <td><?= $bedtkt->want_date?></td>
            </tr>
            <tr>
                <th>应住日期</th>
                <td id="plan_date_show_<?=$bedtkt->id?>"><?= $bedtkt->plan_date == '0000-00-00' ? '未设置' : $bedtkt->plan_date; ?></td>
            </tr>
            <tr>
                <th>预约医生</th>
                <td><?= $bedtkt->doctor->name ?></td>
            </tr>
            <tr>
                <th>预约类型</th>
                <td>
                    <?php
                        $arr = [
                            'treat' => '住院预约<span style="color:red">[治疗]</span>',
                            'checkup' => '住院预约<span style="color:red">[检查]</span>'
                        ];
                        echo $arr["{$bedtkt->typestr}"];
                    ?>
                </td>
            </tr>
            <tr>
                <th>患者姓名</th>
                <td><?= $bedtkt->patient->name ?></td>
            </tr>
            <tr>
                <th>诉求</th>
                <td><?= $typestr ?></td>
            </tr>
            <tr>
                <th>审核状态</th>
                <td id="auditor_status_show_<?=$bedtkt->id?>">
                    <?php
                        $arr = BedTkt::TYPESTR_STATUS;
                        $patient_status = BedTkt::TYPESTR_PATIENT_STATUS;
                        if ($bedtkt->status == 5) {
                            echo $arr["{$bedtkt->status}"] . " " . $patient_status["{$bedtkt->status_by_patient}"];
                        } else {
                            echo $arr["{$bedtkt->status}"];
                        }
                    ?>
                </td>
            </tr>
        </table>
        <?php if ($bedtkt->status == 1) { ?>
            <div class="bedtktAuditPanel">
                <input type="hidden" name="bedtktid" class="bedtktid" value="<?= $optask->objid ?>"/>
                <div style="float:right;" class="modifytimePanel">
                    <input type="text" name="plan_date" class="calendar plan_date" value="<?= $bedtkt->plan_date ?>"/>
                    <div class="btn btn-success bedtktAuditPlanTime">
                        设置应住院日期
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="mt10" style="float:right;">
                    <div class="btn btn-success bedtktAuditPass">
                        通过
                    </div>
                    <div class="btn btn-success bedtktAuditRefuse">
                        拒绝
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        <?php }?>
    </div>
</div>
