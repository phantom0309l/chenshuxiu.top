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

class Sendnotice_51
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        //$sql = "select id from wxusers where wxshopid=1 and doctorid in (179,153,25,24,53,483,432,268,394,438,111,286,96,98) and subscribe=1";
        $sql = "select id from wxusers where id in (97)";
        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $wxuser = WxUser::getById($id);
            echo "\n\n---------================================================----- " . $id;

            if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {

                echo "\n\n--------- " . $wxuser->id;

                $str = "医生助理";
                $content = "ADHD安全有效用药九原则！(下)\n\n尽管对于ADHD的儿童来说，药物治疗是非常重要的环节，但仍然我们需要密切注意药物治疗的安全性和有效性。简而言之，我们要尽量最大化药物治疗效果，最小化药物治疗的副作用。\n如何做到这一点呢？以下是安全有效使用药物的九个原则。";
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
                $url = Config::getConfig("wx_uri") . "/wxmall/active51xia?openid={$wxuser->openid}&from=activemsg&doctorid={$doctorid}";
                PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
            }
            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Sendnotice_51.php]=====");

$process = new Sendnotice_51();
$process->dowork();

Debug::trace("=====[cron][end][Sendnotice_51.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
