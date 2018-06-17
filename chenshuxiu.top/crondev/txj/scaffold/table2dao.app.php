<?php
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

print_r($argv);
$entityName = $argv[1];
$hostdir = ROOT_TOP_PATH . "/domain/dao/";
$filenames = scandir($hostdir);
foreach ($filenames as $name) {
    if ($name == "{$entityName}Dao.class.php") {
        echo "已经生成过该文件";
        echo "\n";
        return;
    }
}

createDaoFile($entityName);

function createDaoFile ($entityName) {
    $str = '<?php
/*
 * _EntityName_Dao
 */
class _EntityName_Dao extends Dao {

}';
    $str = str_replace("_EntityName_", $entityName, $str);
    echo "\n";
    echo $filename = ROOT_TOP_PATH . "/domain/dao/{$entityName}Dao.class.php";
    echo "\n";

    file_put_contents($filename, $str);
    return $str;
}
