<?php
/*
 * StudyDao
 */
class StudyDao extends Dao {

    // 名称: getListByStudyplanid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByStudyplanid ($studyplanid, $condEx="") {
        $cond = " and studyplanid = :studyplanid {$condEx} ";
        $bind = [];
        $bind[':studyplanid'] = $studyplanid;

        return Dao::getEntityListByCond('Study', $cond, $bind);
    }

    // 名称: getOneByStudyplanid
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByStudyplanid ($studyplanid, $condEx="") {
        $cond = " and studyplanid = :studyplanid {$condEx} ";
        $bind = [];
        $bind[':studyplanid'] = $studyplanid;

        return Dao::getEntityByCond('Study', $cond, $bind);
    }

    // 名称: getOneByStudyplanidThedate
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByStudyplanidThedate ($studyplanid, $thedate, $condEx="") {
        $cond = " and studyplanid = :studyplanid and left(createtime, 10) = :thedate {$condEx} ";
        $bind = [];
        $bind[':studyplanid'] = $studyplanid;
        $bind[':thedate'] = $thedate;

        return Dao::getEntityByCond('Study', $cond, $bind);
    }

}
