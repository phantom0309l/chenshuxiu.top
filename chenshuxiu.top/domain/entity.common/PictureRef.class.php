<?php
// PictureRef
// 图片-对象-关联 多对一,多对多

// owner by sjp
// create by sjp
// review by sjp 20160628

class PictureRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array( //
            'objtype',  //
            'objid',  //
            'pictureid',  //
            'name',  //
            'pos'); //

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'objtype',
            'objid',
            'pictureid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos['obj'] = array(
            "type" => $this->objtype,
            "key" => "objid");
        $this->_belongtos['picture'] = array(
            "type" => "Picture",
            "key" => "pictureid");
    }

    // 创建对象 createByBiz
    public static function createByBiz ($objtype, $objid, $pictureid, $name = "", $pos = 0) {
        $entity = PictureRefDao::getBy2Id($objtype, $objid, $pictureid);
        if ($entity instanceof PictureRef) {
            return $entity;
        }

        $row = array();
        $row["objtype"] = $objtype;
        $row["objid"] = $objid;
        $row["pictureid"] = $pictureid;
        $row["name"] = $name;
        $row["pos"] = $pos;

        return new self($row);
    }

    // 创建对象 createByObj
    public static function createByObj ($obj, $pictureid) {
        return self::createByBiz(get_class($obj), $obj->id, $pictureid);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================
}
