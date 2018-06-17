<?php if ($bedtktlogs) {?>
<div class="table-responsive">
    <table class="table">
    <?php foreach ($bedtktlogs as $a) {
            $str = '';
            if ($a->bedtkt_status > 4) {
                $str .= '[医生：' . $a->bedtkt->doctor->name . ']';
            } else if ($this->auditorid > 0) {
                $str .= '[运营：' . $a->auditor->name . ']';
            } else {
                $str .= '[本人][' . $a->bedtkt->patient->disease->name . ']';
            }  
    ?> 
    <tr>
    <td><?=$a->createtime?></td>
    <td><?php echo $a->getBedTktStatusDesc();?></td>
    <td><?=$str?></td>
    </tr>
    <?php }?>
    </table>
</div>
<?php } else {?>
    <?php if ($bedtkt->status == 0) { ?>
    <span class="pull-right">
    <a target="_blank" href="/bedtktmgr/modify?bedtktid=<?=$bedtkt->id?>" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> 完善数据</a>
    </span>
    <div class="clearfix"></div>
    <?php } else { ?>
    <p class="push-10-t text-center">暂无数据</p>
    <?php } ?>
<?php } ?>
