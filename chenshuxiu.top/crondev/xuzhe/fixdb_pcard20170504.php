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

class Fixdb_Pcard
{

    public function dowork () {

        echo "\n [Fixdb_Pcard] begin ";
        $bedtkts = Dao::getEntityListByCond('BedTkt'," and doctorid=1002 ");

        foreach ($bedtkts as $bedtkt) {
            $unitofwork = BeanFinder::get("UnitOfWork");
            $patient = $bedtkt->patient;
            $pcard_803 = PcardDao::getByPatientidDoctorid($patient->id, 803);
            if( false == $pcard_803 instanceof Pcard ){
                continue;
            }
            echo "\n{$patient->name}   {$pcard_803->diseaseid}";

            $tale = "[重复][fin]";

            $pcard = PcardDao::getByPatientidDoctorid($patient->id, 1002);
            if ( false == $pcard instanceof Pcard ) {
                $row = array();
                $row["patientid"] = $patient->id;
                $row["doctorid"] = 1002;
                $row["diseaseid"] = $pcard_803->diseaseid;
                $row["patient_name"] = $patient->name;
                $pcard = Pcard::createByBiz($row);
                $tale = "[新建][fin]";
            }
            echo "{$tale}";
            $unitofwork->commitAndInit();
        }

        echo "\n [Fixdb_Pcard] finished \n";

    }
}

$process = new Fixdb_Pcard();
$process->dowork();
