<?php

class PushMsgService extends MsgBase
{
    // Doctor 发送 文本消息 ToWxUser
    public static function sendTxtMsgToWxUserByDoctor (WxUser $wxuser, Doctor $doctor, $content, $appendarr = array()) {
        $appendarr = self::setSendByObj($doctor, $appendarr);

        $appendarr['doctorid'] = $doctor->id;

        return WechatMsg::sendmsg2wxuser($wxuser, $content, $appendarr);
    }

    // Doctor 发送 文本消息 ToPatient (患者关注当前医生的所有微信用户)
    // (解决不了 杨莉给王玉凤的患者)
    public static function sendTxtMsgToPatientByDoctor (Patient $patient, Doctor $doctor, $content, $appendarr = array()) {
        $pcard = $doctor->getPcardByPatientid($patient->id);
        $wxusers = $pcard->getWxUsers();

        $appendarr['doctorid'] = $doctor->id;

        foreach ($wxusers as $wxuser) {
            self::sendTxtMsgToWxUserByDoctor($wxuser, $doctor, $content, $appendarr);
        }
    }

    // Doctor 发送 模板消息 ToWxUser
    public static function sendTplMsgToWxUserByDoctor (WxUser $wxuser, Doctor $doctor, $wxtemplate_ename, $content, $url = "", $appendarr = array ()) {
        $appendarr = self::setSendByObj($doctor, $appendarr);

        $tplid = WxTemplate::getTemplateid($wxuser, $wxtemplate_ename);
        Debug::trace("++++++++++ {$wxuser->id} {$wxuser->wxshopid} {$tplid} ++++++++++++++++");
        if (empty($tplid)) {
            return null;
        }

        $appendarr['doctorid'] = $doctor->id;

        return WechatMsg::sendtemplate2wxuser($wxuser, $tplid, $content, $appendarr, $url);
    }

    // Doctor 发送 模板消息 ToPatient (患者关注当前医生的所有微信用户)
    // (解决不了 杨莉给王玉凤的患者)
    public static function sendTplMsgToPatientByDoctor (Patient $patient, Doctor $doctor, $wxtemplate_ename, $content, $url = "", $appendarr = array ()) {
        $pcard = $doctor->getPcardByPatientid($patient->id);
        $wxusers = $pcard->getWxUsers();

        $appendarr['doctorid'] = $doctor->id;

        foreach ($wxusers as $wxuser) {
            self::sendTplMsgToWxUserByDoctor($wxuser, $doctor, $wxtemplate_ename, $content, $url, $appendarr);
        }
    }

    // Auditor 发送模板消息 ToPatient
    public static function sendTplMsgToPatientByAuditor (Patient $patient, Auditor $auditor, $wxtemplate_ename, $content, $url = "", $appendarr = array ()) {
        $pcard = $patient->getMasterPcard();
        $wxusers = $pcard->getWxUsers();

        foreach ($wxusers as $wxuser) {
            self::sendTplMsgToWxUserByAuditor($wxuser, $auditor, $wxtemplate_ename, $content, $url);
        }
    }

    // Auditor 发送 文本消息 ToWxUser
    public static function sendTxtMsgToWxUserByAuditor (WxUser $wxuser, Auditor $auditor, $content, $appendarr = array()) {
        $appendarr = self::setSendByObj($auditor, $appendarr);
        return WechatMsg::sendmsg2wxuser($wxuser, $content, $appendarr);
    }

    // Auditor 发送 文本消息 ToWxUsersOfPcard
    public static function sendTxtMsgToWxUsersOfPcardByAuditor (Pcard $pcard, Auditor $auditor, $content, $appendarr = array()) {
        $wxusers = $pcard->getWxUsers();

        $pushmsg = null;
        foreach ($wxusers as $wxuser) {
            $pushmsg = self::sendTxtMsgToWxUserByAuditor($wxuser, $auditor, $content, $appendarr) ?? $pushmsg;
        }

        return $pushmsg;
    }

    // Auditor 发送 模板消息 ToWxUser
    public static function sendTplMsgToWxUserByAuditor (WxUser $wxuser, Auditor $auditor, $wxtemplate_ename, $content, $url = "", $appendarr = array ()) {
        $appendarr = self::setSendByObj($auditor, $appendarr);

        $tplid = WxTemplate::getTemplateid($wxuser, $wxtemplate_ename);
        if (empty($tplid)) {
            return null;
        }

        return WechatMsg::sendtemplate2wxuser($wxuser, $tplid, $content, $appendarr, $url);
    }

