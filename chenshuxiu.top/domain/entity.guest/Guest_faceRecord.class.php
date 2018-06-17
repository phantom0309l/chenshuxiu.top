<?php
/*
 * Guest_faceRecord
 */
class Guest_faceRecord extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'guest_faceid',  // guest_faceid
            'mpictureid',  // main pictureid
            'fguestid',  // follow guestid
            'fpictureid',  // follow pictureid
            'similarity',  // 相似度
            'remark'); // 备注

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'guest_faceid',
            'mpictureid',
            'fguestid',
            'fpictureid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["guest_face"] = array(
            "type" => "Guest_face",
            "key" => "guest_faceid");
        $this->_belongtos["mpicture"] = array(
            "type" => "Picture",
            "key" => "mpictureid");
        $this->_belongtos["fguest"] = array(
            "type" => "Guest",
            "key" => "fguestid");
        $this->_belongtos["fpicture"] = array(
            "type" => "Picture",
            "key" => "fpictureid");
    }

    // $row = array();
    // $row["guest_faceid"] = $guest_faceid;
    // $row["mpictureid"] = $mpictureid;
    // $row["fguestid"] = $fguestid;
    // $row["fpictureid"] = $fpictureid;
    // $row["similarity"] = $similarity;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Guest_faceRecord::createByBiz row cannot empty");

        $default = array();
        $default["guest_faceid"] = 0;
        $default["mpictureid"] = 0;
        $default["fguestid"] = 0;
        $default["fpictureid"] = 0;
        $default["similarity"] = '';
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

}
