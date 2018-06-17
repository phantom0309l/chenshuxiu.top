<div class="optaskOneShell">
    <?php
    $patientname = $optask->patient->name;
    $recipe = $optask->obj;
    $picture = $recipe->picture;
    $pagetitle = "{$patientname}{$optask->optasktpl->title}详情";
    include $tpl . "/_pagetitle.php"; ?>
    <div class="optaskContent">
        <div class="col-md-12">
            <div class="block-header bg-gray-lighter" style="margin-bottom : 5px;">
                设置处方日期
                <ul class="block-options">
                    <li> <a class="text-info" target="_blank" style="opacity: 1.0;color:#3169b1;" href="/shopordermgr/listforaudit?patientid=<?=$recipe->patientid?>">绑定订单</a> </li>
                </ul>
            </div>
            <div class="col-md-6" style="overflow:hidden;max-width:200px;">
                <img class="img-responsive recipe-viewphoto viewer-toggle"  data-url="<?= $picture->getSrc() ?>" src="<?=$picture->getSrc(200, 200, true)?>" alt="">
            </div>
            <!-- <div class="clearfix"></div> -->
            <?php if ($recipe->status == 0) { ?>
            <div class="col-md-6" style="max-width:auto;">
                <div class="recipeAuditPanel">
                    <input type="hidden" name="recipeid" class="recipeid" value="<?= $recipe->id ?>"/>
                    <div style="" class="modifytimePanel">
                        <div class="mt10" style="float:right;">
                            <input type="text" name="thedate" class="calendar thedate" style="margin-bottom:5px;" value="<?= '0000-00-00' == $recipe->thedate ? '' : $recipe->thedate ?>"/>
                            <span class="input-group-btn">
                               <button class="btn btn-default recipeAuditPass" id="button-search-patient" type="button">审核通过</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php }?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
