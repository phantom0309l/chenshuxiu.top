<?php

class Assistant extends Entity
{

    const STATUS_COMMON = 1;

    const STATUS_LOCK = 2;

    const STATUS_DELETE = 0;

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'name',  // 助理名字
            'userid',  // userid
            'doctorid',  // doctorid
            'doctorresourceids',  // 权限id逗号分隔
            'status'); // 状态
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
    }

    // $row = array();
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, __METHOD__ . " row cannot empty");

        $default = array();
        $default["name"] = '';
        $default["userid"] = '';
        $default["doctorid"] = '';
        $default["doctorresourceids"] = '';
        $default["status"] = self::STATUS_COMMON;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function lock () {
        $this->status = self::STATUS_LOCK;
    }

    public function unlock () {
        $this->status = self::STATUS_COMMON;
    }

    public function isLock () {
        return $this->status == self::STATUS_LOCK;
    }

    public function isCommon () {
        return $this->status == self::STATUS_COMMON;
    }

    public function hasAuth ($auditresourceid) {
        return strpos($this->doctorresourceids, $auditresourceid) !== false;
    }

    public function getDoctorResources () {
        $ids = array();
        if ($this->doctorresourceids) {
            $ids = explode(',', $this->doctorresourceids);
        }
        return Dao::getEntityListByIds('DoctorResource', $ids);
    }
}
