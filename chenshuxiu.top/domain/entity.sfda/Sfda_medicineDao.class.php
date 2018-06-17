<?php

/*
 * Sfda_medicineDao
 */

class Sfda_medicineDao extends Dao
{

    public static function getBySfdaid($sfda_id = 0) {
        $cond = " AND sfda_id = :sfda_id ";

        $bind = [];
        $bind[':sfda_id'] = $sfda_id;

        return Dao::getEntityByCond("Sfda_medicine", $cond, $bind);
    }

    public static function getMaxSfdaid($is_en = null) {
        $sql = "SELECT MAX(sfda_id)
                FROM sfda_medicines
                WHERE 1 = 1 ";

        $bind = [];

        if ($is_en != null) {
            $sql .= " AND is_en = :is_en";
            $bind[':is_en'] = $is_en;
        }

        return Dao::queryValue($sql, $bind);
    }
}
