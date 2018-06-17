<?php
/*
 * DoctorConfigDao
 */
class DoctorConfigDao extends Dao
{
    // 名称: getListByDoctorid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDoctorid ($doctorid) {

        $sql = "select a.*
                from doctorconfigs a
                join doctorconfigtpls b on b.id=a.doctorconfigtplid
                where a.doctorid=:doctorid
                order by b.pos asc";

        $bind = [];
        $bind[':doctorid'] = $doctorid;

        return Dao::loadEntityList("DoctorConfig", $sql, $bind);
    }

    // 名称: getByDoctoridDoctorConfigTplid
    // 备注:
    // 创建:
    // 修改:
    public static function getByDoctoridDoctorConfigTplid ( $doctorid, $doctorconfigtplid ) {

        $cond = " AND doctorid=:doctorid AND  doctorconfigtplid=:doctorconfigtplid ";
        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':doctorconfigtplid'] = $doctorconfigtplid;

        return Dao::getEntityByCond("DoctorConfig", $cond, $bind);
    }
}
