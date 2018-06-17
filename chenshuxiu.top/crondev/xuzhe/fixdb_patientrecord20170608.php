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

class Fixdb_PatientRecord
{

    public function dowork () {

        echo "\n [Fixdb_PatientRecord] begin ";
        $unitofwork = BeanFinder::get("UnitOfWork");
        $xanswersheetids = Dao::queryValues(" select id from xanswersheets where xquestionsheetid=108892626 ");

        foreach( $xanswersheetids as $k => $xanswersheetid ){
            $unitofwork->commitAndInit();
            $checkup = Dao::getEntityByCond("Checkup"," and xanswersheetid=$xanswersheetid ");
            if( false == $checkup instanceof Checkup){
                continue;
            }

            $checkuppictures = CheckupPictureDao::getListByCheckupid( $checkup->id );
            if( empty($checkuppictures)){
                continue;
            }

            $patientpicture = PatientPictureDao::getByObj($checkuppictures[0]);

            $xanswersheet = XAnswerSheet::getById($xanswersheetid);
            echo "\nxanswersheetid [$k] {$xanswersheet->id}";

            $xanswer_WBC = $xanswersheet->getAnswer(108892627);
            $xanswer_HGB = $xanswersheet->getAnswer(108892628);
            $xanswer_NEUT = $xanswersheet->getAnswer(108892629);
            $xanswer_PLT = $xanswersheet->getAnswer(108892630);

            $thedate = $checkup->check_date;
            $baixibao = $xanswer_WBC->content;
            $xuehongdanbai = $xanswer_HGB->content;
            $zhongxingli = $xanswer_NEUT->content;
            $xuexiaoban = $xanswer_PLT->content;

            $data = [
                'baixibao'=>$baixibao,
                'xuehongdanbai'=>$xuehongdanbai,
                'zhongxingli'=>$zhongxingli,
                'xuexiaoban'=>$xuexiaoban,
            ];

            $row = [];
            $row["patientid"] =  $checkup->patientid;
            $row["type"] =  'wbc_checkup';
            $row["thedate"] =  $thedate;

            $patientrecord = PatientRecord::createByBiz($row);
            $patientrecord->saveJsonContent($data);
            $patientpicture->content = "{$patientrecord->id}(若为血常规类型图片，前面的数字不能修改，备注内容在后面添加即可)".$patientpicture->content;

            if( $k % 100 == 0){
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }

        echo "\n [Fixdb_PatientRecord] finished \n";

    }
}

$process = new Fixdb_PatientRecord();
$process->dowork();
