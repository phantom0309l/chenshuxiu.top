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

class Dbfix_statdb2xworkdb_xobjlogs_3
{

    public function dowork () {
        $tablenos = [];
        $tablenos[] = '201611';
        $tablenos[] = '201612';
        for ($i = 1; $i < 9; $i ++) {
            $tablenos[] = "2017" . sprintf("%02d", $i);
        }
        $tablenos[] = '201899';

        foreach ($tablenos as $a) {
            for ($i = 1000; $i < 1256; $i ++) {
                $time = date("Y-m-d H:i:s");
                echo "\n";
                echo $sql = "select count(*) from xworkdb.xobjlogs{$a} where randno_fix={$i} ";
                $cnt = Dao::queryValue($sql, [], 'xworkdb');
                echo "\n {$time} : {$a} : {$i} =  {$cnt} ";
                echo $sql = "\n insert into xworkdb.xobjlogs{$i} select * from xobjlogs{$a} where randno_fix={$i} ";
                Dao::executeNoQuery($sql, [], 'xworkdb');
            }
        }

        echo "\n";
    }
}

$process = new Dbfix_statdb2xworkdb_xobjlogs_3();
$process->dowork();
echo "\n";
