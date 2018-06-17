<?php
// DoctorSettleOrder
// 医生结算单

// owner by lijie
// create by lijie 2016-5-28 下午4:45
// review by sjp 20160629

class DoctorSettleOrder extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctorid',  // doctorid
            'themonth',  // themonth
            'activecnt',  // 当前医生当月活跃患者数
            'price',  // 市场部门所定的医生补助规则
            'amount',  // 金额
            'status',  // 是否有效
            'auditstatus',  // 审核状态
            'auditremark',  // 审核备注,原因
            'auditorid',  // auditorid
            'remark'); //

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'doctorid',
            'themonth',
            'activecnt',
            'amount');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["doctorid"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "DoctorSettleOrder::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] = 0;
        $default["themonth"] = '0000-00-00';
        $default["activecnt"] = 0;
        $default["price"] = 0;
        $default["amount"] = 0;
        $default["status"] = 0;
        $default["auditstatus"] = 0;
        $default["auditremark"] = '';
        $default["auditorid"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================

}
