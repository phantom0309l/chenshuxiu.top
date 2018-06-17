<tr>
    <td>
        <?php
        if ($u instanceof User) {
            foreach ($u->getWxUsers() as $w) {
                ?>
        <div><?=$w->id ?> <?=$w->nickname ?> <?=$w->subscribe_time ?></div>
        <?php
            }
        }
        ?>

    </td>
    <td><?=$u->id ?></td>
    <td><?=$u->shipstr ?></td>
    <td>
        <?php
        if ($u instanceof User) {
            echo $u->getMaskMobile();
        }
        ?>
    </td>
    <td>
        <a target="_blank" class="<?= ($thePatient->id == $a->id)?'red':''; ?>" href="/patientmgr/list4bind?patientid=<?=$a->id ?>"><?=$a->id ?></a>
    </td>
    <?php
        $pcard = $a->getMasterPcard();
    ?>
    <td><?=$pcard->getYuanNeiStr() ?></td>
    <td><?=$pcard->doctor->name ?></td>
    <td><?=$a->createtime ?></td>
    <td><?=$a->name ?></td>
    <td><?=$a->birthday ?></td>
    <td><?=XConst::status_withcolor($a->status); ?></td>
    <td><?=XConst::auditStatus($a->auditstatus); ?></td>
    <td><?=$a->getStatusStr(); ?></td>
    <td><?= $a->getSameNamePatientCnt() ?></td>
    <td>
        <a target="_blank" href="/patientmgr/mvToPatientHistoryPost?patientid=<?=$a->id ?>">迁移</a>
    </td>
</tr>
