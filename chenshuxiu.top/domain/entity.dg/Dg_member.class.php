<?php
/*
 * Dg_member
 */
class Dg_member extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'dg_projectid'    //项目id,冗余
        ,'dg_centerid'    //中心id
        ,'doctorid'    //医生id
        ,'is_project_master'    //是否为项目负责人
        ,'is_center_master'    //是否为中心负责人
        ,'status'    //状态
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'dg_projectid' ,'dg_centerid' ,'doctorid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["dg_project"] = array ("type" => "Dg_project", "key" => "dg_projectid" );
        $this->_belongtos["dg_center"] = array ("type" => "Dg_center", "key" => "dg_centerid" );
        $this->_belongtos["doctor"] = array ("type" => "Doctor", "key" => "doctorid" );
    }

    // $row = array();
    // $row["dg_projectid"] = $dg_projectid;
    // $row["dg_centerid"] = $dg_centerid;
    // $row["doctorid"] = $doctorid;
    // $row["is_project_master"] = $is_project_master;
    // $row["is_center_master"] = $is_center_master;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Dg_member::createByBiz row cannot empty");

        $default = array();
        $default["dg_projectid"] =  0;
        $default["dg_centerid"] =  0;
        $default["doctorid"] =  0;
        $default["is_project_master"] =  0;
        $default["is_center_master"] =  0;
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
    // 获取某个项目下医生的患者数量
    public static function getPatientCntByDoctoridDg_projectid ($doctorid, $dg_projectid) {
        $sql = " select count(*)
            from dg_patients
            where doctorid = :doctorid and dg_projectid = :dg_projectid ";
        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':dg_projectid'] = $dg_projectid;

        return Dao::queryValue($sql, $bind);
    }

    // 获取某个中心下所有医生(Json)
    public static function getArrayJsonByDg_centerid ($dg_centerid) {
        $dg_members = Dg_memberDao::getListByDg_centerid($dg_centerid);

        if (!$dg_members) {
            return array();
        }

        $arr = array();
        $arr['0'] = '全部医生';
        foreach ($dg_members as $member) {
            $arr["{$member->id}"] = $member->doctor->name;
        }

        return $arr;
    }
}
