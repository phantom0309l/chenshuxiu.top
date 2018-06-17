<?php

/*
 * Rpt_date_patientpgroup
 */
class Rpt_date_patientpgroup extends Entity
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
            'pcnt',  // 有效患者总人数
            'ppgroupcnt',  // 入组患者总数人数
            'pinpgroupcnt',  // 正在组中患者总数人数
            'needfollowcnt',  // 需要跟进的入组患者
            'overduecnt',  // 过期未出租患者
            'addcnt'); // 当天新增入组患者
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["thedate"] = $thedate;
    // $row["pcnt"] = $pcnt;
    // $row["ppgroupcnt"] = $ppgroupcnt;
    // $row["needfollowcnt"] = $needfollowcnt;
    // $row["overduecnt"] = $overduecnt;
    // $row["addcnt"] = $addcnt;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Rpt_date_patientpgroup::createByBiz row cannot empty");

        $default = array();
        $default["thedate"] = '0000-00-00';
        $default["pcnt"] = 0;
        $default["ppgroupcnt"] = 0;
        $default["pinpgroupcnt"] = 0;
        $default["needfollowcnt"] = 0;
        $default["overduecnt"] = 0;
        $default["addcnt"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getWxTxtOfAdhd () {
        $rpt_date_patient = Rpt_date_patientDao::getByThedate($this->thedate);

        return $rpt_date_patient->wxtxtmsg_sumcnt;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
