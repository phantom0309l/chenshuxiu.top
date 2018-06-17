<?php
/*
 * Media
 */
class Media extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'media_id',  // media_id
            'media_type',  // image,voice,video
            'created_at',  // created_at
            'expire_seconds',  // 有效期3天
            'objtype',  // objtype
            'objid',  // objid
            'objcode'); // objcode

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // $row = array();
    // $row["media_id"] = $media_id;
    // $row["media_type"] = $media_type;
    // $row["create_at"] = $create_at;
    // $row["expire_seconds"] = $expire_seconds;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["objcode"] = $objcode;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Media::createByBiz row cannot empty");

        $default = array();
        $default["media_id"] = "";
        $default["media_type"] = '';
        $default["created_at"] = 0;
        $default["expire_seconds"] = 259200;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["objcode"] = '';

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
