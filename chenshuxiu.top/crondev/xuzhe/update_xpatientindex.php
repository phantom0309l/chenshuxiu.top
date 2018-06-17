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

class Update_XPatientIndex
{

    public function dowork () {

        echo "\n [Update_XPatientIndex] begin ";

        $sql = "select id from patients";
        $patientids = Dao::queryValues($sql);

        foreach ($patientids as $k => $patientid) {
            $unitofwork = BeanFinder::get("UnitOfWork");

            $patient = Patient::getById($patientid);
            echo "\n [Update_XPatientIndex] {$k} {$patientid} ";

//             $patient->updateXPatientIndex();

            $unitofwork->commitAndInit();
        }

        echo "\n [Update_XPatientIndex] finished \n";

    }

}

$process = new Update_XPatientIndex();
$process->dowork();

