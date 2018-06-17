<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);
//Debug::$debug = 'Dev';

class Fix_5991
{
    public function work () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select a.id
                from optasktpls a
                inner join opnodes b on b.optasktplid = a.id
                where b.code = 'unfinish'";
        $ids = Dao::queryValues($sql);

        foreach ($ids as $id) {
            $optasktpl = OpTaskTpl::getById($id);

            // 创建opnode
            $row = [];
            $row["optasktplid"] = $id;
            $row["code"] = 'other_close';
            $row["is_hang_up"] = 0;
            $row["title"] = '其他终结';
            $row["content"] = '其他情况关闭';
            $opnode = OpNode::createByBiz($row);

            echo "{$optasktpl->title} create opnode {$opnode->code}\n";

            // 创建opnodeflow
            $opnode_unfinish = OpNodeDao::getByCodeOpTaskTplId('unfinish', $id);
            $opnodeflow_to_unfinishs = OpNodeFlowDao::getListByTo_opnode($opnode_unfinish);
            foreach ($opnodeflow_to_unfinishs as $to_unfinish) {
                $row = [];
                $row["from_opnodeid"] = $to_unfinish->from_opnodeid;
                $row["to_opnodeid"] = $opnode->id;
                $row["type"] = 'manual';
                $row["content"] = '其他关闭';
                $opnodeflow = OpNodeFlow::createByBiz($row);

                echo "{$optasktpl->title} create opnodeflow {$opnodeflow->from_opnode->code} => {$opnodeflow->to_opnode->code}\n";
            }
            echo "\n";
        }

        $unitofwork->commitAndInit();
    }
}

$test = new Fix_5991();
$test->work();
