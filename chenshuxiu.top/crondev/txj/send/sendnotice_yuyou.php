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

class Sendnotice_yuyou
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        //$sql = "select id from wxusers where wxshopid=1 and doctorid in (179,153,25,24,5,53) and subscribe=1";
        $sql = "select id from wxusers where id in (97)";
        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $wxuser = WxUser::getById($id);
            echo "\n\n---------================================================----- " . $id;

            if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                echo "\n\n--------- " . $wxuser->id;

                $str = "医生助理";
                $content = "鱼油对ADHD的价值\n\n在遵医嘱服药的基础上还有什么可以改善孩子的症状呢？深海鱼油无疑是选择之一，鱼油富含Omega3，正是ADHD儿童所缺乏的 。相信您看完这篇文章，会有更深入了解。";
                $openid = $wxuser->openid;

                $first = array(
                    "value" => "",
                    "color" => "");
                $keywords = array(
                    array(
                        "value" => $str,
                        "color" => "#aaa"),
                    array(
                        "value" => $content,
                        "color" => "#ff6600"));
                $content = WxTemplateService::createTemplateContent($first, $keywords);

                $url = Config::getConfig("wx_uri") . "/wxmall/activeOfYuYou?openid={$wxuser->openid}&from=activemsg";
                PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
            }
            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Sendnotice_yuyou.php]=====");

$process = new Sendnotice_yuyou();
$process->dowork();

Debug::trace("=====[cron][end][Sendnotice_yuyou.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
