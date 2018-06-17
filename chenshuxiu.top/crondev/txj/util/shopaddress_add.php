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

class Shopaddress_add
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $linkman_name = "舒晓";
        $linkman_mobile = "18075572399";
        $xprovinceid = 430000;
        $xcityid = 431200;
        $xcountyid = 431224;
        $content = "小横垅乡杨柳村十组";
        $postcode = "";

        $patientid = 542595326;
        $userid = 542593648;
        $wxuserid = 542593646;

        $row = array();
        $row["wxuserid"] = $wxuserid;
        $row["userid"] = $userid;
        $row["patientid"] = $patientid;
        $row["linkman_name"] = $linkman_name;
        $row["linkman_mobile"] = $linkman_mobile;
        $row["xprovinceid"] = $xprovinceid;
        $row["xcityid"] = $xcityid;
        $row["xcountyid"] = $xcountyid;
        $row["content"] = $content;
        $row["postcode"] = $postcode;

        $shopAddress = ShopAddress::createByBiz($row);

        $unitofwork->commitAndInit();
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Shopaddress_add.php]=====");

$process = new Shopaddress_add();
$process->dowork();

Debug::trace("=====[cron][end][Shopaddress_add.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
