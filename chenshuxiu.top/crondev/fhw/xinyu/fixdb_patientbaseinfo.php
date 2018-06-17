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

class Fixdb_patientbaseinfo
{

    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        // $cond = " and doctorid = 32 and name not like '%测试%' and is_closed_bydoctor = 0 and status = 1 ";
        // $patients = Dao::getEntityListByCond('Patient', $cond);
        // $patientbases = array();

        //echo "姓名,性别,年龄,病历号,诊断,职业,1次随访,时间点,2次随访,时间点,3次随访,时间点,身高,体重,发病日期,首发症状,ＩＬＤ诊断日期,ＩＬＤ症状,吸烟,呼吸困难平评分,备注"."\n";

        $patientids = array(105623521,105621207,103998911,103349551);

        $i = 0;
        foreach ($patientids as $id) {
            $patient = Patient::getById($id);

            $patientbases[$i]['姓名'] = $patient->name;
            $patientbases[$i]['性别'] = $patient->getSexStr();
            $patientbases[$i]['年龄'] = $patient->getAgeStr();
            $patientbases[$i]['病历号'] = $patient->out_case_no;
            $patientbases[$i]['诊断'] = $patient->complication;
            $patientbases[$i]['职业'] = $patient->career;

            $patientbases[$i]['1次随访'] = '';
            $patientbases[$i]['时间点'] = '';
            $patientbases[$i]['2次随访'] = '';
            $patientbases[$i]['时间点'] = '';
            $patientbases[$i]['3次随访'] = '';
            $patientbases[$i]['时间点'] = '';

            //患者基本信息量表
            $xquestionsheetid_base = 101887597;
            $xquestionid_height = 101887823;
            $xquestionid_weight = 101887831;
            $patientbases[$i]['身高'] = XAnswer::getAnswer($xquestionsheetid_base, $patient->id, $xquestionid_height);
            $patientbases[$i]['体重'] = XAnswer::getAnswer($xquestionsheetid_base, $patient->id, $xquestionid_weight);

            $xquestionsheetid_fabing = 101832557;
            $xquestionid_fabingdate = 101832571;
            $xquestionid_shoufazz = 101832575;
            $patientbases[$i]['发病日期'] = XAnswer::getAnswer($xquestionsheetid_fabing, $patient->id, $xquestionid_fabingdate);
            $patientbases[$i]['首发症状'] = XAnswer::getAnswer($xquestionsheetid_fabing, $patient->id, $xquestionid_shoufazz);

            $xquestionsheetid_ILD = 101832611;
            $xquestionid_ILDzzTime = 101832617;
            $patientbases[$i]['ＩＬＤ诊断日期'] = XAnswer::getAnswer($xquestionsheetid_ILD, $patient->id, $xquestionid_ILDzzTime);
            $patientbases[$i]['ＩＬＤ症状'] = '';

            $patientbases[$i]['吸烟'] = '';

            $xquestionsheetid_huxikunnan = 101457245;
            $xquestionid_huxihunnan = 101457269;
            $patientbases[$i]['呼吸困难平评分'] = XAnswer::getAnswer($xquestionsheetid_huxikunnan, $patient->id, $xquestionid_huxihunnan);

            $patientbases[$i]['备注'] = '';
            $i++;
        }

        $unitofwork->commitAndInit();
    }
}

$fixdb_patientbaseinfo = new Fixdb_patientbaseinfo();
$fixdb_patientbaseinfo->dowork();
