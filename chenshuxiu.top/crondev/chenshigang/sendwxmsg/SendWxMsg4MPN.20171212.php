<?php
/*
 * @desc MPN特定用药患者发送通知
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

class SendWxMsg {
    public function __construct() {

    }

    public function run() {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $doctorid = 360;
        $diseaseid = 6;
        ##medicine 芦可替尼
        $medicineid = '516';
        $doctor = Doctor::getById($doctorid);
        $sql = "SELECT DISTINCT(a.patientid) FROM patientmedicinesheets a
            INNER JOIN patientmedicinesheetitems b 
            ON b.patientmedicinesheetid=a.id
            WHERE b.medicineid='$medicineid' AND a.doctorid='$doctorid'";
        $patientids = Dao::queryValues($sql);
        $patients = Dao::getEntityListByIds('Patient', $patientids);
        foreach ($patients as $patient) {
            //忽略掉无效患者和测试患者
            if ($patient->status != 1 || $patient->is_test == 1) {
                echo "忽略测试患者和无效患者\n";
                continue;
            }
            $wxusers = WxUserDao::getListByPatient($patient);
            foreach ($wxusers as $wxuser) {
                echo $patient->id, " ", $patient->name, " ", $wxuser->nickname, "\n";
                //$this->sendTxtMsg($wxuser, $doctor);
            }
        }

        //$wxuser = WxUser::getById('193857196');//李琨亭
        ////$wxuser = WxUser::getById('120476785');//冯伟
        //$this->sendTxtMsg($wxuser, $doctor);
        $unitofwork->commitAndInit();
    }

    private function sendTxtMsg($wxuser, $doctor) {
        $content = '您好，服用芦可替尼期间您需要把：①脾脏大小的检查报告；②疾病诊断；③MPN10量表（现在完成一次：在【我要】【做评估】中完成）；我们需要把结果汇报给段主任，医生评估您的病情，并帮助您通过电子处方时需要填写。请您现在立即完成。';
        PushMsgService::sendTxtMsgToWxUserByDoctor ($wxuser, $doctor, $content);
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

$obj = new SendWxMsg();
$obj->run();

