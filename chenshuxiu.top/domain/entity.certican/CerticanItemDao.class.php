<?php
/*
 * CerticanItemDao
 */
class CerticanItemDao extends Dao
{   
    public static function getListByCertican (Certican $certican) {
        $cond = " and certicanid = :certicanid order by plan_date ";
        $bind[':certicanid'] = $certican->id;

        return Dao::getEntityListByCond('CerticanItem', $cond, $bind);
    }

    // 获取填写完的item
    public static function getDoneListByCertican (Certican $certican) {
        $cond = " and certicanid = :certicanid and is_fill = 1 order by plan_date ";
        $bind[':certicanid'] = $certican->id;

        return Dao::getEntityListByCond('CerticanItem', $cond, $bind);
    }

    public static function getByCerticanPlan_date (Certican $certican, $plan_date) {
        $cond = " and plan_date = :plan_date and certicanid in (
                select id 
                from certicans
                where patientid = :patientid
            ) order by plan_date ";
        $bind[':patientid'] = $certican->patientid;
        $bind[':plan_date'] = $plan_date;

        return Dao::getEntityByCond('CerticanItem', $cond, $bind);
    }
}