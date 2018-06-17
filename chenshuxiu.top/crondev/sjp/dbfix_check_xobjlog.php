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

class Dbfix_check_xobjlog extends DbFixBase
{

    private $ii = 0;

    private $time_start = 0;

    public function dowork ($begin_objtype, $begin_id = 0) {
        $this->time_start = time();

        echo "\n [Dbfix_check_xobjlog] [start]";
        $unitofwork = BeanFinder::get("UnitOfWork");
        $unitofworkid = Debug::getUnitofworkId();

        $objtypes = $this->getObjTypes();

        $objtype_cnt = count($objtypes);

        $time = date('Y-m-d H:i:s');
        echo "\n{$time} ==== objtype_cnt = {$objtype_cnt} ====\n";

        $jump = true;

        $jumpObjTypes = [
            'Address',
            'Comment',
            'LessonUserRef',
            'Guest_schulterecord',
            'OpTask',
            'PatientAddress',
            'PatientDrugState',
            'PatientPgroupActItem',
            'PatientPgroupTask',
            'Picture',
            'Pipe',
            'PnodeState',
            'PushMsg',
            'PvLog',
            'Sfda_medicine',
            'Study',
            'StudyPlan',
            'User',
            'WxTaskItem',
            'XAnswer',
            'XAnswerOptionRef',
            'XAnswerSheet',
            'XPatientIndex'];

        foreach ($objtypes as $i => $objtype) {

            if ($objtype == 'DealWithTpl') {
                $objtype = 'DealwithTpl';
            }

            if (in_array($objtype, $jumpObjTypes)) {
                echo "\n{$i} {$objtype} ==== jump ====";
                continue;
            }

            if (strpos($objtype, 'pt_') > 0) {
                echo "\n{$i} {$objtype} ==== jump ====";
                continue;
            }

            $minid = 0;
            if ($begin_objtype == $objtype) {
                $jump = false;
                $minid = $begin_id;
            }

            if ($jump) {
                echo "\n{$i}/{$objtype_cnt} {$objtype} ==";
                continue;
            }

            sleep(1);

            $time = date('Y-m-d H:i:s');

            echo "\n{$time} ----- {$objtype} ----- cnt = ";
            $this->checkOneObjType($objtype, $minid);
        }

        echo "\n [Dbfix_check_xobjlog] [end]";
    }

    private function checkOneObjType ($objtype, $minid = 0) {
        $table = strtolower($objtype) . 's';

        $sql = "select count(*) as cnt from {$table} where id > {$minid} ";

        $database = 'fcqxdb';
        if ($objtype == 'CronLog') {
            $database = "statdb";
        }
        $cnt = Dao::queryValue($sql, [], $database);

        echo $cnt;

        $id = $minid;
        $i = 0;

        $KeysDefine = $objtype::getKeysDefine();

        while (true) {

            $this->ii ++;

            if ($this->ii % 200 == 0) {
                file_put_contents(dirname(__FILE__) . '/dbfix_check_xobjlog.ini', "{$objtype}:{$id}");
            }

            $time_end = time();

            if (($time_end - $this->time_start) > 50) {

                file_put_contents(dirname(__FILE__) . '/dbfix_check_xobjlog.ini', "{$objtype}:{$id}");

                $time = date('Y-m-d H:i:s');
                echo "\n{$time} ==== end == {$objtype}:{$id} ====\n";
                exit();
            }

            $break = $this->doNext($objtype, $table, $KeysDefine, $cnt, $i, $id);
            gc_collect_cycles();

            if ($break) {
                break;
            }
        }

        gc_collect_cycles();
    }

