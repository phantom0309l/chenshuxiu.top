<?php
// CheckupDao

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701
class CheckupDao extends Dao
{
    // 名称: getByPatientCheckupTplCheck_date
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientCheckupTplCheck_date(Patient $patient, CheckupTpl $checkuptpl, $check_date) {
        $cond = 'and patientid = :patientid
            and checkuptplid = :checkuptplid
            and check_date = :check_date
            and status = 0
            limit 1 ';

        $bind = array(
            ':patientid' => $patient->id,
            ':checkuptplid' => $checkuptpl->id,
            ':check_date' => $check_date);

        return Dao::getEntityByCond('Checkup', $cond, $bind);
    }

    // 名称: getByPatientidCheckuptplid_last
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientidCheckuptplid_last($patientid, $checkuptplid, $fromdate = null, $todate = null) {
        $cond = 'and patientid = :patientid
            and checkuptplid = :checkuptplid
            and status = 0
            order by check_date desc
            limit 1 ';

        $bind = array(
            ':patientid' => $patientid,
            ':checkuptplid' => $checkuptplid);

        if ($fromdate != null) {
            $cond = ' and check_date > :fromdate ' . $cond;
            $bind[':fromdate'] = $fromdate;
        }

        if ($todate != null) {
            $cond = ' and check_date <= :todate ' . $cond;
            $bind[':todate'] = $todate;
        }

        return Dao::getEntityByCond('Checkup', $cond, $bind);
    }

    // 名称: getByPatientidCheckuptplid_last_hasxanswersheet
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientidCheckuptplid_last_hasxanswersheet($patientid, $checkuptplid, $fromdate = null, $todate = null) {
        $cond = 'and patientid = :patientid
            and checkuptplid = :checkuptplid
            and xanswersheetid > 0
            and status = 0
            order by check_date desc
            limit 1 ';

        $bind = array(
            ':patientid' => $patientid,
            ':checkuptplid' => $checkuptplid);

        if ($fromdate != null) {
            $cond = ' and check_date > :fromdate ' . $cond;
            $bind[':fromdate'] = $fromdate;
        }

        if ($todate != null) {
            $cond = ' and check_date <= :todate ' . $cond;
            $bind[':todate'] = $todate;
        }

        return Dao::getEntityByCond('Checkup', $cond, $bind);
    }

    // 名称: getListByPatientCheckupTpl
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientCheckupTpl(Patient $patient, CheckupTpl $checkuptpl, $sort = 'DESC') {
        $cond = 'and patientid = :patientid
            and checkuptplid = :checkuptplid
            and status = 0
            order by check_date ' . $sort;

        $bind = array(
            ':patientid' => $patient->id,
            ':checkuptplid' => $checkuptpl->id);

        return Dao::getEntityListByCond('Checkup', $cond, $bind);
    }

    // 名称: getListByPatientidCheckuptplid_last7
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientidCheckuptplid_last7($patientid, $checkuptplid) {
        $cond = 'and patientid = :patientid
            and checkuptplid = :checkuptplid
            and status = 0
            order by check_date desc
            limit 7 ';

        $bind = array(
            ':patientid' => $patientid,
            ':checkuptplid' => $checkuptplid);

        return Dao::getEntityListByCond('Checkup', $cond, $bind);
    }

    // 名称: getListByPatient
    // 备注:检查列表 of 某患者
    // 创建:
    // 修改:
    public static function getListByPatient(Patient $patient) {
        $cond = 'and patientid = :patientid
            and status = 0
            order by id desc ';

        $bind = array(
            ':patientid' => $patient->id);

        return Dao::getEntityListByCond('Checkup', $cond, $bind);
    }

    // 名称: getListByPatientDoctor
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientDoctor(Patient $patient, Doctor $doctor) {

        // #4130, 协和风湿免疫科, 王迁 也能看 (医生自己和监管的医生)
        $doctorids_str = $doctor->getDoctorIdsStr();

        $sql = "select c.*
            from checkups c
            inner join checkuptpls ct on ct.id = c.checkuptplid
            where c.status = 0 and c.patientid = :patientid and ct.doctorid in ({$doctorids_str})
            order by c.id desc";

        $bind = array(
            ':patientid' => $patient->id);

        return Dao::loadEntityList('Checkup', $sql, $bind);
    }

    // 报告数 这函数有存在的意义吗 -- 20170531 by 许喆
    public static function getCheckupCnt($cond = '', $bind = []) {
        $sql = "select count(*) from checkups where 1=1 {$cond} ";
        return Dao::queryValue($sql, $bind);
    }

    // 获取某患者,各种检查报告的数目 array('zhusu'=>2,'BSA'=>4)
    public static function getEnameCheckupCntArray($enames, $patientid) {
        if (empty($enames)) {
            return array();
        }

        $inStr = implode("','", $enames);

        $sql = "select ename, count(*) as cnt
            from checkups a
            inner join checkuptpls b on b.id = a.checkuptplid
            where b.ename in ('{$inStr}') and a.patientid = :patientid
            group by ename
        ";
        $bind = [];
        $bind[':patientid'] = $patientid;

        $rows = Dao::queryRows($sql, $bind);

        $arr = array();
        foreach ($rows as $row) {
            $arr[$row['ename']] = $row['cnt'];
        }

        return $arr;
    }
}
