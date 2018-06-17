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

class Check_cnt_xobjlogs_2
{

    public function dowork () {
        $tablenos = [];
        for ($i = 0; $i < 256; $i ++) {
            $tablenos[] = 1000 + $i;
        }

        $sum_cnt_xworkdb_1 = 0;
        $sum_cnt_xworkdb_2 = 0;

        foreach ($tablenos as $a) {
            $sql = "select count(*) from xworkdb.xobjlogs{$a} where xunitofworkid=0";
            $cnt_xworkdb_1 = Dao::queryValue($sql, [], 'xworkdb');

            $sql = "select count(*) from xworkdb.xobjlogs{$a} where xunitofworkid>0";
            $cnt_xworkdb_2 = Dao::queryValue($sql, [], 'xworkdb');

            $sum_cnt_xworkdb_1 += $cnt_xworkdb_1;
            $sum_cnt_xworkdb_2 += $cnt_xworkdb_2;

            echo "\n [{$a}]  : {$cnt_xworkdb_1} + {$cnt_xworkdb_2} =>  {$sum_cnt_xworkdb_1} + {$sum_cnt_xworkdb_2} ";
        }

        $sql = "select count(*) from statdb.objlogs where xunitofworkid = 0";
        $sum_cnt_statdb_1 = Dao::queryValue($sql, [], 'statdb');

        $sql = "select count(*) from statdb.objlogs where xunitofworkid > 0";
        $sum_cnt_statdb_2 = Dao::queryValue($sql, [], 'statdb');

        echo "\n\n sum_1 : {$sum_cnt_statdb_1} => {$sum_cnt_xworkdb_1} ";
        echo "\n\n sum_2 : {$sum_cnt_statdb_2} => {$sum_cnt_xworkdb_2} ";

        echo "\n";
    }
}

$process = new Check_cnt_xobjlogs_2();
$process->dowork();
echo "\n";
