<?php
/*
 * GuestRecordDao
 */
class GuestRecordDao extends Dao
{
    // 名称: getCntByType
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByType ($type = 'haomama') {
        $sql = "select count(*) as cnt from guestrecords where type = :type ";

        $bind = [];
        $bind[':type'] = $type;

        return Dao::queryValue($sql, $bind);
    }
}
