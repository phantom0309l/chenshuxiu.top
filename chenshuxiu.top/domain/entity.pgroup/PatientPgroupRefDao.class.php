<?php

/*
 * PatientPgroupRefDao
 * sql注入风险 TODO by sjp 20170503
 */
class PatientPgroupRefDao extends Dao
{
    // 名称: getCntByDate
    // 备注: sql注入风险 TODO by sjp 20170503
    // 创建:
    // 修改:
    public static function getCntByDate ($fromdate, $todate, $condFix) {
        $sql = "select count(*)
        from (
        SELECT count(*)
        FROM patients a
        inner join patientpgrouprefs b ON a.id = b.patientid
        WHERE left(a.createtime, 10) >= '{$fromdate}' AND left(a.createtime, 10) <= '{$todate}'
        {$condFix}
        ) t ";

        return Dao::queryValue($sql);
    }

    // 名称: getCntByPatientidAndTypestr
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByPatientidAndTypestr ($patientid, $typestr) {
        $sql = " select count(id)
        from patientpgrouprefs
        WHERE patientid = {$patientid} and typestr='{$typestr}' ";

        return Dao::queryValue($sql);
    }

    // 名称: getCntByPgroupid
    // 备注: sql注入风险 TODO by sjp 20170503
    // 创建:
    // 修改:
    public static function getCntByPgroupid ($pgroupid, $condFix) {
        $sql = " select count(id)
        from patientpgrouprefs
        WHERE pgroupid = {$pgroupid} {$condFix} ";

        return Dao::queryValue($sql);
    }

    // 名称: getList
    // 备注:
    // 创建:
    // 修改:
    public static function getList ($condFix = "") {
        $sql = " select a.*
            from patientpgrouprefs a
            inner join pgroups b on a.pgroupid = b.id
            where 1=1 {$condFix}
            group by a.patientid";
        return Dao::loadEntityList("PatientPgroupRef", $sql);
    }

    // 名称: getListByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientid ($patientid, $conFix = "") {
        $cond = " and patientid = :patientid " . $conFix;
        $bind = [];
        $bind[':patientid'] = $patientid;
        return Dao::getEntityListByCond("PatientPgroupRef", $cond, $bind);
    }

    // 名称: getListByPgroupid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPgroupid ($pgroupid, $conFix = "") {
        $cond = " and pgroupid = :pgroupid " . $conFix;
        $bind = [];
        $bind[':pgroupid'] = $pgroupid;
        return Dao::getEntityListByCond("PatientPgroupRef", $cond, $bind);
    }

    // 名称: getListOverdue
    // 备注:2n+1天未出组人数：有课程的分组，n=课程的天数。
    // 创建:
    // 修改:
    public static function getListOverdue ($condFix) {
        $sql = "select a.*
            from patientpgrouprefs a
            inner join
            (   select max(b.pos) as pos,p.id as id from pgroups p
            inner join courselessonrefs b
            on p.courseid = b.courseid
            where p.courseid>0
            group by b.courseid
            ) t on a.pgroupid = t.id
            where a.status = 1 and a.typestr = 'manage' and datediff(CURDATE(),a.startdate) >= (2*t.pos+1)
            {$condFix}
            group by a.patientid";
        return Dao::loadEntityList("PatientPgroupRef", $sql);
    }

    // 名称: getOneByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByPatientid ($patientid, $conFix = "") {
        $cond = " and patientid = :patientid " . $conFix;

        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityByCond("PatientPgroupRef", $cond, $bind);
    }

    // 名称: getOneByPatientidPgroupid
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByPatientidPgroupid ($patientid, $pgroupid, $conFix = "") {
        $cond = " and patientid = :patientid and pgroupid = :pgroupid " . $conFix;

        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':pgroupid'] = $pgroupid;

        return Dao::getEntityByCond("PatientPgroupRef", $cond, $bind);
    }

    // 名称: getPatientCnt
    // 备注:
    // 创建:
    // 修改:
    public static function getPatientCnt ($condFix = "") {
        $sql = "select count( distinct(patientid) ) as cnt
            from patientpgrouprefs
            where 1=1 and typestr='manage' and (userid<10000 OR userid>20000) {$condFix}";
        return Dao::queryValue($sql, []);
    }

    // 名称: getPatientCntOverdue
    // 备注:2n+1天未出组人数：有课程的分组，n=课程的天数。
    // 创建:
    // 修改:
    public static function getPatientCntOverdue ($condFix) {
        $sql = "select count( distinct(a.patientid) ) as cnt
            from patientpgrouprefs a
            inner join (
                select max(b.pos) as pos, a.id as id
                from pgroups a
                inner join courselessonrefs b on a.courseid = b.courseid
                where a.courseid>0
                group by b.courseid
            ) t on t.id = a.pgroupid
            where a.status = 1 and a.typestr = 'manage' and datediff(CURDATE(),a.startdate) >= (2*t.pos+1) {$condFix}";
        return Dao::queryValue($sql, []);
    }
}
