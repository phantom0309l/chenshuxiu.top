<div class="border1-blue mt20">
    <div>
        <div>
            <?php $patient = $revisitrecord->patient; ?>
            <?php include $tpl . "/patientmgr/_patientbase_info.php";?>
        </div>
        <div class="mt20 contentBoxTitle">
            <span class="f16">门诊记录ID : <?=$revisitrecord->id ?> 门诊日期 : <?= $revisitrecord->thedate ?></span>
            <br />
            <span class="gray"> createtime : <?= $revisitrecord->createtime ?> </span>
        </div>

        <?php

        if ($revisitrecord->revisittktid > 0) {
            $revisittkt = $revisitrecord->revisittkt;
                ?>
            <div class="border1-blue mt20">
                <?php $checkuptpls_tkt = $revisittkt->getCheckupTpls(); ?>
                <div>
                <div style="background: url('<?= $img_uri ?>/static/img/ipad/appointment.png') no-repeat center center;background-size: contain;
                        width: 14px;height: 14px;display: inline-block;position: relative;top:2px; " ></div>
                <span style="color: #1996ca; font-size: 15px; margin-left: 8px;">预约详情 (<?= $revisittkt->id ?>)</span>
                <div>
                    <div style="margin: 15px 0px 0px 15px">
                            下次预约时间：
                            <?=$revisittkt->thedate?>
                        </div>
                        <?php if( ! empty($checkuptpls_tkt) ){?>
                            <div style="margin: 15px 0px 0px 15px">
                                下次检查项目：
                                <?php

                    foreach ($checkuptpls_tkt as $i => $checkuptpl_tkt) {
                        ?>
                                    <div style="color: #2e61bc; font-size: 18px;"><?= $checkuptpl_tkt->title ?></div>
                        <div>
                                        <?= $checkuptpl_tkt->getContentNl2br()?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php }?>
                    </div>
            </div>
        </div>
            <?php

        }
        ?>
        <?php $patientmedicinepkg = $revisitrecord->patientmedicinepkg; ?>
        <div class="border1-blue mt20">
            <?php
            $patientmedicinepkgitems = array();
            if ($patientmedicinepkg instanceof PatientMedicinePkg) {
                $patientmedicinepkgitems = PatientMedicinePkgItemDao::getListByPatientmedicinepkgid($patientmedicinepkg->id);
            }
            ?>
            <div style="margin-bottom: 20px">
                <div style="background: url('<?= $img_uri ?>/static/img/ipad/drug.png') no-repeat center center;background-size: contain;
                    width: 14px;height: 14px;display: inline-block; position: relative;top:2px;" ></div>
                <span style="color: #1996ca; font-size: 15px; margin-left: 8px;">用药医嘱 (<?= $patientmedicinepkg->id ?>)</span>
                <?php if( date("Y-m-d") == $revisitrecord->thedate ){?>
                    <a href="fcdoctorpad.patientmedicinepkg.add?patientid=<?=$patient->id?>" class="link-blue">修改></a>
                <?php }?>
                <div class="line" style="margin: 15px 0px 0px 20px;"></div>
                <?php
                include $tpl . "/patientmedicinepkgitemmgr/listhtml.tpl.php";
                ?>
            </div>
        </div>
    </div>
</div>
</div>
</div>
