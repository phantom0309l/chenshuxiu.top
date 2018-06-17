<?php

/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-4-15
 * Time: 下午7:02
 */
class AccountMgrAction extends AuditBaseAction
{
    // 账户列表
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);
        $code = XRequest::getValue("code", "All");
        $userid = XRequest::getValue("userid", 0);

        $cond = "";
        $bind = [];

        if ($code != 'All') {
            $cond .= " and code =:code ";
            $bind[':code'] = $code;
        }
        if ($userid) {
            $cond .= " and userid =:userid ";
            $bind[':userid'] = $userid;
        }

        $cond .= " order by id asc ";

        $accounts = Dao::getEntityListByCond4Page("Account", $pagesize, $pagenum, $cond, $bind);
        $codes = Account::getCodeDescs(true);

        $countSql = "select count(*) as cnt from accounts where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/accountmgr/list?code={$code}&userid={$userid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("codes", $codes);
        XContext::setValue("code", $code);
        XContext::setValue("userid", $userid);
        XContext::setValue("accounts", $accounts);
        XContext::setValue("pagelink", $pagelink);
        return self::SUCCESS;
    }

    // 提现并原路退款
    public function doWithdrawRefundPost () {
        $accountid = XRequest::getValue("accountid", 0);

        $account = Account::getById($accountid);
        $myauditor = $this->myauditor;

        // 根据提现单生成退款单
        OrderService::processAccountWithdrawRefund($account, $myauditor);

        XContext::setJumpPath("/accountItemMgr/list?accountid={$accountid}");

        return self::SUCCESS;
    }
}
