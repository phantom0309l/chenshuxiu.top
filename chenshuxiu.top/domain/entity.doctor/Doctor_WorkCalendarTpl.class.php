<?php

/*
 * Doctor_WorkCalendarTpl
 */
class Doctor_WorkCalendarTpl extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctorid'    //doctorid
        ,'diseaseid'    //diseaseid
        ,'code'    //
        ,'title'    //标题
        ,'content'    //数据JSON
        );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'doctorid' ,'diseaseid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["doctor"] = array ("type" => "Doctor", "key" => "doctorid" );
        $this->_belongtos["disease"] = array ("type" => "Disease", "key" => "diseaseid" );
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["diseaseid"] = $diseaseid;
    // $row["code"] = $code;
    // $row["title"] = $title;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Doctor_WorkCalendarTpl::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] =  0;
        $default["diseaseid"] =  0;
        $default["code"] = '';
        $default["title"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

}
