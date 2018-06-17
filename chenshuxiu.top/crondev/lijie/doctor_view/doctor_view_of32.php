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

class Doctor_view_of32
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select a.id
        from patients a
        inner join patientmedicinerefs b on b.patientid=a.id
        where a.doctorid=35 and b.medicineid=2 and b.status=1 and b.value>25";

//        $sql = "SELECT id FROM patients WHERE id IN (106704029)";
    //    $sql = "SELECT id FROM patients WHERE id IN (536313086, 520400506)";

        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $patient = Patient::getById($id);
            echo "\n\n---------================================================----- " . $id;

            $wxusers = WxUserDao::getListByPatient($patient);

            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                    echo "\n\n--------- " . $wxuser->id;

                    //发送
                    $str = "杭州第一人民医院小儿发育行为科";
                    $sendContent = "您好，杭州市第一人民医院近期40mg剂型的择思达暂时缺货。如果需要配这个剂型，可以去省中医院开，或者可以在方寸儿童管理服务平台的开药门诊中自费开药。";
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
                    $url = Config::getConfig("wx_uri") . "/shopmedicine/menzhen?openid={$wxuser->openid}";
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
Debug::trace("=====[cron][beg][Doctor_view_of32.php]=====");

$process = new Doctor_view_of32();
$process->dowork();

Debug::trace("=====[cron][end][Doctor_view_of32.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
