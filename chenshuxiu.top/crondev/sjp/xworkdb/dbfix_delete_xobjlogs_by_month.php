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

class Dbfix_delete_xobjlogs_by_month
{

    public function dowork ($the_month) {
        $tablenos = [];
        $tablenos[] = '201611';
        $tablenos[] = '201612';
        for ($i = 1; $i < 13; $i ++) {
            $tablenos[] = "2017" . sprintf("%02d", $i);
        }

        for ($i = 1; $i < 13; $i ++) {
            $tablenos[] = "2018" . sprintf("%02d", $i);
        }

        for ($i = 0; $i < 256; $i ++) {
            $tablenos[] = 1000 + $i;
        }

        foreach ($tablenos as $tableno) {

            $sql = "OPTIMIZE table xobjlogs{$tableno} ";
            Dao::executeNoQuery($sql, [], 'xworkdb');

            echo "\n{$sql}";
            continue;

            $sql = "select count(*) from `xobjlogs{$tableno}` where randno='{$the_month}'; ";
            $cnt = Dao::queryValue($sql, [], 'xworkdb');
            if ($cnt > 0) {
                echo "\n{$cnt} => {$sql}";

                $sql = "delete from `xobjlogs{$tableno}` where randno='{$the_month}'; ";
                Dao::executeNoQuery($sql, [], 'xworkdb');
            }
        }

        echo "\n";
    }
}

$the_month = $argv[1];
if (empty($the_month)) {
    echo "\nplease input moth, 格式: 201708 \n\n";
    exit();
}

$process = new Dbfix_delete_xobjlogs_by_month();
$process->dowork($the_month);
