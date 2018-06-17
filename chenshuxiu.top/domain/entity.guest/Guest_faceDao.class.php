<?php
/*
 * Guest_faceDao
 */
class Guest_faceDao extends Dao
{
    // 名称: getByGuestid
    // 备注:
    // 创建:
    // 修改:
    public static function getByGuestid ($guestid) {
        $cond = " AND guestid = :guestid order by id ";

        $bind = [];
        $bind[':guestid'] = $guestid;

        return Dao::getEntityByCond('Guest_face', $cond, $bind);
    }

}
