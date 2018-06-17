<?php
// RefundOrder
// 原路退款单

// owner by sjp
// create by sjp
// review by sjp 20160629
class RefundOrder extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'userid',  // 发起者并受益者userid
            'patientwithdraworderid',  // 关联单据id
            'depositeorderid',  // 关联单据id
            'amount',  // 金额,分
            'status',  // 退款状态 0 等待 1 成功
            'return_success_time',  // 向微信发送请求的返回时间
            'return_result_code',  // 向微信发送请求的返回结果
            'last_call_time',  // 最后调用该退款的时间
            'call_last_errcode',  // 末次错误码
            'call_cnt',  // 调用次数
            'remark'); // 订单备注
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'userid',
            'patientwithdraworderid',
            'depositeorderid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["patientwithdraworder"] = array(
            "type" => "PatientWithdrawOrder",
            "key" => "patientwithdraworderid");
        $this->_belongtos["depositeorder"] = array(
            "type" => "DepositeOrder",
            "key" => "depositeorderid");
    }

    // $row = array();
    // $row["userid"] = $userid;
    // $row["patientwithdraworderid"] = $patientwithdraworderid;
    // $row["depositeorderid"] = $depositeorderid;
    // $row["amount"] = $amount;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Refundorder::createByBiz row cannot empty");

        $default = array();
        $default["userid"] = 0;
        $default["patientwithdraworderid"] = 0;
        $default["depositeorderid"] = 0;
        $default["amount"] = '';
        $default["status"] = 0;
        $default["return_success_time"] = '0000-00-00 00:00:00';
        $default["return_result_code"] = '';
        $default["last_call_time"] = '0000-00-00 00:00:00';
        $default["call_last_errcode"] = '';
        $default["call_cnt"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // 由提现单生成一组退款单
    public static function createListByUseridAmountPatientWithdrawOrderid ($userid, $amount, $patientwithdraworderid) {
        $arr = DepositeOrderDao::getArrayRefundableByUseridAmount($userid, $amount);
        return self::createByDepositeOrderAmountArrPatientWithdrawOrderid($arr, $patientwithdraworderid);
    }

    // 计算所有充值单和金额生成退款单
    private static function createByDepositeOrderAmountArrPatientWithdrawOrderid ($arr, $patientwithdraworderid) {
        $tmp = array();
        foreach ($arr as $a) {
            $depositeorder = $a[0];
            $amount = $a[1];
            $row = array();
            $row["userid"] = $depositeorder->userid;
            $row["amount"] = $amount;
            $row["depositeorderid"] = $depositeorder->id;
            $row["patientwithdraworderid"] = $patientwithdraworderid;
            array_push($tmp, self::createByBiz($row));
        }
        return $tmp;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 通过微信接口原路退款
    public function process () {
        $depositeOrder = $this->depositeorder;

        $input = new WxPayRefund();
        $input->SetTransaction_id($depositeOrder->pay_platform_trade_no);
        $input->SetOut_trade_no($depositeOrder->fangcun_trade_no);
        $input->SetTotal_fee($depositeOrder->amount);
        $input->SetRefund_fee($this->amount);
        $input->SetOut_refund_no($this->id);

        $pay_wxshopid = $depositeOrder->pay_wxshopid;

        if($pay_wxshopid > 0){
            $wxshop = WxShop::getById($pay_wxshopid);
            Debug::trace("=========[refund by pay_wxshopid]==============");
        }else{
            //在2018-01-18 23:27 切换了商户平台由 1248427301 => 1497250822
            //在2018-01-20 19:20 方寸儿童管理服务平台商户平台又切换回了 1248427301
            $the_time = "2018-01-18 23:27:00";

            $the_time1 = "2018-01-20 19:00:00";
            //obj 现在有ShopOrder CallOrder
            $obj = $depositeOrder->obj;
            $time_pay = $obj->time_pay;
            if($time_pay < $the_time){
                $wxshop = WxShop::getById(WxShop::WxShopId_ADHD);
            }else{
                if($time_pay < $the_time1){
                    $wxshop = WxShop::getById(WxShop::WxShopId_Cancer);
                }else{
                    $patient = $obj->patient;
                    $diseaseGroup = $patient->disease->diseasegroup;
                    if(3 == $diseaseGroup->id){
                        $wxshop = WxShop::getById(WxShop::WxShopId_Cancer);
                    }else{
                        $wxshop = WxShop::getById(WxShop::WxShopId_ADHD);
                    }
                }
            }
            Debug::trace("=========[refund by old]==============");
        }

        $wxshop->initWxPayConfig();
        $input->SetOp_user_id(WxPayConfig::getMCHID());

        $data = WxPayApi::refund($input);

        if (! $this->status) {
            $valid_return = ($data["return_code"] == 'SUCCESS');

            if ($valid_return && $data["result_code"] == "SUCCESS") {
                $this->return_success_time = date("Y-m-d H:i:s");
                $this->return_result_code = $data["result_code"];
                $this->status = 1;
            } else {
                $this->return_result_code = $valid_return ? $data["err_code"] : $data["return_msg"];
            }
        }

        $this->call_cnt += 1;
        $this->last_call_time = date("Y-m-d H:i:s");
        return (bool) $this->status;
    }
}
