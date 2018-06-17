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

class Dbfix_statdb2xworkdb_xobjlogs_2
{

    public function dowork () {
        $tablenos = [];
        $tablenos[] = '201611';
        $tablenos[] = '201612';
        for ($i = 1; $i < 13; $i ++) {
            $tablenos[] = "2017" . sprintf("%02d", $i);
        }
        $tablenos[] = '201899';

        $i = 0;
        foreach ($tablenos as $a) {
            echo $sql = "\n update xobjlogs{$a} set randno_fix=1000 + CONV(left(md5(concat(objtype,':',objid)),2), 16, 10 ) ";
        }

        echo "\n";
    }
}

$process = new Dbfix_statdb2xworkdb_xobjlogs_2();
$process->dowork();
echo "\n";
