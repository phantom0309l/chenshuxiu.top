<?php
/*
 * ReportPicture
 */
class ReportPicture extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'reportid'    //reportid
        ,'pictureid'    //pictureid
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'reportid' ,'pictureid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

    $this->_belongtos["report"] = array ("type" => "Report", "key" => "reportid" );
    $this->_belongtos["picture"] = array ("type" => "Picture", "key" => "pictureid" );
    }

    // $row = array(); 
    // $row["reportid"] = $reportid;
    // $row["pictureid"] = $pictureid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "ReportPicture::createByBiz row cannot empty");

        $default = array();
        $default["reportid"] =  0;
        $default["pictureid"] =  0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================

}
