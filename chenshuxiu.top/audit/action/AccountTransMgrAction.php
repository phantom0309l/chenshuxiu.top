<?php

/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-4-15
 * Time: 下午7:02
 */
class AccountTransMgrAction extends AuditBaseAction
{
    // 账务事务列表
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);
        $accounttransid = XRequest::getValue("accounttransid", 0);
        $objtype = XRequest::getValue("objtype", "All");
        $fromaccountid = XRequest::getValue("fromaccountid", 0);
        $toaccountid = XRequest::getValue("toaccountid", 0);

        $cond = "";
        $bind = [];

        if ($accounttransid) {
            $cond .= " and id =:id ";
            $bind[':id'] = $accounttransid;
        }

        if ($objtype != "All") {
            $cond .= " and objtype =:objtype ";
            $bind[':objtype'] = $objtype;
        }
        if ($fromaccountid) {
            $cond .= " and fromaccountid =:fromaccountid ";
            $bind[':fromaccountid'] = $fromaccountid;
        }
        if ($toaccountid) {
            $cond .= " and toaccountid =:toaccountid ";
            $bind[':toaccountid'] = $toaccountid;
        }

        // $objtypes= AccountTrans::getArrayOfObj();
        $accounttranss = Dao::getEntityListByCond4Page("AccountTrans", $pagesize, $pagenum, $cond, $bind);

        $countSql = "select count(*) as cnt from accounttranss where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/accounttransmgr/list?objtype={$objtype}&fromaccountid={$fromaccountid}&toaccountid={$toaccountid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("accounttranss", $accounttranss);
        XContext::setValue("pagelink", $pagelink);
        XContext::setValue("objtype", $objtype);
        return self::SUCCESS;
    }
}