<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-7-14
 * Time: 上午11:44
 */
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

class Doctor_view_ofryl
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from patients where doctorid = 328 ";

//        $sql = "SELECT id FROM patients WHERE id IN (106704029)";
//        $sql = "SELECT id FROM patients WHERE id IN (107071575, 107069517, 107487001)";

        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $patient = Patient::getById($id);
            echo "\n\n---------================================================----- " . $id;

            $wxusers = WxUserDao::getListByPatient($patient);

            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                    echo "\n\n--------- " . $wxuser->id;

                    $text = "各位家长，任艳玲医生将于9月21日下午停诊半天，请安排好就诊时间，特此通知。";

                    $openid = $wxuser->openid;

                    $first = array(
                        "value" => $text,
                        "color" => "#ff6600");

                    $keywords = array(
                        array(
                            "value" => '常州市第一人民医院',
                            "color" => "#bbb"),
                        array(
                            "value" => '儿童心理咨询',
                            "color" => "#bbb"),
                        array(
                            "value" => '任艳玲',
                            "color" => "#bbb"),
                        array(
                            "value" => '9月21日',
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
Debug::trace("=====[cron][beg][Doctor_view_ofryl.php]=====");

$process = new Doctor_view_ofryl();
$process->dowork();

Debug::trace("=====[cron][end][Doctor_view_ofryl.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
