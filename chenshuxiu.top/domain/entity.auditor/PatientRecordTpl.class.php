<?php
/*
 * PatientRecordTpl
 */
class PatientRecordTpl extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'diseasegroupid'    //diseasegroupid
        ,'ename'    //英文名称
        ,'title'    //标题
        ,'content'    //内容
        ,'pos'    //排序
        ,'is_show'    //是否显示。0:不显示; 1:显示
        ,'style_class'    //样式class
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array();
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["diseasegroup"] = array ("type" => "Diseasegroup", "key" => "diseasegroupid" );
    }

    // $row = array();
    // $row["diseasegroupid"] = $diseasegroupid;
    // $row["ename"] = $ename;
    // $row["title"] = $title;
    // $row["content"] = $content;
    // $row["pos"] = $pos;
    // $row["is_show"] = $is_show;
    // $row["style_class"] = $style_class;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"PatientRecordTpl::createByBiz row cannot empty");

        $default = array();
        $default["diseasegroupid"] =  0;
        $default["ename"] = '';
        $default["title"] = '';
        $default["content"] = '';
        $default["pos"] =  0;
        $default["is_show"] =  0;
        $default["style_class"] = '';

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
