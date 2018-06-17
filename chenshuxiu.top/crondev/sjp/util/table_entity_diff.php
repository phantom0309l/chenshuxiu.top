<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class table_entity_diff
{

    public function dowork () {
        $tables = $this->getAllTables();

        foreach ($tables as $table) {
            $this->diff_one($table);
        }
    }

    private function diff_one ($table) {
        global $lowerclasspath;

        $entity_name = substr($table, 0, strlen($table) - 1);

        $entity_name = $lowerclasspath[$entity_name];

        // echo "\n===== {$entity_name} {$table} ==========\n";

        if (empty($entity_name) || in_array($table, [
            'xcodes'])) {
            echo "\n\n===== $table NotFoundEntity =====\n";
            return;
        }

        $sql = "show full fields from `$table`";
        $rows = Dao::queryRows($sql);

        $table_fields = array();
        foreach ($rows as $row) {
            $table_fields[] = $row['field'];
        }

        $entity_fields = $entity_name::getKeysDefine();
        $entity_fields[] = 'id';
        $entity_fields[] = 'version';
        $entity_fields[] = 'createtime';
        $entity_fields[] = 'updatetime';

        $field_diff1 = array_diff($entity_fields, $table_fields);
        if (false == empty($field_diff1)) {
            echo "\n{$entity_name} {$table} ===== diff1 =====\n";
            print_r($field_diff1);
        }

        $field_diff2 = array_diff($table_fields, $entity_fields);
        if (false == empty($field_diff2)) {
            echo "\n{$entity_name} {$table} ===== diff2 =====\n";
            print_r($field_diff2);
        }

        foreach ($field_diff2 as $a) {
            echo "\nalter table {$table} drop column {$a};";
        }
    }

    // 加载全部表名
    public function getAllTables () {
        $sql = "show tables";
        return Dao::queryValues($sql);
    }
}

$process = new table_entity_diff();
$process->dowork();