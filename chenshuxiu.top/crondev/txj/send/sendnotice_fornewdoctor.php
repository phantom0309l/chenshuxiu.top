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

class Sendnotice_fornewdoctor
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        //$sql = "select id from wxusers where wxshopid=1 and doctorid in (53,483,432,268,394,438) and subscribe=1";
        $sql = "select id from wxusers where id in (97)";
        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $wxuser = WxUser::getById($id);
            echo "\n\n---------================================================----- " . $id;

            if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {

                echo "\n\n--------- " . $wxuser->id;

                $str = "医生助理";
                $content = "ADHD服务商城上线啦！\n\n为了解决ADHD患者遇到的各种疑问和困难，为患者提供更便捷、更优质的服务，我们推出了“ADHD服务商城“。在此上线之际，我们推出“下单返现”活动：活动期间首单优惠20%！";
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
                $doctorid = $wxuser->doctorid;
                $url = Config::getConfig("wx_uri") . "/wxmall/activeForNewDoctor?openid={$wxuser->openid}&from=activemsg&doctorid={$doctorid}";
                PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
            }
            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Sendnotice_fornewdoctor.php]=====");

$process = new Sendnotice_fornewdoctor();
$process->dowork();

Debug::trace("=====[cron][end][Sendnotice_fornewdoctor.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
