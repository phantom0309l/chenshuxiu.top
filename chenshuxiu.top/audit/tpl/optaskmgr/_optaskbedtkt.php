<?php if ($bedtkts) { ?>
<div class="block block-bordered push-10-t">
<div class="block-header bg-gray-lighter">
    <h3 class="block-title"><i class="si si-user"></i> <span class=""><?=$patient->getMaskName()?></span> 住院预约</h3>
</div>
<div class="block-content remove-padding">
    <?php foreach ($bedtkts as $bedtkt) {?>
    <div class="optask" data-bedtktid="<?=$bedtkt->id?>">
        <div class="optask-t bedtkt-title" data-bedtktid="<?=$bedtkt->id?>">
            <span><?=$bedtkt->getCreateDay()?></span>
            <span class="push-10-l"><?=$bedtkt->getStatusDesc()?></span>
            <span class="push-10-l">关系：本人</span>
            <span class="pull-right push-20-r"><i class="fa fa-angle-right angle"></i></span>
        </div>
        <div class="optask-c bg-gray-lighter none">
            <div class="pb10 pl10 pr10 bedtkt-detail" style="border-bottom:1px solid #e5e5e5">
            </div>
        </div>
    </div>
    <?php }?>
</div>
</div>
<?php } else { ?>
<p class="push-10-t text-center">暂无数据</p>
<?php }?>
