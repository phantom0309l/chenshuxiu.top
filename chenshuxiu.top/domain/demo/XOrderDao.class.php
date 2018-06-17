<?php

/*
 * XOrderDao
 */
class XOrderDao extends Dao
{

    public static function getXOrderNumByXCustomer (XCustomer $xcustomer) {
        $sql = "select count(*) from xorders where xcustomerid=:xcustomerid ";
        $bind = [];
        $bind[':xcustomerid'] = $xcustomer->id;
        return Dao::queryValue($sql, $bind);
    }
}