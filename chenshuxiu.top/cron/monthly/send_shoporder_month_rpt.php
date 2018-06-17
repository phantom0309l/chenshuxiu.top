<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 0);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");
require_once(ROOT_TOP_PATH . "/../core/util/email/class.phpmailer.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Send_shoporder_month_rpt extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::rpt;
        $row["when"] = 'month';
        $row["title"] = '每月1日, 09:01, 发送处方分析';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog() {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog() {
        return true;
    }

    public function doworkImp() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = $this->getCancerDoctorids();

        $brief = 0;
        $logcontent = '';

        $time = strtotime('-1 day', strtotime(date('Y-m-01')));
        $from_date = date('Y-m-01', $time);
        $to_date = date('Y-m-d', $time);

        $dwx_uri = Config::getConfig("dwx_uri");
        $url = $dwx_uri . "/#/shoporder/monthrpt?themonth=" . date('Y-m', $time);
        echo $url;
        foreach ($ids as $id) {
            $doctor = Doctor::getById($id);

            $first = array(
                "value" => "{$doctor->name}医生，您辛苦啦！上月的处方分析已经为您整理好，请点此查看",
                "color" => "#3366ff");

            $keywords = array(
                array(
                    "value" => "{$from_date} 到 {$to_date}",
                    "color" => ""),
                array(
                    "value" => '见详情',
                    "color" => ""));

            $content = WxTemplateService::createTemplateContent($first, $keywords);

            Dwx_kefuMsgService::sendTplMsgToDoctorBySystem($doctor, "PatientMgrNotice", $content, $url);

            $brief++;
            $logcontent .= $doctor->id . " ";

            if ($brief % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }

        $this->cronlog_brief = $brief;
        $this->cronlog_content = $logcontent;

        echo "{$this->cronlog_brief} {$this->cronlog_content} \n";

        $unitofwork->commitAndInit();
    }

    private function getCancerDoctorids() {
        $diseaseids = Disease::getCancerDiseaseidsStr();
        $sql = "SELECT DISTINCT a.id
                FROM doctors a
                INNER JOIN doctordiseaserefs b ON b.doctorid = a.id
                WHERE a.menzhen_offset_daycnt > 0
                AND b.diseaseid IN ({$diseaseids})";

        return Dao::queryValues($sql);
    }
}

$experience = new Send_shoporder_month_rpt(__FILE__);
$experience->dowork();
