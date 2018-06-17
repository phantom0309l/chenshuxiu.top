<?php

/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/9/12
 * Time: 13:09
 */
class RevisitTktService
{

    public static function auditPass (Auditor $auditor, RevisitTkt $revisittkt) {
        $revisittkt->pass();
        $revisittkt->set4lock('auditorid', $auditor->id);
        if ($auditor->id == 1) {
            $revisittkt->auditremark = "系统自动通过";
        }

        // 生成任务: 复诊预约提醒
        OpTaskService::createOpTask_remind_RevisitTkt($revisittkt);

        $revisittkt_d = RevisitTktDao::getLastOfPatient_Open($revisittkt->patientid, $revisittkt->doctorid, 'Doctor');
        if ($revisittkt_d instanceof RevisitTkt) {
            $revisittkt_d->isclosed = 1;
            $revisittkt_d->closeby = 'Auditor';
            $revisittkt_d->status = 0;
        }

        $wxuser = $revisittkt->wxuser;
        if ($wxuser instanceof WxUser) {

            $first = array(
                "value" => "复诊预约审核通过",
                "color" => "");
            $arr = array(
                '#revisittkt_time#' => $revisittkt->thedate);

            // 复诊预约的医生
            $arr['doctor_entity'] = $revisittkt->doctor;

            $keyword2 = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'success_revisittkt', $arr);

            $keywords = array(
                array(
                    "value" => "{$revisittkt->doctor->name}医生随访团队",
                    "color" => "#ff6600"),
                array(
                    "value" => $keyword2,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToWxUserByAuditor($wxuser, $auditor, "doctornotice", $content);
        }

        $pcard = $revisittkt->patient->getMasterPcard();

        if (! $pcard->out_case_no) {
            $pcard->out_case_no = $revisittkt->out_case_no;
        }

        if (! $pcard->patientcardno) {
            $pcard->patientcardno = $revisittkt->patientcardno;
        }

        if (! $pcard->patientcard_id) {
            $pcard->patientcard_id = $revisittkt->patientcard_id;
        }

        if (! $pcard->bingan_no) {
            $pcard->bingan_no = $revisittkt->bingan_no;
        }

        $doctor = $revisittkt->doctor;
        $doctorconfig = $doctor->getConfigByCode('revisittkt_audit_pass');

        if ($doctorconfig->status) {
            $first = array(
                "value" => "患者复诊预约已通过请查阅：",
                "color" => "");

            $keywords = array(
                array(
                    "value" => $revisittkt->patient->name,
                    "color" => ""),
                array(
                    "value" => $revisittkt->patient->getMasterMobile(),
                    "color" => ""),
                array(
                    "value" => $revisittkt->thedate,
                    "color" => ""));
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            Dwx_kefuMsgService::sendTplMsgToDoctorByAuditor($doctor, $auditor, "RevisitTktRemind", $content);

            // #4130, 协和风湿免疫科, 也发给 王迁 一份
            if ($doctor->id == 1294) {
                $doctor_fix = Doctor::getById(32);
                Dwx_kefuMsgService::sendTplMsgToDoctorByAuditor($doctor_fix, $auditor, "RevisitTktRemind", $content);
            }
        }

        // 王迁特殊需求 ILD提前发送量表
        if ($revisittkt->doctorid == 32 && $revisittkt->schedule->diseaseid == 2) {

            $today = date('Y-m-d');
            $thedate = $revisittkt->thedate;

            // 提前的天数, 舍去小数
            $offset_daycnt = floor((strtotime($thedate) - strtotime($today)) / 86400);

            // 复诊前1天或2天, 或 复诊前第3天中午12点跑脚本后, 脚本12点整跑，有等于
            if ((3 > $offset_daycnt) || (3 == $offset_daycnt && date('G', time()) >= 12)) {

                $wx_uri = XContext::getValue('wx_uri');
                $url = "{$wx_uri}/paper/wenzhen/?papertplid=274498246";

                $wxuser = $revisittkt->wxuser;
                if (false == $wxuser instanceof WxUser) {
                    $wxuser = $revisittkt->patient->getMasterWxUser(6);
                }

                $first = array(
                    "value" => '复诊已做检查项目明确',
                    "color" => "#ff6600");
                $keywords = array(
                    array(
                        "value" => "{$revisittkt->doctor->name}医生随访团队",
                        "color" => "#999999"),
                    array(
                        "value" => '在您复诊前需要对您的检查项目进行了解与明确。请您尽快填写。',
                        "color" => "#ff6600"));

                $content = WxTemplateService::createTemplateContent($first, $keywords);

                PushMsgService::sendTplMsgToWxUserBySystem($wxuser, 'doctornotice', $content, $url);
            }
        }
    }
}
