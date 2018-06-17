<?php

/*
 * BasicPicture
 */

class BasicPicture extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
        'pictureid'    //pictureid
        , 'wxuserid'    //类型
        , 'userid'    //类型
        , 'patientid'    //类型
        , 'doctorid'    //类型
        , 'type'    //类型
        , 'objtype'    //
        , 'objid'    //
        , 'patientpictureid'    //
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'pictureid', 'objid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["picture"] = array("type" => "Picture", "key" => "pictureid");
        $this->_belongtos["obj"] = array("type" => "Obj", "key" => "objid");
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
        $this->_belongtos["patientpicture"] = array(
            "type" => "PatientPicture",
            "key" => "patientpictureid");
    }

    // $row = array(); 
    // $row["pictureid"] = $pictureid;
    // $row["type"] = $type;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "BasicPicture::createByBiz row cannot empty");

        $default = array();
        $default["pictureid"] = 0;
        $default["type"] = '';
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["patientpictureid"] = 0;

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
