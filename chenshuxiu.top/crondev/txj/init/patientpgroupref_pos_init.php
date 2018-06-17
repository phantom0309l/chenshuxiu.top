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

class Patientpgroupref_pos_init
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select patientid from patientpgrouprefs where patientid>0 group by patientid";
        $ids = Dao::queryValues( $sql );
        foreach ($ids as $id) {
            echo "\n---patientid[{$id}]----\n";
            $patientpgrouprefs = PatientPgroupRefDao::getListByPatientid( $id, " order by id asc" );
            foreach ($patientpgrouprefs as $i => $a) {
                $pos = $i + 1;
                $a->pos = $pos;
            }
            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Patientpgroupref_pos_init.php]=====");

$process = new Patientpgroupref_pos_init();
$process->dowork();

Debug::trace("=====[cron][end][Patientpgroupref_pos_init.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
