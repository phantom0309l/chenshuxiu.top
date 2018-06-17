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

class Kaiyaomenzhen
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        //$sql = "select id from wxusers where wxshopid=1 and subscribe=1 and groupid in (139,140)";
        $sql = "select id from wxusers where id in (97)";
        $ids = Dao::queryValues($sql);
        $ignore_arr = array(
            100806241,101464577,101607343,101653083,204,103347235,103531931,103808895,174204646,195154666,198060096,
            104082243,104234403,104424807,104498899,104510885,104753351,104760443,104760905,104761277,104858977,
            104980071,105910989,105989553,105989807,106669745,107820795,107946819,108384743,143189836,108995607,
            109327459,111735535,111884525,116808365,119714395,119717955,119965855,120751475,122418475,122421055,
            135690206,136274696,138256496,140185326,151073276,151997026,154077446,155030326,157455596,161787486,
            164393976,168513556,179990446,183596336,189111996,210968996,211960786,227344086,209470126,244059256,
            105147777,105202835,106595949,108521879,109262127,121118575,121692535,140663776,158347506,166908786
        );
        $i = 0;

        foreach($ids as $id){
            $i ++;
            if ($i >= 100) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            if(in_array($id, $ignore_arr)){
                continue;
            }
            $wxuser = WxUser::getById($id);
            echo "\n\n---------================================================----- " . $id;

            if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                $str = "医生助理";
                $content = "“便捷门诊”开通通知：\n\n众多ADHD患儿因异地就医、挂号不便等原因，导致购药困难。为了不让患儿因为购药困难造成中断治疗，我们依托于“方寸互联网医院”推出了便捷开药门诊，方便快捷的为患者提供ADHD药品，并直接送到您手上。我们保证药品的质量，承诺假一赔十！";
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

                $url = Config::getConfig("wx_uri") . "/shopmedicine/index?openid={$wxuser->openid}&from=sendnotice";
                PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
            }
        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Kaiyaomenzhen.php]=====");

$process = new Kaiyaomenzhen();
$process->dowork();

Debug::trace("=====[cron][end][Kaiyaomenzhen.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
