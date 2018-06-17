<?php
/*
 * Faq
 */
class Faq extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'title'    //title
        ,'content'    //content
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array();
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["title"] = $title;
    // $row["content"] = $content;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"Faq::createByBiz row cannot empty");

        $default = array();
        $default["title"] = '';
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