    // Auditor 发送 模板消息 ToWxUsersOfPcard
    public static function sendTplMsgToWxUsersOfPcardByAuditor (Pcard $pcard, Auditor $auditor, $wxtemplate_ename, $content, $url = "", $appendarr = array ()) {
        $wxusers = $pcard->getWxUsers();

        foreach ($wxusers as $wxuser) {
            self::sendTplMsgToWxUserByAuditor($wxuser, $auditor, $wxtemplate_ename, $content, $url, $appendarr);
        }
    }

    // 系统发送 模板消息 ToPatient
    public static function sendTplMsgToPatientBySystem (Patient $patient, $wxtemplate_ename, $content, $url = "", $appendarr = array ()) {
        $wxusers = $patient->getWxUsers();

        $pushmsg = null;
        $return_pushmsh = null;
        foreach ($wxusers as $wxuser) {
            $pushmsg = self::sendTplMsgToWxUserBySystem($wxuser, $wxtemplate_ename, $content, $url);

            $return_pushmsh = $return_pushmsh ?? $pushmsg;
        }

        return $return_pushmsh;
    }

    // 系统发送 文本消息 ToPatient
    public static function sendTxtMsgToPatientBySystem (Patient $patient, $content, $appendarr = array()) {
        $pcard = $patient->getMasterPcard();
        $wxusers = $pcard->getWxUsers();

        foreach ($wxusers as $wxuser) {
            self::sendTxtMsgToWxUserBySystem($wxuser, $content, $appendarr);
        }
    }

    // 系统发送 文本消息 ToWxUser
    public static function sendTxtMsgToWxUserBySystem (WxUser $wxuser, $content, $appendarr = array()) {
        return self::sendTxtMsgToWxUserByAuditor($wxuser, Auditor::getSystemAuditor(), $content, $appendarr);
    }

    // 系统发送 文本消息 ToWxUsersOfPcard
    public static function sendTxtMsgToWxUsersOfPcardBySystem (Pcard $pcard, $content, $appendarr = array()) {
        return self::sendTxtMsgToWxUsersOfPcardByAuditor($pcard, Auditor::getSystemAuditor(), $content, $appendarr);
    }

    // 系统发送 模板消息 ToWxUser
    public static function sendTplMsgToWxUserBySystem (WxUser $wxuser, $wxtemplate_ename, $content, $url = "", $appendarr = array ()) {
        return self::sendTplMsgToWxUserByAuditor($wxuser, Auditor::getSystemAuditor(), $wxtemplate_ename, $content, $url, $appendarr);
    }

    // 系统发送 模板消息 ToWxUsersOfPcard
    public static function sendTplMsgToWxUsersOfPcardBySystem (Pcard $pcard, $wxtemplate_ename, $content, $url = "", $appendarr = array ()) {
        return self::sendTplMsgToWxUsersOfPcardByAuditor($pcard, Auditor::getSystemAuditor(), $wxtemplate_ename, $content, $url, $appendarr);
    }

    // 设置发送人
    private static function setSendByObj (Entity $entity, $appendarr) {
        $appendarr["send_by_objtype"] = get_class($entity);
        $appendarr["send_by_objid"] = $entity->id;
        return $appendarr;
    }

    public static function sendMsgToAuditorWithEnameBySystem($ename, $content, $appendarr = array()) {
        // 设置一个开关,发给运营的opstxt,不入 pushmsg 和 pipe
        XContext::setValue("sendOpsTxtMessage", true);

        $remark = $appendarr["remark"];
        if (isset($remark)) {
            $content .= "\n\n<a href=\"{$remark}\">≧︿≦ 详情 ≧︿≦</a>";
        }
        $auditorPushMsgTpl = AuditorPushMsgTplDao::getByEname($ename);

        if (false == $auditorPushMsgTpl instanceof AuditorPushMsgTpl) {
            Debug::warn("没有找到监控消息类型[ename:{$ename}]");
            return;
        }

        $auditorPushMsgTplRefs = AuditorPushMsgTplRefDao::getListByAuditorPushMsgTplIdAndCan_ops($auditorPushMsgTpl->id, 1);

        foreach ($auditorPushMsgTplRefs as $auditorPushMsgTplRef) {
            // 取到运营
            $auditor = $auditorPushMsgTplRef->auditor;
            if ($auditor->isLeave()) {
                continue;
            }
            $user = $auditor->user;
            // 员工离职记得删除
            $sms_users = [];
            if ($ename == 'QuickConsultOrder') {
                $sms_users = [
                    10009,  // 王宫瑜
                    10049,  // 郭文俊
                    10072,  // 王福生
                ];
            }
            // 发短信给主要负责人
            if (in_array($user->id, $sms_users)) {
                $auditor->user->sendsms($content);
            }

            $wxusers = $user->getWxUsers();
            foreach ($wxusers as $wxuser) {
                if ($wxuser->isOpsOpen()) {
                    $pushMsg = self::sendTxtMsgToWxUserBySystem($wxuser, $content, $appendarr);
                    if ($pushMsg instanceof PushMsg) {
                        $pushMsg->is_monitor_msg = 1; // 标记为监控消息
                    }
                }
            }
        }

        XContext::setValue("sendOpsTxtMessage", false);
    }

