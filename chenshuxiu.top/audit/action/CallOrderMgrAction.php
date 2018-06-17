<?php

/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/9/28
 * Time: 15:32
 */
class CallOrderMgrAction extends AuditBaseAction
{
    public function doList() {
        $callorders = CallOrderDao::getAll();

        XContext::setValue("callorders", $callorders);
        return self::SUCCESS;
    }

//    public function doModifyPost() {
//        $callorderid = XRequest::getValue('callorderid', 0);
//
//        $callorder = CallOrder::getById($callorderid);
//        DBC::requireTrue($callorder instanceof CallOrder, "订单不存在");
//
//        $cdrmeetingid = XRequest::getValue("cdrmeetingid", '');
//        $call_duration = XRequest::getValue("call_duration", 583);
//        $amount = XRequest::getValue("amount", '');
//        $status = XRequest::getValue("status", '');
//
//        $callorder->set4lock("cdrmeetingid", $cdrmeetingid);
//        $callorder->call_duration = $call_duration;
//        $callorder->amount = ceil($call_duration / 60) * 1000;
//        $callorder->status = 3;
//
//        XContext::setJumpPath("/callordermgr/list?preMsg=修改成功");
//    }
//
//    public function doDelete() {
//        $callorderid = XRequest::getValue('callorderid', 0);
//
//
//        $callorder = CallOrder::getById($callorderid);
//        if ($callorder instanceof CallOrder) {
//            $callorder->remove();
//        }
//
//        XContext::setJumpPath("/callordermgr/list?preMsg=删除成功");
//    }
}