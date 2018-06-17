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

class Revisittkt_Remind_Patient_Xuyan_VitD extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 10:05 每天 发送 预约复诊提醒 徐雁 vitD检查';
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
        $today = date( 'Y-m-d 12:00:00' );
        $day_from = date('Y-m-d 12:00:00', time() + 3600 * 24 * 35);
        $day_to = date('Y-m-d 12:00:00', time() + 3600 * 24 * 36);

        $cond = " and doctorid =33 and status=1 and auditstatus=1
        and isclosed=0 and send_cnt=0 and thedate < :day_to and thedate > :day_from
        and checkuptplids like '%105454221%' ";

        $bind = [];
        $bind[':day_to'] = $day_to;
        $bind[':day_from'] = $day_from;

        $revisittkts = Dao::getEntityListByCond('RevisitTkt', $cond, $bind);

        $unitofwork = BeanFinder::get("UnitOfWork");

        foreach ( $revisittkts as $revisittkt ) {
            $patient = $revisittkt->patient;
            $pcard = $patient->getPcardByDoctorOrMasterPcard($revisittkt->doctor);

            echo "\n[{$patient->name}] [{$revisittkt->thedate}]";

            if (false == $revisittkt->schedule instanceof Schedule) {
                continue;
            }
            $content = "本次医生给您开的复诊检查中有一项检查（VitD）需要31天才能出结果，所以您需要提前一个月去协和医院完成所有检查，之后带着本次所有检查的结果去门诊复诊；
            以便您本次复诊时医生查看您的检查资料，评估您的病情并给您治疗建议。";

            PushMsgService::sendTxtMsgToWxUsersOfPcardBySystem($pcard, $content);
        }

        $unitofwork->commitAndInit();

    }

}

// //////////////////////////////////////////////////////

$process = new Revisittkt_Remind_Patient_Xuyan_VitD(__FILE__);
$process->dowork();
