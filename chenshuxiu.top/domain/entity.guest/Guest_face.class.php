<?php
/*
 * Guest_face
 */
class Guest_face extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'guestid',  // guestid
            'pictureid',  // pictureid
            'sharenum',  //
            'fromguestid',  // fromguestid
            'remark'); // 备注

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'guestid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["guest"] = array(
            "type" => "Guest",
            "key" => "guestid");
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
    }

    public function getCompareList () {
        return Guest_faceRecordDao::getListByGuest_faceid($this->id);
    }

    public function getCompareCnt () {
        return Guest_faceRecordDao::getCntByGuest_faceid($this->id);
    }

    // $row = array();
    // $row["guestid"] = $guestid;
    // $row["pictureid"] = $pictureid;
    // $row["sharenum"] = $sharenum;
    // $row["fromguestid"] = $fromguestid;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Guest_face::createByBiz row cannot empty");

        $default = array();
        $default["guestid"] = 0;
        $default["pictureid"] = 0;
        $default["sharenum"] = 0;
        $default["fromguestid"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    public static function getOrCreateByGuestid ($guestid) {
        if (empty($guestid)) {
            return null;
        }

        // 避免重复创建
        $guest_face = Guest_faceDao::getByGuestid($guestid);
        if ($guest_face instanceof Guest_face) {
            return $guest_face;
        }

        $row = array();
        $row["guestid"] = $guestid;
        $guest_face = self::createByBiz($row);

        return $guest_face;
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

}
