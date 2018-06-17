<?php
/*
 * OpTaskTplAuditorRef
 */
class OpTaskTplAuditorRef extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'optasktplid'    //optasktplid
        ,'auditorid'    //运营id
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('optasktplid' ,'auditorid' ,);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
    $this->_belongtos["optasktpl"] = array ("type" => "Optasktpl", "key" => "optasktplid" );
    $this->_belongtos["auditor"] = array ("type" => "Auditor", "key" => "auditorid" );
    }

    // $row = array();
    // $row["optasktplid"] = $optasktplid;
    // $row["auditorid"] = $auditorid;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"OpTaskTplAuditorRef::createByBiz row cannot empty");

        $default = array();
        $default["optasktplid"] =  0;
        $default["auditorid"] =  0;

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
