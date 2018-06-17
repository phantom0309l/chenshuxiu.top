<?php

/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-4-15
 * Time: 下午7:03
 */
class VoiceMgrAction extends AuditBaseAction
{

    public function doList () {
        $voices = VoiceDao::getEntityListByCond("Voice");

        XContext::setValue("voices", $voices);
        return self::SUCCESS;

    }

    public function doModify () {
        $voiceid = XRequest::getValue("voiceid", 0);

        $voice = Voice::getById($voiceid);

        XContext::setValue("voice", $voice);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $voiceid = XRequest::getValue("voiceid", 0);
        $title = XRequest::getValue("title", "");
        $content = XRequest::getValue("content", "");
        $pictureid = XRequest::getValue("pictureid", 0);

        $voice = Voice::getById($voiceid);

        $voice->title = $title;
        $voice->content = $content;
        $voice->pictureid = $pictureid;
        XContext::setValue("voice", $voice);

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/voicemgr/modify?voiceid=" . $voiceid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

}