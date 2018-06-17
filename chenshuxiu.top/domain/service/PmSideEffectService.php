<?php

class PmSideEffectService
{
    // 发送药物副反应检测
    public static function sendOpTaskPmRemid (OpTask $optask) {
        if (false == $optask->obj instanceof PmSideEffect) {
            Debug::warn("optaskid:{$optask->id} [failed][PmSideEffect不存在]");
            return false;
        }

        $patient = $optask->patient;
        if (false == $patient instanceof Patient) {
            Debug::warn("optask->patient is null");
            return false;
        }

        $pcard = $patient->getMasterPcard();
        if (false == $pcard instanceof Pcard) {
            Debug::warn("optask->patient->pcard is null");
            return false;
        }

        $pushcontent = self::getPushContent($pcard, $optask->obj->medicineid);
        if ($pushcontent == null) {
            Debug::warn("optaskid:{$optask->id} [failed][没有选择对应药物]");
            return false;
        }

        PushMsgService::sendTxtMsgToWxUsersOfPcardBySystem($pcard, $pushcontent);

        return true;
    }

    private static function getPushContent ($pcard, $medicineid) {
        $doctor = $pcard->doctor;
        $patient = $pcard->patient;
        if ($doctor->id == 32 || $pcard->diseaseid == 2) {
            switch ($medicineid) {
                case 72:
                case 73:
                case 74:
                    return '您好，您已经服用他克莫司30天，请近期到医院检测他克莫司血药浓度，为了检测数值准确，请在清晨服用药物之前抽血检查。之后请将结果上传微信平台。';
                    break;
                case 5:
                case 20:
                    return '您好，您目前正在服用硫唑嘌呤，为了监测药物的副作用，请近期到医院检查血常规，将结果上传微信平台。三个月内您需要每周检测一次。';
                    break;
                default:
                    return null;
            }

        } elseif ($doctor->id == 33) {
            switch ($medicineid) {
                case 4:
                case 11:
                case 71:
                    return "{$patient->name}您好，请2日内在当地附近医院检查血常规、肝肾功能并上传（如检查已做，直接上传即可），以便我们了解您在家用药副反应情况（服药的第1个月每周检查血常规、肝肾功能，第2个月每2周查血常规、肝肾功能，之后每个月检查1次），针对指导。遇到问题可随时跟我们联系解决。（点击微信左下角的\"小键盘图标\"切换成对话模式跟我们联系。）";
                    break;
                case 5:
                case 20:
                    return "{$patient->name}您好，请2日内在当地附近医院检查血常规、肝肾功能并上传（如检查已做，直接上传即可），以便我们了解您在家用药副反应情况（服药的前2个月每周检查血常规、肝肾功能，之后每个月检查1次），针对指导。遇到问题可随时跟我们联系解决。（点击微信左下角的\"小键盘图标\"切换成对话模式跟我们联系。）";
                    break;
                case 15:
                case 75:
                case 145:
                    return "{$patient->name}您好，请2日内在当地附近医院检查血常规、肝肾功能并上传（如检查已做，直接上传即可），以便我们了解您在家用药副反应情况（每月监测血常规和肝肾功能，每3月查眼底黄斑），针对指导。遇到问题可随时跟我们联系解决。（点击微信左下角的\"小键盘图标\"切换成对话模式跟我们联系。）";
                    break;
                default:
                    return null;
            }
        }
        return null;
    }
}