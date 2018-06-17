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

class Fixdb_checkup_tizheng
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $patients = Dao::getEntityListByCond("Patient"," and doctorid = 32 and is_closed_bydoctor = 0 and status = 1 and name not like '%测试%'");
        echo "姓名,体征"."\n";

        $contents = array();

        $i = 0;
        foreach($patients as $a){
            $revisitrecords = Dao::getEntityListByCond("RevisitRecord"," and patientid = {$a->id} and doctorid = 32 ");
            $contentstr = '';
            foreach($revisitrecords as $revisitrecord){
                $contentstr .= $revisitrecord->content;
            }

            $contents[$i]['name'] = $a->name;
            $contents[$i]['content'] = $contentstr;

            echo "{$contents[$i]['name']},{$contents[$i]['content']}"."\n";

            $i++;
        }

        $unitofwork->commitAndInit();
    }
}

$fixdb_checkup_tizheng = new Fixdb_checkup_tizheng();
$fixdb_checkup_tizheng->dowork();
