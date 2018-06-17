<?php
/*
 * FaqLog
 */
class FaqLog extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'faqid'    //faqid
        ,'auditorid'    //auditorid
        ,'content'    //content
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('faqid' ,'auditorid' ,);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["faq"] = array ("type" => "Faq", "key" => "faqid" );
        $this->_belongtos["auditor"] = array ("type" => "Auditor", "key" => "auditorid" );
    }

    // $row = array();
    // $row["faqid"] = $faqid;
    // $row["auditorid"] = $auditorid;
    // $row["content"] = $content;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"FaqLog::createByBiz row cannot empty");

        $default = array();
        $default["faqid"] =  0;
        $default["auditorid"] =  0;
        $default["content"] = '';

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
