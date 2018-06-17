<div class="patientPictures-Box">
    <div class="row">
        <div class="col-md-12">
            <?php
            $group_names = [
                'CheckupPicture' => '检查报告图片',
                'LiverPicture' => '肝功能图片',
                'WxPicMsg' => '血常规图片',
            ];
            foreach ($groups as $key => $patientPictures) { ?>
                <h2 class="content-heading"><?= $group_names[$key] ?></h2>
                <div class="row items-push">
                    <?php foreach ($patientPictures as $patientPicture) {
                        if ($patientPicture->obj && $patientPicture->obj->picture) {
                            $picture = $patientPicture->obj->picture;
                            $arr = JsonPicture::jsonArray($picture, 140, 140, true, true); ?>
                            <div id="patientPicture_<?= $patientPicture->id ?>"
                                 class="patientPicture-item col-md-3 col-xs-4 animated fadeIn"
                                 data-pictureid="<?= $arr['pictureid'] ?>"
                                 data-patientpictureid="<?= $patientPicture->id ?>"
                                 data-selected="false"
                                 data-thumburl="<?= $arr['thumb_url'] ?>">
                                <div class="img-container">
                                    <img class="img-responsive"
                                         src="<?= $arr['thumb_url'] ?>">
                                    <i class="fa fa-check-circle-o fa-2x text-primary"
                                       style="display: none; width: 28px; border-radius: 5px; padding-left: 2px; background-color: #fff; position: absolute; right: 10px; top: 10px;"></i>
                                </div>
                                <p style="font-size: 12px; line-height: 2; margin-bottom: 0;">
                                    <?= $picture->createtime ?>
                                    <a target="_blank" href="<?= $arr['url'] ?>" class="fr">预览</a>
                                </p>
                            </div>
                        <?php }
                    } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
