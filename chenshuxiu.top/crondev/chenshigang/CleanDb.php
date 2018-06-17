<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "3048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class CleanDb extends DbFixBase
{

    public function dowork () {

        $begin = time();
        echo "开始清洗数据\n";

        $sql = "SELECT id FROM patients WHERE LOCATE('测试', `name`)=0 AND LOCATE('*', NAME) = 0";
        $dbname = 'fcqxdb_tmp';
        $patientids = Dao::queryValues($sql, array(), $dbname);

        $sql = "show tables";
        $tableNames = Dao::queryValues($sql, array(), $dbname);

        $patientidTables = array();

        foreach ($tableNames as $tableName) {
            $sql = "show full fields from `$tableName`";
            $rows = Dao::queryRows($sql, array(), $dbname);

            $i = 0;
            foreach ($rows as $a) {
                if ($a['field'] == 'patientid') {
                    $patientidTables[] = $tableName;
                    break;
                }
            }
        }

        $j = 1;
        $total = count($patientidTables);
        foreach ($patientidTables as $table) {
            $i = 0;
            $cnt = 0;
            while (true) {
                $patientids_slice = array_slice($patientids, $i * 1000, 1000);
                if (empty($patientids_slice)) {
                    break;
                }
                $i ++;
                $sql = "DELETE FROM `$table` WHERE patientid IN ('" . implode("','", $patientids_slice) . "')";
                $cnt += Dao::executeNoQuery($sql, array(), $dbname);
                echo "已清洗 $table($j/$total) $cnt 条数据\n";
            }
            $j ++;
        }
        $sql = "DELETE FROM patients WHERE LOCATE('测试', `name`)=0 AND LOCATE('*', NAME) = 0";
        Dao::executeNoQuery($sql, array(), $dbname);
        $end = time();
        $cost = $end - $begin;
        echo "数据清洗完毕, cost {$cost}s\n";
    }
}

$obj = new CleanDb();
$obj->dowork();
