<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Fixdb_checkup_6min
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
                and c.id = 103304112";

        $checkups = Dao::loadEntityList('Checkup', $sql);

        echo "姓名,检查日期,SpO2%(前),HR(前),SpO2%(后),HR%(后)"."\n";

        $arr = array();

        $i = 0;
        foreach ($checkups as $checkup) {
            $xquestionid_spo2_B = 103304208;
            $xquestionid_hr_B = 104364435;
            $xquestionid_spo2_A = 103304216;
            $xquestionid_hr_A = 104364439;

            $arr[$i]['name'] = $checkup->patient->name;
            $arr[$i]['thedate'] = $checkup->check_date;
            $arr[$i]['SpO2%(前)'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid, $xquestionid_spo2_B);
            $arr[$i]['HR(前)'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid, $xquestionid_hr_B);
            $arr[$i]['SpO2%(后)'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid, $xquestionid_spo2_A);
            $arr[$i]['HR%(后)'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid, $xquestionid_hr_A);

            $i++;
        }

        $unitofwork->commitAndInit();
    }
}

$fixdb_checkup_6min = new Fixdb_checkup_6min();
$fixdb_checkup_6min->dowork();
