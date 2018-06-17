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

class Check_cnt_xunitofworks
{

    public function dowork () {
        $tablenos = [];
        $tablenos[] = '201612';
        for ($i = 1; $i <= 10; $i ++) {
            $tablenos[] = "2017" . sprintf("%02d", $i);
        }

        $from_month = '201611';

        $sum_cnt_xworkdb = 0;

        foreach ($tablenos as $to_month) {
            $from_date = $from_month . "01";
            $to_date = $to_month . "01";

            $fromid = strtotime($from_date) . "000000000";
            $toid = strtotime($to_date) . "000000000";

            $sql = "select count(*) from statdb.xunitofworks where id >= {$fromid} and id < {$toid}";
            $cnt_statdb = Dao::queryValue($sql, [], 'statdb');

            $sql = "select count(*) from xworkdb.xunitofworks{$from_month}";
            $cnt_xworkdb = Dao::queryValue($sql, [], 'xworkdb');

            $sum_cnt_xworkdb += $cnt_xworkdb;

            echo "\n {$from_date} - {$to_date} : {$fromid} - {$toid} : {$cnt_statdb} => {$cnt_xworkdb} : ";

            if ($cnt_statdb == $cnt_xworkdb) {
                echo "==";
            } else {
                echo " != ";
            }

            $from_month = $to_month;
        }

        $sql = "select count(*) from statdb.xunitofworks";
        $sum_cnt_statdb = Dao::queryValue($sql, [], 'statdb');

        echo "\n\n sum : {$sum_cnt_statdb} => {$sum_cnt_xworkdb} ";

        echo "\n";
    }
}

$process = new Check_cnt_xunitofworks();
$process->dowork();
echo "\n";