    private function doNext ($objtype, $table, $KeysDefine, $cnt, &$i, &$id) {
        $i ++;
        $time = date('Y-m-d H:i:s');

        $sql = "select * from {$table} where id > :id order by id limit 1 ";
        $bind = [];
        $bind[':id'] = $id;

        $database = 'fcqxdb';
        if ($objtype == 'CronLog') {
            $database = "statdb";
        }

        $row = Dao::queryRow($sql, $bind, $database);

        $id = $row['id'];

        if (empty($id)) {
            return true;
        }

        $arr = XObjLog::getSnapByObj($objtype, $id, 100000);

        if (count($arr) < 1) {
            echo "\n{$time} {$objtype}[{$id}] not xobjlog ";
        }

        $diff = array_diff_assoc($row, $arr);

        unset($diff['id']);
        unset($diff['version']);
        unset($diff['createtime']);
        unset($diff['updatetime']);

        $cnt_diff = count($diff);

        if ($i % 100 == 0) {

            $memory1 = memory_get_usage(true);

            echo "\n{$time} {$this->ii} : {$i} / {$cnt} : {$table}[{$id}] :memory[{$memory1}] ";
            usleep(100);
        }

        if ($cnt_diff > 0) {
            foreach ($diff as $k => $v) {
                if (false == in_array($k, $KeysDefine)) {
                    unset($diff[$k]);
                }
            }

            $cnt_diff = count($diff);

            if ($cnt_diff > 0) {
                $diff_keys = array_keys($diff);
                $this->createTwoXObjLogs($objtype, $row, $diff);
                echo "\n{$time} {$this->ii} : {$i} / {$cnt} : {$objtype}[{$id}] cnt_diff = {$cnt_diff} : " . implode(',', $diff_keys);
                echo "\n";
            }
        } elseif ($i % 2 == 0) {
            echo ".";
        }

        return false;
    }

    private function createTwoXObjLogs ($objtype, $row, $diff) {
        $xunitofworkid = Debug::getUnitofworkId();

        $objid = $row['id'];
        $objver = $row['version'];

        $randno = XUnitOfWork::getTablenoByXunitofworkid($xunitofworkid);
        $content = json_encode($diff, JSON_UNESCAPED_UNICODE);

        $row = array();
        $row["randno"] = $randno;
        $row["xunitofworkid"] = $xunitofworkid;
        $row["type"] = 5; // 修订
        $row["objtype"] = $objtype;
        $row["objid"] = $objid;
        $row["objver"] = $objver;
        $row["content"] = $content;
        $row["randno_fix"] = XObjLog::getTablenoByObjtypeObjid($objtype, $objid);

        // 生成双份散列
        // 按日期散列
        $dbconf = [];
        $dbconf['database'] = 'xworkdb';
        $dbconf['tableno'] = $randno;
        $xobjlog1 = XObjLog::createByBiz($row, $dbconf);

        $row["id"] = $xobjlog1->id;

        // 按objtype:objid散列
        $dbconf = [];
        $dbconf['database'] = 'xworkdb';
        $dbconf['tableno'] = $row["randno_fix"];
        $xobjlog2 = XObjLog::createByBiz($row, $dbconf);

        $database = 'xworkdb';

        $sqls1 = $xobjlog1->getInsertCommand();
        foreach ($sqls1 as $arr) {
            $sql = $arr['sql'];
            $bind = $arr['param'];

            Dao::executeNoQuery($sql, $bind, $database);
        }

        $sqls2 = $xobjlog2->getInsertCommand();
        foreach ($sqls2 as $arr) {
            $sql = $arr['sql'];
            $bind = $arr['param'];
            Dao::executeNoQuery($sql, $bind, $database);
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

            foreach ($arr as $a) {
                if (false == in_array($a, $objtypes)) {
                    $objtypes[] = $a;
                }
            }

            array_unique($objtypes);

            $cnt0 = count($objtypes);
        }

        echo "\n";

        sort($objtypes);

        $tables = $this->allTables;

        $arr1 = [];
        $arr2 = [];

        foreach ($objtypes as $a) {
            $table = strtolower($a) . "s";
            if (in_array($table, $tables)) {
                $arr1[] = $table;
            } else {
                $arr2[] = $table;
                echo "\n==== {$a} : {$table} ====";
            }
        }

        $arr3 = [];

        foreach ($tables as $table) {
            if (false == in_array($table, $arr1)) {
                $arr3[] = $table;
            }
        }

        return $objtypes;
    }
}

$time = date('Y-m-d H:i:s');

echo "\n{$time} ==== init ====\n";

$arr = file(dirname(__FILE__) . '/dbfix_check_xobjlog.ini');

$line = $arr[0];

echo "\n{$time} ==== {$line} ====\n";

list ($objtype, $minid) = explode(':', $line);
$objtype = trim($objtype);
$minid = trim($minid);

echo "\n{$time} ==== [{$objtype}][{$minid}] ====\n";

$process = new Dbfix_check_xobjlog();
$process->dowork($objtype, $minid);

$time = date('Y-m-d H:i:s');

echo "\n{$time} ==== end2 ====\n";
