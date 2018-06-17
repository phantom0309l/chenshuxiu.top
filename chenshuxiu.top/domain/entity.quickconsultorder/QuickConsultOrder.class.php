<?php

/*
 * QuickConsultOrder
 */

class QuickConsultOrder extends Entity implements PayHandle
{
    const price_ratio = 100;

    /**
     * 工作时间
     *
     * 维护的时候记得am，pm，desc，rest_desc
     * am: 上午, pm: 下午, desc: 工作时间描述, rest_desc: 休息时间描述
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
        'rest_desc' => '很抱歉，医生团队的快速咨询时间为工作日(周一到周五，节假日除外)上午10:00-12:00，下午13:00-19:00，请在该时间段内发起快速咨询。',
    ];

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid'    //wxuserid
        , 'userid'    //userid
        , 'patientid'    //patientid
        , 'diseaseid'    //diseaseid
        , 'auditorid'    //auditorid
        , 'content'    //咨询内容
        , 'interactive_mode'    //交流方式 wechat：微信，sms：短信，phone：电话，wechat_phone：微信+电话，other：其他
        , 'amount'    //订单总金额, 单位分
        , 'status'    //状态 0：已取消，1：初始化，2：待支付，3：已支付，4：已接单，5：已完成
        , 'is_pay'    //已经支付
        , 'is_refund'    //已经退款
        , 'is_timeout'    //是否超时
        , 'time_submit'    //下单时间
        , 'time_pay'    //支付时间
        , 'time_accept'    //接单时间
        , 'time_finished'    //完成时间
        , 'time_refund'    //退款时间
        , 'patient_remark'    //患者评价
        , 'remark'    //运营备注
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'wxuserid', 'userid', 'patientid', 'diseaseid');
    }

    protected function init_belongtos() {
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
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["diseaseid"] = $diseaseid;
    // $row["auditorid"] = $auditorid;
    // $row["content"] = $content;
    // $row["interactive_mode"] = $interactive_mode;
    // $row["amount"] = $amount;
    // $row["status"] = $status;
    // $row["is_pay"] = $is_pay;
    // $row["is_refund"] = $is_refund;
    // $row["is_timeout"] = $is_timeout;
    // $row["time_submit"] = $time_submit;
    // $row["time_pay"] = $time_pay;
    // $row["time_accept"] = $time_accept;
    // $row["time_finished"] = $time_finished;
    // $row["time_refund"] = $time_refund;
    // $row["patient_remark"] = $patient_remark;
    // $row["remark"] = $remark;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "QuickConsultOrder::createByBiz row cannot empty");

        $diseaseid = $row['diseaseid'] ?? 0;
        $amount = self::getPrice($diseaseid) ?? 0;

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["diseaseid"] = $diseaseid;
        $default["auditorid"] = 0;
        $default["content"] = '';
        $default["interactive_mode"] = '';
        $default["amount"] = $amount;
        $default["status"] = 1;
        $default["is_pay"] = 0;
        $default["is_refund"] = 0;
        $default["is_timeout"] = 0;
        $default["time_submit"] = '';
        $default["time_pay"] = '';
        $default["time_accept"] = '';
        $default["time_finished"] = '';
        $default["time_refund"] = '';
        $default["patient_remark"] = '';
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    /**
     * 是否为工作时间
     * @return bool
     */
    public static function isWorkTime() {
        // 非公时间流程
        $hour_mintue_int = date('Hi');
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
     * 根据疾病获取价格
     *
     * @param $diseaseid
     * @return int
     */
    public static function getPrice($diseaseid) {
        $price = self::price_ratio;

        if (Disease::isCancer($diseaseid)) {    // 肿瘤
            $price *= 50;
        } else {
            $price *= 50;
        }

        return $price;
    }

    /**
     * 获取所有交流方式
     */
    public static function getInteractiveModes() {
        return [
            'wechat' => '微信',
            'sms' => '短信',
            'phone' => '电话',
            'wechat_phone' => '微信+电话',
            'other' => '其他',
        ];
    }

    /**
     * 全部状态
     *
     * @param bool $needAll
     * @return array
     */
    public static function getAllStatus($needAll = false) {
        $arr = [];
        if ($needAll) {
            $arr['all'] = '全部';
        }

        $arr['1'] = '仅浏览';
        $arr['2'] = '已下单，待支付';
        $arr['3'] = '已支付，待处理';
        $arr['4'] = '处理中';
        $arr['5'] = '已完成';
//        $arr['0'] = '已取消';
        return $arr;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    /**
     * 重新计算价格
     */
    public function reCalcAmount() {
        $this->amount = self::getPrice($this->diseaseid);
    }

    /**
     * 获取所有的交流方式
     *
     * @return array
     */
    public function getInteractiveModeDesc() {
        return self::getInteractiveModes()[$this->interactive_mode];
    }

    /**
     * 当前状态
     *
     * @return string
     */
    public function getStatusStr() {
        switch ($this->status) {
            case 0:
                return '已取消';
            case 1:
                return '待提交';
            case 2:
                return '待支付';
            case 3:
                return '已支付';
            case 4:
                return '处理中';
            case 5:
                return '已完成';
        }
    }

    //尝试支付
    public function tryPay(Account $rmbAccount) {
        // 尚未支付, 去支付
        if (0 == $this->is_pay || 3 == $this->status) {
            if ($rmbAccount->balance >= $this->amount) {
                $sysAccount = Account::getSysAccount('sys_user_shop_out');

                $doctor = $this->patient->doctor;
                $patient = $this->patient;

                $rmbAccount->transto($sysAccount, $this->amount, $this, 'pay', "快速咨询订单[{$this->id}]支付");

                // 改状态
                $this->status = 3;
                $this->is_pay = 1;
                $this->time_pay = XDateTime::now();
                $this->remark = "Price[{$this->amount}]Patient[{$patient->name}]Doctor[{$doctor->name}]成功支付快速咨询订单";

                $ename = 'QuickConsultOrder';

                // 给运营发消息
                $content = '亲爱的医生助理，' . $doctor->name . '医生的患者' . $patient->name . '，于' . date('Y年m月d日 H:i') . '发起了一条快速咨询，请在10分钟内尽快处理，辛苦了。';
                PushMsgService::sendMsgToAuditorWithEnameBySystem($ename, $content);

                $userids = WebSocketService::getUseridsByEnameOfAuditorPushMsgTpl($ename);

                $title = "快速咨询";
                $body = "『快速咨询』{$doctor->name}医生的患者{$patient->name}，发起了一条快速咨询，请在10分钟内尽快处理，辛苦了。";
                $tag = "quickconsult_" . $patient->id;
                $data = [
                    // 运营任务【快速咨询】，临时先用这个地址吧。
                    'url' => Config::getConfig('audit_uri') . '/optaskmgr/listnew?optaskfilterid=572368876'
                ];
                $tpl = WebSocketService::getNotificationTpl($title, $body, $tag, $data);
                WebSocketService::push('wsquickconsult', 'pushMessage', $tpl, $userids);

                // 创建支付成功流
                $pipe = Pipe::createByEntity($this, 'pay', $this->wxuserid);

                // 创建快速咨询任务
                $appendArr = ['level' => 9, 'level_remark' => '快速咨询'];
                OpTaskService::createPatientOpTask($patient, 'order:QuickConsultOrder', $this, '', 1, $appendArr);

                Debug::warn("成功支付快速咨询订单 QuickConsultOrder[{$this->id}]->amount = {$this->amount}, rmbAccount->balance = {$rmbAccount->balance} ");
            } else {
                Debug::warn("QuickConsultOrder[{$this->id}]支付失败, 余额不足, {$rmbAccount->balance} < {$this->amount}");
            }
        } else {
            Debug::warn("QuickConsultOrder[{$this->id}]已支付了, 不用再支付了");
        }
    }

    public function getWxPayUnifiedOrder_Body () {
        $str = "订单ID" . $this->id;
        return $str;
    }

    public function getWxPayUnifiedOrder_Attach () {
        return "使用了快速咨询服务";
    }

    public function getPayAmount () {
        return $this->amount;
    }

    /**
     * 剩余时间
     */
    public function getTimeRemaining() {
        $time_pay = strtotime($this->time_pay);
        $time_now = time();

        Debug::trace($time_pay);
        Debug::trace($time_now);

        $time = 60 * 10 - ($time_now - $time_pay);
        Debug::trace($time);
        if ($time < 0) {
            return 0;
        }

        return $time;
    }

    /**
     * 是否超时
     *
     * @return bool
     */
    public function isTimeout() {
        return $this->is_timeout == 1 ? true : false;
    }

    /**
     * 超时
     */
    public function timeout() {
        $this->is_timeout = 1;
    }

    /**
     * 运营接单
     */
    public function accept($auditorid) {
        $this->status = 4;
        $this->time_accept = XDateTime::now();

        $this->auditorid = $auditorid;
    }

    /**
     * 完成
     */
    public function finish() {
        $this->status = 5;
        $this->time_finished = XDateTime::now();
    }

    /**
     * 退款至原支付账户, 已支付状态
     *
     * @return bool
     */
    public function refund() {
        // 未支付或已退款
        if (!$this->is_pay || $this->is_refund) {
            return false;
        }

        $remark = "快速咨询[{$this->id}]退款至余额";

        $sysAccount = Account::getSysAccount('sys_user_shop_out');
        $userRmbAccount = $this->user->getAccount('user_rmb');

        $code = 'refund';

        $sysAccount->transto($userRmbAccount, $this->amount, $this, $code, $remark);

        // 根据提现单生成退款单
        OrderService::processAccountWithdrawRefund($userRmbAccount, Auditor::getSystemAuditor());

        $this->is_refund = 1;
        $this->time_refund = XDateTime::now();

        return true;
    }

    public function getRefundAccountTransCnt() {
        $sql = "select count(*) from accounttranss where objtype='QuickConsultOrder' and objid=:objid and code like 'refund%';";
        $bind = [];
        $bind[':objid'] = $this->id;
        return 0 + Dao::queryValue($sql, $bind);
    }

    /**
     * 获取价格，保留两位小数
     *
     * @return string
     */
    public function getAmountDesc() {
        return sprintf("%.2f", $this->amount / 100);
    }

    /**
     * 获取关联的图片
     *
     * @return array
     */
    public function getBasicPictures() {
        return BasicPictureDao::getListByObj($this);
    }

}
