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

class Dbfix_statdb2xworkdb_xobjlogs_fix
{

    public function dowork ($min_id) {
        $sql = "select min(id) as min_id, max(id) as max_id, count(*) as cnt
                from statdb.objlogs
                where id > {$min_id}";
        $row = Dao::queryRow($sql, [], 'statdb');

        $min_id = $row['min_id'];
        $max_id = $row['max_id'];
        $cnt = $row['cnt'];

        echo "\n ===={$min_id} - {$max_id} = {$cnt} ====\n";

        $arr1 = [
            'id',
            'version',
            'createtime',
            'updatetime'];

        $arr2 = XObjLog::getKeysDefine();
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
        while ($min_id < $max_id) {
            $sql = "select * from statdb.objlogs where id > {$min_id} order by id limit 1";
            $row = Dao::queryRow($sql, [], 'statdb');

            if (empty($row)) {
                break;
            }

            if (strlen($row['xunitofworkid']) < 18) {
                $row['xunitofworkid'] = substr($row['xunitofworkid'] . "000000000", 0, 19);
            }

            $xunitofworkid = $row['xunitofworkid'];
            $randno = XUnitOfWork::getTablenoByXunitofworkid($xunitofworkid);

            $row['randno'] = $randno;

            $objtype = $row['objtype'];
            $objid = $row['objid'];
            $randno_fix = XObjLog::getTablenoByObjtypeObjid($objtype, $objid);

            $row['randno_fix'] = $randno_fix;

            $i ++;
            $min_id = $row['id'];

            if ($i % 1000 == 0) {
                echo "\n" . date("H:i:s");
                echo " {$i} - {$randno} - {$min_id} [{$row['createtime']}] : ";
                sleep(1);
            } elseif ($i % 100 == 1) {
                echo ".";
            }

            $bind = [];
            foreach ($arr as $a) {
                $bind[':' . $a] = $row[$a];
            }

            if ($randno != '197001') {
                $sql = "select id from xworkdb.xobjlogs{$randno} where id={$row['id']} ";
                $xx = Dao::queryValue($sql, [], 'xworkdb');
                if (empty($xx)) {
                    $sql = "insert into xworkdb.xobjlogs{$randno} ($columns) values ($bindColumns) ;";
                    Dao::executeNoQuery($sql, $bind, 'xworkdb');
                } else {
                    echo "-";
                }
            }

            $tableno = $randno_fix;

            $sql = "select id from xworkdb.xobjlogs{$tableno} where id={$row['id']} ";
            $xx = Dao::queryValue($sql, [], 'xworkdb');
            if (empty($xx)) {
                $sql = "insert into xworkdb.xobjlogs{$tableno} ($columns) values ($bindColumns) ;";
                Dao::executeNoQuery($sql, $bind, 'xworkdb');
            } else {
                echo "=";
            }

            unset($xx);
            unset($row);
            unset($bind);
        }
    }
}

echo "\n";
$min_id = $argv[1];
if (empty($min_id)) {
    echo "\nplease input min_id\n\n";
    exit();
}
$process = new Dbfix_statdb2xworkdb_xobjlogs_fix();
$process->dowork($min_id);
echo "\n";
