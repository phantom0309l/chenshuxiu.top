<?php
/*
 * Dg_center
 */
class Dg_center extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'dg_projectid'    //项目id
        ,'title'    //中心名称
        ,'content'    //中心目标
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'dg_projectid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["dg_project"] = array ("type" => "Dg_project", "key" => "dg_projectid" );
    }

    // $row = array();
    // $row["dg_projectid"] = $dg_projectid;
    // $row["title"] = $title;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Dg_center::createByBiz row cannot empty");

        $default = array();
        $default["dg_projectid"] =  0;
        $default["title"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    // 获取中心负责人
    public function getMasters () {
        return Dg_memberDao::getMastersByDg_centerid($this->id);
    }

    // 获取中心负责人（json）
    public function getMasterArr () {
        $masters = $this->getMasters();

        $arr = array();
        foreach ($masters as $a) {
            $arr[] = $a->doctor->name;
        }

        return $arr;
    }

    // 中心医生数
    public function getDoctorCnt () {
        $sql = " select count(*)
            from dg_members
            where dg_centerid = :dg_centerid ";
        $bind = [];
        $bind[':dg_centerid'] = $this->id;

        return Dao::queryValue($sql, $bind);
    }

    // 中心患者数
    public function getPatientCnt () {
        $sql = " select count(*)
            from dg_patients
            where dg_centerid = :dg_centerid ";
        $bind = [];
        $bind[':dg_centerid'] = $this->id;

        return Dao::queryValue($sql, $bind);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getArrayJsonByDg_projectid ($dg_projectid, $doctorid) {
        $dg_centers = Dg_centerDao::getListByDg_projectid($dg_projectid);

        $arr = array();
        if (!$dg_centers) {
            return array();
        }
        $member = Dg_memberDao::getByDg_projectidDoctorid($dg_projectid, $doctorid);

        $list = array();
        $arr['id'] = 0;
        $arr['title'] = '全部';
        $arr['role'] = $member->is_project_master;
        $list[] = $arr;
        foreach ($dg_centers as $center) {
            $arr['id'] = $center->id;
            $arr['title'] = $center->title;
            $arr['role'] = $member->is_center_master;

            $list[] = $arr;
        }

        return $list;
    }
}
