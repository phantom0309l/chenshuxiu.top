<?php

/*
 * OpNodeFlowDao
 */
class OpNodeFlowDao extends Dao
{

    public static function getListByOpNode (OpNode $opnode) {
        $cond = " and from_opnodeid = :opnodeid or to_opnodeid = :opnodeid ";
        $bind = [
            ':opnodeid' => $opnode->id];

        return Dao::getEntityListByCond('OpNodeFlow', $cond, $bind);
    }

    public static function getByFrom_opnodeTo_opnode (OpNode $from_opnode, OpNode $to_opnode) {
        $cond = " and from_opnodeid = :from_opnodeid and to_opnodeid = :to_opnodeid ";
        $bind = [
            ':from_opnodeid' => $from_opnode->id,
            ':to_opnodeid' => $to_opnode->id];

        return Dao::getEntityByCond('OpNodeFlow', $cond, $bind);
    }

    public static function getListByFrom_opnode (OpNode $from_opnode) {
        $cond = " and from_opnodeid = :opnodeid ";
        $bind = [
            ':opnodeid' => $from_opnode->id];

        return Dao::getEntityListByCond('OpNodeFlow', $cond, $bind);
    }

    public static function getListByTo_opnode (OpNode $to_opnode) {
        $cond = " and to_opnodeid = :opnodeid ";
        $bind = [
            ':opnodeid' => $to_opnode->id];

        return Dao::getEntityListByCond('OpNodeFlow', $cond, $bind);
    }

    public static function getByFrom_opnodeType (OpNode $from_opnode, $type) {
        $cond = " and from_opnodeid = :from_opnodeid and type = :type ";
        $bind = [
            ':from_opnodeid' => $from_opnode->id,
            ':type' => $type];

        return Dao::getEntityByCond('OpNodeFlow', $cond, $bind);
    }
}