<?php

/*
 * LiverPicture
 */
class LiverPicture extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // doctorid
            'objtype',  // objtype
            'objid',  // objid
            'patientpictureid',  // patientpictureid
            'pictureid',  // pictureid
            'check_date',  // 检查日期
            'title',  // 图片标题
            'image',  // 旧字段
            'path',  // 旧字段
            'source',  // 旧字段
            'wxpicurl',  // 微信图片链接
            'status',  // 状态
            'auditstatus',  // 审核状态
            'auditorid',  // auditorid
            'auditremark'); // 审核备注

    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'pictureid');
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
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["patientpictureid"] = $patientpictureid;
    // $row["pictureid"] = $pictureid;
    // $row["check_date"] = $check_date;
    // $row["title"] = $title;
    // $row["image"] = $image;
    // $row["path"] = $path;
    // $row["source"] = $source;
    // $row["wxpicurl"] = $wxpicurl;
    // $row["status"] = $status;
    // $row["auditstatus"] = $auditstatus;
    // $row["auditorid"] = $auditorid;
    // $row["auditremark"] = $auditremark;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "LiverPicture::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["patientpictureid"] = 0;
        $default["pictureid"] = 0;
        $default["check_date"] = '';
        $default["title"] = '';
        $default["image"] = '';
        $default["path"] = '';
        $default["source"] = '';
        $default["wxpicurl"] = '';
        $default["status"] = 0;
        $default["auditstatus"] = 0;
        $default["auditorid"] = 0;
        $default["auditremark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getImgUrl () {
        if (false == $this->picture instanceof Picture) {
            return "";
        }
        return $this->picture->getSrc();
    }

    public function getThumbUrl ($w=150, $h=150) {
        if (false == $this->picture instanceof Picture) {
            return "";
        }
        return $this->picture->getSrc($w, $h, false);
    }
}
