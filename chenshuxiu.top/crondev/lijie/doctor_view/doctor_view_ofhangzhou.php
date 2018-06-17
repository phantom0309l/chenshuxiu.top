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

class Doctor_view_ofhangzhou
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from patients where doctorid = 35 ";

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

                    $openid = $wxuser->openid;

                    //发送专栏更新公告
                    $str = "医生助理";
                    $sendContent = "来自杭州第一人民医院的项俊华医生 ADHD专题 微课第三期如约而至！\n本期主题是：门诊诊断多动症常用的检查内容和解读方法。\n快点击菜单中的【我的】【热门文章】【医生专栏】收听吧。";
                    $first = array(
                        "value" => "",
                        "color" => "");
                    $keywords = array(
                        array(
                            "value" => $str,
                            "color" => "#aaa"),
                        array(
                            "value" => $sendContent,
                            "color" => "#ff6600"));
                    $content = WxTemplateService::createTemplateContent($first, $keywords);
                    $url = Config::getConfig("wx_uri") . "/hotarticle/list?type=3&openid={$wxuser->openid}";
                    PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
                }
            }

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Doctor_view_ofhangzhou.php]=====");

$process = new Doctor_view_ofhangzhou();
$process->dowork();

Debug::trace("=====[cron][end][Doctor_view_ofhangzhou.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
