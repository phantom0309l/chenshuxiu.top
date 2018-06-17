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

class Fixdb_checkup_fei
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select a.*
                from checkups a
                LEFT JOIN patients b on b.id = a.patientid
                LEFT JOIN checkuptpls c on c.id = a.checkuptplid
                where a.doctorid = 32
                and b.name not like '%测试%'
                and c.id = 103284836
                and b.is_closed_bydoctor = 0 and b.status = 1
                order by a.check_date DESC ";
        $checkups = Dao::loadEntityList('Checkup',$sql);
        $feis = array();

        echo "姓名,检查日期,随访月份,FVC1,FVC,TLC1,TLC,DLco1,DLco,FEV11,FEV1"."\n";

        $i = 0;
        foreach($checkups as $checkup){
            $xquestionid_FVC1 = 103285284;
            $xquestionid_FVC = 103303732;
            $xquestionid_TLC1 = 103303740;
            $xquestionid_TLC = 103303748;
            $xquestionid_DLco1 = 103303776;
            $xquestionid_DLco = 103303780;
            $xquestionid_FEV11 = 103303790;
            $xquestionid_FEV1 = 103303794;

            $feis[$i]['name'] = $checkup->patient->name;
            $feis[$i]['thedate'] = $checkup->check_date;
            $feis[$i]['随访月份'] = '';
            $feis[$i]['FVC1'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid,$xquestionid_FVC1);
            $feis[$i]['FVC'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid,$xquestionid_FVC);
            $feis[$i]['TLC1'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid,$xquestionid_TLC1);
            $feis[$i]['TLC'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid,$xquestionid_TLC);
            $feis[$i]['DLco1'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid,$xquestionid_DLco1);
            $feis[$i]['DLco'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid,$xquestionid_DLco);
            $feis[$i]['FEV11'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid,$xquestionid_FEV11);
            $feis[$i]['FEV1'] = XAnswer::getAnswerByXanswersheetidXquestionid($checkup->xanswersheetid,$xquestionid_FEV1);

            echo "{$feis[$i]['name']},{$feis[$i]['thedate']},{$feis[$i]['随访月份']},{$feis[$i]['FVC1']},{$feis[$i]['FVC']},{$feis[$i]['TLC1']},{$feis[$i]['TLC']},{$feis[$i]['DLco1']},{$feis[$i]['DLco']},{$feis[$i]['FEV11']},{$feis[$i]['FEV1']}"."\n";

            $i++;
        }

        $unitofwork->commitAndInit();
    }
}

$fixdb_checkup_fei = new Fixdb_checkup_fei();
$fixdb_checkup_fei->dowork();