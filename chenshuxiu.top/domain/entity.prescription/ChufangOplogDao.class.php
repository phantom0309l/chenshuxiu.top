<?php

/*
 * ChufangOplogDao
 */

class ChufangOplogDao extends Dao
{
    public static function getListAll() {
        return Dao::getEntityListByCond("ChufangOplog");
    }

    public static function getEntityListByPrescriptionId4Page($prescriptionid, $pagesize, $pagenum) {
        $cond = '';
        $bind = [];
        if (!empty($prescriptionid)) {
            $cond .= " AND prescriptionid = :prescriptionid ";
            $bind[':prescriptionid'] = $prescriptionid;
        }
        $cond .= ' ORDER BY id DESC ';
        return Dao::getEntityListByCond4Page('ChufangOplog', $pagesize, $pagenum, $cond, $bind);
    }

    public static function getCountByPrescriptionId($prescriptionid) {
        $cond = '';
        $bind = [];
        if (!empty($prescriptionid)) {
            $cond .= " AND prescriptionid = :prescriptionid ";
            $bind[':prescriptionid'] = $prescriptionid;
        }
        $sql = 'SELECT COUNT(*) FROM chufangoplogs WHERE 1=1' . $cond;
        return Dao::queryValue($sql, $bind);
    }
}
