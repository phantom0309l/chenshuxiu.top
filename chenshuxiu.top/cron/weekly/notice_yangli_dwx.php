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

class Notice_yangli_dwx extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'week';
        $row["title"] = '每周六, 19:30 给杨莉医生推送疗效效果通知';
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
        $doctorid = 1;
        //杨莉医生
        $doctor = Doctor::getById($doctorid);
        $today = date('Y-m-d', time());
        $fromday = date('Y-m-d', strtotime($today) - 7*86400 );

        $sql ="select count(a.id) as cnt
        from patients a
        inner join (
            select
            patientid,
            max(first_start_date) as first_start_date
            from patientmedicinerefs where medicineid in (2, 3)
            group by patientid
        ) b on b.patientid=a.id
        inner join pcards c on c.patientid=a.id
        where a.is_test = 0 and c.diseaseid=1
        and b.first_start_date > :fromday and b.first_start_date <= :today";

        // 医生主管相关逻辑
        $juniordoctors = Doctor_SuperiorDao::getListBySuperiorDoctorid($doctorid);
        $doctorids = [];
        $doctorids[] = $doctorid;
        foreach ($juniordoctors as $juniordoctor) {
            $doctorids[] = $juniordoctor->doctorid;
        }
        $doctoridstr = implode(',', $doctorids);

        $sql .= " AND c.doctorid IN ($doctoridstr) AND (a.status=1 OR (a.status = 0 AND a.is_live = 0)) AND a.doctor_audit_status=1 ";

        $bind = [];
        $bind[':fromday'] = date('Y-m-d', strtotime($fromday) - 90*86400 );
        $bind[':today'] = date('Y-m-d', strtotime($today) - 90*86400 );

        $cnt = Dao::queryValue($sql, $bind);

        echo "\n{$fromday} 到 {$today}";
        echo "\n共{$cnt}人！";

        if($cnt){
            $dwx_uri = Config::getConfig("dwx_uri");
            $url = $dwx_uri . "/#/patient/list?type=foryangli";

            $first = array(
                "value" => "{$doctor->name}医生，本周有{$cnt}位患者治疗满3个月，请点此查看。",
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

$process = new Notice_yangli_dwx(__FILE__);
$process->dowork();
