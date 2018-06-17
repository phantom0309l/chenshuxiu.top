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

        $sql = " select id from patients where doctorid = 35 AND status = 1 AND subscribe_cnt>0 ";

        //$sql = "SELECT id FROM patients WHERE id IN (106415651)";

        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $patient = Patient::getById($id);
            echo "\n\n---------================================================----- " . $id;

            $wxusers = WxUserDao::getListByPatient($patient);

            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                    echo "\n\n--------- " . $wxuser->id;

                    $str = "医生助理";
                    $content = "来自杭州第一人民医院的项俊华医生ADHD专题微课开讲啦！\n快点击菜单中的【我的】【热门文章】【医生专栏】收听吧。\n今天是第一节，以后每隔周周末更新，各位家长记得定期来收听哟。";
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
                    $url = Config::getConfig("wx_uri") . "/hotarticle/list?openid={$wxuser->openid}&type=3";

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
Debug::trace("=====[cron][beg][Sendweikemsg.php]=====");

$process = new Sendweikemsg();
$process->dowork();

Debug::trace("=====[cron][end][Sendweikemsg.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
