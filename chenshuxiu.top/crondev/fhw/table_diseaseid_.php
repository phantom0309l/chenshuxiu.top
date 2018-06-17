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

class Test
{
    private function table2entityType ($table) {
        global $lowerclasspath;

        $tabl = substr($table, 0, strlen($table) - 1);
        return $lowerclasspath[$tabl];
    }

    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        TableUtil::init();

        $patientid_doctorid_diseaseid = TableUtil::$patientid_doctorid_diseaseid;
        $patientid_doctorid_notdiseaseid = TableUtil::$patientid_doctorid_notdiseaseid;
        $patientid_notdoctorid_diseaseid = TableUtil::$patientid_notdoctorid_diseaseid;
        $notpatient_doctoriddiseaseid = TableUtil::$notpatient_doctoriddiseaseid;

        echo "------------------------------三者都有------------------------------\n";
        print_r($patientid_doctorid_diseaseid);
        echo "------------------------------三者都有------------------------------\n";

        echo "------------------------------无疾病,有医生------------------------------\n";
        print_r($patientid_doctorid_notdiseaseid);
        echo "------------------------------无疾病,有医生------------------------------\n";

        echo "------------------------------有疾病,无医生------------------------------\n";
        print_r($patientid_notdoctorid_diseaseid);
        echo "------------------------------有疾病,无医生------------------------------\n";

        echo "------------------------------没有患者------------------------------\n";
        print_r($notpatient_doctoriddiseaseid);
        echo "------------------------------没有患者------------------------------\n";

        $unitofwork->commitAndInit();
    }
}

$test = new Test();
$test->dowork();