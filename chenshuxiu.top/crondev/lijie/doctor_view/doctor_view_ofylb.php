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

class Doctor_view_ofylb
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from patients where doctorid = 431 ";

//        $sql = "SELECT id FROM patients WHERE id IN (106704029)";
//        $sql = "SELECT id FROM patients WHERE id IN (107071575, 107069517, 107072391)";

        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $patient = Patient::getById($id);
            echo "\n\n---------================================================----- " . $id;

            $wxusers = WxUserDao::getListByPatient($patient);

            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                    echo "\n\n--------- " . $wxuser->id;

                    $text = "各位家长，杨立彬医生9月份只剩9月26号全天门诊，其余时间不出诊，请安排好时间复诊、开药。";

                    $openid = $wxuser->openid;

                    $first = array(
                        "value" => $text,
                        "color" => "#ff6600");

                    $keywords = array(
                        array(
                            "value" => '吉林大学白求恩第一医院',
                            "color" => "#bbb"),
                        array(
                            "value" => '',
                            "color" => "#bbb"),
                        array(
                            "value" => '杨立彬',
                            "color" => "#bbb"),
                        array(
                            "value" => '9月13日至9月30日',
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
Debug::trace("=====[cron][beg][Doctor_view_ofylb.php]=====");

$process = new Doctor_view_ofylb();
$process->dowork();

Debug::trace("=====[cron][end][Doctor_view_ofylb.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
