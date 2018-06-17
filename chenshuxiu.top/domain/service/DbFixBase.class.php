<?php

class DbFixBase
{

    // 全部表名
    protected $allTables = array();

    public function __construct () {

        $this->initAllTables();
    }

    // 表名 => 实体类名
    protected function table2entityType ($table) {
        global $lowerclasspath;

        $tabl = substr($table, 0, strlen($table) - 1);
        $entityType = $lowerclasspath[$tabl];

        echo "\n\n=====[ $table => $entityType ]=====\n\n";

        return $entityType;
    }

    // 加载全部表名
    public function initAllTables () {
        $sql = "show tables";
        $allTables = Dao::queryValues($sql, []);
        foreach ($allTables as $a) {

            $str = substr($a, - 3);
            if ($str == 'bak') {
                continue;
            }

            $this->allTables[] = $a;
        }
    }

    public function getAllTables () {
        return $this->allTables;
    }

    // 获取 有 objtype 和 objid 的表
    public function getObjtypeObjidTables () {
        $tableNames = $this->allTables;

        $arr = array();

        foreach ($tableNames as $tableName) {
            $sql = "show full fields from `$tableName`";
            $rows = Dao::queryRows($sql, []);

            $i = 0;
            foreach ($rows as $a) {
                if (in_array($a['field'], array(
                    'objtype',
                    'objid'))) {
                    $i ++;
                }
            }

            if ($i == 2) {
                $arr[] = $tableName;
            }
        }

        return $arr;
    }

    //获取满足条件的表
    public function getTablesByCondArr($arr, $backList = []){
        $tableNames = $this->allTables;
        $cnt = count($arr);

        $result = array();
        foreach ($tableNames as $tableName) {
            if( !empty($backList) && in_array($tableName, $backList)){
                continue;
            }
            $sql = "show full fields from `$tableName`";
            $rows = Dao::queryRows($sql, []);

            $i = 0;
            foreach ($rows as $a) {
                if (in_array($a['field'], $arr)) {
                    $i ++;
                }
            }

            if ($i == $cnt) {
                $result[] = $tableName;
            }
        }
        return $result;
    }

    // 字段名->表名 tableb
    public function field2tableName ($field) {

        $arr = array();
        $arr['rightoptionid'] = 'xoptions';
        $arr['scheduletplid'] = 'scheduletpls';

        if ($arr[$field]) {
            return $arr[$field];
        }

        $allTables = $this->getAllTables();

        $arr = array();
        foreach ($allTables as $a) {
            $len = strlen($a);
            $str = substr($a, 0, $len - 1);
            $pos = strpos($field, $str);

            // echo "\n$field $str [$pos]";

            if ($pos === false) {
                continue;
            }

            $arr[$a] = $len;
        }

        asort($arr);

        $keys = array_keys($arr);

        return array_pop($keys);
    }

    // queryCntSqls
    public function queryCntSqls (array $sqls) {
        foreach ($sqls as $sql) {
            $this->queryCntSql($sql);
        }
    }

    // queryCntSql
    public function queryCntSql ($sql) {
        echo "\n\n=============\n\n";
        echo $sql;
        $cnt = Dao::queryValue($sql, []);
        echo "\n\n----------\n";
        echo "cnt={$cnt}";

        return $cnt;
    }

    // 执行查询sqls
    public function querySqls (array $sqls) {
        foreach ($sqls as $sql) {
            $this->querySql($sql);
        }
    }

    // 执行查询sql
    public function querySql ($sql) {
        $db = BeanFinder::get('DbExecuter');

        echo "\n\n=============\n\n";
        echo $sql;
        $rows = $db->query($sql);
        echo "\n\n----------\n";
        print_r($rows);
        // $cnt = "跳过";
        echo "\n\n----------\n";
    }

    // 执行删除sqls
    public function exeSqls (array $sqls) {
        foreach ($sqls as $sql) {
            $this->exeSql($sql);
        }
    }

    // 执行删除sql
    public function exeSql ($sql, $isExe = true, $database = '') {
        $db = BeanFinder::get('DbExecuter', $database);

        echo "\n\n=============\n\n";
        echo $sql;
        if ($isExe) {
            $cnt = $db->executeNoQuery($sql);
        } else {
            $cnt = "跳过";
        }
        echo "\n\n----------\n";
        echo "cnt={$cnt}";
    }
}
