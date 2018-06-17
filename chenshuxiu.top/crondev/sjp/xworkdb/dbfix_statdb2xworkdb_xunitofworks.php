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

class Dbfix_statdb2xworkdb_xunitofworks
{

    public function dowork ($the_month, $begin_id = 0) {
        $id_fix = '000000000';

        $min_id = strtotime($the_month . "01") . $id_fix;
        $max_id = strtotime("+1 month", strtotime($the_month . "01")) . $id_fix;

        echo "\n";
        echo $sql = "select max(id) from xworkdb.xunitofworks{$the_month} where id < {$max_id} ";
        echo "\n";
        $xx = Dao::queryValue($sql, [], 'xworkdb');
        if ($xx > $min_id) {
            // $min_id = $xx;
        }

        if ($begin_id > $min_id) {
            $min_id = $begin_id;
        }

        echo "\n ===={$min_id} - {$max_id}==== \n";

        $arr1 = [
            'id',
            'version',
            'createtime',
            'updatetime'];

        $arr2 = XUnitOfWork::getKeysDefine();
        $arr = array_merge($arr1, $arr2);

        $columns = [];
        $bindColumns = [];
        foreach ($arr as $column) {
            $columns[] = "`$column`";
            $bindColumns[] = ":" . $column;
        }
        $columns = implode(", ", $columns);
        $bindColumns = implode(",", $bindColumns);

        $i = 0;
        $j = 0;
        $k = 0;
        while ($min_id > 0 && $min_id < $max_id) {
            $sql = "select * from statdb.xunitofworks where id > {$min_id} order by id limit 1";
            $row = Dao::queryRow($sql, [], 'statdb');

            if (empty($row)) {
                break;
            }

            $xunitofworkid = $row['id'];
            $randno = XUnitOfWork::getTablenoByXunitofworkid($xunitofworkid);

            $row['randno'] = $randno;
            $row['posts'] = '';

            $i ++;
            $min_id = $row['id'];

            if ($i % 10000 == 0) {
                $min_datetime = date('Y-m-d H:i:s', substr($min_id, 0, 10));
                echo "\n" . date("H:i:s");
                echo " {$i} - {$randno} - {$min_id} [{$min_datetime}] : ";
                sleep(1);
            }

            if (strlen($row['id']) < 18) {
                $row['id'] = substr($row['id'] . "000000000", 0, 19);
            }

            $sql = "select id from xworkdb.xunitofworks{$randno} where id={$row['id']} ";
            $xx = Dao::queryValue($sql, [], 'xworkdb');
            if (empty($xx)) {
                $bind = [];
                foreach ($arr as $a) {
                    $bind[':' . $a] = $row[$a];
                }

                $sql = "insert into xworkdb.xunitofworks{$randno} ($columns) values ($bindColumns) ;";

                Dao::executeNoQuery($sql, $bind, 'xworkdb');
                $k ++;

                if ($k % 100 == 1) {
                    echo "+";
                }
            } else {
                $j ++;

                if ($j % 100 == 1) {
                    echo ".";
                }
            }

            unset($xx);
            unset($row);
            unset($bind);
        }
    }
}

echo "\n";

$the_month = $argv[1];
$begin_id = $argv[2];
if (empty($the_month)) {
    echo "\nplease input month and begin_id, 格式: 201701 123 \n\n";
    exit();
}

$process = new Dbfix_statdb2xworkdb_xunitofworks();
$process->dowork($the_month, $begin_id);
echo "\n";
