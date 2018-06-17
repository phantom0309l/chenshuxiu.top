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
class Prescription_auto_audit extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'minute';
        $row["title"] = '每10分钟，对prescription进行自动审核';
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

        $sql = "select id from prescriptions where status = 0 and shoporderid > 0";
        $ids = Dao::queryValues($sql);

        foreach ($ids as $id) {
            $prescription = Prescription::getById($id);
            $prescription->status = 1;
            $shopOrder = $prescription->shoporder;
            $thedoctor = $shopOrder->thedoctor;

            if(false == $thedoctor->needAuditChufang()){
                $prescription->passBySys();
            }else{
                //医生开通了处方审核，给方寸管理端发送消息
                $this->sendTplMsg($prescription);
            }
            $patient = $prescription->patient;
            $this->cronlog_content .= "{$shopOrder->id}\n";
        }

        $this->cronlog_brief = count($ids);
        $this->cronlog_content = trim($this->cronlog_content);

        $unitofwork->commitAndInit();
    }

    private function sendTplMsg($prescription){
        $patient = $prescription->patient;
        $shopOrder = $prescription->shoporder;
        $thedoctor = $shopOrder->thedoctor;

        $templateEname = "auditor2doctor";
        $first = array(
            "value" => "医生您好，有患者需要延伸处方(续方)。请进行审核\n ",
            "color" => "#415a93");
        $keywords = array(
            array(
                "value" => $patient->name,
                "color" => "#aaa"),
            array(
                "value" => $thedoctor->hospital->name,
                "color" => "#aaa"),
            array(
                "value" => "",
                "color" => "#aaa"),
            array(
                "value" => $thedoctor->name,
                "color" => "#aaa"),
            array(
                "value" => $shopOrder->time_pay,
                "color" => "#aaa"));
        $remark = "请您及时点击详情进行处理";
        $content = WxTemplateService::createTemplateContent($first, $keywords, $remark);

        $dwx_uri = Config::getConfig("dwx_uri");
        $url = $dwx_uri . "/#/prescription/one?prescriptionid={$prescription->id}";

        Dwx_kefuMsgService::sendTplMsgToDoctorBySystem($thedoctor, $templateEname, $content, $url);
    }
}

// //////////////////////////////////////////////////////

$process = new Prescription_auto_audit(__FILE__);
$process->dowork();
