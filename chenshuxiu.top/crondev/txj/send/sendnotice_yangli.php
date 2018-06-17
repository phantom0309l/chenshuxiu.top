<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Sendnotice_yangli
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select a.id from patients a
                inner join (
                    select * from (
                    select * from patientmedicinerefs where patientid in (select id from patients where status=1 and diseaseid=1 and doctorid=1) order by first_start_date desc
                    )tt group by patientid
                )tt1 on tt1.patientid = a.id
                where a.status=1 and a.diseaseid=1 and a.doctorid=1 and datediff(now(), tt1.first_start_date) >=90";

        //$sql = "SELECT id FROM patients WHERE id IN (679085516)";

        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $patient = Patient::getById($id);
            echo "\n\n---------================================================----- " . $id;

            $wxusers = WxUserDao::getListByPatient($patient);

            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                    echo "\n\n--------- " . $wxuser->id;

                    $str = "医生助理";
                    $content = "\n为帮助多动症患儿更好的控制相关症状，北医六院即将开展为期13周的系统式注意力训练，符合参与条件的家长可报名参加，请点击详情查看具体介绍。";
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
                    $url = Config::getConfig("wx_uri") . "/lesson/justforshow?lessonid=678995076&gh={$wxuser->wxshop->gh}";

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
Debug::trace("=====[cron][beg][Sendnotice_yangli.php]=====");

$process = new Sendnotice_yangli();
$process->dowork();

Debug::trace("=====[cron][end][Sendnotice_yangli.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
