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

/**
 * 【业务自动化】【姑息、晚期化疗后患者】【自动创建定期随访】【自动创建复诊提醒】
 * 1 从分组之日起每2周生成1次 跟进任务（类型：定期随访）
 * 2 从分组之日起每3个月成1次 跟进任务（类型：复诊提醒）
 *
 * @author fhw
 *
 */
class Chemo_create_optask extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 23:10,入组姑息、晚期化疗的患者自动创建定期随访，复诊提醒';
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
        $ids = $this->getPatientIds();
        foreach ($ids as $id) {
            // 创建定期随访
            $this->createRegularFollow($id);

            // 创建复诊提醒
            $this->createRevisitTktNotice($id);
        }
    }

    private function createRevisitTktNotice ($patientid) {
        $patient = Patient::getById($patientid);

        $revisittkt_notice_optask = OpTaskDao::getOneByPatientUnicode($patient, 'follow:revisittkt_notice', false);

        if ((false == $revisittkt_notice_optask instanceof OpTask) ||
                 ($revisittkt_notice_optask instanceof OpTask && $revisittkt_notice_optask->getPlanDate() == date('Y-m-d'))) {

            $plantime = date('Y-m-d', strtotime("+1 months", time()));

            // 生成任务: 跟进[复诊提醒]
            // 先写死赖雪梅，不然不知道给谁
            $optask = OpTaskService::createPatientOpTask($patient, 'follow:revisittkt_notice', $patient, $plantime, $auditorid = 10048);
        }
    }

    // 定期随访，任务
    private function createRegularFollow ($patientid) {
        $patient = Patient::getById($patientid);

        $regular_follow_optask = OpTaskDao::getOneByPatientUnicode($patient, 'follow:regular_follow', false);

        if ((false == $regular_follow_optask instanceof OpTask) ||
                ($regular_follow_optask instanceof OpTask && $regular_follow_optask->getPlanDate() == date('Y-m-d'))) {

            $plantime = date('Y-m-d', strtotime("+2 weeks", time()));

            // 生成任务: 跟进[定期随访]
            // 先写死赖雪梅，不然不知道给谁
            OpTaskService::createPatientOpTask($patient, 'follow:regular_follow', $patient, $plantime, $auditorid = 10048);
        }
    }

    // 获取进入姑息、晚期化疗后患者的患者 regular
    private function getPatientIds () {
        $sql = "select distinct b.id as patientid
                from tagrefs a
                inner join patients b on b.id = a.objid
                inner join pcards c on c.patientid = b.id
                where a.objtype = 'Patient' and a.tagid in (150,152) and c.diseaseid = 8 ";
        $ids = Dao::queryValues($sql);

        return $ids;
    }
}

$process = new Chemo_create_optask(__FILE__);
$process->dowork();
