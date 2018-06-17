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

class Dbfix_statdb2xworkdb_xobjlogs_1
{

    public function dowork () {
        $tablenos = [];
        $tablenos[] = '201611';
        $tablenos[] = '201612';
        for ($i = 1; $i < 13; $i ++) {
            $tablenos[] = "2017" . sprintf("%02d", $i);
        }

        $from_month = '200001';

        foreach ($tablenos as $a) {
            $to_month = $a;

            $arr = [];
            $arr[] = [
                $from_month . "01",
                $to_month . "01"];
            $arr[] = [
                $from_month . "01",
                $from_month . "11"];
            $arr[] = [
                $from_month . "11",
                $from_month . "21"];
            $arr[] = [
                $from_month . "21",
                $to_month . "01"];

            foreach ($arr as $b) {

                list ($from_date, $to_date) = $b;

                $fromid = strtotime($from_date) . "000000000";
                $toid = strtotime($to_date) . "000000000";

                echo "\n==== {$from_date} - {$to_date} ====\n";
                echo $sql = "select count(*) from statdb.objlogs where xunitofworkid >= {$fromid} and xunitofworkid < {$toid}";
                echo "\n";
                if ($from_date == $from_month . "01" && $to_date == $to_month . "01") {
                    echo "====+++====\n";
                } else {
                    echo $sql = "insert into xobjlogs{$from_month} (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, {$from_month}, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= {$fromid} and xunitofworkid < {$toid}";
                    echo "\n";
                }
                echo "select count(*) from xobjlogs{$from_month} ";
                echo "\n";
            }
            $from_month = $a;
            echo "\n";
        }

        echo "\n";
    }
}

$process = new Dbfix_statdb2xworkdb_xobjlogs_1();
$process->dowork();
echo "\n";
