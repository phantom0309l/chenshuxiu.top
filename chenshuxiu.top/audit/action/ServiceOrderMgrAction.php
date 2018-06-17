<?php

// ServiceOrderMgrAction
class ServiceOrderMgrAction extends AuditBaseAction
{
    public function doList() {
        $myauditor = $this->myauditor;

        $date_range = XRequest::getValue('date_range', '');

        $pagenum = XRequest::getValue('pagenum', '1');

        $pagesize = 30;

        $type = XRequest::getValue('type', 'quickpass');
        $status = XRequest::getValue('status', '0');

        $condEx = '';
        switch ($status) {
            case 0:
                break;
            case 1:
                $condEx .= ' AND a.is_pay = 0 ';
                break;
            case 2:
                $condEx .= ' AND a.is_pay = 1 ';
                break;
            case 3:
                $condEx .= ' AND a.is_pay = 1 AND a.refund_amount > 0 ';
                break;
            default:
                break;
        }
        if (!empty($date_range)) {
            $arr = explode('至', $date_range);
            $from_date = $arr[0];
            $to_date = $arr[1];
            $condEx .= " AND a.createtime BETWEEN '{$from_date} 00:00:00' AND '{$to_date} 23:59:59' ";
        }
        $condEx .= " ORDER BY a.createtime DESC ";
        $serviceorders = ServiceOrderDao::getListByType($type, $pagesize, $pagenum, $condEx);

        $countSql = "SELECT count(a.id)
                    FROM serviceorders a
                    WHERE a.serviceproduct_type = '{$type}'
                    {$condEx} ";

        //获得分页
        $cnt = Dao::queryValue($countSql);
        $url = "/serviceordermgr/list?type={$type}&status={$status}&date_range={$date_range}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        $date_range_str = $date_range ? $date_range : '至今';

        XContext::setValue('date_range', $date_range);
        XContext::setValue('date_range_str', $date_range_str);
        XContext::setValue('serviceorders', $serviceorders);

        $types = ServiceProduct::getTypes();
        XContext::setValue('types', $types);

        $allStatus = ServiceOrder::getAllStatus();
        XContext::setValue('allStatus', $allStatus);
        XContext::setValue('status', $status);

        // 总金额
        $totalAmount = ServiceOrderDao::getTotalAmount($type, " AND a.is_pay = 1 ");
        XContext::setValue('totalAmount', $totalAmount);

        // 退款金额
        $totalRefundAmount = ServiceOrderDao::getTotalRefundAmount($type);
        XContext::setValue('totalRefundAmount', $totalRefundAmount);

        // 本月支付总金额
        $starttime = date('Y-m-01 00:00:00');
        $endtime = date('Y-m-d H:i:s');
        $totalAmount_month = ServiceOrderDao::getTotalAmount($type, " AND a.is_pay = 1 AND a.createtime BETWEEN '{$starttime}' AND '{$endtime}'");
        XContext::setValue('totalAmount_month', $totalAmount_month);

        // 今日支付总金额
        $starttime = date('Y-m-d 00:00:00');
        $totalAmount_today = ServiceOrderDao::getTotalAmount($type, " AND a.is_pay = 1 AND a.createtime BETWEEN '{$starttime}' AND '{$endtime}'");
        XContext::setValue('totalAmount_today', $totalAmount_today);

        return self::SUCCESS;
    }

    public function doPreRefundList() {
        $quickpass_serviceitems = QuickPass_ServiceItemDao::getPreRefundList();

        XContext::setValue('quickpass_serviceitems', $quickpass_serviceitems);

        return self::SUCCESS;
    }

    public function doAjaxRefundPass() {
        $quickpass_serviceitemid = XRequest::getValue('quickpass_serviceitemid');
        if (empty($quickpass_serviceitemid)) {
            $this->returnError('记录不存在');
        }
        $quickpass_serviceitem = QuickPass_ServiceItem::getById($quickpass_serviceitemid);
        if (empty($quickpass_serviceitem)) {
            $this->returnError('记录不存在');
        }

        $quickpass_serviceitem->timeoutRefund();
        return self::TEXTJSON;
    }

    public function doAjaxRefundReject() {
        $quickpass_serviceitemid = XRequest::getValue('quickpass_serviceitemid');
        if (empty($quickpass_serviceitemid)) {
            $this->returnError('记录不存在');
        }
        $quickpass_serviceitem = QuickPass_ServiceItem::getById($quickpass_serviceitemid);
        if (empty($quickpass_serviceitem)) {
            $this->returnError('记录不存在');
        }

        $quickpass_serviceitem->cancelTimeout();
        return self::TEXTJSON;
    }

    public function doOne() {
        return self::SUCCESS;
    }

    public function doModify() {
        return self::SUCCESS;
    }

    public function doModifyPost() {
        return self::SUCCESS;
    }
}
