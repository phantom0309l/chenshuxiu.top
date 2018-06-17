<?php
    /*
     * DoctorServiceOrderTplDao
     */
class DoctorServiceOrderTplDao extends Dao {

    public static function getOneByEname ($ename) {
        $cond = " and ename = :ename";
        $bind = [];
        $bind[':ename'] = $ename;

        return Dao::getEntityByCond('DoctorServiceOrderTpl', $cond, $bind);
    }

}
