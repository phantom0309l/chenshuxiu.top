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
class RevisitTkt_summary_dwx extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 16:00 明日的复诊患者名单';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog() {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog() {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp() {
        $weekarray=array("日","一","二","三","四","五","六");
        $time = time() + 86400;

        $weeknum = $weekarray[date("w", $time)];

        $thedate = date("Y-m-d", $time);

        $doctorconfigtpl = DoctorConfigTplDao::getByCode('revisittkt_list_push');
        $cond = " AND doctorconfigtplid = :doctorconfigtplid AND status=1";

        $bind = [];
        $bind[':doctorconfigtplid'] = $doctorconfigtpl->id;

        $doctorconfigs = Dao::getEntityListByCond("DoctorConfig", $cond, $bind);

        echo "\n";

        foreach ($doctorconfigs as $doctorconfig) {
            $doctor = Doctor::getById($doctorconfig->doctorid);

            if (false == $doctor instanceof Doctor) {
                continue;
            }

            $cond = ' and doctorid = :doctorid and thedate = :thedate and isclosed = 0 and status = 1';

            $bind = [];
            $bind[":doctorid"] = $doctor->id;
            $bind[":thedate"] = $thedate;

            $revisittkts = Dao::getEntityListByCond('RevisitTkt', $cond, $bind);

            $revisittkts_count = count($revisittkts);

            if ($revisittkts_count == 0) {
                continue;
            }

            $patient_name_arr = array();
            foreach ($revisittkts as $revisittkt) {
                $patient_name_arr[] = $revisittkt->patient->name;
            }

            $first = array(
                "value" => "以下是明日要来复诊的患者",
                "color" => "#3366ff");

            $keywords = array(
                array(
                    "value" => $thedate . "星期{$weeknum}",
                    "color" => ""),
                array(
                    "value" => $revisittkts_count . "人",
                    "color" => ""),
                array(
                    "value" => implode('，', $patient_name_arr),
                    "color" => ""));

            $content = WxTemplateService::createTemplateContent($first, $keywords);

            $dwx_uri = Config::getConfig("dwx_uri");
            $url = $dwx_uri . "/#/revisittkt/{$thedate}/list";

            $unitofwork = BeanFinder::get("UnitOfWork");

            Dwx_kefuMsgService::sendTplMsgToDoctorBySystem($doctor, "RevisitTktList", $content, $url);

            // #4130, 协和风湿免疫科, 也发给 王迁 一份
            if ($doctor->id == 1294) {
                $doctor_fix = Doctor::getById(32);
                Dwx_kefuMsgService::sendTplMsgToDoctorBySystem($doctor_fix, "RevisitTktList", $content, $url);
            }

            $unitofwork->commitAndInit();
            echo $revisittkts_count;
            echo "\n{$doctor->name} [push]";
        }
    }
}

// //////////////////////////////////////////////////////

$process = new RevisitTkt_summary_dwx(__FILE__);
$process->dowork();
