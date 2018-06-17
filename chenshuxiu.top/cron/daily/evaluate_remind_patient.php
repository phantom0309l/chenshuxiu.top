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

class Evaluate_Remind_Patient extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 09:57 每天 发送 评估提醒';
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

        $today = date('Y-m-d');
        $day_6 = date('Y-m-d 12:00:00', time() + 3600 * 24 * 6);
        $day_7 = date('Y-m-d 12:00:00', time() + 3600 * 24 * 7);

        $diseaseid = 2;

        $sql = "select r.* from revisittkts r
                join patients pa on pa.id=r.patientid
                join pcards pc on pa.id=pc.patientid
                where pc.diseaseid = :diseaseid
                and r.status=1 and r.auditstatus=1 and r.isclosed=0
                and r.thedate > :fromdate and r.thedate < :todate ";

        $bind = [];
        $bind[':diseaseid'] = $diseaseid;
        $bind[':fromdate'] = $day_6;
        $bind[':todate'] = $day_7;

        $revisittkts = Dao::loadEntityList( 'RevisitTkt', $sql, $bind);

        foreach( $revisittkts as $revisittkt ){
            $patient = $revisittkt->patient;
            if( $patient instanceof Patient ){

                // 20170419 TODO by sjp : 考虑重构, 为何只发给最新扫码的pcard ?
                $pcard = $patient->getOnePcardByDiseaseid($diseaseid);

                echo "\n" . $patient->name ;
                $wx_uri = Config::getConfig("wx_uri");
                $url = $wx_uri . "/paper/index?i=1";

                $first = array(
                    "value" => "评估提醒",
                    "color" => "");
                $keyword2 = "{$patient->name}您好，请点击微信下【我要】【做评估】把评估里的全部问卷填写一下。这些问卷每次复诊前填写一次，是反映您病情变化的一项指标，医生复诊调整治疗方案需要用到，请及时填写。";

                $keywords = array(
                    array(
                        "value" => "{$patient->getMasterDoctor()->name}",
                        "color" => "#ff6600"),
                    array(
                        "value" => $keyword2,
                        "color" => "#ff6600"));
                $content = WxTemplateService::createTemplateContent($first, $keywords);
                if($pcard instanceof Pcard){
                    PushMsgService::sendTplMsgToWxUsersOfPcardBySystem($pcard, "doctornotice", $content, $url);
                }
            }

        }

        $unitofwork->commitAndInit();
    }

}

// //////////////////////////////////////////////////////

$process = new Evaluate_Remind_Patient(__FILE__);
$process->dowork();
