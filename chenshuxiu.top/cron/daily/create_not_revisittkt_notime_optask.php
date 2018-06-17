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

/*
 * #4731 业务自动化-未如约复诊
 */
class Create_not_revisittkt_notime_optask extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天 00:30, 检查门诊后2天到门诊日期前7天,系统是否有患者的门诊记录。如果没有门诊记录则生成【未如约复诊任务】。';
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

    protected function doworkImp () {
        $revisittktids = $this->getRevisitTktIds();

        foreach ($revisittktids as $id) {
            $revisittkt = RevisitTkt::getById($id);
            $patient = $revisittkt->patient;
            if ($patient instanceof Patient) {

                $arr = [];
                $arr['content'] = "患者未如约复诊,理应复诊时间：{$revisittkt->thedate},医生：{$revisittkt->doctor->name} ";

                // 生成任务: 未如约复诊跟进任务
                $optask = OpTaskService::createPatientOpTask($patient, 'not_ontime_revisittkt:RevisitTkt', $revisittkt, $plantime = '', $auditorid = 1, $arr);

                echo "\n{$patient->disease->name} {$patient->name} {$optask->id}\n";
            }
        }
    }

    private function getRevisitTktIds () {
        $two_befrom_date = date('Y-m-d', strtotime("-3 days", time()));
        $sql = "select DISTINCT a.id
                from revisittkts a
                inner join pcards b on b.patientid = a.patientid
                where a.thedate = '{$two_befrom_date}' and b.diseaseid in (2,3,6,22) and a.isclosed=0 and a.status=1 ";
        $ids = Dao::queryValues($sql);

        $no_ids = [];

        // 从今天起的前面九天是否有门诊记录生成
        foreach ($ids as $id) {
            $nine_befrom_date = date('Y-m-d', strtotime("-11 days", time()));
            $today = date('Y-m-d');

            $revisittkt = RevisitTkt::getById($id);
            $sql = "select count(*)
                from revisitrecords
                where doctorid = {$revisittkt->doctorid} and patientid = {$revisittkt->patientid} and thedate > '{$nine_befrom_date}' and thedate < '{$today}' ";
            $cnt = Dao::queryValue($sql);

            if ($cnt <= 0) {
                $no_ids[] = $revisittkt->id;
            }
        }

        return $no_ids;
    }
}

$test = new Create_not_revisittkt_notime_optask(__FILE__);
//$test->dowork();
