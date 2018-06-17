<?php $quickconsultorder = $a->obj;
if ($quickconsultorder instanceof QuickConsultOrder) { ?>
    <div class="optaskContent">
        <h5>
            快速咨询
        </h5>
        <p class="push-10-t pb10 border-b">
            <?= "<span class='label label-primary'>" . $quickconsultorder->getStatusStr() . "</span> &nbsp; " ?>
            <?php
            $is_timeout = $quickconsultorder->isTimeout() ? '已超时' : '';
            echo "<span class='label label-danger'>{$is_timeout}</span> &nbsp;";

            $is_refund = $quickconsultorder->is_refund == 1 ? '已退款' : '';
            echo "<span class='label label-warning'>{$is_refund}</span> &nbsp;";
            ?>
        </p>
        <p class="push-10-t">
            <?= $quickconsultorder->content; ?>
        </p>
        <ul class="quickconsultorder_picbox remove-padding push-10-t" style="list-style: none;">
            <?php
            $basicpictures = $quickconsultorder->getBasicPictures();
            foreach ($basicpictures as $basicpicture) { ?>
                <li class="push-10-r push-10 fl">
                    <img style="width: 150px; height: 150px;" class="img-responsive viewer-toggle"
                         data-url="<?= $basicpicture->picture->getSrc(); ?>"
                         src="<?= $basicpicture->picture->getSrc(150, 150, true) ?>"/>
                </li>
            <?php } ?>
            <div class="clear"></div>
        </ul>
    </div>
<?php } ?>

<script>
    $(function () {
        $('.quickconsultorder_picbox').viewer({
            inline: false,
            url: 'data-url',
            class: 'viewer-toggle',
            navbar: false,
            scalable: false,
            fullscreen: false,
            shown: function (e) {
            }
        })
        $('.quickconsultorder_picbox').viewer('update');
    })
</script>