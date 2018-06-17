<?php

class Dwx_kefuMsgService extends MsgBase
{

    // 系统发送 文本消息 至医生微信端
    public static function sendTxtMsgToDoctorBySystem (Doctor $doctor, $content, $appendarr = array()) {
        return self::sendTxtMsgToDoctorByAuditor($doctor, Auditor::getSystemAuditor(), $content, $appendarr);
    }

    // Auditor发送 文本消息 至医生微信端
    public static function sendTxtMsgToDoctorByAuditor (Doctor $doctor, Auditor $auditor, $content, $appendarr = array()) {
        $appendarr["doctorid"] = $doctor->id;
        $appendarr["auditorid"] = $auditor->id;
        $wxusers = WxUserDao::getListByUserIdAndWxShopId($doctor->userid, WxShop::WxShopId_Doctor);

        foreach ($wxusers as $wxuser) {
            WechatMsg::sendmsg2wxuser_dwx($wxuser, $content, $appendarr);
        }
    }

    // 系统发送 模板消息 至医生微信端
    public static function sendTplMsgToDoctorBySystem (Doctor $doctor, $ename, $content, $url = "", $appendarr = array ()) {
        return self::sendTplMsgToDoctorByAuditor($doctor, Auditor::getSystemAuditor(), $ename, $content, $url, $appendarr);
    }

    // Auditor发送 模板消息 至医生微信端
    public static function sendTplMsgToDoctorByAuditor (Doctor $doctor, Auditor $auditor, $ename, $content, $url = "", $appendarr = array ()) {
        $appendarr["doctorid"] = $doctor->id;
        $appendarr["auditorid"] = $auditor->id;
        $wxtemplate = WxTemplateDao::getByEname(WxShop::WxShopId_Doctor, $ename);
        $tplid = $wxtemplate->code;
        if (empty($tplid)) {
            return null;
        }

        $wxusers = WxUserDao::getListByUserIdAndWxShopId($doctor->userid, WxShop::WxShopId_Doctor);
        foreach ($wxusers as $wxuser) {
            WechatMsg::sendtemplate2wxuser_dwx($wxuser, $tplid, $content, $appendarr, $url);
        }
    }

}
