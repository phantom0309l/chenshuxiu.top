<?php

/*
 * Guest_schulteDao
 */
class Guest_schulteDao extends Dao
{
    // 名称: getByFromguestid
    // 备注:
    // 创建:
    // 修改:
    public static function getByFromguestid ($fromguestid) {
        $bind = [];
        $bind[':fromguestid'] = $fromguestid;

        return Dao::getEntityListByCond("Guest_schulte", "and fromguestid = :fromguestid ", $bind);
    }

    // 名称: getScorePosition
    // 备注:
    // 创建:
    // 修改:
    public static function getScorePosition ($score) {
        $score = intval($score);

        $sql1 = "select count(*) from guest_schultes where toptime != 0";
        $a1 = Dao::queryValue($sql1, []);

        $sql2 = "select count(*) from guest_schultes where toptime != 0 and toptime > :score ";

        $bind = [];
        $bind[':score'] = $score;

        $a2 = Dao::queryValue($sql2, $bind);

        return round(($a2 / $a1) * 100, 2);
    }

    // 名称: getScorePosition1
    // 备注:
    // 创建:
    // 修改:
    public static function getScorePosition1 ($score) {
        $score = intval($score);

        $sql1 = "select count(*) from guest_schultes where toptime1 != 0";
        $a1 = Dao::queryValue($sql1, []);

        $sql2 = "select count(*) from guest_schultes where toptime1 != 0 and toptime1 > :score ";

        $bind = [];
        $bind[':score'] = $score;

        $a2 = Dao::queryValue($sql2, $bind);
        return round(($a2 / $a1) * 100, 2);
    }

    // 名称: getTop
    // 备注:
    // 创建:
    // 修改:
    public static function getTop ($num = 20) {
        $num = intval($num);

        return Dao::loadEntityList("Guest_schulte", "select * from guest_schultes where toptime != 0 order by toptime asc limit {$num}");
    }

    // 名称: getTop1
    // 备注:
    // 创建:
    // 修改:
    public static function getTop1 ($num = 20) {
        $num = intval($num);

        return Dao::loadEntityList("Guest_schulte", "select * from guest_schultes where toptime1 != 0 order by toptime1 asc limit {$num}");
    }
}
