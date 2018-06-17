<?php

/*
 * OpTaskDao
 */
class OpTaskDao extends Dao
{

    // ===== 获取任务列表 =====

    // 任务列表 by 关联对象
    public static function getListByObj (Entity $obj) {
        $objtype = get_class($obj);
        $objid = $obj->id;

        $cond = ' and objtype = :objtype and objid = :objid order by id asc ';

        $bind = array(
            ':objtype' => $objtype,
            ':objid' => $objid);

        return Dao::getEntityListByCond('OpTask', $cond, $bind);
    }

    // 任务列表 by 患者 + 任务状态
    public static function getListByPaitentStatus (Patient $patient, $status) {
        $cond = " and patientid = :patientid and status = :status order by id asc ";
        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':status'] = $status;

        return Dao::getEntityListByCond('OpTask', $cond, $bind);
    }

    // 任务列表 by 患者 + 修正条件
    public static function getListByPatient (Patient $patient, $condFix = "") {
        $cond = "AND patientid = :patientid {$condFix}";
        $bind = array(
                ":patientid" => $patient->id);
        return Dao::getEntityListByCond("OpTask", $cond, $bind);
    }

    // 打开的任务列表 by 患者
    public static function getOpenListByPaitent (Patient $patient) {
        $cond = " and patientid = :patientid and status in (0, 2) order by id asc ";
        $bind = [];
        $bind[':patientid'] = $patient->id;

        return Dao::getEntityListByCond('OpTask', $cond, $bind);
    }

    // 任务列表 by 患者 + unicode + 任务状态 + 时间段
    public static function getListByPatientUnicodeStatus (Patient $patient, $unicode, $status, $fromtime = null, $totime = null) {
        $optasktpl = OpTaskTplDao::getOneByUnicode($unicode);

        $cond = " and patientid=:patientid and optasktplid = :optasktplid and status=:status ";
        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':optasktplid'] = $optasktpl->id;
        $bind[':status'] = $status;

        if ($fromtime != null) {
            $cond .= " and plantime >= :fromtime";
            $bind[':fromtime'] = $fromtime;
        }

        if ($totime != null) {
            $cond .= " and plantime < :totime";
            $bind[':totime'] = $totime;
        }

        return Dao::getEntityListByCond('OpTask', $cond, $bind);
    }

    // 任务列表 by 患者 + 任务模板
    public static function getListByPatientOptasktpl (Patient $patient, OpTaskTpl $optasktpl, $notClose = true) {
        if ($notClose) {
            $condfix = " AND status IN (0, 2) ";
        } else {
            $condfix = " ";
        }

        $cond = " AND patientid = :patientid AND optasktplid = :optasktplid {$condfix} ORDER BY id DESC ";
        $bind = [
            ':patientid' => $patient->id,
            ':optasktplid' => $optasktpl->id];

        return Dao::getEntityListByCond('OpTask', $cond, $bind);
    }

    // 任务列表 by 某模板 + 状态
    public static function getListByUnicodeStatus ($unicode, $status, $fromtime = null, $totime = null) {
        $optasktpl = OpTaskTplDao::getOneByUnicode($unicode);
        DBC::requireNotEmpty($optasktpl, "任务模板[{$unicode}]不存在");

        $cond = " and optasktplid=:optasktplid and status=:status ";
        $bind = [];
        $bind[':optasktplid'] = $optasktpl->id;
        $bind[':status'] = $status;

        if ($fromtime != null) {
            $cond .= " and plantime >= :fromtime";
            $bind[':fromtime'] = $fromtime;
        }

        if ($totime != null) {
            $cond .= " and plantime < :totime";
            $bind[':totime'] = $totime;
        }

        return Dao::getEntityListByCond('OpTask', $cond, $bind);
    }

    // 任务列表 by 某模板 + 状态
    public static function getListByUnicode ($unicode, $fromtime = null, $totime = null, $notClose = true) {
        $optasktpl = OpTaskTplDao::getOneByUnicode($unicode);
        DBC::requireNotEmpty($optasktpl, "任务模板[{$unicode}]不存在");

        $cond = " and optasktplid=:optasktplid ";

        if ($notClose) {
            $cond .= " and status in (0, 2) ";
        }
        $bind = [];
        $bind[':optasktplid'] = $optasktpl->id;

        if ($fromtime != null) {
            $cond .= " and plantime >= :fromtime";
            $bind[':fromtime'] = $fromtime;
        }

        if ($totime != null) {
            $cond .= " and plantime < :totime";
            $bind[':totime'] = $totime;
        }

        return Dao::getEntityListByCond('OpTask', $cond, $bind);
    }

    // ===== 获取单个任务 =====

    // 单个任务: 返回只存在一对一类型的optask
    public static function getOneByObj (Entity $obj) {
        $objtype = get_class($obj);
        $objid = $obj->id;

        $cond = ' and objtype = :objtype and objid = :objid order by status asc, id desc ';

        $bind = array(
            ':objtype' => $objtype,
            ':objid' => $objid);

        return Dao::getEntityByCond('OpTask', $cond, $bind);
    }

    // 单个任务: objtype + objid + optasktpl
    public static function getOneByObjOptasktpl (Entity $obj, OpTaskTpl $optasktpl, $notClose = true) {
        $objtype = get_class($obj);
        $objid = $obj->id;

        $condfix = '';
        if ($notClose) {
            $condfix = " and status in (0, 2) ";
        }

        $cond = " and objtype = :objtype and objid = :objid and optasktplid = :optasktplid {$condfix} order by status asc, createtime desc ";
        $bind = [
            ':objtype' => $objtype,
            ':objid' => $objid,
            ':optasktplid' => $optasktpl->id];

        return Dao::getEntityByCond('OpTask', $cond, $bind);
    }

    // 单个任务: 实体 + unicode
    public static function getOneByObjUnicode (Entity $obj, $unicode, $notClose = true) {
        $optasktpl = OpTaskTplDao::getOneByUnicode($unicode);
        DBC::requireNotEmpty($optasktpl, "任务模板[{$unicode}]不存在");

        return OpTaskDao::getOneByObjOptasktpl($obj, $optasktpl, $notClose);
    }

    // 单个任务: 患者 + condFix
    public static function getOneByPatient (Patient $patient, $condFix = "") {
        $cond = "AND patientid = :patientid {$condFix}";
        $bind = array(
            ":patientid" => $patient->id);
        return Dao::getEntityByCond("OpTask", $cond, $bind);
    }

    // 单个任务: patientid + doctorid + optasktplid
    // 只保留了一处调用点
    public static function getOneByPatientidDoctoridOptasktplid ($patientid, $doctorid, $optasktplid, $notClose = true) {
        $condfix = " ";
        if ($notClose) {
            $condfix = " and status in (0, 2) ";
        }

        $cond = " and patientid = :patientid and doctorid = :doctorid and optasktplid = :optasktplid {$condfix} order by createtime desc ";
        $bind = [
            ':patientid' => $patientid,
            ':doctorid' => $doctorid,
            ':optasktplid' => $optasktplid];

        return Dao::getEntityByCond('OpTask', $cond, $bind);
    }

    // 单个任务: 患者 + 任务模板
    public static function getOneByPatientOptasktpl (Patient $patient, OpTaskTpl $optasktpl, $notClose = true) {
        $condfix = '';
        if ($notClose) {
            $condfix = " and status in (0, 2) ";
        }

        $cond = " and patientid = :patientid and optasktplid = :optasktplid {$condfix} order by status asc, createtime desc ";

        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':optasktplid'] = $optasktpl->id;

        return Dao::getEntityByCond('OpTask', $cond, $bind);
    }

    // 单个任务: 患者 + unicode
    public static function getOneByPatientUnicode (Patient $patient, $unicode, $notClose = true) {
        $optasktpl = OpTaskTplDao::getOneByUnicode($unicode);
        DBC::requireNotEmpty($optasktpl, "任务模板[{$unicode}]不存在");

        return OpTaskDao::getOneByPatientOptasktpl($patient, $optasktpl, $notClose);
    }

    // ==============获取任务个数, TODO fhw : 改成 select count(*)
    public static function getCntByPatient (Patient $patient) {
        $list = OpTaskDao::getListByPatient($patient);

        return count($list);
    }

    // 根据条件获取任务个数
    // 支持根据 optasks 表中字段进行 group by
    public static function getCntByAuditorAndDonetimeAndStart ($auditor_id, $startTime, $endTime, $status=0, $groupBy=false) {
        $sql = "SELECT b.title AS `name`,COUNT(*) AS `value` FROM optasks a
                  LEFT JOIN optasktpls b ON a.optasktplid=b.id
                WHERE a.auditorid=:auditorid 
                  AND a.status=:status 
                  AND a.donetime >= :startTime
                  AND a.donetime < :endTime
                ";

        $bind = [];
        $bind[':auditorid'] = $auditor_id;
        $bind[':startTime'] = $startTime;
        $bind[':endTime'] = $endTime;
        $bind[':status'] = $status;

        if($groupBy) {
            $sql .= " GROUP BY a.{$groupBy}";
        }

        return Dao::queryRows($sql,$bind);
    }
}
