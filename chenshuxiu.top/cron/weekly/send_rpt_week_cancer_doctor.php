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
class Send_rpt_week_cancer_doctor extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '周一9:55, (肿瘤)运营周报';
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

        $thedate = date("Y-m-d", time() - 86400);

        $doctorids = $this->getDoctorIds($thedate);

        $i = 0;
        $logcontent = '';
        foreach ($doctorids as $id) {
            $doctor = Doctor::getById($id);

            $content = $this->getContent($doctor, $thedate);

            $url = $this->getUrl($thedate);
            Dwx_kefuMsgService::sendTplMsgToDoctorBySystem($doctor, "PatientMgrNotice", $content, $url);

            $i ++;
            if ($i % 100 == 0) {
                $unitofwork->commitAndInit();
            }

            $logcontent .= $doctor->id . " ";
            echo "\n{$doctor->name} [push]";
        }

        $this->cronlog_brief = $i;
        $this->cronlog_content = $logcontent;

        $unitofwork->commitAndInit();
    }

    private function getDoctorIds ($thedate) {
        $sql = "select doctorid
                from statdb.rpt_week_cancer_doctors
                where weekend_date = '{$thedate}' ";
        return Dao::queryValues($sql);
    }

    private function getUrl ($thedate) {
        $dwx_uri = Config::getConfig("dwx_uri");
        $url = $dwx_uri . "/#/rpt/cancerweekly?thedate={$thedate}";

        return $url;
    }

    private function getContent (Doctor $doctor, $weekenddate) {
        $weekstartdate = date('Y-m-d', strtotime($weekenddate) - 6 * 84600);

        $first = array(
            "value" => "{$doctor->name}医生，您辛苦啦！上周的周报已经为您整理好，请点此查看",
            "color" => "#3366ff");

        $keywords = array(
            array(
                "value" => "{$weekstartdate} 到 {$weekenddate}",
                "color" => ""),
            array(
                "value" => '详情',
                "color" => ""));

        $content = WxTemplateService::createTemplateContent($first, $keywords);

        return $content;
    }
}

// //////////////////////////////////////////////////////

$process = new Send_rpt_week_cancer_doctor(__FILE__);
$process->dowork();
