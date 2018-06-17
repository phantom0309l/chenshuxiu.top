<?php

// QuickConsultOrderMgrAction
class QuickConsultOrderMgrAction extends AuditBaseAction
{
    public function doList() {
        $myauditor = $this->myauditor;
        $diseaseids = $myauditor->getDiseaseIdArr();
        $ids = implode(',', $diseaseids);

        $pagenum = XRequest::getValue('pagenum', '1');

        $pagesize = 30;

        $status = XRequest::getValue('status', 'all');

        if ($status == 'all') {
            $condEx = " ORDER BY locate(3, status) DESC, locate(4, status) DESC, status DESC, time_finished DESC, time_pay ASC ";

            $countSql = "SELECT COUNT(id) FROM quickconsultorders";
        } else {
            $condEx = " AND status = {$status} ";
            if ($status == 1 || $status == 2) { // 仅浏览、待支付
                $condEx .= " ORDER BY createtime DESC ";
            } elseif ($status == 3 || $status == 4 || $status == 5) {   // 已支付、处理中、完成
                $condEx .= " ORDER BY time_pay DESC ";
            }

            $countSql = "SELECT COUNT(id) FROM quickconsultorders WHERE status = {$status} ";
        }

        $quickconsultorders = QuickConsultOrderDao::getListByDisease4Page($ids, $pagesize, $pagenum, $condEx);

        //获得分页
        $cnt = Dao::queryValue($countSql);
        $url = "/quickconsultordermgr/list?status={$status}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue('quickconsultorders', $quickconsultorders);
        XContext::setValue('status', $status);

        //支付总金额
        $totalAmount = QuickConsultOrderDao::getTotalAmount();
        XContext::setValue('totalAmount', $totalAmount);

        //退款总金额
        $totalRefundAmount = QuickConsultOrderDao::getTotalRefundAmount();
        XContext::setValue('totalRefundAmount', $totalRefundAmount);

        //本月支付总金额
        $starttime = date('Y-m-01 00:00:00');
        $endtime = date('Y-m-d H:i:s');
        $totalAmount_month = QuickConsultOrderDao::getMonthTotalAmount($starttime,$endtime);
        XContext::setValue('totalAmount_month', $totalAmount_month);

        //今天支付总金额
        $starttime = date('Y-m-d 00:00:00');
        $totalAmount_today = QuickConsultOrderDao::getTodayTotalAmount($starttime,$endtime);
        XContext::setValue('totalAmount_today', $totalAmount_today);

        return self::SUCCESS;
    }

    /**
     * 修改交流方式
     * @return string
     */
    public function doAjaxModifyInteractiveMode() {
        $quickconsultorderid = XRequest::getValue('quickconsultorderid');
        $quickconsultorder = QuickConsultOrder::getById($quickconsultorderid);
        if (false == $quickconsultorder instanceof QuickConsultOrder) {
            $this->returnError('快速咨询不存在');
        }

        $interactive_mode = XRequest::getValue('interactive_mode');
        if (empty($interactive_mode)) {
            $this->returnError('请选择交流方式');
        }

        $quickconsultorder->interactive_mode = $interactive_mode;
        return self::TEXTJSON;
    }

}
