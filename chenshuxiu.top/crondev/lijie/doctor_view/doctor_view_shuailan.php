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

class Doctor_view_shuailan
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

//        $sql = " select id from patients where doctorid = 5 AND status = 1; ";

        $sql = "SELECT id FROM patients WHERE id IN (104749239, 104849403, 104969589)";

        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $patient = Patient::getById($id);
            echo "\n\n---------================================================----- " . $id;

            $wxusers = WxUserDao::getListByPatient($patient);

            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                    echo "\n\n--------- " . $wxuser->id;

                    $text = "各位家长，帅澜医生将于8月1日至8月11日停诊，如需取药或复查，请提前或延后一周，特此通知。";

                    $openid = $wxuser->openid;

                    $first = array(
                        "value" => $text,
                        "color" => "#ff6600");

                    $keywords = array(
                        array(
                            "value" => '上海新华医院',
                            "color" => "#bbb"),
                        array(
                            "value" => '临床心理科',
                            "color" => "#bbb"),
                        array(
                            "value" => '帅澜',
                            "color" => "#bbb"),
                        array(
                            "value" => '8月1日至8月11日',
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
Debug::trace("=====[cron][beg][Doctor_view_ofzhang.php]=====");

$process = new Doctor_view_shuailan();
$process->dowork();

Debug::trace("=====[cron][end][Doctor_view_ofzhang.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
