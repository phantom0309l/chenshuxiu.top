<?php
// XUnitOfWorkMgrAction
class XUnitOfWorkMgrAction extends AuditBaseAction
{

    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $tableno = XRequest::getValue("tableno", date('Ym'));

        $client_ip = XRequest::getValue("client_ip", '');
        $sub_domain = XRequest::getValue("sub_domain", 'all');
        $action_name = XRequest::getValue("action_name", '');
        $method_name = XRequest::getValue("method_name", '');

        $sqltypes = XRequest::getValue('sqltypes', array());

        $cond = " ";
        $bind = [];

        $x = 0;

        if ($client_ip != '') {
            $x += 3;
            $cond .= " and client_ip = :client_ip ";
            $bind[':client_ip'] = $client_ip;
        }

        if ($sub_domain != 'all') {
            $x += 1;
            $cond .= " and sub_domain = :sub_domain ";
            $bind[':sub_domain'] = $sub_domain;
        }

        if ($action_name) {
            $x += 1;
            $cond .= " and action_name = :action_name ";
            $bind[':action_name'] = $action_name;
        }

        if ($method_name) {
            $x += 1;
            $cond .= " and method_name = :method_name ";
            $bind[':method_name'] = $method_name;
        }

        $sqltypesql = "";
        foreach ($sqltypes as $sqltype) {
            switch ($sqltype) {
                case 'insert':
                    $cond .= " and commit_insert_cnt > 0 ";
                    break;
                case 'update':
                    $cond .= " and commit_update_cnt > 0 ";
                    break;
                case 'delete':
                    $cond .= " and commit_delete_cnt > 0 ";
                    break;
            }
        }

        $cond .= " order by id desc ";

        $dbconf = [];
        $dbconf['database'] = 'xworkdb';
        $dbconf['tableno'] = $tableno;

        $xunitofworks = Dao::getEntityListByCond4Page('XUnitOfWork', $pagesize, $pagenum, $cond, $bind, $dbconf);

        $cnt = Dao::queryValue("select count(*) from xunitofworks{$tableno} where 1=1 $cond", $bind, 'xworkdb');

        $url = "/xunitofworkmgr/list?tableno={$tableno}&client_ip={$client_ip}&sub_domain={$sub_domain}&action_name={$action_name}&method_name={$method_name}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue('xunitofworks', $xunitofworks);
        XContext::setValue('tableno', $tableno);
        XContext::setValue('client_ip', $client_ip);
        XContext::setValue('sub_domain', $sub_domain);
        XContext::setValue('action_name', $action_name);
        XContext::setValue('method_name', $method_name);
        XContext::setValue('sqltypes', $sqltypes);

        return self::SUCCESS;
    }
}
