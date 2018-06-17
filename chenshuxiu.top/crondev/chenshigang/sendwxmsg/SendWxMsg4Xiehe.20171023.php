<?php
/*
 * @desc 给协和风湿免疫科无诊断患者发送通知
 *
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "3048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class ExportPatient {
    public function __construct() {

    }

    public function run() {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $doctorid = 1294;
        $diseaseid = 22;
        $cond = ' AND doctorid=:doctorid AND diseaseid=:diseaseid GROUP BY patientid';
        $bind = [
            ':doctorid' => $doctorid,
            ':diseaseid' => $diseaseid,
        ];
        $pcards = Dao::getEntityListByCond('Pcard', $cond, $bind);
        foreach ($pcards as $pcard) {
            $patient = $pcard->patient;
            //忽略掉无效患者和测试患者
            if ($patient->status != 1 || $patient->is_test == 1) {
                continue;
            }
            if (!$pcard->complication && !$patient->getTagRefs()) {
                $wxusers = WxUserDao::getListByPatient($patient);
                foreach ($wxusers as $wxuser) {
                    echo $patient->id, " ", $patient->name, " ", $wxuser->nickname, "\n";
                    $this->sendTemplateMsg($wxuser);
                }
            } else {
                //echo '++++++', $patient->name, "\n";
            }
        }

        //$wxuser = WxUser::getById('103282571');//陈士岗
        //$wxuser = WxUser::getById('120476785');//冯伟
        //$this->sendTemplateMsg($wxuser);
        $unitofwork->commitAndInit();
    }

    private function sendTemplateMsg ($wxuser, $url='') {
        $title = "管理通知";

        $content = "您好，跟您明确一下：您的基础疾病诊断是什么？请您把您就诊的病历本翻开拍照上传，我们需要记录您的基础诊断汇报给主任。";

        $templateEname = "followupNotice";
        $wxtemplate = WxTemplateDao::getByEname($wxuser->wxshopid, $templateEname);
        if ($wxtemplate instanceof WxTemplate) {
            $first = array(
                "value" => $title,
                "color" => "#ff6600");
            $keywords = array(
                array(
                    "value" => $wxuser->user->patient->name,
                    "color" => "#aaa"),
                array(
                    "value" => date("Y-m-d H:i:s"),
                    "color" => "#aaa"),
                array(
                    "value" => $content,
                    "color" => "#ff6600"));
        } else {
            // 模板消息不存在,失败退出
            echo "[wxtemplate is null]";
            Debug::error(__METHOD__ . " wxtemplate is null, templateEname [$templateEname]");
            return false;
        }

        $content = WxTemplateService::createTemplateContent($first, $keywords);
        XContext::setValue('is_filter_blacklist', false);
        PushMsgService::sendTplMsgToWxUserBySystem($wxuser, $templateEname, $content, $url);
    }
}

$obj = new ExportPatient();
$obj->run();

