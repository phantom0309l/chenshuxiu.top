<?php

class JsonPipe
{
    // jsonArrayForDapp
    public static function jsonArrayForDapp (Pipe $pipe) {
        $arr = array();
        $arr['createtime'] = date("Y-m-d H:i", strtotime($pipe->createtime));
        $arr['wxuserid'] = $pipe->wxuserid;
        $arr['userid'] = $pipe->userid;
        $arr['patientid'] = $pipe->patientid;
        $arr['objtype'] = $pipe->objtype;
        $arr['objid'] = $pipe->objid;
        $arr['objcode'] = $pipe->objcode;
        $arr['content'] = '';
        switch ($arr['objtype']) {
            case WxTxtMsg:
                $arr['content'] = $pipe->obj->content;
                $arr['showtype'] = "huan";
                $arr['titlestr'] = "患者提问";
                $arr['color'] = "#1996ea";
                break;
            case PatientNote:
                $arr['content'] = $pipe->obj->content;
                $arr['showtype'] = "huan";
                $arr['titlestr'] = "家长日记";
                $arr['color'] = "#1996ea";
                break;
            case PushMsg:
                $pushMsg = $pipe->obj;
                $arr['showtype'] = "yun";
                $arr['content'] = $pushMsg->getContentFix();
                if ($pushMsg->objtype == "CronLog") {
                    $arr['subtype'] = "CronLog";
                    $arr['showtype'] = "yun";
                    $arr['titlestr'] = "运营提醒";
                    $arr['color'] = "#36b036";
                }
                $arr['titlestr'] = "运营回复";
                $arr['color'] = "#36b036";

                break;
            case WxOpMsg:
                $wxopmsg = $pipe->obj;
                if ($wxopmsg->auditorid == 0) {
                    $arr['showtype'] = "yisheng";
                    $arr['content'] = $wxopmsg->content;
                    $arr['titlestr'] = "医生回复";
                    $arr['color'] = "#36b036";
                } else {
                    $arr['showtype'] = "yun";
                    $arr['content'] = $wxopmsg->content;
                    $arr['titlestr'] = "运营提醒";
                    $arr['color'] = "#36b036";
                }
                break;
            case WxPicMsg:
                $picture = $pipe->obj->picture;
                if ($picture instanceof Picture) {
                    $picjson = JsonPicture::jsonArray($picture, 750, 750, false);
                    $arr['thumb_url'] = $picjson['thumb_url'];
                    $arr['thumb_width'] = $picjson['thumb_width'];
                    $arr['thumb_height'] = $picjson['thumb_height'];

                    $arr['url'] = $picture->getBigSrc4App();
                    $arr['width'] = $picture->width;
                    $arr['height'] = $picture->height;

                    if ('Doctor' == $pipe->obj->send_by_objtype && '2patient' == $pipe->obj->send_explain) {
                        $arr['showtype'] = "doctor2huanpicture";
                        $arr['titlestr'] = "医生上传";
                    } elseif ('Doctor' == $pipe->obj->send_by_objtype && '2auditor' == $pipe->obj->send_explain) {
                        $arr['showtype'] = "doctor2auditorpicture";
                        $arr['titlestr'] = "医生上传";
                    } elseif ('Patient' == $pipe->obj->send_by_objtype) {
                        $arr['showtype'] = "huanpicture";
                        $arr['titlestr'] = "患者上传";
                    }

                    $arr['color'] = "#1996ea";
                } else {
                    $arr = array();
                }

                break;
            case Paper:
                $arr['url'] = UrlFor::dmAppAnswerSheet($pipe->obj->xanswersheetid);
                $obj = $pipe->obj;

                if (null == $obj) {
                    return array();
                }

                $title = $obj->getQsheetNameFix();
                $remark = $obj->getRemarkByQsheetName();
                $arr['papertitle'] = $title . $remark;
                $arr['content'] = $title . $remark;
                $arr['showtype'] = "huan";
                $arr['titlestr'] = "量表";
                $arr['color'] = "#1996ea";
                break;
        }

        return $arr;
    }

