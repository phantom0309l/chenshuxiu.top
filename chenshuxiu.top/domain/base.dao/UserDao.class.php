<?php

/*
 * UserDao
 */
class UserDao extends Dao
{

    private static $_testuserid = array();

    // 名称: getByLikeMobile
    public static function getByLikeMobile ($mobile) {
        $bind = [];
        $cond = " AND mobile like :mobile  ";
        $bind[':mobile'] = "%{$mobile}%";

        return Dao::getEntityByCond("User", $cond, $bind);
    }

    // 名称: getByMobile
    public static function getByMobile ($mobile) {
        $bind = [];
        $cond = " AND mobile = :mobile  ";
        $bind[':mobile'] = $mobile;

        return Dao::getEntityByCond("User", $cond, $bind);
    }

    // 名称: getByToken
    public static function getByToken ($token) {
        $cond = " AND token=:token ";

        $bind = array(
            ":token" => $token);

        return Dao::getEntityByCond("User", $cond, $bind);
    }

    // 名称: getByUnionid
    public static function getByUnionid ($unionid) {
        if (! $unionid) {
            return null;
        }

        $cond = " AND unionid = :unionid  ";

        $bind = [];
        $bind[':unionid'] = $unionid;

        return Dao::getEntityByCond("User", $cond, $bind);
    }

    // 名称: getByUsername
    public static function getByUsername ($username) {
        $username = trim($username);
        if (empty($username)) {
            return null;
        }

        $cond = " AND ( (username = :username) or (username <>'' and mobile = :mobile) ) ";

        $bind = [];
        $bind[':username'] = $username;
        $bind[':mobile'] = $username;

        return Dao::getEntityByCond("User", $cond, $bind);
    }

    // 名称: getByXcode
    public static function getByXcode ($xcode) {
        $cond = " and xcode=:xcode ";

        $bind = array(
            ":xcode" => $xcode);

        return Dao::getEntityByCond("User", $cond, $bind);
    }

    // 名称: getLianxirenByPatientid
    public static function getLianxirenByPatientid ($patientid) {
        $cond = " and patientid = :patientid and shipstr != :shipstr ";

        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':shipstr'] = '本人';

        return Dao::getEntityByCond("User", $cond, $bind);
    }

    // 名称: getListByPatient
    public static function getListByPatient ($patientid) {
        $cond = " AND patientid = :patientid order by id ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond("User", $cond, $bind);
    }

    // 名称: getMyselfByPatientid
    public static function getMyselfByPatientid ($patientid) {
        $cond = " and patientid = :patientid and shipstr = :shipstr ";

        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':shipstr'] = '本人';

        return Dao::getEntityByCond("User", $cond, $bind);
    }

    // 名称: getTestUserids
    // 备注: 获取测试userid
    public static function getTestUserids () {
        if (false == empty(self::$_testuserid)) {
            return self::$_testuserid;
        }

        $sql = " ( select distinct x.id
            from users x
            inner join pcards a on a.patientid = x.patientid
            inner join doctors b on b.id = a.doctorid
            inner join hospitals c on c.id = b.hospitalid
            where c.id=5
            order by x.id )
            union
            ( select userid as id from  auditors ) ";

        $arr = Dao::queryValues($sql, []);

        $arr[] = 100000157; // 团团
        $arr[] = 100138096; // 陈敏测试
        $arr[] = 51; // 小皮球

        self::$_testuserid = $arr;

        return $arr;
    }

    // 名称: getTestUseridsStr
    // 备注: 获取测试userid str
    public static function getTestUseridsStr () {
        $ids = self::getTestUserids();
        return implode(",", $ids);
    }
}
