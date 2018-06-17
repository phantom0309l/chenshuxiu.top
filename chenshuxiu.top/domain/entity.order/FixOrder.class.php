<?php

/*
 * 账户手工修正单, 手工充值或退款
 */
class FixOrder extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'accountid',  // accountid
            'amount',  // 金额,分,可为负
            'reason',  // 手工充值(正)或扣款(负)
            'auditorid'); // 操作人员
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'accountid',
            'auditorid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["account"] = array(
            "type" => "Account",
            "key" => "accountid");

        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["accountid"] = $accountid;
    // $row["amount"] = $amount;
    // $row["reason"] = $reason;
    // $row["auditorid"] = $auditorid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "FixOrder::createByBiz row cannot empty");

        $default = array();
        $default["accountid"] = 0;
        $default["amount"] = 0;
        $default["reason"] = '';
        $default["auditorid"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
}
