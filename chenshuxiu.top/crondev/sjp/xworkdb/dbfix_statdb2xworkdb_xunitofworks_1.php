<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "1024M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Dbfix_statdb2xworkdb_xunitofworks_1
{

    public function dowork () {
        for ($i = 1; $i < 29; $i ++) {

            $from_date = '201702' . sprintf("%02d", $i);
            $to_date = '201702' . sprintf("%02d", $i + 1);

            $fromid = strtotime($from_date) . "000000000";
            $toid = strtotime($to_date) . "000000000";

            $time = date("Y-m-d H:i:s");
            echo "\n{$time} {$from_date} - {$to_date} : ";
            $sql = "select count(*) from statdb.xunitofworks where id >= {$fromid} and id < {$toid}";
            $cnt = Dao::queryValue($sql, [], 'statdb');
            echo $cnt;
            echo "\n";

            $sql = "insert into xworkdb.xunitofworks201702";
            $sql .= " (`id`, `version`, `createtime`, `updatetime`, `randno`, `server_ip`, `client_ip`, `dev_user`, ";
            $sql .= " `domain`, `sub_domain`, `action_name`, `method_name`, `cacheopen`,";
            $sql .= " `commit_load_cnt`, `commit_insert_cnt`, `commit_update_cnt`, `commit_delete_cnt`,";
            $sql .= " `method_end`, `commit_end`, `page_end`, `url`, `referer`, `cookie`, `posts`)";
            $sql .= " select id, version, createtime, updatetime, 201702, server_ip, client_ip, dev_user,";
            $sql .= " domain, sub_domain, action_name, method_name, cacheopen,";
            $sql .= " commit_load_cnt, commit_insert_cnt, commit_update_cnt, commit_delete_cnt,";
            $sql .= " method_end, commit_end, page_end, url, referer, cookie, '' as posts";
            $sql .= " from statdb.xunitofworks";
            $sql .= " where id >= {$fromid} and id < {$toid};";

            Dao::executeNoQuery($sql, [], 'xworkdb');
        }

        echo "\n";
    }
}

$process = new Dbfix_statdb2xworkdb_xunitofworks_1();
$process->dowork();
echo "\n";
