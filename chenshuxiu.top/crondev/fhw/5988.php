<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);
//Debug::$debug = 'Dev';

class Fix_5988
{
    public function fix_merge_doctor () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $all_cnt = 0;

        // '中日医院肺血管病' => 1697 '万钧' => 675, '谢万木' => 1373
        $to_doctorid = 1697;

        // ======================================================== 修改万钧 万钧只有一个疾病，所以直接修改即可 start
        $sql = "select table_name from INFORMATION_SCHEMA.Columns where COLUMN_NAME='doctorid' and table_schema='fcqxdb'";
        $tablenames = Dao::queryValues($sql);

        $cnt = 0;
        $doctorid = 675;
        foreach ($tablenames as $tablename) {
            $update_cnt = Dao::queryValue("select count(id) from {$tablename} where doctorid = {$doctorid} ");

            if ($update_cnt > 0) {
                echo "{$tablename} {$doctorid} => {$to_doctorid} {$update_cnt} \n";

                $sql = " update {$tablename} set doctorid = {$to_doctorid} where doctorid = {$doctorid} \n";
                try {
                    Dao::executeNoQuery($sql);
                } catch (Exception $e) {
                    echo "\n====================" . $sql . "\n";
                    echo $e->getMessage() . "\n";
                }

                $cnt += $update_cnt;

                if ($cnt % 200 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
        }
        $all_cnt += $cnt;

        $unitofwork->commitAndInit();
        // ========================================================= 修改万钧 万钧只有一个疾病，所以直接修改即可 start

        //  ======================================================== 谢万木有两个疾病，19：其他癌症 22：肺动脉高压    所以分两种情况修
        // 1.表上有doctorid和diseaseid的，可以确定，直接修
        $sql = "select a.table_name
                from INFORMATION_SCHEMA.Columns a
                inner join INFORMATION_SCHEMA.Columns b on b.table_name = a.table_name
                where a.COLUMN_NAME='diseaseid' and b.COLUMN_NAME='doctorid' and a.table_schema='fcqxdb' and b.table_schema='fcqxdb' ";
        $tablenames = Dao::queryValues($sql);

        $cnt = 0;
        $doctorid = 1373;
        foreach ($tablenames as $tablename) {
            $update_cnt = Dao::queryValue("select count(id) from {$tablename} where doctorid = {$doctorid} and diseaseid = 22 ");

            if ($update_cnt > 0) {
                echo "{$tablename} {$doctorid} => {$to_doctorid} {$update_cnt} \n";

                $sql = " update {$tablename} set doctorid = {$to_doctorid} where doctorid = {$doctorid} and diseaseid = 22 \n";
                try {
                    Dao::executeNoQuery($sql);
                } catch (Exception $e) {
                    echo "\n====================" . $sql . "====================\n";
                    echo $e->getMessage() . "\n";
                }

                $cnt += $update_cnt;

                if ($cnt % 200 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
        }
        $all_cnt += $cnt;

        $unitofwork->commitAndInit();

        // 2.表上有doctorid和patientid
        $sql = "select a.table_name
                from INFORMATION_SCHEMA.Columns a
                inner join INFORMATION_SCHEMA.Columns b on b.table_name = a.table_name
                where a.COLUMN_NAME='patientid' and b.COLUMN_NAME='doctorid' and a.table_schema='fcqxdb' and b.table_schema='fcqxdb' ";
        $tablenames = Dao::queryValues($sql);

        $cnt = 0;
        $doctorid = 1373;
        foreach ($tablenames as $tablename) {
            $sql = "select count(a.id)
                    from {$tablename} a
                    inner join patients b on b.id = a.patientid
                    where b.diseaseid = 22 and a.doctorid = {$doctorid} ";
            $update_cnt = Dao::queryValue($sql);

            if ($update_cnt > 0) {
                echo "{$tablename} {$doctorid} => {$to_doctorid} {$update_cnt} \n";

                $sql = "update {$tablename} a
                        inner join patients b on b.id = a.patientid
                        set a.doctorid = {$to_doctorid}
                        where b.diseaseid = 22 and a.doctorid = {$doctorid} ";
                try {
                    Dao::executeNoQuery($sql);
                } catch (Exception $e) {
                    echo "\n====================" . $sql . "\n";
                    echo $e->getMessage() . "\n";
                }

                $cnt += $update_cnt;

                if ($cnt % 200 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
        }
        $all_cnt += $cnt;
        $unitofwork->commitAndInit();
        //  ======================================================== 谢万木有两个疾病，19：其他癌症 22：肺动脉高压    所以分两种情况修

        echo "\n总共修改：{$all_cnt}条记录\n";
    }
}

$test = new Fix_5988();
//$test->work();
$test->fix_merge_doctor();
