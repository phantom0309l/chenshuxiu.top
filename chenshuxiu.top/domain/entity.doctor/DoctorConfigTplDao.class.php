<?php
/*
 * DoctorConfigTplDao
 */
class DoctorConfigTplDao extends Dao
{
    // 名称: getByCode
    // 备注:
    // 创建:
    // 修改:
    public static function getByCode ($code) {

        $cond = " AND code=:code ";
        $bind = [];
        $bind[':code'] = $code;

        return Dao::getEntityByCond("DoctorConfigTpl", $cond, $bind);
    }
}
