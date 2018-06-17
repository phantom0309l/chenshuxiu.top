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

class Doctor_view_ofzhw
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        //陈敏榕医生的患者id
        $sql = "select id from patients where doctorid=264 and status=1 and left(createtime, 10)<'2017-07-01'";

        // $sql = "select id from patients where name like '%李杰测试%'";

        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $patient = Patient::getById($id);
            echo "\n\n---------================================================----- " . $id;

            $wxusers = WxUserDao::getListByPatient($patient);

            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                    echo "\n\n--------- " . $wxuser->id;

                    $openid = $wxuser->openid;

                    //发送专栏更新公告
                    $str = "医生助理";
                    $sendContent = "家长您好，为了保证治疗的效果，需要定期复诊跟医生沟通孩子的治疗情况。如果距上次就诊已经超过三个月，建议近期安排时间回门诊跟医生沟通孩子的情况，决定后续的治疗方案。郑红卫医生出诊时间：周三全天，周日全天。";
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
Debug::trace("=====[cron][beg][Doctor_view_ofzhw.php]=====");

$process = new Doctor_view_ofzhw();
$process->dowork();

Debug::trace("=====[cron][end][Doctor_view_ofzhw.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
