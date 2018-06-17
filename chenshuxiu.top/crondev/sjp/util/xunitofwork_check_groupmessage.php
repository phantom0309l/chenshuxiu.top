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
// 检查 xunitofworks, 是否是微信消息转发
class Xunitofwork_check_groupmessage
{

    public function dowork () {
        $sql = "select min(id) from xunitofworks order by id limit 1";

        // $minid = Dao::queryValue($sql, '', 'xworkdb') + 0;

        $minid = 1503483482804725295; // 2017-08-23 18:18:02

        $i = 0;
        while (true) {
            $unitofwork = BeanFinder::get('UnitOfWork');

            $sql = "select id from xunitofworks where id > {$minid} order by id limit 10000 , 1;";
            $maxid = Dao::queryValue($sql, '', 'xworkdb') + 0;

            if ($maxid < 1) {
                echo "\nbreak";
                break;
            }

            $entityMin = XUnitOfWork::getById($minid, 'xworkdb');
            $entityMax = XUnitOfWork::getById($maxid, 'xworkdb');

            echo "\n {$i} [$minid - $maxid] [{$entityMin->createtime} - {$entityMax->createtime}] => ";

            $sql = "select id
                from xunitofworks
                where ( url like '%from=singlemessage%' or  url like '%from=groupmessage%' or  url like '%from=timeline%' )
                and id >= {$minid} and id < {$maxid} ";

            $ids = Dao::queryValues($sql, '', 'xworkdb');

            echo $cnt = count($ids);

            foreach ($ids as $id) {
                $entity = XUnitOfWork::getById($id, 'xworkdb');

                if (strpos($entity->url, 'from=singlemessage') > 0) {
                    $entity->cacheopen = 11;
                } elseif (strpos($entity->url, 'from=groupmessage') > 0) {
                    $entity->cacheopen = 12;
                } elseif (strpos($entity->url, 'from=timeline') > 0) {
                    $entity->cacheopen = 13;
                }
            }

            $unitofwork->commitAndInit();

            $minid = $maxid;
            $i += 10000;
        }
    }
}

$process = new Xunitofwork_check_groupmessage();
$process->dowork();
