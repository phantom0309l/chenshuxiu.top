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

class Revisittkt_Patient_Confirm extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 10:06 每天 发送 预约复诊确认';
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
        $cond = " and status = 1 and confirm_status = 1 ";
        $revisitconfigs = Dao::getEntityListByCond('RevisitTktConfig', $cond, []);

        foreach ($revisitconfigs as $revisitconfig) {
            $doctorid = $revisitconfig->doctorid;
            $pre_day_cnt = $revisitconfig->confirm_pre_day_cnt;
            $confirm_notice = $revisitconfig->confirm_notice;
            $issend_miss = $revisitconfig->confirm_issend_miss;

            $today = date('Y-m-d 12:00:00');
            $day_from = date('Y-m-d 12:00:00', time() + 3600 * 24 * $pre_day_cnt);
            $day_to = date('Y-m-d 12:00:00', time() + 3600 * 24 * ($pre_day_cnt + 1));

            $cond = " and doctorid = :doctorid and status=1 and auditstatus=1
            and isclosed=0 and send_cnt<2 and thedate < :day_to ";

            $bind = [];
            $bind[':doctorid'] = $doctorid;
            $bind[':day_to'] = $day_to;

            if (0 == $issend_miss) {
                $cond .= " and thedate > :day_from ";
                $bind[':day_from'] = $day_from;
            } else {
                $cond .= " and thedate > :today ";
                $bind[':today'] = $today;
            }

            $revisittkts = Dao::getEntityListByCond('RevisitTkt', $cond, $bind);

            $unitofwork = BeanFinder::get("UnitOfWork");

            foreach ($revisittkts as $revisittkt) {
                $doctor = $revisittkt->doctor;
                $patient = $revisittkt->patient;

                $revisittkt->send_cnt = 2;

                echo "\n[{$doctor->name}] [{$patient->name}] [{$revisittkt->thedate}]";

                if (false == $patient instanceof Patient) {
                    echo ' jump';
                    continue;
                }

                if (false == $revisittkt->schedule instanceof Schedule) {
                    continue;
                }

                $pcard = $patient->getPcardByDoctorOrMasterPcard($doctor);

                $content_ori = MsgContentService::transform4RevisitTkt($confirm_notice, $revisittkt);

                $first = array(
                    "value" => "约复诊注意事项通知",
                    "color" => "");

                $keywords = array(
                    array(
                        "value" => "{$revisittkt->doctor->name}",
                        "color" => "#ff6600"),
                    array(
                        "value" => $content_ori,
                        "color" => "#ff6600"));
                $content = WxTemplateService::createTemplateContent($first, $keywords);

                $wx_uri = Config::getConfig("wx_uri");
                $url = $wx_uri . "/revisittkt/confirm?revisittktid={$revisittkt->id}";

                PushMsgService::sendTplMsgToWxUsersOfPcardBySystem($pcard, "doctornotice", $content, $url);
            }

            $unitofwork->commitAndInit();
        }
    }
}

// //////////////////////////////////////////////////////

$process = new Revisittkt_Patient_Confirm(__FILE__);
$process->dowork();
