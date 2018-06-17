<?php

/*
 * Rpt_week_ketang
 */
class Rpt_week_ketang extends Entity
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
            'begindate',  // 开始日期
            'enddate',  // 结束日期
            'hwkactivecnt',
            'ketang_newcnt',
            'ketang_allcnt',
            'adhd_newcnt',
            'adhd_allcnt');
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["begindate"] = $begindate;
    // $row["enddate"] = $enddate;
    // $row["activecnt"] = $activecnt;
    // $row["addedcnt"] = $addedcnt;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Rpt_week_ketang::createByBiz row cannot empty");

        $default = array();
        $default["begindate"] = '0000-00-00';
        $default["enddate"] = '0000-00-00';
        $default["hwkactivecnt"] = 0;
        $default["ketang_newcnt"] = 0;
        $default["ketang_allcnt"] = 0;
        $default["adhd_newcnt"] = 0;
        $default["adhd_allcnt"] = 0;

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
