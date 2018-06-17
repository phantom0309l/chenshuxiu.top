<?php
/*
 * BedTktPictureDao
 */
class BedTktPictureDao extends Dao
{
    public static function getListByBedTkt( BedTkt $bedtkt ){
        $cond = " AND bedtktid = :bedtktid  ";

        $bind = [];
        $bind[':bedtktid'] = $bedtkt->id;

        return Dao::getEntityListByCond("BedTktPicture", $cond, $bind);
    }
}