    public static function sendMsgToAuditorBySystem($ename, $wxshopid = 1, $content, $appendarr = array()) {
        // 设置一个开关,发给运营的opstxt,不入 pushmsg 和 pipe
        XContext::setValue("sendOpsTxtMessage", true);

        $remark = $appendarr["remark"];
        if (isset($remark)) {
            $content .= "\n\n<a href=\"{$remark}\">≧︿≦ 详情 ≧︿≦</a>";
        }
        $auditorPushMsgTpl = AuditorPushMsgTplDao::getByEname($ename);

        if(false == $auditorPushMsgTpl instanceof AuditorPushMsgTpl){
            Debug::warn("没有找到监控消息类型[ename:{$ename}]");
            return;
        }

        $auditorPushMsgTplRefs = AuditorPushMsgTplRefDao::getListByAuditorPushMsgTplIdAndCan_ops($auditorPushMsgTpl->id, 1);

        foreach ($auditorPushMsgTplRefs as $auditorPushMsgTplRef) {
            // 取到运营
            $auditor = $auditorPushMsgTplRef->auditor;
            if($auditor->isLeave()){
                continue;
            }
            $userid = $auditor->userid;
            $wxusers = WxUserDao::getListByUserIdAndWxShopId($userid, $wxshopid);
            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->isOpsOpen()) {
                    $pushMsg = self::sendTxtMsgToWxUserBySystem($wxuser, $content, $appendarr);
                    if ($pushMsg instanceof PushMsg) {
                        $pushMsg->is_monitor_msg = 1; // 标记为监控消息
                    }
                }
            }
        }

        XContext::setValue("sendOpsTxtMessage", false);
    }

    // 发送加入礼来项目后的菜单文案
    public static function sendTxtMsgWhenPassSunflower (WxUser $wxuser) {
        $patient = $wxuser->user->patient;
        if ($patient instanceof Patient) {
            $groupid = 134;
            $menu_text = WxMenu::getSerializedMenuText(1, $groupid, $wxuser);

            $pre_text = "下列为您的功能列表：\n";

            $after_text = "正在为您生成服务菜单，您可以于5分钟后再次进入『方寸儿童管理服务平台』微信公众号，或直接使用上述菜单。";

            $content = $pre_text . $menu_text . $after_text;
            self::sendTxtMsgToWxUserBySystem($wxuser, $content);
        } else {
            Debug::warn("没有patient，发送不了分层升级消息");
        }
    }

    // Auditor adminNotice或者followupNotice的模版 ToWxUser
    public static function sendNoticeToWxUserByAuditor (WxUser $wxuser, Auditor $auditor, $title, $content, $url, $appendarr = array ()) {
        $wxtemplate = $wxuser->wxshop->getWxTemplateOfAdminNoticeOrFollowupNotice();

        if(false == $wxtemplate instanceof WxTemplate){
            Debug::warn(__METHOD__ . "wxshopid[{$wxuser->wxshopid}]没有找到adminNotice或者followupNotice的模板");
            return null;
        }
        $content = $wxtemplate->getContentOfAdminNoticeOrFollowupNotice($wxuser->user->patient, $title, $content);
        return PushMsgService::sendTplMsgToWxUserByAuditor($wxuser, $auditor, $wxtemplate->ename, $content, $url, $appendarr);
    }

    // 系统发送 adminNotice或者followupNotice的模版 ToWxUser
    public static function sendNoticeToWxUserBySystem (WxUser $wxuser, $title, $content, $url, $appendarr = array ()) {
        $wxtemplate = $wxuser->wxshop->getWxTemplateOfAdminNoticeOrFollowupNotice();

        if(false == $wxtemplate instanceof WxTemplate){
            Debug::warn(__METHOD__ . "wxshopid[{$wxuser->wxshopid}]没有找到adminNotice或者followupNotice的模板");
            return null;
        }
        $content = $wxtemplate->getContentOfAdminNoticeOrFollowupNotice($wxuser->user->patient, $title, $content);
        return PushMsgService::sendTplMsgToWxUserBySystem($wxuser, $wxtemplate->ename, $content, $url, $appendarr);
    }

}
