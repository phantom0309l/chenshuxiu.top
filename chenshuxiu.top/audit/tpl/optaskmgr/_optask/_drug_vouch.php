<div class="optaskOneShell">
    <?php
    $pmCheck = $optask->obj;
    if ($pmCheck instanceof PatientMedicineCheck) {
        $content = json_decode($pmCheck->content);
        $type = $content->type;
        ?>
        <div class="optaskContent">
            <?php if ("wrong_drug" == $type) { // 错服漏服 ?>
                <h5>
                    错服漏服
                </h5>
                <p class="push-10-t">
                    <?= $content->drug_content; ?>
                </p>
            <?php } elseif ("doctor_advice_change" == $type) { // 医嘱变更 ?>
                <h5>
                    医嘱变更
                </h5>
                <ul class="pmcheck_picbox remove-padding push-10-t" style="list-style: none;">
                    <?php $basicpictureids = $content->basicpictureids;
                    foreach ($basicpictureids as $basicpictureid) {
                        $basicpicture = BasicPicture::getById($basicpictureid); ?>
                        <li class="push-10-r push-10 fl">
                            <img style="width: 150px; height: 150px;" class="img-responsive viewer-toggle"
                                 data-url="<?= $basicpicture->picture->getSrc(); ?>"
                                 src="<?= $basicpicture->picture->getSrc(150, 150, true) ?>"/>
                        </li>
                    <?php } ?>
                    <div class="clear"></div>
                </ul>
                <p class="push-10-t">
                    <?= $content->advice_change_content; ?>
                </p>
            <?php } ?>
        </div>
    <?php } ?>
</div>
<script>
    $(function () {
        $('.pmcheck_picbox').viewer({
            inline: false,
            url: 'data-url',
            class: 'viewer-toggle',
            navbar: false,
            scalable: false,
            fullscreen: false,
            shown: function (e) {
            }
        })
        $('.pmcheck_picbox').viewer('update');
    })
</script>