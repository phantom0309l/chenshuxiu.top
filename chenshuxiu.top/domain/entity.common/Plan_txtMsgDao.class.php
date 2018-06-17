<?php

/*
 * Plan_txtMsgDao
 */

class Plan_txtMsgDao extends Dao
{

    /**
     * 最后一条定时消息 by obj
     * @param Entity $obj
     * @return Plan_txtMsg | null
     */
    public static function getByObj(Entity $obj) {
        $objtype = get_class($obj);
        $objid = $obj->id;

        $cond = " AND objtype = :objtype AND objid = :objid ORDER BY id DESC LIMIT 1";
        $bind = [
            ":objtype" => $objtype,
            ":objid" => $objid,
        ];

        return Dao::getEntityByCond("Plan_txtMsg", $cond, $bind);
    }

    /**
     * 最后一条定时消息 by patientid
     * @param $patientid
     * @return Plan_txtMsg | null
     */
    public static function getByPatientid($patientid) {
        $cond = " AND patientid = :patientid ORDER BY id DESC LIMIT 1";
        $bind = [
            ":patientid" => $patientid,
        ];

        return Dao::getEntityByCond("Plan_txtMsg", $cond, $bind);
    }

    /**
     * 全部列表 by obj
     * @param Entity $obj
     * @return array
     */
    public static function getListByObj(Entity $obj) {
        $objtype = get_class($obj);
        $objid = $obj->id;

        $cond = " AND objtype = :objtype AND objid = :objid ";
        $bind = [
            ":objtype" => $objtype,
            ":objid" => $objid,
        ];

        return Dao::getEntityListByCond("Plan_txtMsg", $cond, $bind);
    }

    /**
     * 全部列表 by patientid
     * @param $patientid
     * @return array
     */
    public static function getListByPatientid($patientid) {
        $cond = " AND patientid = :patientid ORDER BY id ASC";
        $bind = [
            ":patientid" => $patientid,
        ];

        return Dao::getEntityListByCond("Plan_txtMsg", $cond, $bind);
    }

    /**
     * 未发送列表 by obj
     * @param Entity $obj
     * @return array
     */
    public static function getUnsentListByObj(Entity $obj) {
        $objtype = get_class($obj);
        $objid = $obj->id;

        $cond = " AND objtype = :objtype AND objid = :objid AND pushmsgid = 0 ";
        $bind = [
            ":objtype" => $objtype,
            ":objid" => $objid,
        ];

        return Dao::getEntityListByCond("Plan_txtMsg", $cond, $bind);
    }

    /**
     * 未发送列表 by patientid
     * @param $patientid
     * @return array
     */
    public static function getUnsentListByPatientid($patientid) {
        $cond = " AND patientid = :patientid AND pushmsgid = 0 ORDER BY id ASC ";
        $bind = [
            ":patientid" => $patientid,
        ];

        return Dao::getEntityListByCond("Plan_txtMsg", $cond, $bind);
    }

    /**
     * 未发送列表 by patientid
     * @param $patientid
     * @return array
     */
    public static function getUnsentListByPatientidCode($patientid, $code) {
        $cond = " AND patientid = :patientid and code = :code AND pushmsgid = 0 ORDER BY id ASC ";
        $bind = [
            ":patientid" => $patientid,
            ":code" => $code
        ];

        return Dao::getEntityListByCond("Plan_txtMsg", $cond, $bind);
    }

    /**
     * 当天未发送列表 by patientid
     * @param $patientid
     * @return array
     */
    public static function getTodayUnsentListByPatientid($patientid) {
        $from_date = date('Y-m-d') . ' 00:00:00';
        $to_date = date('Y-m-d') . ' 23:59:59';
        $cond = " AND patientid = :patientid AND pushmsgid = 0 AND plan_send_time BETWEEN :from AND :to ORDER BY id ASC ";
        $bind = [
            ":patientid" => $patientid,
            ":from" => $from_date,
            ":to" => $to_date,
        ];

        return Dao::getEntityListByCond("Plan_txtMsg", $cond, $bind);
    }

    /**
     * 未发送数量 by obj
     * @param Entity $obj
     * @return mixed
     */
    public static function getUnsentCntByObj(Entity $obj) {
        $objtype = get_class($obj);
        $objid = $obj->id;

        $sql = "SELECT COUNT(*)
                FROM plan_txtmsgs 
                WHERE objtype = :objtype 
                AND objid = :objid 
                AND pushmsgid = 0
                ORDER BY id DESC 
                LIMIT 1";
        $bind = [
            ":objtype" => $objtype,
            ":objid" => $objid,
        ];

        return Dao::queryValue($sql, $bind);
    }

    /**
     * 未发送数量 by patientid
     * @param $patientid
     * @return mixed
     */
    public static function getUnsentCntByPatientid($patientid) {
        $sql = "SELECT COUNT(*)
                FROM plan_txtmsgs 
                WHERE patientid = :patientid
                AND pushmsgid = 0 ";
        $bind = [
            ':patientid' => $patientid
        ];

        return Dao::queryValue($sql, $bind);
    }

    /**
     * 总数量 by patientid
     * @param $patientid
     * @return mixed
     */
    public static function getCntByPatientid($patientid) {
        $sql = "SELECT COUNT(*)
                FROM plan_txtmsgs 
                WHERE patientid = :patientid";
        $bind = [
            ':patientid' => $patientid
        ];

        return Dao::queryValue($sql, $bind);
    }

    /**
     * 总数量 by obj
     * @param Entity $obj
     * @return mixed
     */
    public static function getCntByObj(Entity $obj) {
        $objtype = get_class($obj);
        $objid = $obj->id;

        $sql = "SELECT COUNT(*)
                FROM plan_txtmsgs 
                WHERE objtype = :objtype 
                AND objid = :objid 
                ORDER BY id DESC 
                LIMIT 1";
        $bind = [
            ":objtype" => $objtype,
            ":objid" => $objid,
        ];

        return Dao::queryValue($sql, $bind);
    }

}