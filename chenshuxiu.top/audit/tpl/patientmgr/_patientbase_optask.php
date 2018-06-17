<!-- Optask -->
<div class="patientBaseBox bgpurple p10 mt10" style="line-height: 120%">
<?php $optasks = OpTaskDao::getListByPatient($patient, ' and status in (0, 2) order by plantime asc, id asc  ');?>

    <table class="table table-bordered tdcenter bgwhite">
        <thead>
            <tr>
                <th>标题</th>
                <th>跟进时间</th>
                <th>状态</th>
                <th>跟进运营</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach( $optasks as $optask ){?>
            <tr>
                <td><?= $optask->optasktpl->title ?></td>
                <td><?= substr( $optask->plantime,0, 10 ) ?></td>
                <td><?= $optask->getStatusStr() ?></td>
                <td><?= $optask->auditor->name ?></td>
                <td>
                    <a class="btn btn-success" target="_blank" href="/optaskmgr/listnew?patient_name=<?= $patient->name ?>">查看</a>
                </td>
            </tr>
    <?php }?>
        </tbody>
    </table>
</div>
