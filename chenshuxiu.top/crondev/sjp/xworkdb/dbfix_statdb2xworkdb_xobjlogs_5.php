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

class Dbfix_statdb2xworkdb_xobjlogs_5
{

    public function dowork () {
        $tablenos = [];

        for ($i = 1000; $i < 1256; $i ++) {
            $tablenos[] = $i;
        }

        $tablenos[] = '201611';
        $tablenos[] = '201612';
        for ($i = 1; $i < 13; $i ++) {
            $tablenos[] = "2017" . sprintf("%02d", $i);
        }

        $old_objtype = 'WithdrawOrder';
        $new_objtype = 'PatientWithdrawOrder';

        $dbExe = BeanFinder::get("DbExecuter", 'xworkdb');

        $sum_cnt = 0;

        foreach ($tablenos as $tableno) {

            $sql = "select count(*) from xobjlogs{$tableno} where objtype = :objtype ";
            $bind = [];
            $bind[':objtype'] = $old_objtype;
            $cnt = Dao::queryValue($sql, $bind, 'xworkdb');

            $sum_cnt += $cnt;

            if ($cnt < 1) {
                echo ".";
            } else {
                echo "\n[{$tableno}:{$old_objtype}] += {$cnt} = $sum_cnt ";

                $sql = "select * from xobjlogs{$tableno} where objtype = :objtype ";
                $bind = [];
                $bind[':objtype'] = $old_objtype;

                $rows = Dao::queryRows($sql, $bind, 'xworkdb');

                foreach ($rows as $row) {

                    $row['objtype'] = $new_objtype; // 新类型
                    $row["randno_fix"] = XObjLog::getTablenoByObjtypeObjid($row['objtype'], $row['objid']);

                    // 生成双份散列
                    // 按日期散列
                    $dbconf = [];
                    $dbconf['database'] = 'xworkdb';
                    $dbconf['tableno'] = $row['randno'];
                    $xobjlog1 = XObjLog::createByBiz($row, $dbconf);

                    $row["id"] = $xobjlog1->id;

                    // 按objtype:objid散列
                    $dbconf = [];
                    $dbconf['database'] = 'xworkdb';
                    $dbconf['tableno'] = $row["randno_fix"];
                    $xobjlog2 = XObjLog::createByBiz($row, $dbconf);

                    $sql = "delete from xobjlogs{$tableno} where id = {$row["id"]} ";
                    Dao::executeNoQuery($sql, [], 'xworkdb');
                    echo "\n {$sql}";

                    $sqls1 = [];
                    $sqls2 = [];

                    if ($row['randno'] == '197001') {
                        echo "\n 197001";
                    } else {
                        $sql = "select * from xobjlogs{$row['randno']} where id = {$row["id"]} ";
                        $old_row = Dao::queryRow($sql, [], 'xworkdb');

                        if (false == empty($old_row)) {
                            $sql = "delete from xobjlogs{$row['randno']} where id = {$row["id"]} ";
                            Dao::executeNoQuery($sql, [], 'xworkdb');
                            echo "\n {$sql}";
                            echo "\n {$old_row['objtype']}";
                        }
                    }

                    $sql = "select * from xobjlogs{$row["randno_fix"]} where id = {$row["id"]} ";
                    $old_row = Dao::queryRow($sql, [], 'xworkdb');

                    if (false == empty($old_row)) {
                        $sql = "delete from xobjlogs{$row["randno_fix"]} where id = {$row["id"]} ";
                        Dao::executeNoQuery($sql, [], 'xworkdb');
                        echo "\n {$sql}";
                        echo "\n {$old_row['objtype']}";
                    }

                    if ($row['randno'] != '197001') {
                        $sqls1 = $xobjlog1->getInsertCommand();
                    }

                    $sqls2 = $xobjlog2->getInsertCommand();

                    $sqls = array_merge($sqls1, $sqls2);

                    foreach ($sqls as $a) {
                        $sql = $a['sql'];
                        $param = $a['param'];
                        $dbExe->executeNoQuery($sql, $param);

                        echo "\n {$sql}";
                    }

                    echo "\n[{$row['objtype']},{$row['objid']}] : {$row['id']} : {$row['randno']} : {$row["randno_fix"]} \n";
                }
            }
        }

        echo "\n";
    }
}

$process = new Dbfix_statdb2xworkdb_xobjlogs_5();
$process->dowork();
echo "\n";
