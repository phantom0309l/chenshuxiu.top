<?php
// PatientWithdrawOrder
// 提现单

// owner by txj
// create by txj
// review by txj 20170907

class PatientWithdrawOrder extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // 发起者并受益者userid
            'patientid',  // patientid
            'amount',  // 退款金额
            'status',  // 退款状态 0 等待 1 成功 2 撤销
            'donetime',  // 该订单的结束时间 撤销 或者成功退款
            'auditorid'); // auditorid

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'userid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["amount"] = $amount;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientWithdrawOrder::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["amount"] = '';
        $default["status"] = 0;
        $default["donetime"] = '0000-00-00 00:00:00';
        $default["auditorid"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 微信通知用户 TODO rework
    public function notifyUser ($title = '原路退款', $reasonStr = '退款原因') {
        $wxuser = $this->wxuser;
        $first = array(
            "value" => $title); // 培训课保证金提现成功,预计2-5天内到账
        $reason = array(
            "value" => $reasonStr); // "完成培训课并进行余额提取操作
        $refund = array(
            "value" => "￥" . number_format($this->amount, 2) . "元");
        $remark = array(
            "value" => "您所充值金额已原路返还,如5天仍未到账，请及时与我们联系。");
        $contentArr = array(
            "first" => $first,
            "reason" => $reason,
            "refund" => $refund,
            "remark" => $remark);
        $content = json_encode($contentArr, JSON_UNESCAPED_UNICODE);

        $appendarr = array(
            "objtype" => "PatientWithdrawOrder",
            "objid" => $this->id);

        PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "REFUND_NOTIFY", $content, "", $appendarr);
        return true;
    }

    //通过
    public function pass(){
        $this->status = 1;
        $this->donetime = date("Y-m-d H:i:s", time());
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
