<div class="fc-breadcrumb">
    <div class="fc-breadcrumb-page-title">
    <?php if (is_array($breadcrumbs)) { foreach ($breadcrumbs as $url => $name) { ?>
        <a href="<?=$url?>"><?=$name?></a>&nbsp;&nbsp;>&nbsp;  
    <?php }} ?>
    <?=$pagetitle ?>
    </div>
    <div class="clear"></div>
</div>
