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

class Rpt_summary_dwx extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'week';
        $row["title"] = '每周一上周周报, 10:00 上周周报';
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

        $last_week_timespan_arr = XDateTime::get_timespan_of_lastweek ();
        $last_week_monday = date('Y-m-d', strtotime( $last_week_timespan_arr[0] ));
        $last_week_sunday = date('Y-m-d', strtotime( $last_week_timespan_arr[1]) - 86400 );

        $dwx_uri = Config::getConfig("dwx_uri");
        $url = $dwx_uri . "/#/weekrpt?date={$last_week_sunday}";

        echo "\n{$last_week_monday} 到 {$last_week_sunday}";

        $doctorids = $this->getDoctorids();

        $cnt = count($doctorids) ;
        echo $cnt;
        $this->cronlog_brief .= $cnt;

        $i = 0;
        foreach( $doctorids as $doctorid ){
            $doctor = Doctor::getById($doctorid);

            if( false == $doctor instanceof Doctor ){
                continue;
            }

            $first = array(
                "value" => "{$doctor->name}医生，您辛苦啦！上周的周报已经为您整理好，请点此查看",
                "color" => "#3366ff");

            $keywords = array(
                array(
                    "value" => "{$last_week_monday} 到 {$last_week_sunday}",
                    "color" => ""),
                array(
                    "value" => '见详情',
                    "color" => ""));

            $content = WxTemplateService::createTemplateContent($first, $keywords);

            //4199 方寸儿童管理服务平台周报，对多动症医生覆盖掉原来的周报
            $is_ADHD = false;
            if(true == $doctor->isAdhdDoctor()){
                $is_ADHD = true;
            }
            //4123 多疾病周报，对多疾病医生覆盖掉原来的周报
            $is_multi = false;
            if(true == $doctor->isMultiDiseaseDoctor()){
                $is_multi = true;
            }

            if (true == $is_ADHD) {
                $url_ADHD = $dwx_uri . "/#/doctor/adhdweekrpt?date={$last_week_sunday}";
                Dwx_kefuMsgService::sendTplMsgToDoctorBySystem($doctor, "PatientMgrNotice", $content, $url_ADHD);
            } else if (true == $is_multi) {
                //针对王迁特殊判断
                if ($doctor->id == 32) {
                    $first["value"] = "{$doctor->name}医生，您辛苦啦！上周的间质性肺病周报已经为您整理好，请点此查看";
                    $content1 = WxTemplateService::createTemplateContent($first, $keywords);
                    $url_multi = $dwx_uri . "/#/doctor/multiplediseaserpt?date={$last_week_sunday}&diseaseid=2";
                    Dwx_kefuMsgService::sendTplMsgToDoctorBySystem($doctor, "PatientMgrNotice", $content1, $url_multi);

                    $first["value"] = "{$doctor->name}医生，您辛苦啦！上周的肺动脉高压周报已经为您整理好，请点此查看";
                    $content1 = WxTemplateService::createTemplateContent($first, $keywords);
                    $url_multi = $dwx_uri . "/#/doctor/multiplediseaserpt?date={$last_week_sunday}&diseaseid=22";
                    Dwx_kefuMsgService::sendTplMsgToDoctorBySystem($doctor, "PatientMgrNotice", $content1, $url_multi);
                } else {
                    $url_multi = $dwx_uri . "/#/doctor/multiplediseaserpt?date={$last_week_sunday}";
                    Dwx_kefuMsgService::sendTplMsgToDoctorBySystem($doctor, "PatientMgrNotice", $content, $url_multi);
                }
            } else {
                Dwx_kefuMsgService::sendTplMsgToDoctorBySystem($doctor, "PatientMgrNotice", $content, $url);
            }

            $i ++;
            if ($i % 100 == 0) {
                $unitofwork->commitAndInit();
            }

            echo "\n{$doctor->name} [push]";
            $this->cronlog_content .= "\n{$doctor->name} [push]";
        }

        $unitofwork->commitAndInit();
    }

    // 非肿瘤医生id
    public function getDoctorids () {
        $doctorconfigtpl = DoctorConfigTplDao::getByCode('rpt_send');

        // 肿瘤不在这个脚本发，单独写脚本发
        $cancer_diseaseids = Disease::getCancerDiseaseidsStr();

        $sql = "select distinct a.id
            from doctors a
            inner join doctorconfigs b on b.doctorid = a.id
            inner join doctordiseaserefs c on c.doctorid = a.id
            where b.doctorconfigtplid = :doctorconfigtplid and b.status = 1 and c.diseaseid not in ($cancer_diseaseids) ";

        $bind = [];
        $bind[':doctorconfigtplid'] = $doctorconfigtpl->id;

        return Dao::queryValues($sql, $bind);
    }

}

// //////////////////////////////////////////////////////

$process = new Rpt_summary_dwx(__FILE__);
$process->dowork();
