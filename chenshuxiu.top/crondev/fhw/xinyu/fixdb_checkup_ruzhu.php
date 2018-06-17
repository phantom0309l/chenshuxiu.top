<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Fixdb_checkup_kl6
{

    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select a.*
            from patientpgrouprefs a
            inner join patients b on b.id = a.patientid
            inner join pgroups c on c.id = a.pgroupid
            where c.id in (105623521,105621207,103998911,103349551)
            order by a.patientid";

        $patientpgrouprefs = Dao::loadEntityList("PatientPgroupRef", $sql);

        $i = 0;
        $arr = array();
        foreach ($patientpgrouprefs as $a) {
            $arr[$i]['姓名'] = $a->patient->name;
            $arr[$i]['入组时间'] = $a->createtime;
            $arr[$i]['入组'] = $a->pgroup->name;

            $i++;
        }

        $unitofwork->commitAndInit();
    }
}

$fixdb_checkup_kl6 = new Fixdb_checkup_kl6();
$fixdb_checkup_kl6->dowork();
