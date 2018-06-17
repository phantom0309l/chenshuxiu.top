<?php

include_once (dirname(__FILE__) . '/autoload.php');

class MiPush
{

    public static function pushMessage ($doctorid, $diseaseid, $patientid, $patientname, $pipeid) {

        Debug::warn("========== pushMessage begin ==========");

        $secret = 'giq6eXRzqwO065FkHoaKYw==';
        $package = 'com.fcqx.fcdoctor';

        Constants::setPackage($package);
        Constants::setSecret($secret);

        $title = '方寸医生';
        $desc = '您收到一条来自方寸医生的消息';
        $payload = '{"diseaseid":' . $diseaseid . ',"patientid":' . $patientid . ',"patientname":' . $patientname . ',"pipeid":' . $pipeid . '}';

        // 推送设置参数
        $message1 = new Builder();
        $message1->title($title);
        $message1->description($desc);
        $message1->passThrough(0);
        $message1->payload($payload);
        $message1->extra(Builder::notifyForeground, 0);
        $message1->notifyType(1);
        $message1->notifyId(1);
        $message1->build();

        // $targetMessage = new TargetedMessage();
        // $targetMessage->setTarget('3',
        // TargetedMessage::TARGET_TYPE_USER_ACCOUNT);
        // $targetMessage->setMessage($message1);

        // $targetMessageList = array(
        // $targetMessage);

        Debug::warn("========== sendToUserAccount begin ==========");
        $sender = new Sender();
        $result = $sender->sendToUserAccount($message1, "$doctorid", 2);

        Debug::warn(json_encode($result), true);
        Debug::warn("========== sendToUserAccount end ==========");

        $str = "";
        if ($result->getErrorCode() == ErrorCode::Success) {
            $str = "success";
        } else {
            $str = "error";
        }

        Debug::warn("========== pushMessage end [{$str}] ==========");

        return $str;
    }
}

