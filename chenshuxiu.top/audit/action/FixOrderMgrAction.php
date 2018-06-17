<?php
// FixOrderMgrAction
class FixOrderMgrAction extends AuditBaseAction
{

    public function doList () {
        return self::SUCCESS;
    }

    public function doAdd () {
        $accountid = XRequest::getValue("accountid", 0);
        $account = Account::getById($accountid);
        XContext::setValue("account", $account);
        return self::SUCCESS;
    }

    public function doAddPost () {
        $accountid = XRequest::getValue("accountid", 0);
        $amount_yuan = XRequest::getValue("amount_yuan", 0);
        $reason = XRequest::getValue("reason", 0);

        $account = Account::getById($accountid);

        $sysAccount = Account::getSysAccount('sys_fix_fund');

        $row = array();
        $row["accountid"] = $accountid;
        $row["amount"] = $amount_yuan * 100;
        $row["reason"] = $reason;
        $row["auditorid"] = $this->myauditor->id;

        $fixOrder = FixOrder::createByBiz($row);

        $sysAccount->transto($account, $fixOrder->amount, $fixOrder, 'process', $fixOrder->reason);

        XContext::setJumpPath("/accountitemmgr/list?accountid={$accountid}");

        return self::SUCCESS;
    }
}
