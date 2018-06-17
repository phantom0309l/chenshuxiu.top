<?php
/*
 * BedTktPaperRefDao
 */
class BedTktPaperRefDao extends Dao
{
    public static function getListByBedTkt (BedTkt $bedTkt) {
        $cond = ' AND bedtktid=:bedtktid';
        $bind = [];
        $bind[":bedtktid"] = $bedTkt->id;
        return Dao::getEntityListByCond("BedTktPaperRef", $cond, $bind);
    }
}
