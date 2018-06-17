<?php
// CheckupPicture
// 检查报告图片

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701
class CheckupPicture extends Entity
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
            'pictureid',  // pictureid
            'checkupid',  // checkupid
            'check_date',  // 检查日期
            'title',  // 图片标题
            'status'); // 状态
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

        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
        $this->_belongtos["checkup"] = array(
            "type" => "Checkup",
            "key" => "checkupid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["pictureid"] = $pictureid;
    // $row["checkupid"] = $checkupid;
    // $row["check_date"] = $check_date;
    // $row["title"] = $title;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "CheckupPicture::createByBiz row cannot empty");

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
        $default["pictureid"] = 0;
        $default["checkupid"] = 0;
        $default["check_date"] = '0000-00-00';
        $default["title"] = '';
        $default["status"] = 0;

        $row += $default;
        $checkuppicture = new self($row);

        $row1 = array();
        $row1["createtime"] = $checkuppicture->createtime;
        $row1["wxuserid"] = $checkuppicture->wxuserid;
        $row1["userid"] = $checkuppicture->userid;
        $row1["patientid"] = $checkuppicture->patientid;
        $row1["doctorid"] = $checkuppicture->doctorid;
        $row1["objtype"] = 'CheckupPicture';
        $row1["objid"] = $checkuppicture->id;
        $row1["source_type"] = 'CheckupPicture';

        PatientPicture::createByBiz($row1);

        return $checkuppicture;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function setClose () {
        $this->status = 1;
    }

    public function setOpen () {
        $this->status = 0;
    }
    // ====================================
    // ----------- static method ----------
    // ====================================
}
