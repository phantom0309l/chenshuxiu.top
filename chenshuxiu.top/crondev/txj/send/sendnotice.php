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

class Sendweikemsg
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from wxusers where wxshopid=1 and doctorid in (179,153,25,24,5) and subscribe=1";
        //$sql = "select id from wxusers where wxshopid=1 and id=97";
        $ids1 = Dao::queryValues($sql);
        $arr1 = array("148560656",
        "179104676",
        "136274696",
        "106487693",
        "106492559",
        "108808367",
        "161687876",
        "151997026",
        "155030326",
        "108620159",
        "106315185",
        "107932733",
        "106669745",
        "121667865",
        "121668655",
        "145");
        $ids = array_merge($ids1,$arr1);

        foreach($ids as $id){
            $wxuser = WxUser::getById($id);
            echo "\n\n---------================================================----- " . $id;

            if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                echo "\n\n--------- " . $wxuser->id;

                $str = "医生助理";
                $content = "专家实时在线培训公益课开始啦！\n\n专家在线指导家长学会正确的、恰当的帮助儿童的方法技巧，调整亲子之间的互动模式，改善教养方式，循序渐进地帮助孩子建立良好的行为习惯。";
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

                $url = Config::getConfig("wx_uri") . "/wxmall/active?openid={$wxuser->openid}&from=activemsg";
                PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
            }
            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Sendweikemsg.php]=====");

$process = new Sendweikemsg();
$process->dowork();

Debug::trace("=====[cron][end][Sendweikemsg.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
