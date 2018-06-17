<?php
/*
 * AuditRole
 */
class AuditRole extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'code',  // 英文名 (用于编码)
            'name');        // 角色名 (用于显示)

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

    }

    // $row = array();
    // $row["code"] = $code;
    // $row["name"] = $name;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "AuditRole::createByBiz row cannot empty");

        $default = array();
        $default["code"] = '';
        $default["name"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getAuditRoleIdArr(){
        return explode(',',$this->auditroleids);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    public static function getDescArr($needAll = false){
        $auditRoles = Dao::getEntityListByCond('AuditRole');
        $arr = array();

        if($needAll){
            $arr[0] = '全部';
        }

        foreach ($auditRoles as $a) {
            $arr[$a->id] = $a->name;
        }
        return $arr;
    }

}
