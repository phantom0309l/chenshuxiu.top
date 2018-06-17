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

class Fixdb_Commonword
{

    public function dowork () {

        echo "\n [Fixdb_Commonword] begin ";
        $unitofwork = BeanFinder::get("UnitOfWork");
        $pmts_32 = Dao::getEntityListByCond('PatientRemarkTpl'," and doctorid=32 and typestr='symptom' ");
        $pmts_548 = Dao::getEntityListByCond('PatientRemarkTpl'," and doctorid=548 and typestr='symptom' ");

        $pmtarr_548 = array();
        foreach( $pmts_548 as $a ){
            $pmtarr_548[$a->name] = $a->id;
        }

        foreach ($pmts_32 as $pmt) {
            echo "\nPatientRemarkTplid {$pmt->id}";
            $commonwords = CommonWordDao::getListByOwnertypeOwneridTypestr("PatientRemarkTpl", $pmt->id, "symptom");
            foreach( $commonwords as $commonword ){
                if($pmtarr_548[$pmt->name]){
                    $row = array();

                    $row['ownertype'] = 'PatientRemarkTpl';
                    $row['ownerid'] = $pmtarr_548[$pmt->name];
                    $row['typestr'] = $commonword->typestr;
                    $row['groupstr'] = $commonword->groupstr;
                    $row['content'] = $commonword->content;
                    $row['weight'] = $commonword->weight;

                    $commonword = CommonWord::createByBiz($row);
                }
            }

        }
        $unitofwork->commitAndInit();

        echo "\n [Fixdb_Commonword] finished \n";

    }
}

$process = new Fixdb_Commonword();
$process->dowork();
