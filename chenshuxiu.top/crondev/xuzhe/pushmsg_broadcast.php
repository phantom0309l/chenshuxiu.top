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

class PushMsg_Broadcast
{

    public function dowork () {

        echo "\n [PushMsg_Broadcast] begin ";

        $sql = "select * from pcards
        where doctorid=477
        and patientid in
        ( select patientid from patienttags where patienttagtplid=215832726)
        and patientid not in
        ( select patientid from patienttags where patienttagtplid=240081076)
        group by patientid";
        $pcards = Dao::loadEntityList('Pcard',$sql);

//        $patient1 = Patient::getById(107076211);
//        $patients = array($patient1);
        $content1 = "王颖轶医生的患者，您好，王大夫因为6月2日到6月7日要去参加国际会议，停诊。6月1日下午是近期最后一个国际部门诊。下一次国际部门诊是6月9日（周五）下午。2日到7日期间，肿瘤内科医生均开会所以很少，请您根据自己的病情酌情安排自己的就诊时间。6月12日后门诊恢复正常";
        $unitofwork = BeanFinder::get("UnitOfWork");
        foreach ($pcards as $pcard) {
            echo "\n patientname {$pcard->patient->name}";

            PushMsgService::sendTxtMsgToWxUsersOfPcardBySystem($pcard, $content1);
        }

        $unitofwork->commitAndInit();
        echo "\n [PushMsg_Broadcast] finished \n";

    }
}

$process = new PushMsg_Broadcast();
$process->dowork();
