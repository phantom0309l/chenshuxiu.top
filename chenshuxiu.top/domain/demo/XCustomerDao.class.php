<?php

/*
 * XCustomerDao
 */
class XCustomerDao extends Dao
{

    public static function getByName ($customername) {
        $cond = "and name=:customername ";
        $bind = [];
        $bind[':customername'] = $customername;
        return Dao::getEntityByCond('XCustomer', $cond, $bind);
    }
}