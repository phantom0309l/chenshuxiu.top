<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

$optaskTpls = Dao::getEntityListByCond("OpTaskTpl");


$unitofwork = BeanFinder::get("UnitOfWork");

foreach ($optaskTpls as $i => $a) {
    $opnode = OpNodeDao::getByCodeOpTaskTplId('appoint_follow', $a->id);

    echo "\n {$i} ";
    if ($opnode instanceof OpNode) {
        $opNodeFlow = OpNodeFlowDao::getByFrom_opnodeTo_opnode($opnode, $opnode);
        if ($opNodeFlow instanceof OpNodeFlow) {
            echo "have OpNodeFlow[{$opNodeFlow->id}] : [{$a->id}] [{$a->title}]";
        } else {
            $row = array();
            $row["from_opnodeid"] = $opnode->id;
            $row["to_opnodeid"] = $opnode->id;
            $row["type"] = 'manual';
            $row["content"] = '可以重复跟进';
            $opNodeFlow = OpNodeFlow::createByBiz($row);
            echo "create OpNodeFlow[{$opNodeFlow->id}] : [{$a->id}] [{$a->title}]";
        }
    } else {
        echo "no opnode[appoint_follow] : [{$a->id}] [{$a->title}]";
    }
}

$unitofwork->commitAndInit();