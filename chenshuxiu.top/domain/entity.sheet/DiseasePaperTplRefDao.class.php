<?php

class DiseasePaperTplRefDao extends Dao
{

    // 名称: getByDiseaseAndPaperTpl
    // 备注:
    public static function getByDiseaseAndPaperTpl (Disease $disease, PaperTpl $papertpl) {
        $cond = ' and diseaseid = :diseaseid and papertplid = :papertplid and doctorid=0 ';
        $bind = array(
            ":diseaseid" => $disease->id,
            ":papertplid" => $papertpl->id);

        return Dao::getEntityByCond("DiseasePaperTplRef", $cond, $bind);
    }

    // 名称: getByDoctorAndPaperTpl
    // 备注:
    // 创建:
    // 修改:
    public static function getByDoctorAndPaperTpl (Doctor $doctor, PaperTpl $papertpl) {
        $cond = ' and doctorid = :doctorid and papertplid = :papertplid ';
        $bind = array(
            ":doctorid" => $doctor->id,
            ":papertplid" => $papertpl->id);

        return Dao::getEntityByCond("DiseasePaperTplRef", $cond, $bind);
    }

    // 名称: getByDoctorAndPaperTpl
    // 备注:
    // 创建:
    // 修改:
    public static function getByDoctorAndDiseaseAndPaperTpl (Doctor $doctor, Disease $disease, PaperTpl $papertpl) {
        $cond = ' AND doctorid = :doctorid AND diseaseid = :diseaseid AND papertplid = :papertplid ';
        $bind = [
            ":doctorid" => $doctor->id,
            ":diseaseid" => $disease->id,
            ":papertplid" => $papertpl->id,
        ];

        return Dao::getEntityByCond("DiseasePaperTplRef", $cond, $bind);
    }

    // 名称: getListByPaperTpl
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPaperTpl (PaperTpl $papertpl) {
        $cond = ' and papertplid = :papertplid ';
        $bind = [];
        $bind[":papertplid"] = $papertpl->id;

        return Dao::getEntityListByCond("DiseasePaperTplRef", $cond, $bind);
    }

    // 名称: getListByDisease
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDisease (Disease $disease, $show_in_audit = null, $show_in_wx = null) {
        $cond = ' and diseaseid = :diseaseid ';
        $bind = [];
        $bind[":diseaseid"] = $disease->id;

        if (! is_null($show_in_audit)) {
            $cond .= ' and show_in_audit = :show_in_audit ';
            $bind[":show_in_audit"] = $show_in_audit;
        }

        if (! is_null($show_in_wx)) {
            $cond .= ' and show_in_wx = :show_in_wx ';
            $bind[":show_in_wx"] = $show_in_wx;
        }

        $cond .= " order by pos,id ";

        return Dao::getEntityListByCond("DiseasePaperTplRef", $cond, $bind);
    }

    // 名称: getListByPapertplidDoctorid
    public static function getListByPapertplidDoctorid ($papertplid, $doctorid, $show_in_audit = null, $show_in_wx = null) {
        $cond = ' and papertplid = :papertplid and doctorid = :doctorid ';
        $bind = [];
        $bind[":papertplid"] = $papertplid;
        $bind[":doctorid"] = $doctorid;

        if (! is_null($show_in_audit)) {
            $cond .= ' and show_in_audit = :show_in_audit ';
            $bind[":show_in_audit"] = $show_in_audit;
        }

        if (! is_null($show_in_wx)) {
            $cond .= ' and show_in_wx = :show_in_wx ';
            $bind[":show_in_wx"] = $show_in_wx;
        }

        $cond .= " and diseaseid > 0 order by pos,id ";

        return Dao::getEntityListByCond("DiseasePaperTplRef", $cond, $bind);
    }

    // 名称: getListByDiseaseidDoctorid
    // 备注: 肿瘤方向, 绑定医生的
    public static function getListByDiseaseidDoctorid ($diseaseid, $doctorid, $show_in_audit = null, $show_in_wx = null) {
        $cond = ' and diseaseid = :diseaseid and doctorid = :doctorid ';
        $bind = [];
        $bind[":diseaseid"] = $diseaseid;
        $bind[":doctorid"] = $doctorid;

        if (! is_null($show_in_audit)) {
            $cond .= ' and show_in_audit = :show_in_audit ';
            $bind[":show_in_audit"] = $show_in_audit;
        }

        if (! is_null($show_in_wx)) {
            $cond .= ' and show_in_wx = :show_in_wx ';
            $bind[":show_in_wx"] = $show_in_wx;
        }

        $cond .= " order by pos,id ";

        return Dao::getEntityListByCond("DiseasePaperTplRef", $cond, $bind);
    }

    // 名称: getListByDiseaseidOrDoctorid
    // 备注: 其他方向, 疾病共用 + 医生专用的
    public static function getListByDiseaseidOrDoctorid ($diseaseid, $doctorid, $show_in_audit = null, $show_in_wx = null) {
        $sql = 'select distinct *
                from  diseasepapertplrefs
                where (( diseaseid = :diseaseid and doctorid=0 ) or (doctorid = :doctorid and diseaseid = :diseaseid )) ';
        $bind = [];
        $bind[":diseaseid"] = $diseaseid;
        $bind[":doctorid"] = $doctorid;

        if (! is_null($show_in_audit)) {
            $sql .= ' and show_in_audit = :show_in_audit ';
            $bind[":show_in_audit"] = $show_in_audit;
        }

        if (! is_null($show_in_wx)) {
            $sql .= ' and show_in_wx = :show_in_wx ';
            $bind[":show_in_wx"] = $show_in_wx;
        }

        $sql .= " order by doctorid, pos ";

        return Dao::loadEntityList("DiseasePaperTplRef", $sql, $bind);
    }

    // 名称: getListByDoctor
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDoctor (Doctor $doctor, $show_in_audit = null, $show_in_wx = null) {
        $cond = ' and doctorid = :doctorid ';
        $bind = [];
        $bind[":doctorid"] = $doctor->id;

        if (! is_null($show_in_audit)) {
            $cond .= ' and show_in_audit = :show_in_audit ';
            $bind[":show_in_audit"] = $show_in_audit;
        }

        if (! is_null($show_in_wx)) {
            $cond .= ' and show_in_wx = :show_in_wx ';
            $bind[":show_in_wx"] = $show_in_wx;
        }

        $cond .= " order by pos,id ";

        return Dao::getEntityListByCond("DiseasePaperTplRef", $cond, $bind);
    }
}
