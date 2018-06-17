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

class Data_XueChangGui
{

    public function dowork () {

        echo "\n [Data_XueChangGui] begin \n";

        $sql = "select distinct a.*
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = 360  AND a.status=1 and a.subscribe_cnt>0
            order by a.id";
        $patients = Dao::loadEntityList("Patient", $sql);

        $xquestionid_1 = 106914827;
        $xquestionid_2 = 106914828;
        $xquestionid_3 = 106914829;
        $xquestionid_4 = 106914830;
        $checkuptpl = CheckupTpl::getById(106914825);

        echo "|姓名|检查日期|白细胞|中性粒细胞|血红蛋白|血小板|\n";
        foreach( $patients as $patient ){
            $checkups = CheckupDao::getListByPatientCheckupTpl($patient, $checkuptpl);

            foreach( $checkups as $k=>$checkup ){
                echo "|{$patient->name}|{$checkup->check_date}|";
                $xanswersheetid = $checkup->xanswersheetid;
                $xanswer_1 = Dao::getEntityByCond("XAnswer", " and xanswersheetid={$xanswersheetid} and xquestionid={$xquestionid_1}");
                $xanswer_2 = Dao::getEntityByCond("XAnswer", " and xanswersheetid={$xanswersheetid} and xquestionid={$xquestionid_2}");
                $xanswer_3 = Dao::getEntityByCond("XAnswer", " and xanswersheetid={$xanswersheetid} and xquestionid={$xquestionid_3}");
                $xanswer_4 = Dao::getEntityByCond("XAnswer", " and xanswersheetid={$xanswersheetid} and xquestionid={$xquestionid_4}");

                echo"{$xanswer_1->content}|{$xanswer_2->content}|{$xanswer_3->content}|{$xanswer_4->content}|\n";
            }

        }

        echo "\n [Data_XueChangGui] finished \n";

    }
}

$process = new Data_XueChangGui();
$process->dowork();
