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

class Goushunotice
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        //$sql = "select id from patients where id in (308627186)";
        $sql = "select id from patients where name in (
'蒋俊豪','刘航语','刘浩','王涵','朱胜伟','刘林珂','曲鸿成','张哲轩','汤伟箔','徐威巍','张偌菡','刘代福','孙一铭','于巨衡','刘相辰','陈瑜擎','王冠乔','于濮嘉','李文瑜','练德智','陈晨','彭湘宁','徐周立','魏文轩','林子轩','李睿','方瑞','赵昱衡','史翰林','王子涵','梁汝钰','户靖卓','林楷博','何俊良','施晨阳','范朝俊','林黄亿','赖雨婷','李天齐','周峻楠','张轩逸','许嘉诺','陈雨轩','李泽浩','顾塍杰','聪聪','叶锐彬','郭子民','谢承睿','邱文博','周家乐','鞠曦睿','汪思辰','邱锦佳','陈宇轩','李昊轩','欧阳康旭','秦振庭','何金纬','尹子睿','高赫辰','莫文轩','伊晨心旭','卞梓涵','孙楷博','张涵','朱浩铭','罗浩','何汐','周南','罗洺淮','郭桐赫','朱','董竞文','张涵瑞','杨潇宇','杨宸林','杨易柠','肖杰元','陈睿','杨杭','李若愚','梁天佑','宋远杰','刘子畅'
) and status = 1 and diseaseid = 1";
        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $patient = Patient::getById($id);
            if(false == $patient instanceof Patient){
                continue;
            }
            $doctorid = $patient->doctorid;
            $bad_doctorids = array(41,157);
            if(in_array($doctorid, $bad_doctorids)){
                continue;
            }

            $wxuser = $patient->getMasterWxUser(1);

            echo "\n\n---------================================================----- " . $id;

            if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                echo "\n\n--------- " . $wxuser->id;

                $str = "医生助理";
                $content = "与ADHD斗争不只是医生、护士的事，每一位家长都应该积极参与进来。为让家长掌握更多的ADHD治疗方法，经医生推荐，我们将向家长赠送《自我治疗小儿多动症》一书100本。此书从自我疗法角度，充分挖掘传统医疗保健潜力，介绍了按摩、拔罐、贴敷、熏洗、艾灸、食疗、运动、音乐、心理等不同自我调治方式，帮助您早日带领患儿走出多动症的困扰。此书将随赠于您在“开药门诊”的开的药品包裹中，一同快递到您的手中。";
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

                $url = Config::getConfig("wx_uri") . "/shopmedicine/index?openid={$wxuser->openid}&from=goushunotice";
                PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
            }
            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Goushunotice.php]=====");

$process = new Goushunotice();
$process->dowork();

Debug::trace("=====[cron][end][Goushunotice.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
