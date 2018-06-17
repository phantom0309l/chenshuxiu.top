<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Init_doctorconfig_letter
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $i = 0;

        $doctorconfigtpl = DoctorConfigTplDao::getByCode("letter_send");
        if(false == $doctorconfigtpl instanceof DoctorConfigTpl){
            echo "=========没有找到感谢信的配置模版！\n";
            exit();
        }

        $sql = " select doctorid from doctordiseaserefs where diseaseid=1; ";
        //ADHD的医生id
        $ids = Dao::queryValues($sql);

        foreach ($ids as $id) {
            $doctor = Doctor::getById($id);
            $i++;
            echo "=========[doctorid]{$id}============[num]{$i}\n";
            // 提交工作单元
            if(0 == $i % 3000){
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $doctorconfig = DoctorConfigDao::getByDoctoridDoctorConfigTplid($doctor->id, $doctorconfigtpl->id);
            if (false == $doctorconfig instanceof DoctorConfig) {
                $row = array();
                $row['doctorid'] = $doctor->id;
                $row['doctorconfigtplid'] = $doctorconfigtpl->id;
                $row['status'] = 1;

                $doctorconfig = DoctorConfig::createByBiz($row);
            }
        }

        $unitofwork->commitAndInit();
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_doctorconfig_letter.php]=====");

$process = new Init_doctorconfig_letter();
$process->dowork();

Debug::trace("=====[cron][end][Init_doctorconfig_letter.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
