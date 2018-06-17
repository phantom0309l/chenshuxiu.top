<?php if( $batmsg->auditstatus==0 ){ ?>
<div>
    <textarea id="content" data-id="<?= $batmsg->id?>"><?= $batmsg->content?></textarea>
</div>
<div style="padding-bottom: 40px;">
    <span class="btn btn-default onlySave">保存</span>
    <span class="btn btn-primary saveAndSend">发送</span>
    <span class="btn btn-danger refuse">拒绝</span>
</div>
<?php }else{ ?>
<div>
    <textarea id="content" data-id="<?= $batmsg->id?>"><?= $batmsg->content?></textarea>
</div>
<?php } ?>
