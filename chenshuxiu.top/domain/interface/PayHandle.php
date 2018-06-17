<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2018/4/26
 * Time: 15:39
 */

interface PayHandle {

    // 尝试支付
    public function tryPay(Account $rmbAccount);

    // Body获取
    public function getWxPayUnifiedOrder_Body();

    // Attach获取
    public function getWxPayUnifiedOrder_Attach();

    // 获取支付金额
    public function getPayAmount();

}
