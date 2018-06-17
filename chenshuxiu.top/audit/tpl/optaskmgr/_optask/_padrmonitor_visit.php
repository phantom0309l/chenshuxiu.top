<div class="optaskOneShell">
    <?php
    $padrmonitor = $optask->obj;
    if ($padrmonitor instanceof PADRMonitor) {
        ?>
        <div class="optaskContent">
            <table class="table">
                <tbody>
                <tr>
                    <td style="width: 110px; text-align: right;">检查类型:</td>
                    <td><?= ADRMonitorRuleItem::getItemStr($padrmonitor->adrmonitorruleitem_ename) ?></td>
                </tr>
                <tr>
                    <td style="text-align: right;">建议检查日期:</td>
                    <td><?= $padrmonitor->plan_date ?></td>
                </tr>
                <tr>
                    <td style="text-align: right;">实际检查日期:</td>
                    <td><?= $padrmonitor->the_date ?></td>
                </tr>
                <tr>
                    <td style="text-align: right;">检查照片:</td>
                    <td>
                        <?php $objPictures = $padrmonitor->getObjPictures(); ?>
                        <ul class="monitor_picbox remove-padding push-10-t" style="list-style: none;">
                            <?php
                            foreach ($objPictures as $objPicture) { ?>
                                <li class="push-10-r push-10 fl">
                                    <img style="width: 100px; height: 100px;" class="img-responsive viewer-toggle"
                                         data-url="<?= $objPicture->picture->getSrc(); ?>"
                                         src="<?= $objPicture->picture->getSrc(200, 200, true) ?>"/>
                                </li>
                            <?php } ?>
                            <div class="clear"></div>
                        </ul>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    <?php } ?>
</div>
<script>
    $(function () {
        $('.monitor_picbox').viewer({
            inline: false,
            url: 'data-url',
            class: 'viewer-toggle',
            navbar: false,
            scalable: false,
            fullscreen: false,
            shown: function (e) {
            }
        })
        $('.monitor_picbox').viewer('update');
    })
</script>
