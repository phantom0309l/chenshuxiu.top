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

// Debug::$debug = 'Dev';

class Drugsheet_init
{

    public function dowork () {
        $now = date("Y-m-d H:i:s", time());
        $sql = "select id from patients where diseaseid=1";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $patient = Patient::getById($id);
            if( $patient instanceof Patient ){
                echo "\n====patientid[{$id}]===\n";
                $drugitems = $this->getDrugitems($patient);
                foreach ($drugitems as $a) {
                    echo "\n====drugitem[{$a->id}]===\n";
                    $this->scanDrugitem($a);
                }
            }
        }
    }

    private function getDrugitems($patient){
        return DrugItemDao::getListByPatientid($patient->id, " and drugsheetid = 0");
    }

    private function scanDrugitem($drugitem){
        $unitofwork = BeanFinder::get("UnitOfWork");
        $thedate = substr($drugitem->createtime, 0, 10);
        $patient = $drugitem->patient;
        $patientid = $patient->patientid;
        $drugsheet = DrugSheetDao::getOneByPatientidThedate($patientid, $thedate);
        if( false == $drugsheet instanceof DrugSheet ){
            $row = array();
            $row["wxuserid"] = $drugitem->wxuserid;
            $row["userid"] = $drugitem->userid;
            $row["patientid"] = $patientid;
            $row["doctorid"] = $patient->doctorid;
            $row["diseaseid"] = $patient->diseaseid;
            $row["thedate"] = $thedate;
            $drugsheet = DrugSheet::createByBiz($row);
        }
        $drugitem->drugsheetid = $drugsheet->id;
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Drugsheet_init.php]=====");

$process = new Drugsheet_init();
$process->dowork();

Debug::trace("=====[cron][end][Drugsheet_init.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
