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
class Send_rpt_day_cancer_doctor extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 07:47 汇总前日患者流信息发送给医生';
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

        // 昨日日期
        $thedate = date("Y-m-d", time() - 84600);

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
                from statdb.rpt_day_cancer_doctors
                where day_date = '{$thedate}' ";
        return Dao::queryValues($sql);
    }

    private function getUrl ($thedate) {
        $dwx_uri = Config::getConfig("dwx_uri");
        $url = $dwx_uri . "/#/rpt/cancerdaily?thedate={$thedate}";

        return $url;
    }

    private function getContent (Doctor $doctor, $thedate) {
        $first = array(
            "value" => "{$doctor->name}医生您辛苦了，{$thedate}提问的患者详情如下：",
            "color" => "#3366ff");

        $rpt_day_cancer_doctor = Rpt_Day_Cancer_DoctorDao::getByDoctoridThedate($doctor->id, $thedate);
        $data = json_decode($rpt_day_cancer_doctor->data, true);
        $emphasicnt = count($data['emphasis']);
        $adversecnt = count($data['adverses']);
        $allcnt = count($data['all']);

        $remark = "全部患者：{$allcnt}\n";
        $remark .= "重点患者：{$emphasicnt}\n";
        $remark .= "严重不良反应：{$adversecnt}";

        $keywords = array(
            array(
                "value" => "{$thedate}",
                "color" => ""),
            array(
                "value" => $allcnt,
                "color" => ""));
        $content = WxTemplateService::createTemplateContent($first, $keywords, $remark);

        return $content;
    }
}

// //////////////////////////////////////////////////////

$process = new Send_rpt_day_cancer_doctor(__FILE__);
$process->dowork();
