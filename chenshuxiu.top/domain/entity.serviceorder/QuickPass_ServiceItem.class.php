<?php

/*
 * QuickPass_ServiceItem
 */

class QuickPass_ServiceItem extends Entity
{

    /**
     * 工作时间
     *
     * 维护的时候记得am，pm，desc
     * am: 上午, pm: 下午, desc: 工作时间描述
     */
    const worktime = [
        'am' => [
            'start' => 1000,
            'end' => 1200,
            'start_desc' => '10:00',
            'end_desc' => '12:00',
        ],
        'pm' => [
            'start' => 1300,
            'end' => 1900,
            'start_desc' => '13:00',
            'end_desc' => '19:00',
        ],
        'desc' => '工作日(周一到周五，节假日除外)上午10:00-12:00，下午13:00-19:00',
    ];

    /**
     * 约定时间，约定1小时内处理
     */
    const appointedtime = 3600 * 1;

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid'    //wxuserid
        , 'userid'    //userid
        , 'patientid'    //patientid
        , 'serviceorderid'    //服务订单id
        , 'starttime'    //开始时间
        , 'endtime'    //结束时间
        , 'price'    //单价 = serviceproduct->price / item_cnt，单位分
        , 'status'    //状态: 0.无效 1.有效   PS：过期了也算有效，status仅用于判断是否有效，不和有效期关联；只有未支付和退订的为无效
        , 'refund_optaskid'    //退款的任务id
        , 'is_refund'    //是否退款
        , 'is_timeout'    //是否超时
        , 'time_refund'    //退款时间
        , 'remark'    //运营备注
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'wxuserid', 'userid', 'patientid', 'serviceorderid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array("type" => "WxUser", "key" => "wxuserid");
        $this->_belongtos["user"] = array("type" => "User", "key" => "userid");
        $this->_belongtos["patient"] = array("type" => "Patient", "key" => "patientid");
        $this->_belongtos["serviceorder"] = array("type" => "ServiceOrder", "key" => "serviceorderid");
        $this->_belongtos["refund_optask"] = array("type" => "Optask", "key" => "refund_optaskid");
    }

    // $row = array(); 
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["serviceorderid"] = $serviceorderid;
    // $row["starttime"] = $starttime;
    // $row["endtime"] = $endtime;
    // $row["price"] = $price;
    // $row["status"] = $status;
    // $row["refund_optaskid"] = $refund_optaskid;
    // $row["is_refund"] = $is_refund;
    // $row["is_timeout"] = $is_timeout;
    // $row["time_refund"] = $time_refund;
    // $row["remark"] = $remark;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "QuickPass_ServiceItem::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["serviceorderid"] = 0;
        $default["starttime"] = '';
        $default["endtime"] = '';
        $default["price"] = 0;
        $default["status"] = 0;
        $default["refund_optaskid"] = 0;
        $default["is_refund"] = 0;
        $default["is_timeout"] = 0;
        $default["time_refund"] = '0000-00-00 00:00:00';
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    /**
     * 是否为工作时间
     *
     * @param $date
     * @return bool
     */
    public static function isWorkTime($date = null) {
        // 非公时间流程

        if (empty($date)) {
            $hour_mintue_int = date('Hi');
        } else {
            $hour_mintue_int = date('Hi', strtotime($date));
        }
        $hour_mintue_int = intval($hour_mintue_int);

        $is_holiday = FUtil::isHoliday();
        Debug::trace($is_holiday ? '休息日' : '工作日');

        $am_begin = self::worktime['am']['start'];
        $am_end = self::worktime['am']['end'];
        $pm_begin = self::worktime['pm']['start'];
        $pm_end = self::worktime['pm']['end'];
        if (!$is_holiday &&                                             // 不是节假日
            (($hour_mintue_int >= $am_begin && $hour_mintue_int <= $am_end) ||   // 上午工作时间
                ($hour_mintue_int >= $pm_begin && $hour_mintue_int <= $pm_end))) {   // 下午工作时间
            Debug::trace('工作时间');
            return true;
        } else {
            Debug::trace('休息时间');
            return false;
        }
    }

    /**
     * 尝试支付 快速通行证
     *
     * @param ServiceOrder $serviceOrder
     */
    public static function tryPay(ServiceOrder $serviceOrder) {
        $serviceproduct = $serviceOrder->serviceproduct;

        $item_cnt = $serviceproduct->item_cnt;

        $price = $serviceproduct->price / $item_cnt;

        $remainder = $serviceproduct->price % $item_cnt;

        $starttime = date('Y-m-d H:i:s');

        // 如果有未过期的快速通行证，则以最后的一条记录的endtime作为starttime
        $quickpass_serviceitem = QuickPass_ServiceItemDao::getLastValidOneByPatientid($serviceOrder->patientid);
        if ($quickpass_serviceitem instanceof QuickPass_ServiceItem && $quickpass_serviceitem->isValidityPeriod()) {
            $starttime = $quickpass_serviceitem->endtime;
        }

        $days = 31;

        for ($i = 0; $i < $item_cnt; $i++) {
            $endtime = date('Y-m-d H:i:s', strtotime("+{$days} day", strtotime($starttime)));

            $row = array();
            $row["wxuserid"] = $serviceOrder->wxuserid;
            $row["userid"] = $serviceOrder->userid;
            $row["patientid"] = $serviceOrder->patientid;
            $row["serviceorderid"] = $serviceOrder->id;

            $row["starttime"] = $starttime;
            $row["endtime"] = $endtime;
            $row["price"] = $price;
            $row["status"] = 1;
            $quickpass_serviceitem = QuickPass_ServiceItem::createByBiz($row);

            $starttime = $endtime;
        }

        // 除不开的补到最后一个月上。
        $quickpass_serviceitem->price += $remainder;

        // #5658 患者升级为VIP等级
        $serviceOrder->patient->level = PatientLevel::LEVEL_400;

        // 发送开通成功消息
        $content = "你好，你已成功开通『快速通行证』服务。\n\n我们保证在快速通行证服务有效期内，你的咨询能在1小时内得到回复。\n\n如有其他问题，请通过微信与我们交流。";
        PushMsgService::sendTxtMsgToWxUserBySystem($serviceOrder->wxuser, $content);

        // 给运营发送消息
        $content = "Price[{$serviceOrder->amount}]Patient[{$serviceOrder->patient->name}]Doctor[{$serviceOrder->patient->doctor->name}]成功支付{$serviceOrder->serviceproduct_type}订单";
        PushMsgService::sendMsgToAuditorWithEnameBySystem('QuickPass_ServiceOrder', $content);
    }

    /**
     * 全部退款
     */
    public static function refundAllItem() {
        // TODO 待实现

    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    /**
     * 金额 (元), 包括快递费
     */
    public function getPrice_yuan() {
        return sprintf("%.2f", $this->price / 100);
    }

    /**
     * 超时退款
     */
    public function timeoutRefund() {
        $this->serviceorder->refund($this->price, '超时退款');
        $this->time_refund = XDateTime::now();

        // 给患者发消息
        $content = "很抱歉。由于当前咨询人数较多，未能在1小时内处理你的咨询。为表达歉意，本月的『快速通行证』费用将由我们承担，本月你可以免费享受『快速通行证』服务。24小时内，我们会将本月的『快速通行证』费用全部退还给。请及时查收。如有其他问题，可以继续通过微信与我们交流。";
        PushMsgService::sendTxtMsgToWxUserBySystem($this->wxuser, $content);
    }

    /**
     * 重复购买退款
     */
    public function repeatBuyRefund() {
        $this->is_timeout = 0;
        $this->is_refund = 1;
        $this->serviceorder->refund($this->price, '重复购买退款');
        $this->time_refund = XDateTime::now();
    }

    /**
     * 在有效期内且有效
     *
     * @return bool
     */
    public function isValidityPeriod() {
        if ($this->status == 1) { // 有效
            $time = time();
            $endtime = strtotime($this->endtime);

            if ($time < $endtime) { // 在有效范围内
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * 标记超时
     */
    public function setTimeout($refund_optaskid) {
        // 超过一小时，标记超时，由脚本进行实际退款
        $this->is_timeout = 1;
        $this->is_refund = 1;
        $this->refund_optaskid = $refund_optaskid;
    }

    /**
     * 取消标记超时
     *
     * @return bool
     */
    public function cancelTimeout() {
        if ($this->isRefund()) {
            return false;
        }

        $this->is_timeout = 0;
        $this->is_refund = 0;
        $this->refund_optaskid = 0;

        return true;
    }

    /**
     * 是否已经退过款
     */
    public function isRefund() {
        // 标记退款，并且有退款时间
        if ($this->is_refund == 1 && $this->time_refund != '0000-00-00 00:00:00') {
            return true;
        }
        return false;
    }
}
