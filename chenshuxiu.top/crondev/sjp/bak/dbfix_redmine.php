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
Debug::$debug_mergexworklog = false;
// Debug::$debug = 'Dev';

class dbfix_redmine extends DbFixBase
{

    public function dowork () {

        $db002 = BeanFinder::get('DbExecuter', 'redmine002');
        $sql = "show tables";
        $tables = $db002->queryValues($sql);

        foreach ($tables as $a) {
            $this->checkOneTable($a);
        }
    }

    public function checkOneTable ($table) {
        $db001 = BeanFinder::get('DbExecuter', 'redmine001');
        $db002 = BeanFinder::get('DbExecuter', 'redmine002');

        try {
            $sql = "select id from {$table}";
            $ids1 = $db001->queryValues($sql);
            $ids2 = $db002->queryValues($sql);

            print_r($ids1);
            print_r($ids2);
            exit();

            $ids_diff = array_diff($ids1, $ids2);
            if (count($ids_diff) > 0) {
                echo "\n\n === {$table} ===";
                foreach ($ids_diff as $id) {
                    echo $sql = "\ndelete from {$table} where id={$id}";
                    $sql = "select * from {$table} where id={$id}";
                    // $row = $db001->queryOneRow($sql);
                    // $db002->insert($table, $row);
                }
            }
        } catch (Exception $ex) {
            Debug::trace("$table has not id field");
        }
    }
}

$process = new dbfix_redmine();
$process->dowork();
