<?php
/*
 * PipeLevel
 */
class PipeLevel extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'pipeid'    //pipeid
        ,'optaskid'    //optaskid
        ,'is_urgent'    //is_urgent初始化: 0；不紧急；1；紧急：2
        ,'is_urgent_fix'    //is_urgent_fix初始化: 0；不紧急；1；紧急：2
        ,'auditorid'    //auditorid
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('pipeid' ,'auditorid' ,);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["pipe"] = array ("type" => "Pipe", "key" => "pipeid" );
        $this->_belongtos["optask"] = array ("type" => "OpTask", "key" => "optaskid" );
        $this->_belongtos["auditor"] = array ("type" => "Auditor", "key" => "auditorid" );
    }

    // $row = array();
    // $row["pipeid"] = $pipeid;
    // $row["auditorid"] = $auditorid;
    // $row["level"] = $level;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"PipeLevel::createByBiz row cannot empty");

        $default = array();
        $default["pipeid"] =  0;
        $default["optaskid"] =  0;
        $default["is_urgent"] =  0;
        $default["is_urgent_fix"] =  0;
        $default["auditorid"] =  1;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getUrgentStr(){
        $arr = self::getUrgentArr();
        return $arr[$this->is_urgent];
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getUrgentArr(){
        $arr = array(
            "0" => "未处理",
            "1" => "不紧急",
            "2" => "紧急",
        );
        return $arr;
    }
}
