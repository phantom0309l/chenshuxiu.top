<?php

/*
 * PaperTplDao
 */
class PaperTplDao extends Dao
{
    // 名称: getByEname
    // 备注:
    // 创建:
    // 修改:
    public static function getByEname ($ename) {
        $bind = [];
        $bind[":ename"] = $ename;

        return Dao::getEntityByBind("PaperTpl", $bind);
    }

    // 名称: getByGroupstr
    // 备注:
    // 创建:
    // 修改:
    public static function getListByGroupstr ($groupstr) {
        $cond = ' AND groupstr=:groupstr';
        $bind = [];
        $bind[":groupstr"] = $groupstr;
        return Dao::getEntityListByCond("PaperTpl", $cond, $bind);
    }

    // 名称: getListByDiseaseid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDiseaseid ($diseaseid, $condEx = "") {
        $sql = "SELECT distinct a.*
            FROM papertpls a
            INNER JOIN diseasepapertplrefs b ON b.papertplid = a.id
            WHERE b.diseaseid = :diseaseid {$condEx}";

        $bind = [];
        $bind[':diseaseid'] = $diseaseid;

        return Dao::loadEntityList("PaperTpl", $sql, $bind);
    }

    // 名称: getListByDoctorid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDoctorid ($doctorid, $condEx = "") {
        $sql = "SELECT a.*
            FROM papertpls a
            INNER JOIN diseasepapertplrefs b ON b.papertplid = a.id
            WHERE b.doctorid = :doctorid {$condEx}";

        $bind = [];
        $bind[':doctorid'] = $doctorid;

        return Dao::loadEntityList("PaperTpl", $sql, $bind);
    }

    // 名称: getListByDoctoridDiseaseid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDoctoridDiseaseid ($doctorid, $diseaseid, $condEx = "") {
        if (Disease::isCancer($diseaseid)) {
            $sql = "SELECT distinct a.*
            FROM papertpls a
            INNER JOIN diseasepapertplrefs b ON b.papertplid = a.id
            WHERE b.diseaseid = :diseaseid  and b.doctorid = :doctorid {$condEx}";
        } else {
            $sql = "SELECT distinct a.*
            FROM papertpls a
            INNER JOIN diseasepapertplrefs b ON b.papertplid = a.id
            WHERE b.diseaseid = :diseaseid  and (b.doctorid = :doctorid or b.doctorid=0) {$condEx}";
        }

        $bind = [];
        $bind[':diseaseid'] = $diseaseid;
        $bind[':doctorid'] = $doctorid;

        return Dao::loadEntityList("PaperTpl", $sql, $bind);
    }

    // getAllList
    public static function getAllList () {
        return Dao::getEntityListByCond("PaperTpl");
    }

    // 获取有问卷，且问卷不为空的papertpl
    public static function getNotXquestionSheetList () {
        $sql = "select distinct a.*
            from papertpls a
            inner join xquestionsheets b on b.id = a.xquestionsheetid
            inner join xquestions c on c.xquestionsheetid = b.id
            order by title";
        return Dao::loadEntityList("PaperTpl", $sql);
    }
}
