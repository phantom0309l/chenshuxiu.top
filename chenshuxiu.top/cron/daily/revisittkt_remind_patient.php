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

class Revisittkt_Remind_Patient extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 10:03 每天 发送 预约复诊提醒';
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
        $cond = " and status = 1 and remind_status = 1 ";
        $revisitconfigs = Dao::getEntityListByCond('RevisitTktConfig', $cond, []);

        foreach ($revisitconfigs as $revisitconfig) {
            $doctorid = $revisitconfig->doctorid;
            $pre_day_cnt = $revisitconfig->remind_pre_day_cnt;
            $remind_notice = $revisitconfig->remind_notice;
            $issend_miss = $revisitconfig->remind_issend_miss;

            $today = date('Y-m-d 12:00:00');
            $day_from = date('Y-m-d 12:00:00', time() + 3600 * 24 * $pre_day_cnt);
            $day_to = date('Y-m-d 12:00:00', time() + 3600 * 24 * ($pre_day_cnt + 1));

            $cond = " and doctorid = :doctorid and status=1 and auditstatus=1
            and isclosed=0 and send_cnt=0 and thedate < :day_to ";

            $bind = [];
            $bind[':doctorid'] = $doctorid;
            $bind[':day_to'] = $day_to;

            if (0 == $issend_miss) {
                $cond .= "and thedate > :day_from  ";
                $bind[':day_from'] = $day_from;
            } else {
                $cond .= "and thedate > :today  ";
                $bind[':today'] = $today;
            }

            $revisittkts = Dao::getEntityListByCond('RevisitTkt', $cond, $bind);

            $unitofwork = BeanFinder::get("UnitOfWork");

            foreach ($revisittkts as $revisittkt) {
                $doctor = $revisittkt->doctor;
                $patient = $revisittkt->patient;

                echo "\n[{$revisittkt->id}] [{$doctor->name}] [{$patient->name}] [{$revisittkt->thedate}]";
                $revisittkt->send_cnt = 1;

                if (false == $patient instanceof Patient) {
                    echo "\n[{$revisittkt->id}] patient is null";
                    continue;
                }

                if (false == $revisittkt->schedule instanceof Schedule) {
                    echo "\n[{$revisittkt->id}] schedule is null";
                    continue;
                }

                $pcard = $patient->getPcardByDoctorOrMasterPcard($doctor);

                $content = MsgContentService::transform4RevisitTkt($remind_notice, $revisittkt);

                PushMsgService::sendTxtMsgToWxUsersOfPcardBySystem($pcard, $content);
            }

            $unitofwork->commitAndInit();
        }
    }
}

// //////////////////////////////////////////////////////

$process = new Revisittkt_Remind_Patient(__FILE__);
$process->dowork();
