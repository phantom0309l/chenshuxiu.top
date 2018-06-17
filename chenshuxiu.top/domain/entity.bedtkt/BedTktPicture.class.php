<?php
/*
 * BedTktPicture
 */
class BedTktPicture extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid'    //wxuserid
        ,'userid'    //userid
        ,'patientid'    //patientid
        ,'doctorid'    //doctorid
        ,'bedtktid'    //bedtktid
        ,'pictureid'    //pictureid
        ,'patientpictureid' //patientpictureid
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid' ,'userid' ,'patientid' ,'doctorid' ,'bedtktid' ,'pictureid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array ("type" => "WxUser", "key" => "wxuserid" );
        $this->_belongtos["user"] = array ("type" => "User", "key" => "userid" );
        $this->_belongtos["patient"] = array ("type" => "Patient", "key" => "patientid" );
        $this->_belongtos["doctor"] = array ("type" => "Doctor", "key" => "doctorid" );
        $this->_belongtos["bedtkt"] = array ("type" => "BedTkt", "key" => "bedtktid" );
        $this->_belongtos["picture"] = array ("type" => "Picture", "key" => "pictureid" );
        $this->_belongtos["patientpicture"] = array ("type" => "PatientPicture", "key" => "patientpictureid" );
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["bedtktid"] = $bedtktid;
    // $row["pictureid"] = $pictureid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "BedTktPicture::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] =  0;
        $default["userid"] =  0;
        $default["patientid"] =  0;
        $default["doctorid"] =  0;
        $default["bedtktid"] =  0;
        $default["pictureid"] =  0;
        $default["patientpictureid"] =  0;

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

    // ====================================
    // ----------- static method ----------
    // ====================================

}
