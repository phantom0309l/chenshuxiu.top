<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2018/4/26
 * Time: 11:40
 */
?>
<style>
</style>

<div class="optaskOneShell">
    <?php
    $applyform = $optask->content;
    $applyform = json_decode($applyform, true);
    if ($applyform) { ?>
        <div class="optaskContent">
            <p>申请数量：<?= $applyform['producttotalnum'] ?></p>
            <p>收货人：<?= $applyform['linkman_name'] ?></p>
            <p>手机号：<?= $applyform['linkman_mobile'] ?></p>
            <p>收货地址：<?= $applyform['address'] ?></p>
        </div>
    <?php } ?>
</div>
