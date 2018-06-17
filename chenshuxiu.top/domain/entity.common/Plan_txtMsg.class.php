<?php

/*
 * Plan_txtMsg
 */

class Plan_txtMsg extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid'    //wxuserid
        , 'userid'    //userid
        , 'patientid'    //patientid
        , 'auditorid'    //auditorid
        , 'objtype'    //来源type
        , 'objid'    //来源id
        , 'pushmsgid'    //pushmsgid
        , 'type'    //消息类型: 1 自动发送 , 2 手动发送 , 3 立即发送
        , 'code'    // 更细的类型
        , 'url'     // 消息url，可以为空
        , 'plan_send_time'    //计划发送时间
        , 'content'    //文本内容
        , 'remark'    //
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'wxuserid', 'userid', 'patientid', 'auditorid', 'objid', 'pushmsgid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array("type" => "WxUser", "key" => "wxuserid");
        $this->_belongtos["user"] = array("type" => "User", "key" => "userid");
        $this->_belongtos["patient"] = array("type" => "Patient", "key" => "patientid");
        $this->_belongtos["auditor"] = array("type" => "Auditor", "key" => "auditorid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
        $this->_belongtos["pushmsg"] = array("type" => "PushMsg", "key" => "pushmsgid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["auditorid"] = $auditorid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["pushmsgid"] = $pushmsgid;
    // $row["type"] = $type;
    // $row["code"] = $code;
    // $row["url"] = $url;
    // $row["plan_send_time"] = $plan_send_time;
    // $row["content"] = $content;
    // $row["remark"] = $remark;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "Plan_txtMsg::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["auditorid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["pushmsgid"] = 0;
        $default["type"] = 1;
        $default["code"] = '';
        $default["url"] = '';
        $default["plan_send_time"] = '0000-00-00 00:00:00';
        $default["content"] = '';
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    public static function getTypes() {
        return [
            1 => '自动发送',
            2 => '手动发送',
            3 => '立即发送',
        ];
    }

    public static function getTypesOfShort() {
        return [
            1 => '自动',
            2 => '手动',
            3 => '立即',
        ];
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getTypeDesc() {
        return self::getTypes()[$this->type];
    }

    public function getTypesDescOfShort() {
        return self::getTypesOfShort()[$this->type];
    }

    public function send(Auditor $auditor) {
        $pcard = $this->patient->getMasterPcard();
        $send_flag = true;

        // 发送之前的检查
        if (method_exists($this->obj, 'send_before')) {
            $send_flag = $this->obj->send_before();
        }

        // 6037 倍泰龙组才发
        if ($this->code == 'nmo_btl' && $this->patient->patientgroupid != PatientGroup::beitailongid) {
            $send_flag = false;

            // 删除未发送的定时消息
            $plan_txtmsgs = Plan_txtMsgDao::getUnsentListByPatientidCode($this->patientid, 'nmo_btl');
            foreach ($plan_txtmsgs as $plan_txtmsg) {
                $plan_txtmsg->remove();
            }
        }

        if ($send_flag) {
            if ($this->code == 'nmo_btl') {
                $pushmsg = $this->sendNMOBetaseron();
            } else {
                if ($this->url) {
                    $pushmsg = $this->sendUrl();
                } else {
                    $pushmsg = PushMsgService::sendTxtMsgToWxUsersOfPcardByAuditor($pcard, $auditor, $this->content);
                }
            }

            if ($pushmsg instanceof PushMsg) {
                $this->fixPushMsgId($pushmsg);

                // 发送之后的回调
                if (method_exists($this->obj, 'send_callback')) {
                    $this->obj->send_callback();
                }
            } else {
                Debug::warn('定时消息发送失败 patientid：' . $this->patientid . ' Plan_txtMsgid：' . $this->id);
            }
        }
    }

    // 6037
    public function sendNMOBetaseron () {
        $papertpl = PaperTpl::getById(599386386);
        $patient = $this->patient;
        $pushmsg = null;

        if ($papertpl instanceof PaperTpl) {
            $wx_uri = Config::getConfig("wx_uri");
            $url = "{$wx_uri}/paper/wenzhen/?papertplid={$papertpl->id}";

            $first = array(
                "value" => "药物治疗满意度评分",
                "color" => "#ff6600");
            $keywords = array(
                array(
                    "value" => $patient->name,
                    "color" => "#aaa"),
                array(
                    "value" => date("Y-m-d H:i:s"),
                    "color" => "#aaa"),
                array(
                    "value" => '您好，注射倍泰龙期间医生需要关注您的使用情况以及药品疗效，故注射期间您需要每个月填写一次【药物治疗满意度评分】量表，以便医生关注您的治疗情况。',
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            $pushmsg = PushMsgService::sendTplMsgToPatientBySystem($patient, 'followupNotice', $content, $url);

            // 6037 NMO『倍泰龙组』的患者每月发送一次『药物治疗满意度评分』量表。
            $row = [];
            $row["patientid"] = $patient->id;
            $row["auditorid"] = 1;
            $row["objtype"] = 'Patient';
            $row["objid"] = $patient->id;
            $row["type"] = 1;
            $row["code"] = 'nmo_btl';
            $row["plan_send_time"] = date('Y-m-d', strtotime("+1 months", strtotime($this->plan_send_time)));
            Plan_txtMsg::createByBiz($row);
        }

        return $pushmsg;
    }

    public function sendUrl () {
        $wx_uri = Config::getConfig("wx_uri");
        $url = "{$wx_uri}{$this->url}";

        $first = array(
            "value" => $this->content,
            "color" => "#ff6600");
        $keywords = array(
            array(
                "value" => "{$this->patient->doctor->name}医生随访团队",
                "color" => "#999999"),
            array(
                "value" => "{$this->patient->name}您好，请点击详情填写问卷，以方便医生更好地管理。",
                "color" => "#ff6600"));

        $content = WxTemplateService::createTemplateContent($first, $keywords);

        return PushMsgService::sendTplMsgToPatientBySystem($this->patient, 'doctornotice', $content, $url);
    }

    public function fixPushMsgId($pushmsg) {
        if ($pushmsg instanceof PushMsg) {
            $this->set4lock('pushmsgid', $pushmsg->id);
        }
    }

}
