<?php
$pagetitle = "添加专属疾病二维码";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="border1 p10">
            <span><?=$doctorwxshopref->doctor->name ?></span>
            <span class="blue f16"><?=$doctorwxshopref->wxshop->name ?></span>
            <span class="blue f16"><?=$doctorwxshopref->wxshop->disease->name ?></span>
            <div class="mt10">
            <?php if(count($doctordiseaserefs) > 0) {  ?>
                <form action="/doctorwxshoprefmgr/addOnlyOnediseasePost">
                    <input type="hidden" name="doctorwxshoprefid" value="<?=$doctorwxshopref->id ?>"/>
                    <?php foreach($doctordiseaserefs as $a){ ?>
                        <input type="radio" name="diseaseid" value="<?= $a->diseaseid ?>"/>
                        <span><?= $a->disease->name ?></span>
                    <?php } ?>
                    <br />
                    <br />
                    <input class="btn btn-success" type="submit" title="提交" />
                </form> <?php }else{ echo "已全部绑定完毕!"; }?>
            </div>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
