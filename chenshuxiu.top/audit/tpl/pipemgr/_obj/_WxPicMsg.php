<style>
.pic-archive {
    padding-top: 10px;
    padding-bottom: 10px;
}
.pic-archive p {
    margin-bottom: 0;
}
.display-block {
    display: block;
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
            <div class="pipe-img" style="max-width:200px;overflow:hidden">
                <img class="img-responsive viewer-toggle"  data-url="<?= str_replace('fangcunhulian.cn','fangcunyisheng.com',$a->obj->getImgUrl()) ?>" src="<?=str_replace('fangcunhulian.cn','fangcunyisheng.com',$a->obj->getThumbUrl(200, 1000))?>" alt="">
                        <!--<a class="btn btn-sm btn-default" target="_blank" href="<?= $a->obj->getImgUrl()?>"><i class="si si-magnifier-add"></i> 查看原图</a>-->
            </div>
        </div>
        <?php
            $picArchiveBgColor = '';
            if($patientpicture->obj instanceof CheckupPicture && $patientpicture->obj->checkup instanceof Checkup){
                $picArchiveBgColor = 'bg-gray-lighter';
            }
        ?>
        <div class="col-md-6 pic-archive <?=$picArchiveBgColor?>">
            <h5 class="font-w400"><?php if($patientpicture->title) {echo $patientpicture->title;}else{echo "无标题";}?></h5>
            <p class="text-warning push-10-t"><?= $patientpicture->getStatusDesc() ?></p>
            <?php if( $patientpicture->obj instanceof WxPicMsg ){?>
                <p><?= nl2br($patientpicture->getContent_brief()) ?></p>
            <?php }elseif( $patientpicture->obj instanceof CheckupPicture ){?>
                <?php $checkup = $patientpicture->obj->checkup; ?>
                <?php
                if ($checkup instanceof Checkup) {
                    ?>
                <?php
                    foreach ($checkup->xanswersheet->getAnswers() as $xanswer) {
                            echo $xanswer->getQuestionCtr()->getQaHtml4lesson();
                    }
                }
                ?>
            <?php }?>
            <a target="_blank" class="btn btn-default btn-sm push-20-t" href="/patientpicturemgr/one?patientpictureid=<?= $patientpicture->id ?>"><i class="fa fa-pencil"></i> 修改</a>
        </div>
        <?php
        }
    }else{
        echo "图片上传失败。建议患者重新上传";
        }?>
</div>


<script>

</script>

