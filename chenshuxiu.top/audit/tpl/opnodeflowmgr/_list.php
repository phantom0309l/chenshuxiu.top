
<div class="border-top-blue pt10">
<?php
$i = 0;
foreach ($opnodes as $from_opnode) {
    foreach ($opnodes as $to_opnode) {
        $opnodeflow = OpNodeFlowDao::getByFrom_opnodeTo_opnode($from_opnode, $to_opnode);
        if ($opnodeflow instanceof OpNodeFlow) {
            $i ++;
            ?>
    <div>
        <div>
            <span class="gray"><?=$i ?></span>
            <span class="fb f16">
            <?=$from_opnode->title ?> => <?=$to_opnode->title ?>
            </span>

            <?php

            if ($opnodeflow_can_modify) {
                ?>
            <button class="btn btn-sm btn-primary" data-is_hang_up="<?=$from_opnode->is_hang_up?>" data-opnodeflowid="<?=$opnodeflow->id?>" data-type="<?=$opnodeflow->type?>" data-content="<?=$opnodeflow->content?>" data-from_opnode_title="<?=$from_opnode->title?>"
                data-from_opnodeid="<?=$from_opnode->id?>" data-to_opnode_title="<?=$to_opnode->title?>" data-to_opnodeid="<?=$to_opnode->id?>" data-toggle="modal" data-target="#opnode-edit" type="button"
            >
                <i class="fa fa-edit push-5-r"></i><?=$opnodeflow->getTypeStr()?>
            </button>
            <?php }else{ ?>
            <span class="gray">(<?=$opnodeflow->getTypeStr()?>)</span>
            <?php } ?>
        </div>
        <div class="bgcolorborderbox blue">
            <?= nl2br($opnodeflow->content); ?>
        </div>
    </div>
<?php
        }
    }
}

?>
</div>
<div class="border-top-blue mt20 pt10">
<?php $optasktpl_row = $optasktpl->getRptData();?>
    <span class="blue fb">任务模板(OpTaskTpl)</span> <span class="red fb"><?= $optasktpl->title ?></span> [ <span class="blue fb"> <?= $optasktpl->code ?> , <?= $optasktpl->subcode ?> </span> ]
    <a target="_blank" href="/optasktplmgr/modify?optasktplid=<?= $optasktpl->id ?>">
        <i class="fa fa-edit push-5-r"></i>
    </a>
    <div class="bg-warning mt10 p10">
        数据: <?= $optasktpl_row['min_date'] ?> 至 <?= $optasktpl_row['max_date'] ?> => <?= $optasktpl_row['cnt'] ?> [<?= $optasktpl_row['cnt_0'] ?>, <?= $optasktpl_row['cnt_1'] ?>, <?= $optasktpl_row['cnt_2'] ?>] 条
    </div>
    <div class="bgcolorborderbox">
    <?= nl2br($optasktpl->content) ?>
    </div>
</div>
