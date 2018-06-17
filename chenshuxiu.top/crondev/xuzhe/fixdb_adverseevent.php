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

class Fixdb_AdverseEvent
{

    public function dowork () {

        echo "\n [Fixdb_AdverseEvent] begin ";
        $unitofwork = BeanFinder::get("UnitOfWork");
        $pmts = Dao::getEntityListByCond('PatientRemarkTpl'," AND name like '不良事件%' ");

        foreach ($pmts as $pmt) {
            echo "\nPatientRemarkTplid {$pmt->id}";
            $pmt->typestr = 'adverseevent';
            $commonwords = CommonWordDao::getListByOwnertypeOwneridTypestr("PatientRemarkTpl", $pmt->id, "symptom");
            foreach( $commonwords as $commonword ){
                $commonword->typestr = 'adverseevent';
            }

        }
        $unitofwork->commitAndInit();

        $unitofwork = BeanFinder::get("UnitOfWork");

        $pms = Dao::getEntityListByCond('PatientRemark'," AND name like '不良事件%' ");

        foreach ($pms as $pm) {
            echo "\nPatientRemarkid {$pm->id}";
            $pm->typestr = 'adverseevent';
        }
        $unitofwork->commitAndInit();
        echo "\n [Fixdb_AdverseEvent] finished \n";

    }
}

$process = new Fixdb_AdverseEvent();
$process->dowork();
