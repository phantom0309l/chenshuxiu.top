
<!-- ========================================================WxUser======================================================== -->
<td>
    <?php
        if ($u instanceof User) {
            foreach ($u->getWxUsers() as $w) {
                ?>
        <div><?=$w->id ?> [<?=$w->wxshopid ?>] <?=$w->nickname ?> <?=$w->subscribe_time ?></div>
        <?php
            }
        }
    ?>
</td>

<!-- ========================================================User======================================================== -->
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
    <?php
        if ($a->id != $thePatient->id) {
            $revisitrecord_thedate_arr = Dao::queryValues("select thedate from revisitrecords where patientid = {$a->id} and doctorid = {$a->doctorid}");
            $thePatient_revisitrecord_thedate_arr = Dao::queryValues("select thedate from revisitrecords where patientid = {$thePatient->id} and doctorid = {$thePatient->doctorid}");
            $ismerge = 1;
            foreach ($revisitrecord_thedate_arr as $thedate) {
                if (in_array($thedate, $thePatient_revisitrecord_thedate_arr)) {
                    $ismerge = 0;
                    break;
                }
            }
            if ($u->id) {
                if ($ismerge == 1) {
                    ?>
                        <a href="/patientmgr/mergePatientPost?to_patientid=<?=$thePatient->id ?>&from_userid=<?=$u->id?>">
                            合并本条到
                            <span class='red'>目标</span>
                        </a>
                    <?php
                }else{
                    ?>
                        <a href="#" style="color:#708090">
                            (门诊日期有重合，不能合并)
                            合并本条到
                            <span class='red'>目标</span>
                        </a>
                    <?php
                }
            }
        }
    ?>
</td>
