<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';
class Dbfix_patient_lastpipeid
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = Dao::queryValues(" select id from patients ");

        $cnt = count($ids);

        foreach ($ids as $i => $id) {
            $patient = Patient::getById($id);

            if ($i % 100 == 0) {
                echo "\n {$i} / {$cnt} : ";
            } else {
                echo ".";
            }

            $lastpipe = $patient->getLastPipeByUser();
            if ($lastpipe instanceof Pipe && $patient->lastpipeid < $lastpipe->id) {
                echo "\n Patient[{$id}]->lastpipeid {$patient->lastpipeid} => {$lastpipe->id} ";
                $patient->lastpipeid = $lastpipe->id;
                echo " ";
                echo $patient->lastpipe_createtime = $lastpipe->createtime;
                echo "\n";
            }

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }
        $unitofwork->commitAndInit();
    }
}

$process = new Dbfix_patient_lastpipeid();
$process->dowork();