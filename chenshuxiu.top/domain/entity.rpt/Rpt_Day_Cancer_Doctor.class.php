<?php

/*
 * Rpt_Day_Cancer_Doctor
 */
class Rpt_Day_Cancer_Doctor extends Entity
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
            'doctorid'    //医生id
        ,'diseaseid'    //疾病id
        ,'day_date'    //当日日期
        ,'data'    //统计数据json
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
    // $row["day_date"] = $day_date;
    // $row["data"] = $data;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Rpt_Day_Cancer_Doctor::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] =  0;
        $default["diseaseid"] =  0;
        $default["day_date"] = '';
        $default["data"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

}
