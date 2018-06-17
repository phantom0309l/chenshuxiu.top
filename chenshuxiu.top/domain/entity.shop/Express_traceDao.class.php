<?php
/*
 * Express_traceDao
 */
class Express_traceDao extends Dao {

    // 获取 by express_no
    public static function getOneByExpress_no($express_no) {
        $cond = " and express_no = :express_no";
        $bind = [];
        $bind[':express_no'] = $express_no;
        return Dao::getEntityByCond('Express_trace', $cond, $bind);
    }
}
