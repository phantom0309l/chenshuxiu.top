<?php

// 住院预约审核任务
class OpTaskTpl_audit_bedtkt extends OpTaskTplBase
{

    // 钩子实现: to_doctor_apply, 提交给医生审核
    public static function to_doctor_apply (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 发送消息给医生
        $bedtkt = $optask->obj;
        if (false == $bedtkt instanceof BedTkt) {
            Debug::warn("住院预约审核任务OpTask:[{$optask->id}]对应的obj[BedTkt] is null");
            return;
        }

        // 运营通过状态
        $bedtkt->setAuditorPassStatus();

        $myauditor = Auditor::getById($auditorid);
        $doctor = $bedtkt->doctor;

        $dwx_uri = Config::getConfig("dwx_uri");
        // MARK: - #3785
        $url = $dwx_uri . "/#/bedtkt/apply/{$bedtkt->id}";

        $date = date('Y-m-d');
        $first = array(
            "value" => "于{$date}向医生提交申请",
            "color" => "#009900");

        $keywords = array(
            array(
                "value" => $bedtkt->patient->name,
                "color" => ""),
            array(
                "value" => $bedtkt->plan_date,
                "color" => ""));
        $content = WxTemplateService::createTemplateContent($first, $keywords);

        Dwx_kefuMsgService::sendTplMsgToDoctorByAuditor($doctor, $myauditor, "BedTktAuditNotice", $content, $url);

        // #4130, 协和风湿免疫科, 也发给 王迁 一份
        if ($doctor->id == 1294) {
            $doctor_fix = Doctor::getById(32);
            Dwx_kefuMsgService::sendTplMsgToDoctorByAuditor($doctor_fix, $myauditor, "BedTktAuditNotice", $content, $url);
        }
    }

    // 钩子实现: to_audit_pass, 通过(关闭)
    public static function to_audit_pass (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);

        // 发送消息给患者
        $bedtkt = $optask->obj;
        if (false == $bedtkt instanceof BedTkt) {
            Debug::warn("住院预约审核任务OpTask:[{$optask->id}]对应的obj[BedTkt] is null");
            return;
        }

        // 运营通过状态
        $bedtkt->setAuditorPassStatus();

        $myauditor = Auditor::getById($auditorid);

        $bedtkt->audit_time = date('Y-m-d H:i:s', time());
        $logcontent = "医助审核通过患者的住院预约\n医助：{$myauditor->name}\n应住院日期：{$bedtkt->plan_date}";
        $bedtkt->saveLog('auditor_pass', $logcontent, $myauditor->id);

        $doctor = $bedtkt->doctor;

        DBC::requireNotEmpty($bedtkt->typestr, "{$bedtkt->id} typestr 为空");
        $bedtktconfig = BedTktConfigDao::getByDoctoridType($bedtkt->doctorid, $bedtkt->typestr);
        DBC::requireNotEmpty($bedtktconfig, "{$bedtkt->doctor->name} 没有配置住院预约 {$bedtkt->typestr}");
        DBC::requireTrue($bedtktconfig->is_allow_bedtkt == 1, "{$bedtkt->doctor->name} 没有开启住院预约 {$bedtkt->typestr}");

        $doctorconfig = $doctor->getConfigByCode('bedtkt_audit_pass');

        if ($doctorconfig->status) {
            $dwx_uri = Config::getConfig("dwx_uri");
            // MARK: - #3785
            $url = $dwx_uri . "/#/bedtkt/list?bedtktid={$bedtkt->id}&sex={$bedtkt->patient->sex}";

            $first = array(
                "value" => "新有患者申请住院，可点击详情进行处理",
                "color" => "#009900");

            $keywords = array(
                array(
                    "value" => $bedtkt->patient->name,
                    "color" => ""),
                array(
                    "value" => $bedtkt->plan_date,
                    "color" => ""));
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            Dwx_kefuMsgService::sendTplMsgToDoctorByAuditor($doctor, $myauditor, "BedTktAuditNotice", $content, $url);

            // #4130, 协和风湿免疫科, 也发给 王迁 一份
            if ($doctor->id == 1294) {
                $doctor_fix = Doctor::getById(32);
                Dwx_kefuMsgService::sendTplMsgToDoctorByAuditor($doctor_fix, $myauditor, "BedTktAuditNotice", $content, $url);
            }
        }

