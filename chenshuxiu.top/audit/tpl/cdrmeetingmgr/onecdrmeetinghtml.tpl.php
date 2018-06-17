<div class="cdr-close-div">
    <button type="button" class="cdr-close-btn"><i class="si si-close"></i></button>
</div>
<?php if (empty($resultObj) || $resultObj->result != 'success') { ?>
    <p style="color: #d26a5c;">抱歉，2018年5月10日之前的通话录音未使用双轨录音，暂不支持文字识别</p>
<?php } else { ?>
    <form action="" name="cdr-form">
        <input type="hidden" name="cdr-meeting-id" value="<?= $cdrmeeting->id ?>">
        <?php
        foreach ($resultObj->msg->data as $index=>$msg) {
            if ($msg->side == 'Agent') {
                ?>
                <div class="clear">
                    <img class="imgBlue" src="<?= $cdrmeeting->auditor->getHeadImgUrl() ?>" alt="">
                    <span class="textM textBlue">
                        <textarea name="Agent" class="cdr-textarea" cols="40" rows="1" data-index="<?= $index?>" data-text-back="<?= $msg->text ?>" ><?= $msg->text ?></textarea>
                    </span>
                </div>
                <?php
            } else if ($msg->side == 'Customer') {
                ?>
                <div class="clear">
                    <img class="imgWhite" src="<?= $default_wxuser_header ?>" alt="">
                    <span class="textM textWhite">
                        <textarea name="Customer" class="cdr-textarea" cols="40" rows="1" data-index="<?= $index?>" data-text-back="<?= $msg->text ?>" ><?= $msg->text ?></textarea>
                    </span>
                </div>
                <?php
            }
        }
        ?>
    </form>
    <div class="cdr-btn-div">
        <button class="btn btn-sm btn-default cdr-close-btn">关闭</button>
    </div>
<?php } ?>

