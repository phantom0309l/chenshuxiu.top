<?php if ($revisittkts) { ?>
<div class="block block-bordered push-10-t">
<div class="block-header bg-gray-lighter">
    <h3 class="block-title"><i class="si si-user"></i> <span class=""><?=$patient->getMaskName()?></span> 复诊预约</h3>
</div>
<div class="block-content remove-padding">
    <?php foreach ($revisittkts as $revisittkt) {?>
    <div class="optask" data-revisittktid="<?=$revisittkt->id?>">
        <div class="optask-t revisittkt-title" data-revisittktid="<?=$revisittkt->id?>">
            <?php 
                $daypartArr = $revisittkt->schedule->getDaypartArray();
                $daypartDesc = $daypartArr[$revisittkt->schedule->daypart]; 
            ?>
            <span class=""><?php echo $revisittkt->schedule->thedate, ' ', $revisittkt->doctor->name, ' ', $daypartDesc; ?></span>
            <span class="push-5-l" style="color:<?=$revisittkt->getDescArr()[1]?>"><?=$revisittkt->getDescArr()[0]?></span>
            <span class="pull-right push-10-r"><i class="fa fa-angle-right angle"></i></span>
        </div>
        <div class="optask-c bg-gray-lighter none">
            <div class="pb10 pl10 pr10 revisittkt-detail" style="border-bottom:1px solid #e5e5e5">
                <table class="table">
                <!--申请复诊是起始状态-->
                <tr>
                <td><?=$revisittkt->createtime?></td>
                <td>申请复诊</td>
                <td>[本人][<?=$revisittkt->patient->disease->name?>]</td>
                </tr>
                <!--运营审核-->
                <?php if($revisittkt->auditorid) {?>
                <tr>
                <td><?=$revisittkt->audittime?></td>
                <td><?=$revisittkt->getDescStep()?></td>
                <td>[运营][<?=$revisittkt->auditor->name?>]</td>
                </tr>
                <?php }?>
                <!--患者确认-->
                <?php if($revisittkt->patient_confirm_status > 0) {?>
                <tr>
                <td><?=$revisittkt->updatetime?></td>
                <td><?=$revisittkt->getPatient_confirm_statusStr()?></td>
                <td>[本人][<?=$revisittkt->patient->disease->name?>]</td>
                </tr>
                <?php }?>
                <!-- 没有操作日志，很难判断具体的先后关系，不显示了-->
                <!--关闭是终结状态-->
                <!--
                <?php if($revisittkt->status == 0 && $revisittkt->isclosed == 1) {?>
                <tr>
                <td><?=$revisittkt->updatetime?></td>
                <td><?=$revisittkt->getDescArr()[0]?></td>
                <td>[本人][<?=$revisittkt->patient->disease->name?>]</td>
                </tr>
                <?php }?>
                -->
                </table>
            </div>
        </div>
    </div>
    <?php }?>
</div>
</div>
<?php } else { ?>
<p class="push-10-t text-center">暂无数据</p>
<?php }?>
