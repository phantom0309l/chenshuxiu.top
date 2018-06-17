<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 17-12-27
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

class Ceshi_tplmsg
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        // $sql = "SELECT id FROM wxusers WHERE userid IN (10007)";
        $sql = "SELECT id FROM wxusers WHERE userid IN (10040) and wxshopid IN (17, 27)";

        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $wxuser = WxUser::getById($id);

            if (1 != $wxuser->subscribe) {
                echo "\n--{$wxuser->wxshopid}--取关了----- " . $id;
                continue;
            }

            $str = "医生助理";
            $sendContent = "测试此类模板是否申请";
            $first = array(
                "value" => "标题",
                "color" => "");
            $keywords = array(
                array(
                    "value" => $str,
                    "color" => "#aaa"),
                array(
                    "value" => $sendContent,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);
            $url = Config::getConfig("wx_uri") . "/patient/baodao?openid={$wxuser->openid}";
            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "doctornotice", $content, $url);

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Ceshi_tplmsg.php]=====");

$process = new Ceshi_tplmsg();
$process->dowork();

Debug::trace("=====[cron][end][Ceshi_tplmsg.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
