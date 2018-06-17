<div class="border1-blue mt20">
    <div>
        <input type="hidden" name="revisittktid" value="<?= $revisittkt->id?>">
        <p>加号单id：<?= $revisittkt->id?></p>
        <p>患者姓名：<?= $revisittkt->patient->name?></p>
        <p>
            预约时间：
            <span id="thedate"><?= $revisittkt->thedate;?></span>
            &nbsp;
            <input style="width: 100px" name="thedate" class="calendar" value="">
            <input class="btn btn-success modify_thedate" type="button" data-revisittktid="<?= $revisittkt->id?>" value="修改预约时间">
        </p>
        <p>患者确认状态：<?=$revisittkt->getPatient_confirm_statusStr();?></p>
        <p>患者说：<?= $revisittkt->patient_content;?></p>
        <p>当前状态：<?= $revisittkt->getStatusStrWithColor();?></p>
        <?php if ( $revisittkt->auditstatus == 1) {?>
            审核状态：<span class="green">已审核</span>
        <input class="btn btn-danger audit-btn-refuse" type='button' value='重新拒绝' />
        <?php }elseif($revisittkt->auditstatus == 0){?>
            <input class="btn btn-success audit-btn-pass" type='button' data-revisittktid='<?= $revisittkt->id ?>' value='通过' />
        <input class="btn btn-danger audit-btn-refuse" type='button' value='拒绝' />
        <?php } else { ?>
                 审核状态：<span class="red">已拒绝</span>
        <input class="btn btn-success audit-btn-pass" type='button' data-revisittktid='<?= $revisittkt->id ?>' value='重新通过' />
        <?php
        }
        ?>
        <div id="audit-reason" class="border1 bggray none p10 mt10">
            <span>拒绝原因:</span>
            <div class="mt5 mb5">
                <textarea id="auditremark" name="auditremark" cols=40><?=$revisittkt->auditremark ?></textarea>
            </div>
            <input class="btn btn-danger audit-btn-refuse-submit" type='button' data-revisittktid='<?= $revisittkt->id ?>' value='提交(拒绝+原因)' />
        </div>
    </div>
</div>
