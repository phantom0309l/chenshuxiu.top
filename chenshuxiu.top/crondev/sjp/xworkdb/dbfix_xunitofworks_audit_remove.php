<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "3048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Dbfix_xunitofworks_audit_remove
{

    public function dowork () {
        $min_id = Dao::queryValue("select min(id) from statdb.xunitofworks", [], 'statdb');
        $max_id = $min_id;

        $i = 0;
        $cnt1 = 0;
        $cnt2 = 0;
        while (true) {
            $time = date("H:i:s");
            echo "\n{$time} {$i}0000 : {$min_id} - {$max_id} : {$cnt1}";
            $i ++;

            $min_id = $max_id;

            $sql = "select max(id) from ( select id from statdb.xunitofworks where id > {$min_id} order by id limit 10000) tt;";

            $max_id = Dao::queryValue($sql, [], 'statdb');

            if ($max_id <= $min_id) {
                break;
            }

            $sql = "select count(*) from statdb.xunitofworks
                where id >= {$min_id} and id <= {$max_id} and sub_domain='audit'
                    and commit_insert_cnt=0 and commit_update_cnt=0 and commit_delete_cnt=0; ";
            $cnt1 = Dao::queryValue($sql, [], 'statdb');

            $cnt2 = 0;
            if ($cnt1 > 0) {
                $sql = "delete from statdb.xunitofworks
            where id >= {$min_id} and id <= {$max_id} and sub_domain='audit'
            and commit_insert_cnt=0 and commit_update_cnt=0 and commit_delete_cnt=0; ";
                $cnt2 = Dao::executeNoQuery($sql, [], 'statdb');
            }
        }
    }
}

echo "\n";
$process = new Dbfix_xunitofworks_audit_remove();
$process->dowork();
echo "\n";
