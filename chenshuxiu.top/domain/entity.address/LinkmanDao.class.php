<?php
/*
 * LinkmanDao
 */
class LinkmanDao extends Dao {
    public static function getListByPatientid ($patientid) {
        $cond = " and patientid = :patientid order by id desc ";
        $bind = [
            ':patientid' => $patientid
        ];

        return Dao::getEntityListByCond('Linkman', $cond, $bind);
    }

    public static function getListByUseridPatientid ($userid, $patientid) {
        $cond = " and userid = :userid and patientid = :patientid order by id desc ";
        $bind = [
            ':userid' => $userid,
            ':patientid' => $patientid
        ];

        return Dao::getEntityListByCond('Linkman', $cond, $bind);
    }

    public static function getByPatientidMobile ($patientid, $mobile) {
        $cond = " and patientid = :patientid and mobile = :mobile ";
        $bind = [
            ':patientid' => $patientid,
            ':mobile' => $mobile
        ];

        return Dao::getEntityByCond('Linkman', $cond, $bind);
    }

    public static function getByMobile ($mobile) {
        $cond = " and mobile = :mobile ";
        $bind = [
            ':mobile' => $mobile
        ];

        return Dao::getEntityByCond('Linkman', $cond, $bind);
    }


    public static function getByUseridMobile ($userid, $mobile) {
        $cond = " and userid = :userid and mobile = :mobile ";
        $bind = [
            ':userid' => $userid,
            ':mobile' => $mobile
        ];

        return Dao::getEntityByCond('Linkman', $cond, $bind);
    }

    public static function getMasterByPatientid ($patientid) {
        $cond = " and patientid = :patientid and is_master = 1 ";
        $bind = [
            ':patientid' => $patientid
        ];

        return Dao::getEntityByCond('Linkman', $cond, $bind);
    }

    /**
     * 次要联系人列表
     * @param $patientid
     * @return array
     */
    public static function getOtherListByPatientid ($patientid) {
        $cond = " AND patientid = :patientid AND is_master = 0 ORDER BY id ASC ";
        $bind = [
            ':patientid' => $patientid
        ];

        return Dao::getEntityListByCond('Linkman', $cond, $bind);
    }

    /**
     * 最早的次要联系人
     * @param $patientid
     * @return array
     */
    public static function getOtherByPatientid ($patientid) {
        $cond = " AND patientid = :patientid AND is_master = 0 ORDER BY id ASC limit 1 ";
        $bind = [
            ':patientid' => $patientid
        ];

        return Dao::getEntityByCond('Linkman', $cond, $bind);
    }
}
