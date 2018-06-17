<?php

/*
 * WxPicMsg 患者图片消息
 */
class WxPicMsg extends Entity
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
            'objtype',  // objtype
            'objid',  // objid
            'patientpictureid',  // patientpictureid
            'pictureid',  // pictureid
            'check_date',  // check_date
            'title',  // title
            'image',  // 旧字段
            'path',  // 旧字段
            'source',  // 旧字段
            'wxpicurl',  // 微信的图片链接
            'media_id',  // 微信素材id
            'send_by_objtype',  // 发送人
            'send_by_objid',  // 发送人id
            'send_explain',  // 发送说明
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
            'doctorid',
            'pictureid');
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
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
        $this->_belongtos["patientpicture"] = array(
            "type" => "PatientPicture",
            "key" => "patientpictureid");
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["pictureid"] = $pictureid;
    // $row["image"] = $image;
    // $row["path"] = $path;
    // $row["wxpicurl"] = $wxpicurl;
    // $row["media_id"] = $media_id;
    // $row["source"] = $source;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "WxPicMsg::createByBiz row cannot empty");

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
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["patientpictureid"] = 0;
        $default["pictureid"] = 0;
        $default["check_date"] = '0000-00-00';
        $default["title"] = '';
        $default["image"] = '';
        $default["path"] = '';
        $default["source"] = '';
        $default["wxpicurl"] = '';
        $default["media_id"] = '';
        $default["send_by_objtype"] = '';
        $default["send_by_objid"] = 0;
        $default["send_explain"] = '';
        $default["status"] = 1;
        $default["auditstatus"] = 0;
        $default["auditorid"] = 0;
        $default["auditremark"] = '';

        $row += $default;
        $wxpicmsg = new self($row);

        $row1 = array();
        $row1["createtime"] = $wxpicmsg->createtime;
        $row1["wxuserid"] = $wxpicmsg->wxuserid;
        $row1["userid"] = $wxpicmsg->userid;
        $row1["patientid"] = $wxpicmsg->patientid;
        $row1["doctorid"] = $wxpicmsg->doctorid;
        $row1["objtype"] = 'WxPicMsg';
        $row1["objid"] = $wxpicmsg->id;
        $row1["source_type"] = 'WxPicMsg';
        $row1["thedate"] = date('Y-m-d');

        $patientpicture = PatientPicture::createByBiz($row1);
        $wxpicmsg->patientpictureid = $patientpicture->id;

        return $wxpicmsg;
    }

    public static function createbyxmlObj ($xml) {
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
        $row = array();
        $row["wxuserid"] = $wxuser->id;
        $row["userid"] = $userid;
        $row["patientid"] = $patientid;
        $row["doctorid"] = $doctorid;
        $row["auditremark"] = $openid;
        $row["pictureid"] = 0;
        $row["wxpicurl"] = $xmlArr['PicUrl'];
        $row["media_id"] = $xmlArr['MediaId'];
        $row["source"] = "self";
        $row["send_by_objtype"] = 'Patient';
        $row["send_by_objid"] = $patientid;
        $row["send_explain"] = 'normal';
        return self::createByBiz($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getTitleStr () {
        switch ($this->send_by_objtype) {
            case 'Doctor':
                $title = '医生上传';
                break;
            case 'Patient':
                $title = '患者上传';
                break;
            case 'Auditor':
                $title = '运营上传';
                break;
        }

        return $title;
    }

    public function getWxPicUrl4Fetch () {
        $url = $this->wxpicurl;
        if ($this->media_id != '') {
            $ACCESS_TOKEN = $this->wxuser->wxshop->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token={$ACCESS_TOKEN}&media_id={$this->media_id}";
        }

        return $url;
    }

    public function getContent () {
        return '图片一张';
    }

    public function getImgUrl () {
        if (false == $this->picture instanceof Picture) {
            return "";
        }
        return $this->picture->getSrc();
    }

    public function getThumbUrl ($w = 150, $h = 150) {
        if (false == $this->picture instanceof Picture) {
            return "";
        }
        return $this->picture->getSrc($w, $h, false);
    }

    public function getTagRefs ($typestr = '') {
        return TagRefDao::getListByObj($this, $typestr);
    }

    public function getTagRefByTagid ($tagid) {
        $tagrefs = $this->getTagRefs();
        foreach ($tagrefs as $a) {
            if ($a->tagid == $tagid) {
                return $a;
            }
        }

        return null;
    }

    public function isTagBy ($tagid) {
        return ($this->getTagRefByTagid($tagid) instanceof TagRef);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
