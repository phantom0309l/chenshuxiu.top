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
                from checkups a
                LEFT JOIN patients b on b.id = a.patientid
                LEFT JOIN checkuptpls c on c.id = a.checkuptplid
                where a.doctorid = 32
                and b.name not like '%测试%'
                and c.id = 103304078
                and b.status = 1
                group by a.patientid,a.check_date";

        $checkups = Dao::loadEntityList('Checkup', $sql);

        echo "姓名,检查日期,KL6"."\n";

        $kl6s = array();

        $i = 0;
        foreach ($checkups as $checkup) {
            $xquestionid_KL6 = 103304098;

            $kl6s[$i]['name'] = $checkup->patient->name;
            $kl6s[$i]['thedate'] = $checkup->check_date;
            $kl6s[$i]['KL-6'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid, $xquestionid_KL6);

            echo "{$kl6s[$i]['name']},{$kl6s[$i]['thedate']},{$kl6s[$i]['KL-6']}"."\n";

            $i++;
        }

        $unitofwork->commitAndInit();
    }
}

$fixdb_checkup_kl6 = new Fixdb_checkup_kl6();
$fixdb_checkup_kl6->dowork();
