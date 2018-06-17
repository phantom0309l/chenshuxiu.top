<?php
/*
 * DoctorConfigTpl
 */
class DoctorConfigTpl extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'title'    //配置标题
        ,'code'    //编码用的code
        ,'groupstr'    //配置所属组标题
        ,'brief'    //配置简介
        ,'pos'    //配置排序
        ,'status'    //状态
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            );
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

    }

    // $row = array();
    // $row["title"] = $title;
    // $row["code"] = $code;
    // $row["groupstr"] = $groupstr;
    // $row["brief"] = $brief;
    // $row["pos"] = $pos;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "DoctorConfigTpl::createByBiz row cannot empty");

        $default = array();
        $default["title"] = '';
        $default["code"] = '';
        $default["groupstr"] = '';
        $default["brief"] = '';
        $default["pos"] =  0;
        $default["status"] =  0;

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
