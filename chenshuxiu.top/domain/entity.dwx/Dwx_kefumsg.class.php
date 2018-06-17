<?php
/*
 * Dwx_kefumsg
 */
class Dwx_kefumsg extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'doctorid',  // doctorid
            'assistantid',  // assistantid
            'relate_patientid',  // 内容相关的patientid , 备用
            'auditorid',  // auditorid 发送人
            'objtype',  // 来源type
            'objid',  // 来源id
            'template_name',  // 内部模板名称, wx_templete_id 也存在这里
            'content',  // 文本内容
            'dest_url',  // 跳转链接
            'send_status',  // 发送状态: 0 待发送, 1 发送中 , 2 发送成功 , -1 发送失败
            'send_by_way',  // 发送途径
            'send_response_code',  // 发送结果code
            'send_response_str'); // 发送结果文本

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'doctorid',
            'assistantid',
            'relate_patientid',
            'auditorid',
            'objtype',
            'objid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["assistant"] = array(
            "type" => "Assistant",
            "key" => "assistantid");
        $this->_belongtos["relate_patient"] = array(
            "type" => "Patient",
            "key" => "relate_patientid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["doctorid"] = $doctorid;
    // $row["assistantid"] = $assistantid;
    // $row["relate_patientid"] = $relate_patientid;
    // $row["auditorid"] = $auditorid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["template_name"] = $template_name;
    // $row["content"] = $content;
    // $row["send_status"] = $send_status;
    // $row["send_by_way"] = $send_by_way;
    // $row["send_response_code"] = $send_response_code;
    // $row["send_response_str"] = $send_response_str;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Dwx_kefumsg::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["doctorid"] = 0;
        $default["assistantid"] = 0;
        $default["relate_patientid"] = 0;
        $default["auditorid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["template_name"] = '';
        $default["content"] = '';
        $default["dest_url"] = '';
        $default["send_status"] = 0;
        $default["send_by_way"] = '';
        $default["send_response_code"] = '';
        $default["send_response_str"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getInsertSqlsFix () {
        Debug::trace('Dwx_kefumsg::getInsertSqlsFix 1');

        // gearman 异步推送
        $job = Job::getInstance();

        Debug::trace('Dwx_kefumsg::getInsertSqlsFix 2');

        $job->doBackground('send_dwx_kefumsg', $this->id);

        Debug::trace('Dwx_kefumsg::getInsertSqlsFix 3');

        return array();
    }

    public function getContentFix () {
        $content = $this->content;
        if ($this->template_name) {
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
    }

    // cron 发送消息
    public function sendByCron () {
        $wxshop = $this->wxuser->wxshop;
        $openid = $this->wxuser->openid;
        $content = $this->content;

        // 模板id
        $template_id = $this->template_name;
        $url = $this->dest_url;
        $data = json_decode($content, true);

        $title = $this->doctor->name . "医生随访团队";

        // echo " [wxshopid={$this->wxuser->wxshopid}] ";
        if( '' === $template_id ){
            // echo " [wechat_custom] ";

            $content = "{$content}";

            $errcode = WxApi::kefuTextMsg($wxshop, $openid, addslashes($content));
            // echo " [{$errcode}] ";

            // 成功则退出
            if ($errcode === 0) {
                $this->setPhpSendResult('custom', 0, '');
                return true;
            } else {
                $templateEname = "auditor2doctor";
                $wxtemplate = WxTemplateDao::getByEname($this->wxuser->wxshopid, $templateEname);
                if ($wxtemplate instanceof WxTemplate) {
                    $first = array(
                        "value" => $content."\n ",
                        "color" => "#415a93");
                    $keywords = array(
                        array(
                            "value" => '方寸运营/医生助理',
                            "color" => "#aaa"),
                        array(
                            "value" => $this->doctor->hospital->name,
                            "color" => "#aaa"),
                        array(
                            "value" => "",
                            "color" => "#aaa"),
                        array(
                            "value" => $this->doctor->name,
                            "color" => "#aaa"),
                        array(
                            "value" => date('Y-m-d H:i:s'),
                            "color" => "#aaa"));
                } else {
                    // 模板消息不存在,失败退出
                    // echo "[wxtemplate is null]";
                    Debug::error(__METHOD__ . " wxtemplate is null, templateEname [$templateEname]");
                    return false;
                }

                $template_id = $wxtemplate->code;

                $data = WxTemplateService::createTemplateContent($first, $keywords);
            }
        }

        // echo " [template] ";

        // 发模板消息 或 客服消息转模板消息
        $errcode = WxApi::kefuTplMsg($wxshop, $openid, $template_id, $url, $data);

        // echo " [{$errcode}] ";

        $this->setPhpSendResult('template', $errcode, WxApi::$last_errmsg);

        return true;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
