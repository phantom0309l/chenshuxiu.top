<?php
/*
 * WxOpMsg, 医助和医生,围绕患者的沟通消息
 */
class WxOpMsg extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // doctorid
            'auditorid',  // 0=医生发言, >0 运营发言
            'objtype',  // objtype
            'objid',  // objid
            'title',  // 标题,可空
            'content',  // 文本内容
            'isnew',  // 飘new
            'status'); // 状态
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'auditorid');
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
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["auditorid"] = $auditorid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["title"] = $title;
    // $row["content"] = $content;
    // $row["isnew"] = $isnew;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "WxOpMsg::createByBiz row cannot empty");

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
        $default["auditorid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["title"] = '';
        $default["content"] = '';
        $default["isnew"] = 0;
        $default["status"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getSendByWho () {
        $str = "";
        if ($this->auditorid > 0) {
            $str = "医助({$this->auditor->name})";
        } else {
            $str = "医生";
        }

        return $str;
    }

    public function getSendToWho () {
        $str = "";
        if ($this->auditorid > 0) {
            $str = "医生";
        } else {
            $str = "医助({$this->auditor->name})";
        }
        return $str;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // todo此处传入doctor实体，运营人员多的时候，可通过doctorid得到对应的运营人员openid，
    // 将消息传给特定的运营人员 by lijie
    public static function sendMsgtoAuditorOfDoctor (Entity $patient, Entity $mydoctor, Entity $wxopmsg, $content) {
        // 陈萍的方寸课堂openid
        $openid = "oiOZEw1HH4p7fTnItzUA-QS-Ays8";
        $wxuser = WxUserDao::getByOpenid($openid);

        $msg = $mydoctor->name . "医生答复：" . $content . "（" . $patient->name . "患者）";

        $appendarr = array(
            "patientid" => $patient->id,
            "objtype" => "WxOpMsg",
            "objid" => $wxopmsg->id);

        return PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $msg, $appendarr);
    }
}
