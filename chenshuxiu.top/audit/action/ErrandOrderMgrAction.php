<?php

// ErrandOrderMgrAction
class ErrandOrderMgrAction extends AuditBaseAction
{
    public function doList() {
        $date_range = XRequest::getValue('date_range', '');

        $status = XRequest::getValue('status', '0');

        $pagenum = XRequest::getValue('pagenum', '1');

        $pagesize = 30;

        $condEx = '';
        switch ($status) {
            case 0:
                break;
            case 1:
                $condEx .= ' AND is_pay = 0 ';
                break;
            case 2:
                $condEx .= ' AND is_pay = 1 ';
                break;
            case 3:
                $condEx .= ' AND is_pay = 1 AND refund_amount > 0 ';
                break;
            default:
                break;
        }
        if (!empty($date_range)) {
            $arr = explode('至', $date_range);
            $from_date = $arr[0];
            $to_date = $arr[1];
            $condEx .= " AND createtime BETWEEN '{$from_date} 00:00:00' AND '{$to_date} 23:59:59' ";
        }
        $condEx .= " ORDER BY createtime DESC ";
        $errandorders = ErrandOrderDao::getListByCondEx($pagesize, $pagenum, $condEx);

        //获得分页
        $cnt = ErrandOrderDao::getCountByCondEx($condEx);
        $url = "/errandordermgr/list?&status={$status}&date_range={$date_range}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        $date_range_str = $date_range ? $date_range : '至今';

        XContext::setValue('date_range', $date_range);
        XContext::setValue('date_range_str', $date_range_str);
        XContext::setValue('errandorders', $errandorders);

        $allStatus = ErrandOrder::getAllStatus();
        XContext::setValue('allStatus', $allStatus);
        XContext::setValue('status', $status);

        // 总金额
        $totalAmount = ErrandOrderDao::getTotalAmount(" AND is_pay = 1 ");
        XContext::setValue('totalAmount', $totalAmount);

        // 退款金额
        $totalRefundAmount = ErrandOrderDao::getTotalRefundAmount();
        XContext::setValue('totalRefundAmount', $totalRefundAmount);

        // 本月支付总金额
        $starttime = date('Y-m-01 00:00:00');
        $endtime = date('Y-m-d H:i:s');
        $totalAmount_month = ErrandOrderDao::getTotalAmount(" AND is_pay = 1 AND createtime BETWEEN '{$starttime}' AND '{$endtime}'");
        XContext::setValue('totalAmount_month', $totalAmount_month);

        // 今日支付总金额
        $starttime = date('Y-m-d 00:00:00');
        $totalAmount_today = ErrandOrderDao::getTotalAmount(" AND is_pay = 1 AND createtime BETWEEN '{$starttime}' AND '{$endtime}'");
        XContext::setValue('totalAmount_today', $totalAmount_today);

        return self::SUCCESS;
    }

    public function doAjaxRefundPost() {
        $errandorderid = XRequest::getValue('errandorderid');
        if (empty($errandorderid)) {
            $this->returnError('订单不存在');
        }

        $errandorder = ErrandOrder::getById($errandorderid);
        if (empty($errandorder)) {
            $this->returnError('订单不存在');
        }

        if ($errandorder->canRefund()) {
            $errandorder->refund();
        }

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
