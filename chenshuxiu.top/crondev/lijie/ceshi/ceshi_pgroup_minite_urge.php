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

class Ceshi_pgroup_minite_urge
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $fromtime = date('Y-m-d H:i:s', time() - 15 * 60);
        $totime = date('Y-m-d H:i:s', time() - 14 * 60);

//        $sql = "SELECT a.id FROM patients a
//        LEFT JOIN patientpgrouprefs b ON b.patientid=a.id
//        WHERE b.id IS NULL AND a.status=1 AND a.diseaseid=1 AND a.createtime>='{$fromtime}' AND a.createtime<'{$totime}'";

        $sql = "SELECT id FROM patients WHERE id IN (106047061)";
//        $sql = "SELECT id FROM patients WHERE id IN (105443323, 104379491, 104403953)";

        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $patient = Patient::getById($id);
            echo "\n\n---------================================================----- " . $id;

            $wxusers = WxUserDao::getListByPatient($patient);

            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                    echo "\n\n--------- " . $wxuser->id;

                    $str = "医生助理";
                    $sendContent = "{$patient->name}家长您好，因为我们管的孩子比较多，发现家长反馈的很多问题都是共性的，比如作业拖拉、爱发脾气、上课纪律差、同伴关系不好等，对于这类问题我们也整理了一些有效的行为引导课程。您家孩子如果也有类似的情况，可以在微信上选适合的课程！\n具体操作方法： 点击【每日一练】，进入页面选择课程后，就可以开始学习了。\n温馨提示：平台上提供的所有服务均免费。";
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
                    $url = Config::getConfig("wx_uri") . "/pgroup/show?openid={$wxuser->openid}";
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
Debug::trace("=====[cron][beg][Ceshi_pgroup_minite_urge.php]=====");

$process = new Ceshi_pgroup_minite_urge();
$process->dowork();

Debug::trace("=====[cron][end][Ceshi_pgroup_minite_urge.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
