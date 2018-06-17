<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/12/5
 * Time: 10:45
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

echo "\n\n-----begin----- " . XDateTime::now() . "\n\n";

$unitofwork = BeanFinder::get("UnitOfWork");

$sql = "SELECT *
        FROM optasktpls
        WHERE id NOT IN (
            SELECT optasktplid
            FROM opnodes
            GROUP BY optasktplid
        )";
$optasktpls = Dao::loadEntityList("OpTaskTpl", $sql);

$common_nodes = [
    'root' => '根节点',
    'appoint_follow' => '约定跟进',
    'finish' => '完成',
];

$brief = 0;
foreach ($optasktpls as $optasktpl) {
    $nodes = [];
    foreach ($common_nodes as $code => $title) {
        $opnode = OpNodeDao::getByCodeOpTaskTplId($code, $optasktpl->id);
        if (false == $opnode instanceof OpNode) {
            $row = [];
            $row['title'] = $title;
            $row['code'] = $code;
            $row['optasktplid'] = $optasktpl->id;
            $row['is_hang_up'] = $code == 'root' ? 0 : 1;
            $opnode = OpNode::createByBiz($row);

            $nodes[$code] = $opnode;
        }
    }

    $node_root = $nodes['root'];
    $node_appoint_follow = $nodes['appoint_follow'];
    $node_finish = $nodes['finish'];

    // 根节点->约定跟进
    $row = [];
    $row["from_opnodeid"] = $node_root->id;
    $row["to_opnodeid"] = $node_appoint_follow->id;
    $row["type"] = 'manual';
    OpNodeFlow::createByBiz($row);

    // 根节点->完成
    $row = [];
    $row["from_opnodeid"] = $node_root->id;
    $row["to_opnodeid"] = $node_finish->id;
    $row["type"] = 'manual';
    OpNodeFlow::createByBiz($row);

    // 约定跟进->完成
    $row = [];
    $row["from_opnodeid"] = $node_appoint_follow->id;
    $row["to_opnodeid"] = $node_finish->id;
    $row["type"] = 'manual';
    OpNodeFlow::createByBiz($row);

    $brief++;

    if ($brief % 100 == 0) {
        $unitofwork->commitAndInit();
    }
}
$unitofwork->commitAndInit();

$sql = "SELECT * FROM optasks WHERE opnodeid = 0 AND status != 1";
$optasks = Dao::loadEntityList("OpTask", $sql);

$brief = 0;
foreach ($optasks as $optask) {
    $opnode = OpNodeDao::getByCodeOpTaskTplId('root', $optask->optasktplid);
    if (false == $opnode instanceof OpNode) {
        continue;
    }

    $optask->opnodeid = $opnode->id;

    $brief++;

    if ($brief % 100 == 0) {
        $unitofwork->commitAndInit();
    }
}

echo "\n\n";
$unitofwork->commitAndInit();