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

class Fixdb_CeshiName
{

    public function dowork () {

        echo "\n [Fixdb_CeshiName] begin ";
        $unitofwork = BeanFinder::get("UnitOfWork");
        $auditors = Dao::getEntityListByCond('Auditor'," order by id asc ");

        foreach ($auditors as $auditor) {
            $auditorname = $auditor->name;
            echo "\n {$auditor->id} {$auditorname} ";
            $patients = Dao::getEntityListByCond('Patient'," and createuserid={$auditor->id} order by id asc ");
            $count = count($patients);
            echo "[{$count}]";
            foreach ($patients as $k => $patient) {
                $tempname = $auditorname."测试".$k;
                echo "\n       {$patient->name} => $tempname";
                $patient->name = $tempname;
            }

        }
        $unitofwork->commitAndInit();

        echo "\n [Fixdb_CeshiName] finished \n";

    }
}

$process = new Fixdb_CeshiName();
$process->dowork();
