<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-8-14
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

class Shangcheng_urge
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

       $sql = "SELECT a.id FROM patients a
          INNER JOIN doctors b ON b.id=a.doctorid
          WHERE b.id IN (179,153,25,24)
          AND a.status=1 AND a.subscribe_cnt>0
          AND a.id NOT IN
          (107480361,100975189,109213291,104061687,107833731,103863883,120015655,
          107936241,119964735,105156665,103768059,106725323,105948583,108201915,
          105202861,103344093,102209811,373,102842509,104760485,101648845,120393155,
          108561915,221,104702201,104761295,100076529,101499615,104169913,121131175,
          324,107933119,102071987,107944789,102818355,102578989,102618669,108192007,
          108742189,104056653,223,120938105,109262141,108858085,104493085,104503563,
          111703865,121511075,107833017,296,121157355)";

        // $sql = "SELECT id FROM patients WHERE id IN (112011595)";

        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $patient = Patient::getById($id);
            echo "\n\n---------================================================----- " . $id;

            $wxusers = WxUserDao::getListByPatient($patient);

            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                    echo "\n\n--------- " . $wxuser->id;

                    $str = "ADHD商城";
                    $sendContent = "最后一天啦！
经过专家的筛选，我们甄选了部分ADHD相关的书籍，现在以1元的价格向您推荐，并且在您购买后，就能获得我们其他服务的优惠。活动最后一天啦！";
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
                    $url = Config::getConfig("wx_uri") . "/wxmall/index?menucode=wxmall_index&openid={$wxuser->openid}";

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
Debug::trace("=====[cron][beg][Shangcheng_urge.php]=====");

$process = new Shangcheng_urge();
$process->dowork();

Debug::trace("=====[cron][end][Shangcheng_urge.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
