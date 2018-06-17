<?php
/*
 * AuditorGroupRef
 */
class AuditorGroupRef extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'auditorid'    //运营id
        ,'auditorgroupid'    //运营组id
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array();
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["auditor"] = array ("type" => "Auditor", "key" => "auditorid" );
        $this->_belongtos["auditorgroup"] = array ("type" => "AuditorGroup", "key" => "auditorgroupid" );
    }

    // $row = array();
    // $row["auditorid"] = $auditorid;
    // $row["auditorgroupid"] = $auditorgroupid;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"AuditorGroupRef::createByBiz row cannot empty");

        $default = array();
        $default["auditorid"] =  0;
        $default["auditorgroupid"] =  0;

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
    