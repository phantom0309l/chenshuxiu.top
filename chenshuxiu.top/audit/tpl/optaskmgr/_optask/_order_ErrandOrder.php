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
    $errandorder = $optask->obj;
    if ($errandorder instanceof ErrandOrder) { ?>
        <div class="optaskContent">
            <?php if ($errandorder->is_use_ybk) {
                $shopaddress = $errandorder->shopaddress;
                if ($shopaddress instanceof ShopAddress) { ?>
                    <p>医保卡：使用医保卡</p>
                    <p>收货人：<?= $shopaddress->linkman_name ?></p>
                    <p>手机号：<?= $shopaddress->linkman_mobile ?></p>
                    <p>收货地址：<?= $shopaddress->getDetailAddress() ?></p>
                <?php } ?>
            <?php } else { ?>
                <p>不使用医保卡</p>
            <?php } ?>
        </div>
    <?php } ?>
</div>
<script>
</script>