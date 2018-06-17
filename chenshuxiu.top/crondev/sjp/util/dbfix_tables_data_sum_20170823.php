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

// 汇总各表数据情况, 生成一个表格 |table|cnt|min_createdate|max_createdate|
class Dbfix_tables_data_sum extends DbFixBase
{

    private $table_cnt_arr = array();

    public function dowork () {
        $tableNames = $this->allTables;

        foreach ($tableNames as $table) {
            $this->doOneTable($table);
        }

        arsort($this->table_cnt_arr);

        $jumpTables = array(
            'idgenerator',
            'patientid_userids',
            'userid_wxuserids',
            'xcodes');

        echo "\n|table|cnt|min_createdate|max_createdate|";

        foreach ($this->table_cnt_arr as $table => $cnt) {

            if (strpos($table, 'rpt_') === 0) {
                continue;
            }

            $row = array();
            $min_createdate = '';
            $max_createdate = '';

            if (false == in_array($table, $jumpTables)) {
                $sql = "select min(createtime) as min_createtime ,
                max(createtime) as max_createtime
                from $table ";
                $row = Dao::queryRow($sql);

                $min_createtime = $row['min_createtime'];
                $max_createtime = $row['max_createtime'];

                $mintime = strtotime($min_createtime);
                $maxtime = strtotime($max_createtime);

                if ($maxtime > strtotime('2016-10-01')) {
                    // continue;
                }

                $min_createdate = substr($min_createtime, 0, 10);
                $max_createdate = substr($max_createtime, 0, 10);
            }

            echo "\n|{$table}|{$cnt}|{$min_createdate}|{$max_createdate}|";
        }
    }

    public function doOneTable ($table) {
        $sql = "select count(*) as cnt from $table";
        $this->table_cnt_arr[$table] = Dao::queryValue($sql);
    }
}

echo "\n==== begin ====\n";
$process = new Dbfix_tables_data_sum();
$process->dowork();
echo "\n==== end ====\n";
