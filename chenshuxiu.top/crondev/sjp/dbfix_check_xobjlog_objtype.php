<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "512M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

error_reporting(E_ALL ^ E_NOTICE);

TheSystem::init(__FILE__);

class Dbfix_check_xobjlog_objtype extends DbFixBase
{

    public function dowork () {
        $allTables = $this->allTables;

        $objtypes = $this->getObjTypes();

        echo "\n===============\n";

        $tables = [];
        foreach ($objtypes as $a) {
            $tables[] = $table = strtolower($a) . "s";

            if (in_array($table, $allTables)) {
                echo " . ";
            } else {
                echo "\n{$a}";
            }
        }
        echo "\n===============\n";
        foreach ($allTables as $a) {
            if (in_array($a, $tables)) {
                echo " . ";
            } else {
                echo "\n{$a}";
            }
        }
    }

    private function getObjTypes () {
        $tablenos = [];
        $tablenos[] = '201611';
        $tablenos[] = '201612';
        for ($i = 1; $i < 13; $i ++) {
            $tablenos[] = "2017" . sprintf("%02d", $i);
        }

        $objtypes = [];
        foreach ($tablenos as $tableno) {
            $sql = "select objtype from xobjlogs{$tableno} group by objtype order by objtype;";

            $arr = Dao::queryValues($sql, [], 'xworkdb');

            $cnt1 = count($arr);

            // echo "\n{$tableno} : ----- beg ----- {$cnt1} ----- :";

            foreach ($arr as $a) {
                if (false == in_array($a, $objtypes)) {
                    // echo " {$a} ";
                    $objtypes[] = $a;
                }
            }

            array_unique($objtypes);

            $cnt0 = count($objtypes);

            // echo "\n{$tableno} : ----- end ----- {$cnt0} ----- ";
        }

        echo "\n";

        unset($arr);
        unset($tablenos);

        return $objtypes;
    }
}

$time = date('Y-m-d H:i:s');
echo "\n{$time} ==== init ====\n";

$process = new Dbfix_check_xobjlog_objtype();
$process->dowork();

$time = date('Y-m-d H:i:s');
echo "\n{$time} ==== end2 ====\n";
