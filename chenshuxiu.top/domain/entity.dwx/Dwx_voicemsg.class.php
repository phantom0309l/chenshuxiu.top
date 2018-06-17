<?php
/*
 * Dwx_voicemsg
 */
class Dwx_voicemsg extends Entity
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
            'relate_patientid',  // 内容相关的patientid, 备用
            'objtype',  // objtype, 关联对象, 备用
            'objid',  // objid, 关联对象, 备用
            'voiceid',  // voiceid
            'downloadurl',  // 下载地址
            'remark'); // 音频备注

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'doctorid',
            'assistantid',
            'relate_patientid',
            'objtype',
            'objid',
            'voiceid');
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
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
        $this->_belongtos["voice"] = array(
            "type" => "Voice",
            "key" => "voiceid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["doctorid"] = $doctorid;
    // $row["assistantid"] = $assistantid;
    // $row["relate_patientid"] = $relate_patientid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["voiceid"] = $voiceid;
    // $row["downloadurl"] = $downloadurl;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Dwx_voicemsg::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["doctorid"] = 0;
        $default["assistantid"] = 0;
        $default["relate_patientid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["voiceid"] = 0;
        $default["downloadurl"] = '';
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    public static function createbyXmlObj ($xml) {
        $xmlArr = FUtil::xmlobj2array($xml);

        $wxuserid = 0;
        $userid = 0;
        $doctorid = 0;
        $assistantid = 0;
        $relate_patientid = 0; // 内容相关的患者id
        $objtype = ''; // 备用
        $objid = 0; // 备用

        $openid = $xmlArr['FromUserName'];
        $wxuser = WxUserDao::getByOpenid($openid);
        $user = $wxuser->user;

        if ($wxuser instanceof WxUser) {
            $wxuserid = $wxuser->id;
        }

        if ($user instanceof User) {
            $userid = $user->id;
            // 助理或医生
            if ($user->isAssistant()) {
                $assistant = $user->getAssistant();
                $doctor = $assistant->doctor;
            } else {
                $doctor = $user->getDoctor();
            }

            $doctorid = $doctor ? $doctor->id : 0;
            $assistantid = $assistant ? $assistant->id : 0;
        }

        // 抓取下载地址
        $wxshop = WxShop::getById(2);
        $access_token = $wxshop->getAccessToken();
        $media_id = $xmlArr['MediaId'];
        $downloadurl = "https://api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$media_id}";

        $row = array();
        $row["wxuserid"] = $wxuserid;
        $row["userid"] = $userid;
        $row["doctorid"] = $doctorid;
        $row["assistantid"] = $assistantid;
        $row["relate_patientid"] = $relate_patientid;
        $row["voiceid"] = 0;
        $row["downloadurl"] = $downloadurl;
        $entity = self::createByBiz($row);

        Dwx_pipe::createByEntity($entity);

        return $entity;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================

}
