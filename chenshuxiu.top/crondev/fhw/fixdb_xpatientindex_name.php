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

// fhw address
class Fixdb_xpatientindex_name
{
    public function doWork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id,name from patients where name <> '' ";
        $id_names = Dao::queryRows($sql);
        $cnt = count($id_names);

        $i = 0;
        $k = 0;
        foreach ($id_names as $a) {
            $patientid = $a['id'];
            $patient = Patient::getById($patientid);
            $name = $a['name'];

            XPatientIndex::updateXPatientIndexName($name, $patient);

            $i++;
            if ($i % 100 == 0) {
                $k += 100;
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

$test = new Fixdb_xpatientindex_name();
$test->doWork();