        // 向患者发消息
        self::send_pass_msg($bedtkt, $auditorid);
    }

    // 钩子实现: to_audit_refuse, 拒绝(关闭)
    public static function to_audit_refuse (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);

        // 发送消息给患者
        $bedtkt = $optask->obj;
        if (false == $bedtkt instanceof BedTkt) {
            Debug::warn("住院预约审核任务OpTask:[{$optask->id}]对应的obj[BedTkt] is null");
            return;
        }

        // 运营拒绝状态
        $bedtkt->setAuditorRefuseStatus();

        // bedtkt进入运营拒绝状态
        $myauditor = Auditor::getById($auditorid);
        $bedtkt->audit_time = date('Y-m-d H:i:s', time());
        $logcontent = "住院预约医助审核不通过\n医助：{$myauditor->name}\n";
        $bedtkt->saveLog('auditor_refuse', $logcontent, $myauditor->id);

        // 向患者发消息
        self::send_refuse_msg($bedtkt, $auditorid);
    }

    // 钩子实现: to_doctor_pass, 医生通过(关闭)
    public static function to_doctor_pass (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 关闭任务
        OpTaskStatusService::changeStatus($optask, 1, $auditorid);

        // 医生资料审核通过，进入待审核状态
        $bedtkt = $optask->obj;
        if (false == $bedtkt instanceof BedTkt) {
            Debug::warn("住院预约审核任务OpTask:[{$optask->id}]对应的obj[BedTkt] is null");
            return;
        }

        // 医生通过状态
        $bedtkt->setDoctorPassStatus();

        // 向患者发消息
        self::send_pass_msg($bedtkt, $auditorid);
    }

    // 钩子实现: to_doctor_refuse, 医生拒绝(关闭)
    public static function to_doctor_refuse (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {

        // 医生资料审核通过，进入待审核状态
        $bedtkt = $optask->obj;
        if (false == $bedtkt instanceof BedTkt) {
            Debug::warn("住院预约审核任务OpTask:[{$optask->id}]对应的obj[BedTkt] is null");
            return;
        }

        // 医生拒绝状态
        $bedtkt->setDoctorRefuseStatus();

        // bedtkt进入运营拒绝状态
        $myauditor = Auditor::getById($auditorid);
        $bedtkt->audit_time = date('Y-m-d H:i:s', time());
        $logcontent = "医生审核不通过\n医生：{$bedtkt->doctor->name}\n";
        $bedtkt->saveLog('auditor_refuse', $logcontent, $myauditor->id);

        // 向患者发消息
        self::send_refuse_msg($bedtkt, $auditorid);
    }

    // send_pass_msg 向患者发消息
    private static function send_pass_msg (BedTkt $bedtkt, $auditorid = 0) {
        $wxuser = $bedtkt->wxuser;
        $doctor = $bedtkt->doctor;
        $myauditor = Auditor::getById($auditorid);

        DBC::requireNotEmpty($bedtkt->typestr, "{$bedtkt->id} typestr 为空");
        $bedtktconfig = BedTktConfigDao::getByDoctoridType($bedtkt->doctorid, $bedtkt->typestr);
        DBC::requireNotEmpty($bedtktconfig, "{$bedtkt->doctor->name} 没有配置住院预约 {$bedtkt->typestr}");
        DBC::requireTrue($bedtktconfig->is_allow_bedtkt == 1, "{$bedtkt->doctor->name} 没有开启住院预约 {$bedtkt->typestr}");

        $config_content = json_decode($bedtktconfig->content, true);

        if ($config_content['is_auditpass_notice_open'] == 1) {
            // 发通知
            $first = array(
                "value" => "住院申请通过",
                "color" => "");
            $keyword2 = $config_content['auditpass_notice_content'] ? $config_content['auditpass_notice_content'] : "您的住院申请已通过审核，请保持电话畅通会有医生与您联系";

            $keywords = array(
                array(
                    "value" => "{$doctor->name}",
                    "color" => "#ff6600"),
                array(
                    "value" => $keyword2,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToWxUserByAuditor($wxuser, $myauditor, "doctornotice", $content);
        }
    }

    // send_refuse_msg 向患者发消息
    private static function send_refuse_msg (BedTkt $bedtkt, $auditorid = 0) {
        $wxuser = $bedtkt->wxuser;
        $doctor = $bedtkt->doctor;
        $myauditor = Auditor::getById($auditorid);

        DBC::requireNotEmpty($bedtkt->typestr, "{$bedtkt->id} typestr 为空");
        $bedtktconfig = BedTktConfigDao::getByDoctoridType($bedtkt->doctorid, $bedtkt->typestr);
        DBC::requireNotEmpty($bedtktconfig, "{$bedtkt->doctor->name} 没有配置住院预约 {$bedtkt->typestr}");
        DBC::requireTrue($bedtktconfig->is_allow_bedtkt == 1, "{$bedtkt->doctor->name} 没有开启住院预约 {$bedtkt->typestr}");

        $config_content = json_decode($bedtktconfig->content, true);

        if ($config_content['is_auditrefuse_notice_open'] == 1) {
            // 发通知
            $first = array(
                "value" => "住院申请被拒绝",
                "color" => "");
            $keyword2 = $config_content['auditrefuse_notice_content'] ? $config_content['auditrefuse_notice_content'] : "您的住院申请未通过，如有问题请与我们联系";

            $keywords = array(
                array(
                    "value" => "{$bedtkt->doctor->name}",
                    "color" => "#ff6600"),
                array(
                    "value" => $keyword2,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToWxUserByAuditor($wxuser, $myauditor, "doctornotice", $content);
        }
    }
}
