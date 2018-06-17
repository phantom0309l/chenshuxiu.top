<?php
/*
 * PipeTpl
 */
class PipeTpl extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'title'    //title
        ,'show_in_doctor'    //在医生端显示,1:显示；0:不显示
        ,'objtype'    //objtype
        ,'objcode'    //objcode
        ,'content'    //备注
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
    // $row["show_in_doctor"] = $show_in_doctor;
    // $row["objtype"] = $objtype;
    // $row["objcode"] = $objcode;
    // $row["content"] = $content;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"PipeTpl::createByBiz row cannot empty");

        $default = array();
        $default["title"] = '';
        $default["show_in_doctor"] =  0;
        $default["objtype"] = '';
        $default["objcode"] = '';
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
