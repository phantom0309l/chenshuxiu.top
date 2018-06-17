<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-7-14
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

class Doctor_view_ofcmr
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        //陈敏榕医生的患者id
        $sql = "select id from patients where doctorid=268";

    //    $sql = "select id from patients where doctorid=11 and name like '%李杰%'";

        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $patient = Patient::getById($id);
            echo "\n\n---------================================================----- " . $id;

            // $age = $patient->getAgeStr();
            // 如果不是【1， 8】岁的跳过
            // if(false == (1 <= $age && $age <= 8)){
                // continue;
            // }

            $wxusers = WxUserDao::getListByPatient($patient);

            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                    echo "\n\n--------- " . $wxuser->id;

                    $openid = $wxuser->openid;

                    //发送专栏更新公告
                    $str = "医生助理";
                    $sendContent = "亲爱的家长晚上好。\n2017.5.6（本周六）14:30~16:30在福州儿童医院远志楼7楼会议室有薛漳主任面向家长的免费讲座，主题是“认知多动症”。一个家庭可以有多个家长参与，不能带孩子。\n\n如果您期望参加，请在此公众号上给我回复消息报名，说明参与的人数。（示例：报名参加，2人）";
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
                    PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content);
                }
            }

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Doctor_view_ofcmr.php]=====");

$process = new Doctor_view_ofcmr();
$process->dowork();

Debug::trace("=====[cron][end][Doctor_view_ofcmr.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
