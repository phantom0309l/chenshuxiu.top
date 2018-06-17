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

class Letter_summary_dwx extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'week';
        $row["title"] = '每周六, 19:00 发送感谢信通知';
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
        $today = XDateTime::now();
        $fromday = date('Y-m-d H:i:s', strtotime($today) - 7*86400 );
        echo "\n{$fromday} 到 {$today}";

        $sql ="SELECT count(id) AS cnt
        FROM letters
        WHERE audit_status = 1 AND show_in_doctor = 1
        AND audit_time > :fromday AND audit_time <= :today";

        $bind = [];
        $bind[':today'] = $today;
        $bind[':fromday'] = $fromday;

        $lettercnt = Dao::queryValue($sql, $bind);

        if(0 == $lettercnt){
            Debug::warn("本周没有审核感谢信！！！");
            return;
        }

        $dwx_uri = Config::getConfig("dwx_uri");
        $url = $dwx_uri . "/#/letter/list?menucode=template_letter_list";


        $sql ="SELECT DISTINCT a.id
        FROM doctors a 
        INNER JOIN doctorconfigs b ON b.doctorid=a.id
        INNER JOIN doctorconfigtpls c ON c.id=b.doctorconfigtplid
        INNER JOIN users d ON d.id=a.userid
        INNER JOIN wxusers e ON e.userid=d.id
        WHERE a.status = 1 AND b.status=1 AND c.code = 'letter_send' AND e.wxshopid=2";

        $ids = Dao::queryValues($sql);
        $cnt = count($ids) ;
        echo "\n共{$cnt}人！";

        foreach( $ids as $id ){
            $doctor = Doctor::getById($id);

            $first = array(
                "value" => "{$doctor->name}医生，本周有{$lettercnt}位患者向诊后管理服务表示感谢和认可，请点此查看",
                "color" => "#3366ff");

            $keywords = array(
                array(
                    "value" => "{$fromday} 到 {$today}",
                    "color" => ""),
                array(
                    "value" => '见详情',
                    "color" => ""));

            $content = WxTemplateService::createTemplateContent($first, $keywords);

            Dwx_kefuMsgService::sendTplMsgToDoctorBySystem($doctor, "PatientMgrNotice", $content, $url);
            echo "\n{$doctor->name} [push]";
            $this->cronlog_content .= "\n{$doctor->name} [push]";
        }
    }

}

// //////////////////////////////////////////////////////

$process = new Letter_summary_dwx(__FILE__);
$process->dowork();
