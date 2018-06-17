<style>
.pic-archive {
/*     padding-top: 10px; */
/*     padding-bottom: 10px; */
}
.pic-archive p {
    margin-bottom: 0;
}
</style>
<div class="col-md-12 remove-padding">
    <?php
    if( $a->obj->picture instanceof Picture ){
        $picture = $a->obj->picture;
        ?>
        <div class="col-md-6">
            <div class="overflow:hidden" style="max-width:200px;">
                <img class="img-responsive viewer-toggle"  data-url="<?= $picture->getSrc() ?>" src="<?=$picture->getSrc(200, 200, true)?>" alt="">
            </div>
        </div>
        <?php
    }else{
        echo "图片上传失败。建议患者重新上传";
    }?>
</div>
