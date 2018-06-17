<?php
// WxRedbagOrder
// 微信红包单

// owner by txj
// create by txj
// review by sjp 20160629
class WxRedbagOrder extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'wxshopid',  // wxshopid
            'total_amount',  // 金额,单位分
            'total_num',  // 发放总人数
            'mch_billno',  // 商户订单号
            'mch_id',  // 商户号
            'wxappid',  // 公众账号appid
            'send_name',  // 商户名称
            'wishing',  // 祝福语
            'act_name',  // 活动名称
            'remark',  //
            'return_code',  // SUCCESS/FAIL
                           // 此字段是通信标识，非交易标识，交易是否成功需要查看result_code来判断
            'return_msg',  // 返回信息，如非空，为错误原因 如签名失败
            'result_code',  // 业务结果 SUCCESS/FAIL
            'err_code',  // 错误码信息
            'err_code_des',  // 错误代码描述
            'send_time',  // 红包发送时间
            'send_listid',  // 红包订单的微信单号
            'q_return_code',  // SUCCESS/FAIL
                             // 此字段是通信标识，非交易标识，交易是否成功需要查看result_code来判断
            'q_return_msg',  // 返回信息，如非空，为错误原因 如签名失败
            'q_result_code',  // 业务结果 SUCCESS/FAIL
            'q_err_code',  // 错误码信息
            'q_err_code_des',  // 错误代码描述
            'q_detail_id',  // 使用API发放现金红包时返回的红包单号
            'q_status',  // 红包状态SENDING:发放中 SENT:已发放待领取 FAILED：发放失败 RECEIVED:已领取
                        // REFUND:已退款
            'q_send_type',  // 发放类型
            'q_hb_type',  // 红包类型
            'q_reason',  // 失败原因
            'q_rcv_time',  // 领取红包的时间
            'q_refund_time',  // 红包退款时间
            'q_refund_amount'); // 红包退款金额
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'wxshopid',
            'total_amount');
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
        $this->_belongtos["wxshop"] = array(
            "type" => "WxShop",
            "key" => "wxshopid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["wxshopid"] = $wxshopid;
    // $row["total_amount"] = $total_amount;
    // $row["total_num"] = $total_num;
    // $row["mch_billno"] = $mch_billno;
    // $row["mch_id"] = $mch_id;
    // $row["wxappid"] = $wxappid;
    // $row["send_name"] = $send_name;
    // $row["wishing"] = $wishing;
    // $row["act_name"] = $act_name;
    // $row["remark"] = $remark;
    // $row["return_code"] = $return_code;
    // $row["return_msg"] = $return_msg;
    // $row["result_code"] = $result_code;
    // $row["err_code"] = $err_code;
    // $row["err_code_des"] = $err_code_des;
    // $row["send_time"] = $send_time;
    // $row["send_listid"] = $send_listid;
    // $row["q_return_code"] = $q_return_code;
    // $row["q_return_msg"] = $q_return_msg;
    // $row["q_result_code"] = $q_result_code;
    // $row["q_err_code"] = $q_err_code;
    // $row["q_err_code_des"] = $q_err_code_des;
    // $row["q_detail_id"] = $q_detail_id;
    // $row["q_status"] = $q_status;
    // $row["q_send_type"] = $q_send_type;
    // $row["q_hb_type"] = $q_hb_type;
    // $row["q_reason"] = $q_reason;
    // $row["q_rcv_time"] = $q_rcv_time;
    // $row["q_refund_time"] = $q_refund_time;
    // $row["q_refund_amount"] = $q_refund_amount;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "WxRedbagOrder::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["wxshopid"] = 0;
        $default["total_amount"] = 0;
        $default["total_num"] = 0;
        $default["mch_billno"] = '';
        $default["mch_id"] = '';
        $default["wxappid"] = '';
        $default["send_name"] = '';
        $default["wishing"] = '';
        $default["act_name"] = '';
        $default["remark"] = '';
        $default["return_code"] = '';
        $default["return_msg"] = '';
        $default["result_code"] = '';
        $default["err_code"] = '';
        $default["err_code_des"] = '';
        $default["send_time"] = '0000-00-00 00:00:00';
        $default["send_listid"] = '';
        $default["q_return_code"] = '';
        $default["q_return_msg"] = '';
        $default["q_result_code"] = '';
        $default["q_err_code"] = '';
        $default["q_err_code_des"] = '';
        $default["q_detail_id"] = '';
        $default["q_status"] = '';
        $default["q_send_type"] = '';
        $default["q_hb_type"] = '';
        $default["q_reason"] = '';
        $default["q_rcv_time"] = '0000-00-00 00:00:00';
        $default["q_refund_time"] = '0000-00-00 00:00:00';
        $default["q_refund_amount"] = 0;

        $row += $default;
        return new self($row);
    }

    // createByActOrder
    public static function createByActOrder ($actorder) {
        $arr = array();
        $arr['wxuserid'] = $actorder->wxuserid;
        $arr['userid'] = $actorder->userid;
        $arr['patientid'] = $actorder->patientid;
        $arr['total_amount'] = $actorder->amount;
        $arr['total_num'] = 1;
        $wxuser = WxUser::getById($actorder->wxuserid);

        $arr['wxshopid'] = $wxuser->wxshopid;
        return self::createByBiz($arr);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 过账
    public function posting () {
        $user = User::getById($this->userid);
        $fromAccount = $user->getAccount("user_rmb");
        $toAccount = Account::getSysAccount("sys_user_wxredbag_out");
        // 用户账户余额大于等于，支付金额
        $amount = $this->total_amount;
        if ($fromAccount->balance >= $amount) {
            PostingRule::createAndProcess($fromAccount, $toAccount, $amount, $this);
            return true;
        }
        return false;
    }

    // 执行发红包
    public function process () {
        $helper = new WxRedPackHelper();

        $wxuser = $this->wxuser;

        $openid = $wxuser->openid;

        $helper->setBaseParams($openid, $this->total_amount, $this->id);

        $helper->setValue("nick_name", '方寸医生'); // 提供方名称
        $helper->setValue("send_name", $this->send_name); // 红包发送者名称
        $helper->setValue("wishing", $this->wishing); // 红包祝福语
        $helper->setValue("act_name", $this->act_name); // 活动名称
        $helper->setValue("remark", $this->remark); // 备注信息

        $json = $helper->dowork();
        return $json;
    }

    // 发红包
    public function sendWxRedPack ($config) {
        $helper = new WxRedPackHelper();

        $wxuser = $this->wxuser;

        $openid = $wxuser->openid;

        $helper->setBaseParams($openid, $config['amount']);

        $helper->setValue("nick_name", $config['nick_name']); // 提供方名称
        $helper->setValue("send_name", $config['send_name']); // 红包发送者名称
        $helper->setValue("wishing", $config['wishing']); // 红包祝福语
        $helper->setValue("act_name", $config['act_name']); // 活动名称
        $helper->setValue("remark", $config['remark']); // 备注信息

        $json = $helper->dowork();
        return $json;
    }

    // 需要改名字改调用点 TODO rework
    public function initWxRedbagOrder ($config, $returnJson) {
        $this->send_name = $config['send_name'];
        $this->wishing = $config['wishing'];
        $this->act_name = $config['act_name'];
        $this->remark = $config['remark'];

        $arr = array(
            'return_code',
            'return_msg',
            'result_code',
            'mch_billno',
            'mch_id',
            'wxappid',
            'send_time',
            'send_listid',
            'err_code',
            'err_code_des');

        foreach ($arr as $i => $value) {
            if (isset($returnJson[$value])) {
                $this->$value = $returnJson[$value];
            }
        }
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
