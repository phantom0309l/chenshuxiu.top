<?php
/*
 * WxVoiceMsg 患者voice消息
 */
class WxVoiceMsg extends Entity
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
            'voiceid',  // voiceid
            'content',  // content
            'downloadurl',  // downloadurl
            'status',  // 状态
            'auditstatus',  // 审核状态
            'auditorid',  // auditorid
            'auditremark'); // 审核备注
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

        $this->_belongtos["voice"] = array(
            "type" => "Voice",
            "key" => "voiceid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["voiceid"] = $voiceid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "WxVoiceMsg::createByBiz row cannot empty");

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
        $default["voiceid"] = 0;
        $default["content"] = '';
        $default["downloadurl"] = '';
        $default["status"] = 1;
        $default["auditstatus"] = 0;
        $default["auditorid"] = 0;
        $default["auditremark"] = '';

        $row += $default;
        return new self($row);
    }

    public static function createbyXmlObj ($xml) {
        $xmlArr = FUtil::xmlobj2array($xml);

        $openid = $xmlArr['FromUserName'];
        $wxuser = WxUserDao::getByOpenid($openid);
        $user = $wxuser->user;
        $userid = ($user instanceof User) ? $user->id : 0;
        $patientid = 0;
        $doctorid = 0;
        if (($user instanceof User) and ($user->patient instanceof Patient)) {
            $patientid = $user->patient->id;
            $doctorid = $user->patient->doctorid;
        }

        $wxshop = WxShop::getById($wxuser->wxshopid);
        $access_token = $wxshop->getAccessToken();
        $media_id = $xmlArr['MediaId'];
        $downloadurl = "https://api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$media_id}";

        $row = array();
        $row["wxuserid"] = $wxuser->id;
        $row["userid"] = $userid;
        $row["patientid"] = $patientid;
        $row["doctorid"] = $doctorid;
        $row["downloadurl"] = $downloadurl;
        $row["auditremark"] = $openid;
        $row["voiceid"] = 0;
        $row["content"] = isset($xmlArr['Recognition']) && false == is_array($xmlArr['Recognition']) ? $xmlArr['Recognition'] : '';
        return self::createByBiz($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function geUrl () {
        if (false == $this->Voice instanceof Voice) {
            return "";
        }
        return $this->voice->getUrl();
    }

    public function getWxVoiceUrl4Fetch () {
        $url = "";
        $media_id = $this->getMediaId();
        if ($media_id != '') {
            $ACCESS_TOKEN = $this->wxuser->wxshop->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token={$ACCESS_TOKEN}&media_id={$media_id}";
        }
        return $url;
    }

    public function getMediaId(){
        $downloadurl = $this->downloadurl;
        $data = parse_url($downloadurl);
        $query = $data["query"];
        return explode("media_id=", $query)[1];
    }



    // ====================================
    // ----------- static method ----------
    // ====================================

}
