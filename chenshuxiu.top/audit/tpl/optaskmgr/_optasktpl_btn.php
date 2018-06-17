<div class=" optasktplBox bg-white" style="padding:10px;">
    <div class="btnBox">
        <button class="btn btn-primary btn-sm selected btn-default oneHistory" data-optasktplid=0>全部</button>
        <button class="btn btn-default btn-sm btn-follow oneHistory" data-code="follow" >跟进任务</button>
        <?php
        if(count($optasktpls_notfollow)>0){
            foreach ($optasktpls_notfollow as $optasktpl) { ?>
            <button class="btn btn-default btn-sm oneHistory" data-optasktplid="<?= $optasktpl->id ?>" style="margin: 3px 0px">
                <?= $optasktpl->title ?>
            </button>
        <?php
            }
        }
        ?>
    </div>
    <div class="btnBox-follow none">
        <hr style="border:1px dashed #000;">
        <?php
        if(count($optasktpls_follow)){
            foreach ($optasktpls_follow as $optasktpl) { ?>
            <button class="btn btn-default btn-sm oneHistory" data-optasktplid="<?= $optasktpl->id ?>" style="margin: 3px 0px">
                <?= $optasktpl->title ?>
            </button>
        <?php
            }
        } ?>
    </div>
</div>
