<?php
/*
 * Dg_project
 */
class Dg_project extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'title'    //项目名称
        ,'content'    //项目目标
        ,'create_doctorid'  //项目创建医生id
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            );
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["create_doctor"] = array ("type" => "Doctor", "key" => "create_doctorid" );
    }

    // $row = array();
    // $row["title"] = $title;
    // $row["content"] = $content;
    // $row["create_doctorid"] = $create_doctorid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Dg_project::createByBiz row cannot empty");

        $default = array();
        $default["title"] = '';
        $default["content"] = '';
        $default["create_doctorid"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    // 获取项目负责人
    public function getMasters () {
        return Dg_memberDao::getMastersByDg_projectid($this->id);
    }

    // 获取项目负责人（json）
    public function getMasterArr () {
        $masters = $this->getMasters();

        $arr = array();
        foreach ($masters as $a) {
            $arr[] = $a->doctor->name;
        }

        return $arr;
    }

    // 患者数量
    public function getPatientCnt () {
        $sql = " select count(*)
            from dg_patients
            where dg_projectid = :dg_projectid ";
        $bind = [];
        $bind[':dg_projectid'] = $this->id;

        return Dao::queryValue($sql, $bind);
    }

    // 医生数量
    public function getDoctorCnt () {
        $sql = " select count(*)
            from dg_members
            where dg_projectid = :dg_projectid ";
        $bind = [];
        $bind[':dg_projectid'] = $this->id;

        return Dao::queryValue($sql, $bind);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getProjectsByDoctorid ($doctorid) {
        $cond = " and id in (
                select dg_projectid
                from dg_members
                where doctorid = :doctorid
            ) ";
        $bind = [];
        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityListByCond('Dg_project', $cond, $bind);
    }

    public static function getArrayJson($doctorid) {
        $dg_projects = self::getProjectsByDoctorid($doctorid);

        if (!$dg_projects) {
            return array();
        }

        $arr = array();
        foreach ($dg_projects as $project) {
            $arr["{$project->id}"] = $project->title;
        }

        return $arr;
    }
}
