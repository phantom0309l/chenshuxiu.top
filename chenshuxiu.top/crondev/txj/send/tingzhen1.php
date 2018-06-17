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

class Tingzhen
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = " select id from patients where doctorid = 328 AND status = 1 and subscribe_cnt>0 ";

        //$sql = "SELECT id FROM patients WHERE id IN (106415651)";

        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $patient = Patient::getById($id);
            echo "\n\n---------================================================----- " . $id;

            $wxusers = WxUserDao::getListByPatient($patient);

            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                    echo "\n\n--------- " . $wxuser->id;

                    $text = "各位家长，任艳玲医生将于8月31日下午停诊半天，请安排好就诊时间，特此通知。";

                    $openid = $wxuser->openid;

                    $first = array(
                        "value" => $text,
                        "color" => "#ff6600");

                    $keywords = array(
                        array(
                            "value" => '常州市第一人民医院',
                            "color" => "#bbb"),
                        array(
                            "value" => '心理咨询科',
                            "color" => "#bbb"),
                        array(
                            "value" => '任艳玲',
                            "color" => "#bbb"),
                        array(
                            "value" => '8月31日下午',
                            "color" => "#bbb")
                    );
                    $content = WxTemplateService::createTemplateContent($first, $keywords);

                    PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "scheduleNotice", $content);
                }
            }

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Tingzhen.php]=====");

$process = new Tingzhen();
$process->dowork();

Debug::trace("=====[cron][end][Tingzhen.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
