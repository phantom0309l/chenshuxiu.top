<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Fix_refund
{
    public function dowork () {
        return;
        $unitofwork = BeanFinder::get("UnitOfWork");
        $user = User::getById(622288518);
        //$refundOrder = RefundOrder::getById(566665661);
        $row = array();
        $row["userid"] = 622288518;
        $row["amount"] = 16500;
        $row["depositeorderid"] = 623885926;
        $row["patientwithdraworderid"] = 632602786;

        $refundOrder = RefundOrder::createByBiz($row);
        OrderService::processFreezeAccountWithdrawRefund($user, $refundOrder);
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Fix_refund.php]=====");

$process = new Fix_refund();
$process->dowork();

Debug::trace("=====[cron][end][Fix_refund.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
