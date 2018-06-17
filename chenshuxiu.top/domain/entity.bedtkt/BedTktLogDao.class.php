<?php
/*
 * BedTktLogDao
 */
class BedTktLogDao extends Dao
{
    public static function getListByBedTkt( BedTkt $bedtkt, $condfix='' ){
        $cond = " AND bedtktid = :bedtktid  ".$condfix." ORDER BY id ASC";;

        $bind = [];
        $bind[':bedtktid'] = $bedtkt->id;

        return Dao::getEntityListByCond("BedTktLog", $cond, $bind);
    }

}
