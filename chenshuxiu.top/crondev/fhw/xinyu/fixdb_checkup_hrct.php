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

class Fixdb_checkup_hrct
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
                and b.status = 1
                and c.id = 103303838";

        $checkups = Dao::loadEntityList('Checkup', $sql);
        $hrcts = array();

        echo "姓名,检查日期,随访月份,GGO,网格影,蜂窝,合计,其他,影像诊断"."\n";

        $i = 0;
        foreach ($checkups as $checkup) {
            $xquestionid_G = 103303998;
            $xquestionid_R = 103304006;
            $xquestionid_H = 103304016;
            $xquestionid_HRCT = 103303890;

            $hrcts[$i]['name'] = $checkup->patient->name;
            $hrcts[$i]['thedate'] = $checkup->check_date;
            $hrcts[$i]['随访月份'] = '';
            $hrcts[$i]['G'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid, $xquestionid_G);
            $hrcts[$i]['R'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid, $xquestionid_R);
            $hrcts[$i]['H'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid, $xquestionid_H);
            $hrcts[$i]['sum'] = $hrcts[$i]['G'] + $hrcts[$i]['R'] + $hrcts[$i]['H'];
            $hrcts[$i]['other'] = '';
            $hrcts[$i]['HRCT'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid, $xquestionid_HRCT);

            echo "{$hrcts[$i]['name']},{$hrcts[$i]['thedate']},{$hrcts[$i]['随访月份']},{$hrcts[$i]['G']},{$hrcts[$i]['R']},{$hrcts[$i]['H']},{$hrcts[$i]['sum']},{$hrcts[$i]['other']},{$hrcts[$i]['HRCT']}"."\n";

            $i++;
        }

        $unitofwork->commitAndInit();
    }
}

$fixdb_checkup_hrct = new Fixdb_checkup_hrct();
$fixdb_checkup_hrct->dowork();
