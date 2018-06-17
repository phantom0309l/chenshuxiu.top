<?php
/*
 * PipeDao
 */
class PipeDao extends Dao
{
    // 名称: getAllPipesOfPatient
    // 备注: 获取全部流
    // 创建:
    // 修改:
    public static function getAllPipesOfPatient ($patientid, $contFix = "") {
        $cond = "and patientid = :patientid
            {$contFix}
            order by createtime ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond("Pipe", $cond, $bind);
    }

    // 名称: getByEntity
    // 备注:
    // 创建:
    // 修改:
    public static function getByEntity (Entity $entity, $objcode = "create") {
        $objtype = get_class($entity);
        $objid = $entity->id;

        $cond = " and objtype = :objtype and objid = :objid and objcode = :objcode ";

        $bind = array(
            ":objtype" => $objtype,
            ":objid" => $objid,
            ":objcode" => $objcode);

        return Dao::getEntityByCond("Pipe", $cond, $bind);
    }

    // 名称: getCntByObjcode
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByObjcode ($objcode) {
        $ids = UserDao::getTestUseridsstr();

        $sql = " select count(*)
            from pipes
            where objcode = :objcode and userid NOT IN ({$ids}) ";

        $bind = array(
            ":objcode" => $objcode);

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getLastPipeByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getLastPipeByPatientid ($patientid) {
        $cond = " and patientid = :patientid
            order by createtime desc
            limit 1";

        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityByCond("Pipe", $cond, $bind);
    }

    // 名称: getLastPipeByUser
    // 备注:获取最新的那条用户行为流, todate 为截至日期
    // 创建:
    // 修改:
    public static function getLastPipeByUser ($patientid, $todate = '') {
        $bind = [];
        $bind[':patientid'] = $patientid;

        $cond = '';
        if ($todate) {
            $cond = " and createtime < :todate ";
            $bind[':todate'] = $todate;
        }

        $cond .= " and patientid = :patientid and subdomain='wx'
            order by createtime desc
            limit 1";

        return Dao::getEntityByCond("Pipe", $cond, $bind);
    }

    // 名称: getOneByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByPatientid ($patientid, $condEx = "") {
        $cond = " and patientid = :patientid {$condEx}";
        $bind = [];
        $bind[':patientid'] = $patientid;
        return Dao::getEntityByCond("Pipe", $cond, $bind);
    }

    // 名称: getListByUser
    // 备注:
    // 创建:
    // 修改:
    public static function getListByUser (User $user) {
        $cond = " AND userid = :userid ";

        $bind = array(
            ":userid" => $user->id);

        return Dao::getEntityListByCond("Pipe", $cond, $bind);
    }

    // 名称: getListByUser
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatient (Patient $patient, $condEx = "") {
        $cond = " AND patientid = :patientid {$condEx}";

        $bind = array(":patientid" => $patient->id);

        return Dao::getEntityListByCond("Pipe", $cond, $bind);
    }

    // 名称: 获取实体关联的流列表
    // 备注:
    // 创建: 20160919 by sjp
    // 修改:
    public static function getListByEntity (Entity $entity) {
        $objtype = get_class($entity);
        $objid = $entity->id;

        $cond = " and objtype = :objtype and objid = :objid  ";

        $bind = array(
            ":objtype" => $objtype,
            ":objid" => $objid);

        return Dao::getEntityListByCond("Pipe", $cond, $bind);
    }

    // 名称: getPatientLastPipeByObjtype
    // 备注:
    // 创建:
    // 修改:
    public static function getPatientLastPipeByObjtype ($patientid, $objtype, $objcode = "") {
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':objtype'] = $objtype;

        $cond = "";
        if (! empty($objcode)) {
            $cond .= " and objcode = '{$objcode}'";
        }

        $cond .= " and patientid = :patientid and objtype = :objtype
            order by createtime desc
            limit 1";

        return Dao::getEntityByCond("Pipe", $cond, $bind);
    }

    // 名称: getPipecntByDateYm
    // 备注:
    // 创建:
    // 修改:
    public static function getPipecntByDateYm ($patientid, $themonth) {
        $sql = " SELECT count(*) as cnt
            from pipes
            where patientid = :patientid
                AND LEFT (createtime, 7) = :themonth
                AND (objtype IN ('DrugItem','Paper', 'WxTxtMsg', 'WxPicMsg', 'WxVoiceMsg', 'PatientNote' )
                    or (objtype = 'Patient' AND  objcode = 'baodao')
                    or (objtype = 'LessonUserRef' AND (objcode = 'hwk' or objcode = 'test'))
                    or (objtype = 'Comment' AND objcode = 'share')) ";

        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':themonth'] = $themonth;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getPreListByPatientOrderByTime
    // 备注:获取某条前面的 cnt 条数据
    // 创建:
    // 修改:
    public static function getPreListByPatientOrderByTime ($patientid, $cnt = 10, $offsetpipetime = '', $condEx = '') {
        $cnt = intval($cnt);

        $cond = " and patientid = :patientid ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        if ($offsetpipetime) {
            $cond .= " and createtime < :offsetpipetime ";
            $bind[':offsetpipetime'] = $offsetpipetime;
        }

        $cond .= " {$condEx}
            order by createtime desc
            limit {$cnt}";

        return Dao::getEntityListByCond("Pipe", $cond, $bind);
    }
}
