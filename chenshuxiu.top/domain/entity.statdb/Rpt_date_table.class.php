<?php

/*
 * Rpt_date_table
 */
class Rpt_date_table extends Entity
{

    protected function init_database () {
        $this->database = 'statdb';
    }

    // 不需要记录xobjlog
    public function notXObjLog () {
        return true;
    }

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'thedate',  // 日期
            'tablename',  // 表名
            'rowcnt',  // 行数
            'total_rowcnt',  // 累计行数
            'maxid'); // maxid
    }

    protected function init_keys_lock () {
        $this->_keys_lock = self::getKeysDefine();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["thedate"] = $thedate;
    // $row["tablename"] = $tablename;
    // $row["rowcnt"] = $rowcnt;
    // $row["total_rowcnt"] = $total_rowcnt;
    // $row["maxid"] = $maxid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Rpt_date_table::createByBiz row cannot empty");

        $default = array();
        $default["thedate"] = '';
        $default["tablename"] = '';
        $default["rowcnt"] = 0;
        $default["total_rowcnt"] = 0;
        $default["maxid"] = 0;

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
