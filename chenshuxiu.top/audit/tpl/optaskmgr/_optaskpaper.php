<?php if ($papers) { ?>
<div class="block block-bordered push-10-t">
<div class="block-header bg-gray-lighter">
    <ul class="block-options">
        <li class="dropdown navbar-right">
        <button type="button" style="opacity:1" data-toggle="dropdown" id="optask-paper-filter"><span id="paper-filter-title">筛选</span> <span class="caret"></span></button>
        <ul class="dropdown-menu dropdown-menu-papertpl">
            <li> <a tabindex="-1" data-papertplid="0" href="javascript:">全部</a> </li>
            <?php foreach ($papertpls as $papertpl) { ?>
            <li> <a tabindex="-1" data-papertplid="<?=$papertpl->id?>" data-papertpltitle="<?=$papertpl->title?>" href="javascript:"><?=$papertpl->title?></a> </li>
            <?php }?>
        </ul>
        </li>
    </ul>
    <h3 class="block-title"><i class="si si-user"></i> <span class=""><?=$patient->getMaskName()?></span> 量表</h3>
</div>
<div class="block-content remove-padding">
    <?php foreach ($papers as $paper) {?>
    <div class="optask" data-papertplid="<?=$paper->papertplid?>">
        <div class="optask-t paper-title" data-paperid="<?=$paper->id?>">
            <span><?=$paper->getCreateDay()?></span>
            <span class="push-10-l"><?=$paper->XAnswerSheet->score?>分</span>
            <span class="push-10-l"><?=$paper->writer?></span>
            <span class="pull-right push-20-r"><i class="fa fa-angle-right angle"></i></span>
            <p><span class=""><?=$paper->papertpl->title?></span></p>
        </div>
        <div class="optask-c bg-gray-lighter none">
            <div class="pb10 pl10 pr10 paper-detail" style="border-bottom:1px solid #e5e5e5"></div>
        </div>
    </div>
    <?php }?>
    <div class="clearfix"></div>
</div>
</div>
<script>
$(function() {

    $(document).off('click', '#optask-paper-filter').on('click', '#optask-paper-filter', function() {
        $(this).dropdown();
    });
    $(document).off('click', '.dropdown-menu-papertpl li a').on('click', '.dropdown-menu-papertpl li a', function() {
        var papertplid= $(this).data('papertplid');
        var papertpltitle= $(this).data('papertpltitle');
        $(this).closest('.block-header').siblings('.block-content').find('.optask').each(function() {
            if (papertplid == 0) {
                $(this).show();
                $('#paper-filter-title').html('筛选');
            } else {
                $('#paper-filter-title').html(papertpltitle);
                if ($(this).data('papertplid') != papertplid) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            }
        });
    });

});
</script>
<?php } else { ?>
<p class="push-10-t text-center">暂无数据</p>
<?php }?>
