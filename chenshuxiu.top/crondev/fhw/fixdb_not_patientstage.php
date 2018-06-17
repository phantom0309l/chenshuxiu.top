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

class Fixdb_not_patientstage
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from patients where patientstageid = 0 and diseaseid in (8,15,19,21) ";
        $ids = Dao::queryValues($sql);

        $k = 0;
        $cnt = count($ids);
        foreach ($ids as $id) {
            $patient = Patient::getById($id);

            $patient->patientstageid = 1;

            $k++;
            if ($k % 100 == 0) {
                echo $k . "/" . $cnt . "\n";
                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
        }
        echo "{$cnt}/{$cnt}\n";

        $unitofwork->commitAndInit();
    }
}

$test = new Fixdb_not_patientstage();
$test->dowork();
