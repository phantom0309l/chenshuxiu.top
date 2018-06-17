<?php
/*
 * OpTaskPipeRef
 */
class OpTaskPipeRef extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'optaskid'    //optaskid
        ,'pipeid'    //pipeid
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('optaskid' ,'pipeid' ,);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["optask"] = array ("type" => "OpTask", "key" => "optaskid" );
        $this->_belongtos["pipe"] = array ("type" => "Pipe", "key" => "pipeid" );
    }

    // $row = array();
    // $row["optaskid"] = $optaskid;
    // $row["pipeid"] = $pipeid;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"OpTaskPipeRef::createByBiz row cannot empty");

        $default = array();
        $default["optaskid"] =  0;
        $default["pipeid"] =  0;

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
