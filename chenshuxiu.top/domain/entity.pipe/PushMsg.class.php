<?php

/*
 * PushMsg
 */
class PushMsg extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    // sms 11604
    // wechat_custom 83086
    // wechat_custom->sms 606
    // wechat_template 54147
    // wx|sms 3757
    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // doctorid 发送消息时的医生id :
                        // 逐个字段的含义需要修订
            'objtype',  //
            'objid',  //
            'sendway',  // 发送方式：1 sms, 2 wx, 3 wx_templete, 4 wx|sms
            'template_name',  // 内部模板名称 , wx_template_id 也存在这里
            'content',  // 文本内容
            'remark',  //
            'send_status',  // 发送状态: 0 待发送, 1 发送中 , 2 发送成功 , -1 发送失败
            'send_by_way',  // 发送途径
            'send_by_objtype',  // 发送人
            'send_by_objid',  // 发送人id
            'send_response_code',  // 推送结果code
            'send_response_str',  // 推送结果文本
            'is_monitor_msg'); // 是否监控消息
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid');
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
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["sendbyobj"] = array(
            "type" => $this->send_by_objtype,
            "key" => "send_by_objid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["sendway"] = $sendway;
    // $row["template_name"] = $template_name;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PushMsg::createByBiz row cannot empty");

        if ($row["wxuserid"] == null) {
            $row["wxuserid"] = 0;
        }

        if ($row["userid"] == null) {
            $row["userid"] = 0;
        }

        if ($row["patientid"] == null) {
            $row["patientid"] = 0;
        }

        if ($row["doctorid"] == null) {
            $row["doctorid"] = 0;
        }

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["objtype"] = "";
        $default["objid"] = 0;
        $default["sendway"] = 'wechat_custom';
        $default["template_name"] = '';
        $default["content"] = '';
        $default["remark"] = "";

        $default["send_status"] = 0;
        $default["send_by_way"] = '';
        $default["send_by_objtype"] = '';
        $default["send_by_objid"] = 0;
        $default["send_response_code"] = '';
        $default["send_response_str"] = '';
        $default["is_monitor_msg"] = 0;

        $row += $default;

        $patient = Patient::getById($row["patientid"]);
        if ($patient instanceof Patient) {
            // 如果患者死亡，则不发送任务消息
            if ($patient->is_live == 0) {
                Debug::trace(">>>>>>>>>>>>>>>>>>>> 如果患者死亡，则不发送任务消息");
                return null;
            }
        }

        $entity = new self($row);
        return $entity;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getInsertSqlsFix () {
        Debug::trace('PushMsg::getInsertSqlsFix 1');

        // gearman 异步推送
        $job = Job::getInstance();

        Debug::trace('PushMsg::getInsertSqlsFix 2');

        $job->doBackground('send_push_msg', $this->id);

        Debug::trace('PushMsg::getInsertSqlsFix 3');

        return array();
    }

    public function getContentFix () {
        $content = $this->content;
        if ($this->sendway == 'wechat_template') {
            $content = $this->getTemplateContent($this->template_name, $content);
        }
        return $content;
    }

    public function getTemplateContent ($code, $content) {
        $wxtemplate = WxTemplateDao::getByCode($code);
        if (empty($wxtemplate)) {
            return "";
        }
        $showkey = $wxtemplate->showkey;
        $title = $wxtemplate->title;
        $content = json_decode($content, true);

        $text = $title . ' | ';
        foreach ($content as $k => $v) {
            if ($v['value']) {
                $text .= $v['value'] . ' | ';
            }
        }
        return $text;
    }

    public function getByWho () {
        $sendByObj = $this->sendbyobj;

        $who = "";

        if ($sendByObj instanceof Auditor) {
            if ($sendByObj->id == 1) {
                $who = "系统";
            } else {
                $who = "医助" . $sendByObj->name;
            }
            return $who;
        }

        if ($sendByObj instanceof Doctor) {
            $who = $sendByObj->name;
            return $who;
        }

        return $who;
    }

    public function getCodeForPipe () {
        $code = "create";
        if ($this->send_by_objtype == 'Doctor') {
            $code = "byDoctor";
        }
        if ($this->send_by_objtype == 'Auditor' && $this->send_by_objid > 1) {
            $code = "byAuditor";
        }
        if ($this->send_by_objtype == 'Auditor' && $this->send_by_objid == 1) {
            $code = "bySystem";
        }
        return $code;
    }

    // 保存发送消息结果
    public function setPhpSendResult ($send_by_way, $send_response_code, $send_response_str = '') {
        $this->send_by_way = $send_by_way;
        $this->send_response_code = $send_response_code;
        $this->send_response_str = $send_response_str;

        if ($send_response_code === 0) {
            $this->send_status = 2;
        } else {
            $this->send_status = - 1;
        }

        $log = __METHOD__;
        $log .= " send_by_way [{$send_by_way}] send_response_code [{$send_response_code}] send_response_str [{$send_response_str}] send_status [{$this->send_status}]";

        Debug::trace($log);

        if ($this->is_monitor_msg && 2 == $this->send_status) {
            Debug::trace("-- delete PushMsg ({$this->id})");

            // 监控消息, 脚本调用, 不用记录 xunitofwork
            Debug::$xunitofwork_create_close = true;

            $this->remove();
            $pipes = PipeDao::getListByEntity($this);
            foreach ($pipes as $a) {
                Debug::trace("-- delete Pipe ({$a->id})");
                $a->remove();
            }
        }
    }

    // cron 发送消息
    public function sendByCron () {
        $wxuser = $this->wxuser;
        if (false == $wxuser instanceof WxUser) {
            return false;
        }

        $wxshop = $wxuser->wxshop;
        if (false == $wxshop instanceof WxShop) {
            return false;
        }

        $patient = $wxuser->user->patient;
        if ($patient instanceof Patient) {
            // 如果已死亡，直接退出
            if ($patient->is_live == 0) {
                return false;
            }
        }

        $openid = $wxuser->openid;
        $content = $this->content;

        // 模板id
        $template_id = $this->template_name;
        $url = $this->remark;
        $data = json_decode($content, true);

        if ($this->sendbyobj instanceof Doctor) {
            $title = $this->sendbyobj->name . "医生";
        } else {
            if ($patient instanceof Patient) {
                $title = $patient->doctor->name . "医生随访团队";
            } else {
                $title = "医生随访团队";
            }
        }

        // 先做客服消息发送
        if (in_array($this->sendway, array(
            'wechat_custom',
            'wechat_custom->sms'))) {

            $content = "{$content}";

            $errcode = WxApi::kefuTextMsg($wxshop, $openid, addslashes($content));

            // 成功则退出
            if ($errcode === 0) {
                $this->setPhpSendResult('custom', 0, '');
                return true;
            } else {
                $wxtemplate = $wxshop->getWxTemplateOfAdminNoticeOrFollowupNotice();
                if(false == $wxtemplate instanceof WxTemplate){
                    Debug::warn(__METHOD__ . "wxshopid[{$wxshop->id}]没有找到adminNotice或者followupNotice的模板");
                    return false;
                }
                $template_id = $wxtemplate->code;
                $data = $wxtemplate->getContentOfAdminNoticeOrFollowupNotice($patient, $title, $content);
            }
        }

        if ("" == $url && mb_strlen($content) > 188) {
            $url = Config::getConfig('wx_uri') . "/common/tplmsgdetail?pushmsgid={$this->id}&template_id={$template_id}";
        }

        // 发模板消息 或 客服消息转模板消息
        $errcode = WxApi::kefuTplMsg($wxshop, $openid, $template_id, $url, $data);

        $this->setPhpSendResult('template', $errcode, WxApi::$last_errmsg);

        return true;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // 定义一下
    public static function getSendWays () {
        $arr = array();
        $arr['wx'] = 'wx';
        $arr['sms'] = 'sms';
        $arr['wx_template'] = 'wx_template';
        $arr['wx|sms'] = 'wx|sms';

        return $arr;
    }
}
