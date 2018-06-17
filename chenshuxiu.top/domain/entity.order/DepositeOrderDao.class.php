<?php
// DepositeOrderDao

// owner by sjp
// create by sjp
// review by sjp 20160628
class DepositeOrderDao extends Dao
{
    // 获取可用于退款的充值单
    public static function getDepositeOrderListForRefundByUseridAmount ($userid, $total_amount) {

        // 可退充值单
        $depositeOrders = DepositeOrderDao::getAllDepositeOrderListForRefundByUserid($userid);

        // 累计金额
        $sum_amount = 0;
        $arr = [];

        // 模拟退款
        foreach ($depositeOrders as $a) {

            // 跳出, 够用了
            if ($sum_amount >= $total_amount) {
                break;
            }

            // 跳过, 充值失败的 或 已经退完的
            if (0 == $a->recharge_status || $a->amount <= $a->refund_amount) {
                continue;
            }

            $sum_amount += ($a->amount - $a->refund_amount);

            $arr[] = $a;
        }

        return $arr;
    }

    // 获取可退款的列表, 属于某个用户
    public static function getAllDepositeOrderListForRefundByUserid ($userid) {
        $cond = "and userid = :userid and recharge_status = 1 and amount > refund_amount
            order by id desc";

        $bind = [];
        $bind[":userid"] = $userid;

        return Dao::getEntityListByCond("DepositeOrder", $cond, $bind);
    }

    // 获取单条, 通过 fangcun_trade_no
    public static function getDepositeOrderByfangcun_trade_no ($fangcun_trade_no) {
        $cond = "and fangcun_trade_no = :fangcun_trade_no";

        $bind = [];
        $bind[':fangcun_trade_no'] = $fangcun_trade_no;

        return Dao::getEntityByCond("DepositeOrder", $cond, $bind);
    }

    // 获取列表, 通过关联单据
    public static function getDepositeOrderListByEntity (Entity $entity) {
        $objtype = get_class($entity);
        $objid = $entity->id;

        $cond = "and objtype = :objtype and objid = :objid";

        $bind = [];
        $bind[":objtype"] = $objtype;
        $bind[":objid"] = $objid;

        return Dao::getEntityListByCond("DepositeOrder", $cond, $bind);
    }

    // 获取单条(recharge_status=1), 通过关联单据
    public static function getDepositeOrderRechargeOneByEntity (Entity $entity) {
        $objtype = get_class($entity);
        $objid = $entity->id;

        $cond = " and objtype = :objtype and objid = :objid and recharge_status=1 order by id desc ";

        $bind = [];
        $bind[":objtype"] = $objtype;
        $bind[":objid"] = $objid;

        return Dao::getEntityByCond("DepositeOrder", $cond, $bind);
    }

    // 获取列表, 属于某个用户
    public static function getDepositeOrderListByUserId ($userid) {
        $cond = "and userid = :userid
            order by id";

        $bind = [];
        $bind[":userid"] = $userid;

        return Dao::getEntityListByCond("DepositeOrder", $cond, $bind);
    }

    // 获取列表, 属于某个账户
    public static function getDepositeOrderListByAccountId ($accountid) {
        $cond = " and accountid = :accountid";

        $bind = [];
        $bind[':accountid'] = $accountid;

        return Dao::getEntityListByCond("DepositeOrder", $cond, $bind);
    }

    // 某个用户充值总额
    public static function getSumAmountRechargedByUserId ($userid) {
        $sql = "select sum(amount)
            from depositeorders
            where userid = :userid and recharge_status = 1
            order by id";

        $bind = [];
        $bind[':userid'] = $userid;

        return 0 + Dao::queryValue($sql, $bind);
    }
}
