<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "3048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// 将一批王迁的患者迁移到保险测试组
class dbfix_patients_mv_wxgroup_5113
{

    public function dowork () {
        $names = file(dirname(__FILE__) . '/dbfix_patients_mv_wxgroup_5113.txt');

        $unitofwork = BeanFinder::get("UnitOfWork");

        foreach ($names as $name) {
            $name = trim($name);

            $sql = "select a.*
from patients a
inner join pcards b on b.patientid = a.id
where a.name=:name and b.doctorid=477 ";

            $bind = [];
            $bind[':name'] = $name;

            $patients = Dao::loadEntityList('Patient', $sql, $bind);
            $cnt = count($patients);
            echo "\n{$name} => $cnt";

            foreach ($patients as $patient) {
                $wxusers = $patient->getWxUsers();
                echo "\n    patient[{$patient->id}] ==> ".count($wxusers);

                foreach ($wxusers as $w) {
                    echo "\n        wxuser[{$w->id}][{$w->wxshopid}] => xinjianbao";
                    $w->joinWxGroup('xinjianbao');
                }
            }
        }

        $unitofwork->commitAndInit();
    }
}

echo " \n===begin===\n ";
$process = new dbfix_patients_mv_wxgroup_5113();
$process->dowork();
echo " \n===end===\n ";
