\<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

$dbExecuter = BeanFinder::get("DbExecuter");

// $sql = "show tables";
// $tableNames = $dbExecuter->queryValues ( $sql );

// $tableNames = array ('xquestionsheets', 'xquestions', 'xoptions',
// 'xanswersheets', 'xanswers', 'xansweroptionrefs' );

$entityNames = [
    'Rpt_Week_Cancer_Doctor',
    'Rpt_Day_Cancer_Doctor'
];

$allTables = array();
foreach ($entityNames as $a) {
    $allTables[$a] = table2array(strtolower($a . 's'));
}

// 生成全部实体类文件
foreach ($allTables as $entityName => $fields) {
    echo $entityName;
    echo "\n";

    print_r($fields);
    array2entity($entityName, $fields);
    createDao($entityName, $fields);
    createMgrAction($entityName, $fields);
}

// 导出表定义
function table2array ($tableName) {
    $dbExecuter = BeanFinder::get("DbExecuter", 'fcqxdb');

    // 提取字段
    $sql = "show full fields from `$tableName`";
    $rows = $dbExecuter->query($sql);
//    $rows = Dao::queryRows($sql, [], 'statdb');

    return $rows;
}

// 将数组生成entity
function array2entity ($entityName, $fields) {
    // if(!in_array($tableName,array('groupon_edm_item','groupon_edm_rpt')))
    // {
    // return;
    // }
    $str = '<?php

/*
 * _EntityName_
 */
class _EntityName_ extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            _KEYS_
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            _KEY_LOCK_);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        _BELONGTO_
    }

    // $row = array(); _ROW_
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "_EntityName_::createByBiz row cannot empty");

        $default = array();_DEFAULT_

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

}
';

    // 替换 _EntityName_
    // $pos = 2;
    // $char = substr($entityName, $pos,1);
    // $char = ucfirst($char);
    // $entityNameFix = substr($entityName, 0,$pos).$char.substr($entityName,
    // $pos+1);
    // $entityName = $entityNameFix;

    $str = str_replace("_EntityName_", $entityName, $str);

    // 替换 _KEYS_
    $str_keys = "";
    // 替换 _KEY_LOCK_
    $str_keys_lock = '';
    // 替换 _BELONGTO_
    $str_belongto = '';
    // 替换 _ROW_
    $str_row = "";
    // 替换 _DEFAULT_
    $default_str = "";

    $i = 0;
    foreach ($fields as $a) {

        $field = $a['field'];
        $type = $a['type'];
        $comment = $a['comment'];

        if ($field == "id" || $field == "version" || $field == "createtime" || $field == "updatetime") {
            continue;
        }

        if ($i > 0) {
            $str_keys .= "\n        ,";
        }

        $str_keys .= "'$field'    //$comment";

        $isBigint = 0;
        $isInt = 0;

        if (strpos($type, 'int') === 0 || strpos($type, 'int') > 0) {
            $isInt = 1;
        }

        if (strpos($type, 'bigint') === 0) {
            $isBigint = 1;
        }

        echo "\n $type $isBigint $isInt  ";

        if ($isBigint) {
            $str_keys_lock .= "'$field' ,";

            echo $fieldEntityName = substr($field, 0, strlen($field) - 2);
            $str_belongto .= "\n    ";
            $str_belongto .= '$this->_belongtos["' . $fieldEntityName . '"] = array ("type" => "' . ucfirst($fieldEntityName) . '", "key" => "' . $field . '" );';
        }

        if ($isInt) {
            $default_str .= "\n        ";
            $default_str .= '$default["' . $field . '"] = ' . " 0;";
        } else {
            $default_str .= "\n        ";
            $default_str .= '$default["' . $field . '"] = ' . "'';";
        }

        $str_row .= "\n";
        $str_row .= ('    // $row["' . $field . '"] = $' . $field . ';');

        $i ++;
    }

    echo "\n";
    echo $str_belongto;
    echo "\n";
    echo $str_keys_lock;
    echo "\n";

    $str = str_replace("_KEYS_", $str_keys, $str);
    $str = str_replace("_KEY_LOCK_", $str_keys_lock, $str);
    $str = str_replace("_BELONGTO_", $str_belongto, $str);
    $str = str_replace("_ROW_", $str_row, $str);
    $str = str_replace("_DEFAULT_", $default_str, $str);

    echo "\n";
    echo $filename = ROOT_TOP_PATH . "/cron/db2entity/entity.new/$entityName.class.php";
    echo "\n";

    file_put_contents($filename, $str);
    return $str;
}

function createDao ($entityName) {
    $str = '<?php
/*
 * _EntityName_Dao
 */
class _EntityName_Dao extends Dao
{

}';

    $str = str_replace("_EntityName_", $entityName, $str);

    echo "\n";
    echo $filename = ROOT_TOP_PATH . "/cron/db2entity/dao.new/{$entityName}Dao.class.php";
    echo "\n";

    file_put_contents($filename, $str);
    return $str;
}

function createMgrAction ($entityName) {
    $str = '<?php
// _EntityName_MgrAction
class _EntityName_MgrAction extends AuditBaseAction
{

    public function doList () {
        return self::SUCCESS;
    }

    public function doOne () {
        return self::SUCCESS;
    }

    public function doModify () {
        return self::SUCCESS;
    }

    public function doModifyPost () {
        return self::SUCCESS;
    }
}
';
    $str = str_replace("_EntityName_", $entityName, $str);

    echo "\n";
    echo $filename = ROOT_TOP_PATH . "/cron/db2entity/action.new/{$entityName}MgrAction.php";
    echo "\n";

    file_put_contents($filename, $str);
    return $str;
}
