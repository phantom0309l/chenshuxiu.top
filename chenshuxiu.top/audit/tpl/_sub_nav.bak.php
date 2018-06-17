<?php

$sub_menu_url = "/{$action}/{$method}";

if (isset($navs[$menu]) && false == empty($navs[$menu]['subs'])) {
    $menu_title = $navs[$menu]['title'];
    $subs = $navs[$menu]['subs'];
    ?>
    <style>
        .sub_menu_item_a:hover{
            font-weight: bold;
            text-decoration: none;
        };
    </style>
<div class="sub_menu">
    <div class="sub_menu_title"><?= $menu_title;  ?></div>
    <div class="sub_menu_body">
<?php
    foreach ($subs as $a) {
        if ($sub_menu_url == $a['url']) {
            ?>
        <div class="sub_menu_item sub_menu_itemActive">
            <?= $a['title']?>
        </div>

<?php } elseif($a['url']) { ?>
        <a class="sub_menu_item_a" href="<?= $a['url'] ?>">
            <div class="sub_menu_item">
                <?= $a['title'] ?>
            </div>
        </a>
<?php
        } else {
            ?>
        <div class="sub_menu_item" style="color: #999">
            <?= $a['title']?>
        </div>
<?php
        }
    }
    ?>

    </div>
</div>
<?php } ?>
