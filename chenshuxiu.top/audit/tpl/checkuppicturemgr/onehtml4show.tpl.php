<div class="mt20">
    <div style="width: 100%; text-align: center;">
        <?php
        foreach ($checkuppictures_arr as $check_date => $checkuppictures) {?>
            <div>
                <h3><?=$check_date?></h3>
                <?php foreach( $checkuppictures as $a ){ ?>
                    <?php
                    $picture = $a->picture;
                    if ($picture instanceof Picture) {
                        ?>
                        <div class="fl <?= $a->status == 1 ? "border1-gray" : "border1-blue" ?> " style="display: inline-block; margin: 10px">
                            <div>
                                <a data-gallery class="imgShell" target="_blank" href="/checkuppicturemgr/list?checkuppictureid=<?= $a->id ?>">
                                    <img src="<?= $picture->getSrc(200,200,false); ?>">
                                </a>
                            </div>
                        </div>
                        <?php
                    }?>
                <?php }?>
            </div>
            <div style="clear: both;"></div>
        <?php } ?>
    </div>
</div>
