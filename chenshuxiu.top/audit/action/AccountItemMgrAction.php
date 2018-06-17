<?php

/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-4-15
 * Time: 下午7:03
 */
class AccountItemMgrAction extends AuditBaseAction
{
    // 账务明细列表
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 100);
        $pagenum = XRequest::getValue("pagenum", 1);
        $accountid = XRequest::getValue("accountid", 0);
        $accounttransid = XRequest::getValue("accounttransid", 0);
        $accountitemtype = XRequest::getValue("accountitemtype", "All");

        $account = null;
        $accountTrans = null;

        $cond = "";
        $bind = [];

        if ($accountid) {
            $cond .= " and accountid =:accountid ";
            $bind[':accountid'] = $accountid;

            $account = Account::getById($accountid);
        }

        if ($accountitemtype == "In") {
            $cond .= " and amount > 0 ";
        } elseif ($accountitemtype == "Out") {
            $cond .= " and amount < 0";
        }

        if ($accounttransid) {
            $cond .= " and accounttransid =:accounttransid ";
            $bind[':accounttransid'] = $accounttransid;

            $accountTrans = AccountTrans::getById($accounttransid);
        }

        $cond .= " order by id asc";

        $accountitems = Dao::getEntityListByCond4Page("AccountItem", $pagesize, $pagenum, $cond, $bind);

        $countSql = "select count(*) as cnt from accountitems where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/accountitemmgr/list?accountid={$accountid}&accounttransid={$accounttransid}&accountitemtype={$accountitemtype}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("account", $account);
        XContext::setValue("accountTrans", $accountTrans);
        XContext::setValue("accountitemtype", $accountitemtype);
        XContext::setValue("accountitems", $accountitems);
        XContext::setValue("pagelink", $pagelink);
        return self::SUCCESS;
    }
}
