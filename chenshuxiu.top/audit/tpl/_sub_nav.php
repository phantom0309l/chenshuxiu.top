<style>
.sub_menu_item_a:hover {
	font-weight: bold;
	text-decoration: none;
}
</style>
<div class="sub_menu">
    <div class="sub_menu_title">
            <?php $auditmenu_4menu = $auditresource_menu->auditmenu; ?>
            <?= $auditmenu_4menu->parentmenu instanceof AuditMenu ? $auditmenu_4menu->parentmenu->title : $auditmenu_4menu->title;  ?>
    </div>
    <div class="sub_menu_body">
<?php
if (false == empty($menuarr['subs'])) {
    foreach ($menuarr['subs'] as $a) {
        if ($a->id == $auditresource_menu->auditmenuid) {
            ?>
        <a href="<?= $a->url ?>">
            <div class="sub_menu_item sub_menu_itemActive">
                    > <?= $a->title?>
            </div>
        </a>
    <?php } elseif($a->url) { ?>
        <a class="sub_menu_item_a" href="<?= $a->url ?>">
            <div class="sub_menu_item">
                <?= $a->title?>
            </div>
        </a>
    <?php
        } else {
            ?>
        <div class="sub_menu_item" style="color: #999">
            <?= $a->title?>
        </div>
<?php
        }
    }
}
?>
    </div>
</div>