    // jsonArrayForDwx
    public static function jsonArrayForDwx (Pipe $pipe) {
        $arr = array();
        $arr['pipeid'] = $pipe->id;
        $arr['createtime'] = date("Y-m-d H:i", strtotime($pipe->createtime));
        $arr['type'] = 'text';
        $arr['owner_type'] = 'Patient';
        $arr['title'] = '空标题';
        $arr['content'] = '';
        $arr['url'] = '';
        $arr['color'] = "1996ea";

        switch ($pipe->objtype) {
            case "WxTxtMsg":
                $arr['type'] = 'text';
                $arr['owner_type'] = 'Patient';
                $arr['title'] = "患者提问";
                $arr['content'] = $pipe->obj->content;
                break;

            case "PatientNote":
                $arr['type'] = 'text';
                $arr['owner_type'] = 'Patient';
                $arr['title'] = "家长日记";
                $arr['content'] = $pipe->obj->content;
                break;

            case "PushMsg":
                $obj = $pipe->obj;

                $arr['type'] = "text";
                $arr['owner_type'] = "Auditor";
                $arr['title'] = "运营回复";
                $arr['content'] = $obj->getContentFix();

//                $arr['color'] = "36b036";
                $arr['color'] = "54ca54";

                if ($obj->objtype == "CronLog") {
                    $arr['title'] = "运营提醒";
                }
                if ($obj->send_by_objtype == "Doctor") {
                    $arr['owner_type'] = "Doctor";
                    $arr['title'] = "医生回复";
                    $arr['color'] = "8f78fb";
                }
                break;

            case "DoctorComment":
                $obj = $pipe->obj;

                $arr['type'] = "text";
                $arr['owner_type'] = "Doctor";
                $arr['title'] = "医生批复";
                $arr['content'] = $obj->content;

                $arr['color'] = "FF9000";
                break;

            case "WxPicMsg":
                if ($pipe->obj instanceof Entity) {
                    $picture = $pipe->obj->picture;
                    $titlestr = $pipe->obj->getTitleStr();
                    if ($picture instanceof Picture) {
                        $arr['type'] = "picture";
                        $arr['owner_type'] = "Patient"; // TODO by sjp 20160901 需要修改
                        $arr['title'] = $titlestr;
                        $arr['content'] = '图片';
                        $arr['picture'] = JsonPicture::jsonArrayForIpad($picture);
                    } else {
                        // 重置结果
                        $arr = array();
                    }
                }
                break;

            case "Paper":
                $obj = $pipe->obj;

                if (null == $obj) {
                    // 重置
                    $arr = array();
                } else {
                    $title = $obj->getQsheetNameFix();
                    $remark = $obj->getRemarkByQsheetName();

                    $arr['type'] = 'paper';
                    $arr['owner_type'] = 'Patient';
                    $arr['title'] = "患者量表";
                    $arr['content'] = "量表";
                    $arr['paperid'] = $obj->id;
                }

                break;
        }

        return $arr;
    }

    // jsonArrayForIpad
    public static function jsonArrayForIpad (Pipe $pipe) {
        $arr = array();
        $arr['pipeid'] = $pipe->id;
        $arr['createtime'] = date("Y-m-d H:i", strtotime($pipe->createtime));
        $arr['type'] = 'text';
        $arr['owner_type'] = 'Patient';
        $arr['title'] = '空标题';
        $arr['content'] = '';
        $arr['url'] = '';
        $arr['color'] = "1996ea";

        switch ($pipe->objtype) {
            case "WxTxtMsg":
                $arr['type'] = 'text';
                $arr['owner_type'] = 'Patient';
                $arr['title'] = "患者提问";
                $arr['content'] = $pipe->obj->content;
                break;

            case "PatientNote":
                $arr['type'] = 'text';
                $arr['owner_type'] = 'Patient';
                $arr['title'] = "家长日记";
                $arr['content'] = $pipe->obj->content;
                break;

            case "PushMsg":
                $obj = $pipe->obj;

                $arr['type'] = "text";
                $arr['owner_type'] = "Auditor";
                $arr['title'] = "运营回复";
                $arr['content'] = $obj->content;

                $arr['color'] = "36b036";

                if ($obj->objtype == "CronLog") {
                    $arr['title'] = "运营提醒";
                }
                break;

            case "WxOpMsg":
                $obj = $pipe->obj;

                $arr['type'] = "text";
                $arr['owner_type'] = "Auditor";
                $arr['title'] = "运营提醒";
                $arr['content'] = $obj->content;

                $arr['color'] = "36b036";

                if ($obj->auditorid == 0) {
                    $arr['owner_type'] = "Doctor";
                    $arr['title'] = "医生回复";
                }
                break;

            case "WxPicMsg":
                $picture = $pipe->obj->picture;
                if ($picture instanceof Picture) {

                    $arr['type'] = "picture";
                    $arr['owner_type'] = "Patient"; // TODO by sjp 20160901 需要修改
                    $arr['title'] = "患者上传";
                    $arr['content'] = '';
                    $arr['picture'] = JsonPicture::jsonArrayForIpad($picture);
//                } else {
                    // 重置结果
//                    $arr = array();
                }
                break;

            case "Paper":
                $obj = $pipe->obj;

                if (null == $obj) {
                    // 重置
                    $arr = array();
                } else {
                    $title = $obj->getQsheetNameFix();
                    $remark = $obj->getRemarkByQsheetName();

                    $token = XContext::getValueEx("token", '');

                    $arr['type'] = 'text';
                    $arr['owner_type'] = 'Patient';
                    $arr['title'] = "患者量表";
                    $arr['content'] = $title . $remark . ' [得分：' . $obj->xanswersheet->score . ']';
                    $arr['url'] = Config::getConfig("dm_uri") . "/app/answersheet?answersheetid={$pipe->obj->xanswersheetid}&token={$token}";
                }

                break;
        }

        return $arr;
    }
}
