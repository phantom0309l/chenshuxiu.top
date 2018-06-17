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

class Fhw
{
    public function work () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $date = '2018-06-11';
        echo $date . " ";
        if (FUtil::isHoliday($date)) {
            echo "节假日";
        } else {
            echo "非节假日";
        }
        echo "\n";

        $unitofwork->commitAndInit();
    }


}

$test = new Fhw();
$test->work();
