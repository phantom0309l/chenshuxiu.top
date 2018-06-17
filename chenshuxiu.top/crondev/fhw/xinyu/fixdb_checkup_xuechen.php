<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Fixdb_checkup_xuechen
{

    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");
        //(105623521,105621207,103998911,103349551)

        $sql = "select a.*
                from checkups a
                LEFT JOIN patients b on b.id = a.patientid
                LEFT JOIN checkuptpls c on c.id = a.checkuptplid
                where a.doctorid = 32
                and b.name not like '%测试%'
                and c.id = 103304078
                and b.status = 1
                and b.id in (105623521,105621207,103998911,103349551)
                group by a.patientid,a.check_date";

        $checkups = Dao::loadEntityList('Checkup', $sql);

        $arr = array();

        $i = 0;
        foreach ($checkups as $checkup) {
            $xquestionid_ESR = 104633753;

            $arr[$i]['姓名'] = $checkup->patient->name;
            $arr[$i]['检查日期'] = $checkup->check_date;
            $arr[$i]['ESR'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid, $xquestionid_ESR);

            echo "{$arr[$i]['name']},{$arr[$i]['thedate']},{$arr[$i]['KL-6']}"."\n";

            $i++;
        }

        print_r($arr);

        $unitofwork->commitAndInit();
    }
}

$fixdb_checkup_xuechen = new Fixdb_checkup_xuechen();
$fixdb_checkup_xuechen->dowork();
