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
//     $patientpicture = PatientPictureDao::getByObj($a->obj); // by xuzhe
    if( $a->obj->patientpicture instanceof PatientPicture ){
        $patientpicture = $a->obj->patientpicture;
        if ($patientpicture instanceof PatientPicture) {
        ?>
        <div class="col-md-6">
            <div class="overflow:hidden" style="max-width:200px;">
                <img class="img-responsive viewer-toggle"  data-url="<?= $a->obj->getImgUrl() ?>" src="<?=$a->obj->getThumbUrl(200, 1000)?>" alt="">
            </div>
        </div>
        <div class="col-md-6 pic-archive">
            <h5 class="font-w400"><?php if($patientpicture->title) {echo $patientpicture->title;}else{echo "无标题";}?></h5>
            <p class="text-warning push-10-t"><?= $patientpicture->getStatusDesc() ?></p>
            <a target="_blank" class="btn btn-default btn-sm push-20-t" href="/patientpicturemgr/one?patientpictureid=<?= $patientpicture->id ?>"><i class="fa fa-pencil"></i> 修改</a>
        </div>
        <?php
        }
    }else{
        echo "图片上传失败。建议患者重新上传";
        }?>
</div>
