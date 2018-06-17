<?php
// RefundOrderDao

// owner by sjp
// create by sjp
// review by sjp 20160629
class RefundOrderDao extends Dao
{
    // 名称: getSumAmountRefundedByUserid
    // 备注:某用户已退款总额
    // 创建:
    // 修改:
    public static function getSumAmountRefundedByUserid ($userid) {
        $sql = "select sum(amount)
                from refundorders
                where userid = :userid and status = 1
                order by id";

        $bind = [];
        $bind[':userid'] = $userid;

        return 0 + Dao::queryValue($sql, $bind);
    }
}