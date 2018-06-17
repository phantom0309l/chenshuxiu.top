<?php

class WechatMsg extends MsgBase
{

    public static function sendmsg_asyn ($userid, $wxuserid, $patientid, $doctorid, $content, $appendarr = array()) {
        if(false == WechatMsg::checkIsNeedSend($patientid)){
            return null;
        }

        $row = array(
            "wxuserid" => $wxuserid,
            "userid" => $userid,
            "patientid" => $patientid,
            "doctorid" => $doctorid,
            "sendway" => "wechat_custom",
            "content" => $content);
        $row += $appendarr;
        $row += parent::$defaultsrcarr;
        $pushMsg = PushMsg::createByBiz($row);
        if (false == XContext::getValueEx('sendOpsTxtMessage', false)) {
            if ($pushMsg instanceof PushMsg) {
                $objcode = $pushMsg->getCodeForPipe();
                Pipe::createByEntity($pushMsg, $objcode, $wxuserid);
            }
        }
        return $pushMsg;
    }

    public static function sendmsg2wxuser (WxUser $wxuser, $content, $appendarr = array ()) {
        $user = $wxuser->user;
        $userid = ($user instanceof User) ? $user->id : 0;
        $patientid = 0;
        $doctorid = 0;
        if (($user instanceof User) and ($user->patient instanceof Patient)) {
            $patientid = $user->patient->id;

            // 最次 patient->doctorid
            $doctorid = $user->patient->doctorid;
        }

        // 次之 wxuser->doctorid
        if ($wxuser->doctorid > 0) {
            $doctorid = $wxuser->doctorid;
        }

        // 优先 参数doctorid
        if (isset($appendarr['doctorid'])) {
            $doctorid = $appendarr['doctorid'];
        }

        return self::sendmsg_asyn($userid, $wxuser->id, $patientid, $doctorid, $content, $appendarr);
    }

    public static function sendtemplate_asyn ($userid, $wxuserid, $patientid, $doctorid, $templatename, $content, $appendarr = array (), $url = "") {
        if(false == WechatMsg::checkIsNeedSend($patientid)){
            return null;
        }

        $row = array(
            "wxuserid" => $wxuserid,
            "userid" => $userid,
            "patientid" => $patientid,
            "doctorid" => $doctorid,
            "sendway" => "wechat_template",
            "template_name" => $templatename,
            "content" => $content,
            "remark" => $url);
        $row += $appendarr;
        $row += parent::$defaultsrcarr;
        $pushMsg = PushMsg::createByBiz($row);
        if (false == XContext::getValueEx('sendOpsTxtMessage', false)) {
            if ($pushMsg instanceof PushMsg) {
                $objcode = $pushMsg->getCodeForPipe();
                Pipe::createByEntity($pushMsg, $objcode, $wxuserid);
            }
        }
        return $pushMsg;
    }

    public static function sendtemplate2wxuser (WxUser $wxuser, $templatename, $content, $appendarr = array (), $url = "") {
        $user = $wxuser->user;
        $userid = ($user instanceof User) ? $user->id : 0;
        $patientid = 0;
        $doctorid = 0;
        $url = self::addOpenid($url, $wxuser);
        if (($user instanceof User) and ($user->patient instanceof Patient)) {
            $patientid = $user->patient->id;

            // 最次 patient->doctorid
            $doctorid = $user->patient->doctorid;
        }

        // 次之 wxuser->doctorid
        if ($wxuser->doctorid > 0) {
            $doctorid = $wxuser->doctorid;
        }

        // 优先 参数doctorid
        if (isset($appendarr['doctorid'])) {
            $doctorid = $appendarr['doctorid'];
        }

        return self::sendtemplate_asyn($userid, $wxuser->id, $patientid, $doctorid, $templatename, $content, $appendarr, $url);
    }

    // 是否需要发送
    private static function checkIsNeedSend ($patientid) {
        $patient = Patient::getById($patientid);
        $is_filter_blacklist = XContext::getValueEx('is_filter_blacklist', true);
        if ($is_filter_blacklist) {
            if($patient instanceof Patient && $patient->isOnTheBlackList()){
                return false;
            }
        }

        $is_filter_doubtlist = XContext::getValueEx('is_filter_doubtlist', true);
        if ($is_filter_doubtlist) {
            if($patient instanceof Patient && $patient->isDoubt()){
                return false;
            }
        }
        return true;
    }

    // dwx 使用的
    public static function sendmsg_asyn_dwx ($userid, $wxuserid, $doctorid, $content, $appendarr = array()) {
        $row = array(
            "wxuserid" => $wxuserid,
            "userid" => $userid,
            "doctorid" => $doctorid,
            "content" => $content);
        $row += $appendarr;
        $row += parent::$defaultsrcarr;
        return Dwx_kefumsg::createByBiz($row);
    }

    public static function sendmsg2wxuser_dwx (WxUser $wxuser, $content, $appendarr = array ()) {
        $user = $wxuser->user;
        $userid = ($user instanceof User) ? $user->id : 0;
        $doctorid = 0;
        if (($user instanceof User) and ($user->isDoctor())) {
            $doctorid = $user->getDoctor()->id;
        }
        return self::sendmsg_asyn_dwx($userid, $wxuser->id, $doctorid, $content, $appendarr);
    }

    public static function sendtemplate_asyn_dwx ($userid, $wxuserid, $doctorid, $templatename, $content, $appendarr = array (), $url = "") {
        $row = array(
            "wxuserid" => $wxuserid,
            "userid" => $userid,
            "doctorid" => $doctorid,
            "template_name" => $templatename,
            "content" => $content,
            "dest_url" => $url);
        $row += $appendarr;
        $row += parent::$defaultsrcarr;
        return Dwx_kefumsg::createByBiz($row);
    }

    public static function sendtemplate2wxuser_dwx (WxUser $wxuser, $templatename, $content, $appendarr = array (), $url = "") {
        $user = $wxuser->user;
        $userid = ($user instanceof User) ? $user->id : 0;
        $doctorid = 0;
        $url = self::addOpenid($url, $wxuser);
        if (($user instanceof User) and ($user->isDoctor())) {
            $doctorid = $user->getDoctor()->id;
        }
        return self::sendtemplate_asyn_dwx($userid, $wxuser->id, $doctorid, $templatename, $content, $appendarr, $url);
    }

    // 拼接 openid
    private static function addOpenid ($url, $wxuser) {
        if ($url != "") {
            if (preg_match("/\?/i", $url)) {
                $url .= "&openid={$wxuser->openid}";
            } else {
                $url .= "?openid={$wxuser->openid}";
            }
        }
        return $url;
    }
}
