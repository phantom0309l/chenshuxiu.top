<?php
// CommentDao

// owner by xxx
// create by sjp
// review by sjp 20160627
class CommentDao extends Dao
{
    // 名称: getArrayOfDoctor
    // 备注: 某Doctor所有操作记录, 修改历史, 可以用 getListByObjtypeObjid 替换 TODO rework
    // 创建:
    // 修改:
    public static function getArrayOfDoctor ($doctorid, $objtype = "Doctor", $pagesize = 100, $pagenum = 1) {
        $cond = " and objid=:objid and objtype=:objtype order by id desc ";
        $bind = array(
            ":objid" => $doctorid,
            ":objtype" => $objtype);

        return Dao::getEntityListByCond4Page("Comment", $pagesize, $pagenum, $cond, $bind);
    }

    // 名称: getByUserObjTypestr
    // 备注: 个性需求: 某个用户针对 obj+typestr 的 一个comment TODO rework
    // 创建:
    // 修改:
    public static function getByUserObjTypestr (User $user, $obj, $typestr) {
        $cond = ' and userid=:userid and objtype=:objtype and objid=:objid and typestr=:typestr ';
        $bind = array(
            'userid' => $user->id,
            'objtype' => get_class($obj),
            'objid' => $obj->id,
            'typestr' => $typestr);

        return Dao::getEntityByCond("Comment", $cond, $bind);
    }

    // 名称: getCntByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByPatientid ($patientid, $condFix = '') {
        $sql = " SELECT count(*) FROM comments WHERE patientid = :patientid " . $condFix;

        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getCntOfObj
    // 备注: 某obj的评论数
    // 创建:
    // 修改:
    public static function getCntOfObj ($objtype, $objid) {
        $sql = "select count(*) from comments where objid=:objid and objtype=:objtype ";

        $bind = array(
            ":objid" => $objid,
            ":objtype" => $objtype);

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getListByCourseType
    // 备注: 可以用 getListByObjtypeObjidTypestr 替换 TODO rework
    // 创建:
    // 修改:
    public static function getListByCourseType (Course $course, $typestr) {
        $bind = array(
            ":objid" => $course->id,
            ":typestr" => $typestr);

        $cond = " and objtype='Course' and objid=:objid and typestr=:typestr order by createtime desc ";
        return Dao::getEntityListByCond("Comment", $cond, $bind);
    }

    // 名称: getListByObjtypeObjid
    // 备注: 获取某个 obj 的 comment 列表
    // 创建:
    // 修改:
    public static function getListByObjtypeObjid ($objtype, $objid, $condex = '') {
        $cond = "AND objtype=:objtype AND objid=:objid $condex ";
        $bind = array(
            ":objtype" => $objtype,
            ":objid" => $objid);
        return Dao::getEntityListByCond("Comment", $cond, $bind);
    }

    // 名称: getListByObjtypeObjidTypestr
    // 备注:获取某个 obj+typestr 的 comment 列表
    // 创建:
    // 修改:
    public static function getListByObjtypeObjidTypestr ($objtype, $objid, $typestr, $condex = '') {
        $cond = " AND objtype=:objtype AND objid=:objid AND typestr=:typestr $condex ";
        $bind = array(
            ":objtype" => $objtype,
            ":objid" => $objid,
            ":typestr" => $typestr);
        return Dao::getEntityListByCond("Comment", $cond, $bind);
    }

    // 名称: getOneByObjtypeObjid
    // 备注: 获取某个 obj 的第一条comment, 目前只有一处调用点
    // 创建:
    // 修改:
    public static function getOneByObjtypeObjid ($objtype, $objid, $condex = '') {
        $cond = "AND objtype=:objtype AND objid=:objid $condex ";
        $bind = array(
            ":objtype" => $objtype,
            ":objid" => $objid);
        return Dao::getEntityByCond("Comment", $cond, $bind);
    }

    // 名称: getPreListByObj
    // 备注: 获取某条前面的 cnt 条数据, Entity::getComments 调用了本函数
    // 创建:
    // 修改:
    public static function getPreListByObj ($objtype, $objid, $cnt = 10, $offsetcommentid = 0) {
        $cnt = intval($cnt);

        $cond = " and objtype=:objtype and objid=:objid ";

        $bind = [];
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;

        if ($offsetcommentid > 0) {
            $cond .= " and id < :offsetcommentid ";
            $bind[':offsetcommentid'] = $offsetcommentid;
        }

        $cond .= " order by id desc limit {$cnt}";

        return Dao::getEntityListByCond("Comment", $cond, $bind);
    }

    // 名称: getZongjieListByUserid
    // 备注: 个性需求 TODO rework
    // 创建:
    // 修改:
    public static function getZongjieListByUserid ($userid) {
        $cond = " and userid=:userid and typestr='zongjie' ";
        $bind = array(
            ':userid' => $userid);
        return Dao::getEntityListByCond("Comment", $cond, $bind);
    }
}
