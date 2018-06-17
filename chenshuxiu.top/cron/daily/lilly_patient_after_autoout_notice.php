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

// Debug::$debug = 'Dev';

class Lilly_patient_after_autoout_notice extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 08:59 合作患者顺利出组后，推送提醒文案；';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from patient_hezuos where status=2 and company='Lilly' and enddate = :enddate";

        $bind = [];
        $bind[':enddate'] = date("Y-m-d");
        $ids = Dao::queryValues($sql, $bind);
        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 50) {
                $i = 0;
                echo "\n\n-----commit----- " . XDateTime::now();
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            echo "\n====id[{$id}]===" . XDateTime::now();

            $patient_hezuo = Patient_hezuo::getById($id);

            $patient = $patient_hezuo->patient;
            if(false == $patient instanceof Patient){
                continue;
            }

            $doctor = $patient->doctor;
            if(false == $doctor instanceof Doctor){
                continue;
            }

            $wxusers = $patient->getWxUsers();
            foreach ($wxusers as $wxuser) {
                if ($wxuser instanceof WxUser && 1 == $wxuser->wxshopid && 1 == $wxuser->subscribe) {
                    $doctor_name = $doctor->name;
                    $str = $doctor_name . "医生助理";
                    $content = "";

                    if($patient->canIntoMenzhen()){
                        $content = "sunflower 管理服务项目已结束，您可以点击任意按钮阅读《知情同意书》，确认同意后将继续方寸儿童管理服务平台的管理服务，后续您可以点击菜单栏【在这学习】继续课程学习！\n同时，对于一些开药不便的家长，为您开通了【开药门诊】服务，可点击菜单栏【开药门诊】或者输入“开药门诊”进行操作。如有任何疑问，请及时与我们联系。";
                    } else {
                        $content = "sunflower 管理服务项目已结束，您可以点击任意按钮阅读《知情同意书》，确认同意后将继续方寸儿童管理服务平台的管理服务，后续您可以点击菜单栏【在这学习】继续课程学习！如有任何疑问，请及时与我们联系。";
                    }

                    $first = array(
                        "value" => "",
                        "color" => "#ff6600");
                    $keywords = array(
                        array(
                            "value" => $str,
                            "color" => "#aaa"),
                        array(
                            "value" => $content,
                            "color" => "#ff6600"));
                    $content = WxTemplateService::createTemplateContent($first, $keywords);

                    PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content);
                }
            }

        }

        $unitofwork->commitAndInit();
    }

}

// //////////////////////////////////////////////////////

$process = new Lilly_patient_after_autoout_notice(__FILE__);
$process->dowork();
