<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2018/4/26
 * Time: 11:35
 */

$errandorder = $a->obj;
if ($errandorder instanceof ErrandOrder) { ?>
    <div class="optaskContent">
        <h5>
            代您开药
        </h5>
        <p></p>
        <?php if ($errandorder->is_use_ybk) {
            $shopaddress = $errandorder->shopaddress;
            if ($shopaddress instanceof ShopAddress) { ?>
                <p style="push-10-b">医保卡：使用医保卡</p>
                <p style="push-10-b">收货人：<?= $shopaddress->linkman_name ?></p>
                <p style="push-10-b">手机号：<?= $shopaddress->linkman_mobile ?></p>
                <p style="push-10-b">收货地址：<?= $shopaddress->getDetailAddress() ?></p>
            <?php } ?>
        <?php } else { ?>
            <p>不使用医保卡</p>
        <?php } ?>
    </div>
<?php } ?>

<script>
    $(function () {
    })
</script>