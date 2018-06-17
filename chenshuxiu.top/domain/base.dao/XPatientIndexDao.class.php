<?php

/*
 * XPatientIndexDao
 */
class XPatientIndexDao extends Dao
{
    // 名称: getByPatientidWord
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientidWord ($patientid, $word) {
        $cond = " and patientid=:patientid and word=:word";
        $bind = array(
            ':patientid' => $patientid,
            ':word' => $word);

        return Dao::getEntityByCond('XPatientIndex', $cond, $bind);
    }

    // 名称: getByPatientidType
    // 备注:
    // 创建:
    // 修改:
    public static function getByPatientidTypeWord ($patientid, $type, $word) {
        $cond = " and patientid=:patientid and type=:type and word=:word ";
        $bind = [
            ':patientid' => $patientid,
            ':type' => $type,
            ':word' => $word
        ];

        return Dao::getEntityByCond('XPatientIndex', $cond, $bind);
    }

    // 名称: getListByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientid ($patientid, $type = '') {
        $cond = " and patientid = :patientid ";
        $bind = [];
        $bind[':patientid'] = $patientid;

        if ($type) {
            $cond .= ' and type=:type ';
            $bind[':type'] = $type;
        }

        return Dao::getEntityListByCond('XPatientIndex', $cond, $bind);
    }
}