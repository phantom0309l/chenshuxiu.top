<?php

// 流管理
class PipeMgrAction extends AuditBaseAction
{

    // 为市场出对话列表 by sjp 20171129
    public function doTextPipeListForMarket() {
        $doctorid = XRequest::getValue('doctorid', 0);

        $cond = "and doctorid=:doctorid";
        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $patients = Dao::getEntityListByCond('Patient', $cond, $bind);

        XContext::setValue('patients', $patients);

        return self::SUCCESS;
    }

    public function doListNewHtml() {
        $mydisease = $this->mydisease;

        $patientid = XRequest::getValue("patientid", 0);
        $page_size = XRequest::getValue("page_size", 10);
        $offsetpipetime = XRequest::getValue("offsetpipetime", '');
        $filter = XRequest::getValue("filter", array());

        $patient = Patient::getById($patientid);
        if (false == $patient instanceof Patient) {
            echo "patient is null";
            return self::blank;
        }

        $pipes = $patient->getHasFilteredPipes($offsetpipetime, $page_size, $filter);

        // 多动症
        if ($patient->diseaseid == 1) {
            $diseasePaperTplRefs = DiseasePaperTplRefDao::getListByDiseaseidDoctorid($patient->diseaseid, 0, 1, null);
        } else {
            $diseasePaperTplRefs = [];
            // 肿瘤, 显示医生绑定的且运营可见的量表
            if ($patient->disease->diseasegroupid == 3) {
                $diseasePaperTplRefs = DiseasePaperTplRefDao::getListByDiseaseidDoctorid($patient->diseaseid, $patient->doctorid);
            } else {
                // 其他方向, 疾病共用 + 医生专用的
                $diseasePaperTplRefs = DiseasePaperTplRefDao::getListByDiseaseidOrDoctorid($patient->diseaseid, $patient->doctorid);
            }
        }
        $papertpls = [];
        foreach ($diseasePaperTplRefs as $a) {
            $papertpls[] = $a->papertpl;
        }

        $wx_uri = XContext::getValue('wx_uri');
        $papertpl_arr = [];
        if ($mydisease->id == 1) {
            $papertpl_arr[0]["title"] = "SNAP-IV评估+用药";
            $papertpl_arr[0]["url"] = $wx_uri . "/drugscale/show?ename=adhd_iv";
        }
        foreach ($papertpls as $a) {
            $papertpl_arr[$a->id]["title"] = $a->title;
            $groupstr = $a->groupstr;
            if ($groupstr == 'scale') {
                $ename = $a->ename;
                $papertpl_arr[$a->id]["url"] = $wx_uri . "/paper/scale?papertplid={$a->id}&ename={$ename}";
            } else {
                $papertpl_arr[$a->id]["url"] = $wx_uri . "/paper/wenzhen?papertplid={$a->id}";
            }
        }

        $courses = Dao::getEntityListByCond("Course", " and id in (207647086, 208693826, 293482486) ");

        XContext::setValue("pipes", $pipes);
        XContext::setValue("papertpl_arr", $papertpl_arr);
        XContext::setValue("courses", $courses);
        XContext::setValue("patient", $patient);

        return self::SUCCESS;
    }

    public function doListHtml_optask() {
        $mydisease = $this->mydisease;

        $patientid = XRequest::getValue("patientid", 0);
        $page_size = XRequest::getValue("page_size", 10);
        $offsetpipetime = XRequest::getValue("offsetpipetime", '');
        $filter = XRequest::getValue("filter", array());
        $istrack = XRequest::getValue("istrack", false);
        $pipeid = XRequest::getValue("pipeid", 0);
        $patient = Patient::getById($patientid);
        if (false == $patient instanceof Patient) {
            echo "patient is null";
            return self::blank;
        }

        if ($istrack) {
            $pipes = $patient->getPipesForTrack($pipeid);
        } else {
            $pipes = $patient->getHasFilteredPipes($offsetpipetime, $page_size, $filter);
        }

        // 多动症
        if ($patient->diseaseid == 1) {
            $diseasePaperTplRefs = DiseasePaperTplRefDao::getListByDiseaseidDoctorid($patient->diseaseid, 0, 1, null);
        } else {
            $diseasePaperTplRefs = [];
            // 肿瘤, 显示医生绑定的且运营可见的量表
            if ($patient->disease->diseasegroupid == 3) {
                $diseasePaperTplRefs = DiseasePaperTplRefDao::getListByDiseaseidDoctorid($patient->diseaseid, $patient->doctorid);
            } else {
                // 其他方向, 疾病共用 + 医生专用的
                $diseasePaperTplRefs = DiseasePaperTplRefDao::getListByDiseaseidOrDoctorid($patient->diseaseid, $patient->doctorid);
            }
        }
        $papertpls = [];
        foreach ($diseasePaperTplRefs as $a) {
            $papertpls[] = $a->papertpl;
        }

        $wx_uri = XContext::getValue('wx_uri');
        $papertpl_arr = [];
        if ($mydisease->id == 1) {
            $papertpl_arr[0]["title"] = "SNAP-IV评估+用药";
            $papertpl_arr[0]["url"] = $wx_uri . "/drugscale/show?ename=adhd_iv";
        }
        foreach ($papertpls as $a) {
            $papertpl_arr[$a->id]["title"] = $a->title;
            $groupstr = $a->groupstr;
            if ($groupstr == 'scale') {
                $ename = $a->ename;
                $papertpl_arr[$a->id]["url"] = $wx_uri . "/paper/scale?papertplid={$a->id}&ename={$ename}";
            } else {
                $papertpl_arr[$a->id]["url"] = $wx_uri . "/paper/wenzhen?papertplid={$a->id}";
            }
        }

        $courses = Dao::getEntityListByCond("Course", " and id in (207647086, 208693826) ");

        XContext::setValue("pipes", $pipes);
        XContext::setValue("papertpl_arr", $papertpl_arr);
        XContext::setValue("courses", $courses);
        XContext::setValue("patient", $patient);
        XContext::setValue("offsetpipetime", $offsetpipetime);

        return self::SUCCESS;
    }

    public function doListOfWxUserHtml() {
        $wxuserid = XRequest::getValue("wxuserid", 0);
        $page_size = XRequest::getValue("page_size", 10);
        $offsetpipetime = XRequest::getValue("offsetpipetime", '');

        $wxuser = WxUser::getById($wxuserid);
        if (false == $wxuser instanceof WxUser) {
            echo "wxuser is null";
            return self::blank;
        }

        $cnt = intval($page_size);

        $cond = " and wxuserid = :wxuserid ";
        $bind = [];
        $bind[':wxuserid'] = $wxuserid;

        if ($offsetpipetime) {
            $cond .= " and createtime < :offsetpipetime ";
            $bind[':offsetpipetime'] = $offsetpipetime;
        }

        $cond .= " order by createtime desc
            limit {$cnt}";

        $pipes = Dao::getEntityListByCond("Pipe", $cond, $bind);

        XContext::setValue("pipes", $pipes);
        XContext::setValue("wxuser", $wxuser);

        return self::SUCCESS;
    }

    public function doPushWenzhenMsgJson() {
        $openid = XRequest::getValue("open_id", 0);
        $content = XRequest::getValue("content", '');
        $papertpl_url = XRequest::getValue("papertpl_url", '');

        $content = $content . "\n\n点击详情填写问卷。";

        $wxuser = WxUserDao::getByOpenid($openid);

        $patient = $wxuser->patient;
        $pcard = null;

        if ($patient instanceof Patient) {
            $pcard = $patient->getMasterPcard();
            $pcard->has_update = 0;
        }

        $myauditor = $this->myauditor;

        $doctorid = ($pcard->doctor instanceof Doctor) ? $pcard->doctor->id : 0;

        $title = $wxuser->doctor->name . "医生随访团队";

        $url = $papertpl_url . "&openid={$openid}";

        $appendarr = array(
            'doctorid' => $doctorid,
            'objtype' => "Paper_wenzhen");
        XContext::setValue('is_filter_blacklist', false);
        XContext::setValue('is_filter_doubtlist', false);
        $pushmsg = PushMsgService::sendNoticeToWxUserByAuditor($wxuser, $myauditor, $title, $content, $url, $appendarr);

        if (false == $pushmsg instanceof PushMsg) {
            echo "fail";
            return self::BLANK;
        }

        if ($patient instanceof Patient) {
            QuickPassService::auditorReply($patient);
        }

        echo "ok";
        return self::BLANK;
    }

    public function doPushWxPicMsgJson() {
        $openid = XRequest::getValue("open_id", '');
        $pictureid = XRequest::getValue('pictureid', '');
        if (!$openid || !$pictureid) {
            echo '';
            return self::BLANK;
        }
        $picture = Dao::getEntityById('Picture', $pictureid);
        if (!$picture) {
            echo '';
            return self::BLANK;
        }

        $wxuser = WxUserDao::getByOpenid($openid);
        $wxshop = $wxuser->wxshop;

        $patient = $wxuser->patient;
        if ($patient instanceof Patient) {
            $pcard = $patient->getMasterPcard();
            $pcard->has_update = 0;
        }

        $filename = Config::getConfig('xphoto_path') . '/' . $picture->getFilePath();

        $myauditor = $this->myauditor;
        $row = [
            'wxuserid' => $wxuser->id,
            'userid' => $wxuser->userid,
            'patientid' => $patient->id,
            'pictureid' => $picture->id,
            'auditorid' => $myauditor->id,
            'title' => '运营回复患者图片',
            'send_by_objtype' => 'Auditor',
            'send_by_objid' => $myauditor->id,
            'send_explain' => '2patient'];
        $wxpicmsg = WxPicMsg::createByBiz($row);
        $pipe = Pipe::createByEntity($wxpicmsg);

        $access_token = $wxshop->getAccessToken();
        $mediaidjson = WxApi::uploadimgByUrl($access_token, $picture->getSrc());
        WxApi::kefuImageMsg($wxshop, $openid, $mediaidjson['media_id']);

        if ($patient instanceof Patient) {
            QuickPassService::auditorReply($patient);
        }

        echo 'ok';

        return self::BLANK;
    }

    // 运营手动下载录音
    public function doDownloadVoiceJson() {
        $cdrmeetingid = XRequest::getValue('cdrmeetingid', 0);
        DBC::requireNotEmpty($cdrmeetingid, "cdrmeetingid为空");
        $cdrmeeting = CdrMeeting::getById($cdrmeetingid);
        DBC::requireNotEmpty($cdrmeeting, "cdrmeeting为空");

        $userName = Config::getConfig('cdr_userame');
        $pwd = Config::getConfig('cdr_pwd');
        $seed = time();
        $pwdonemd5 = md5($pwd);
        $pwdtwomd5 = md5($pwdonemd5 . $seed);

        $date = Date("Ymd", $cdrmeeting->cdr_start_time);
        $recordurl = '';
        if ($cdrmeeting->cdr_record_file) {
            $recordurl = "http://api.clink.cn/{$date}/{$cdrmeeting->cdr_record_file}?enterpriseId={$cdrmeeting->cdr_enterprise_id}&userName={$userName}&pwd={$pwdtwomd5}&seed={$seed}";
        }

        $paramArr = array(
            'cdr_main_unique_id' => $cdrmeeting->cdr_main_unique_id,
            'recordurl' => $recordurl);
        $params = json_encode($paramArr, JSON_UNESCAPED_UNICODE);

        $job = Job::getInstance();
        $job->doBackground('download_cdrmeeting_airvoice', $params);
        Debug::trace(__METHOD__ . ' send sig to nsq topic download_cdrmeeting_airvoice params:' . $params);

        echo "success";
        return self::BLANK;
    }

    // 运营人员向某微信用户发消息
    public function doPushMsgByOpenidJson() {
        $openid = XRequest::getValue("open_id", '');
        $content = XRequest::getValue("content", '');

        $wxuser = WxUserDao::getByOpenid($openid);

        $patient = $wxuser->patient;
        if ($patient instanceof Patient) {
            $pcard = $patient->getMasterPcard();
            $pcard->has_update = 0;
        }

        $myauditor = $this->myauditor;

        // 设置是否过滤黑名单
        XContext::setValue('is_filter_blacklist', false);
        XContext::setValue('is_filter_doubtlist', false);
        PushMsgService::sendTxtMsgToWxUserByAuditor($wxuser, $myauditor, $content);

        $result = [];
        if ($patient instanceof Patient) {
            QuickPassService::auditorReply($patient);

            $result['has_optask_of_quickconsult'] = $this->checkHasOpTaskOfQuickConsult($patient);
        }

        $this->result['data'] = $result;
        return self::TEXTJSON;
    }

    // 检查是否有快速咨询任务
    public function checkHasOpTaskOfQuickConsult(Patient $patient) {
        $optasktpl = OpTaskTplDao::getOneByUnicode('order:QuickConsultOrder');
        $optask = OpTaskDao::getOneByPatientOptasktpl($patient, $optasktpl, true);

        if ($optask instanceof OpTask) {
            return 1;
        } else {
            return 0;
        }
    }

    // 运营人员向某微信用户发消息
    public function doPushLessonJson() {
        $openid = XRequest::getValue("open_id", '');
        $content = XRequest::getValue("content", '');
        $lessonid = XRequest::getValue("lessonid", 0);

        $wxuser = WxUserDao::getByOpenid($openid);
        $lesson = Lesson::getById($lessonid);

        if ($lesson instanceof Lesson && $wxuser instanceof WxUser) {
            $picture = $lesson->picture;
            $img_src = "";
            if ($picture instanceof Picture) {
                $img_src = $picture->getSrc();
            }

            $wx_uri = Config::getConfig("wx_uri");
            $url = "{$wx_uri}/lesson/justforshow?openid={$openid}&lessonid={$lessonid}";

            if ($img_src) {
                $wxshop = $wxuser->wxshop;

                $title = $lesson->title;
                $articles = array();
                $articles[] = new SimpleWxMsg($title, $img_src, $content, $url);

                $errcode = WxApi::kefuNewsMsg($wxshop, $openid, $articles);
                // 发送失败时转模板消息
                if ($errcode != '0') {
                    $this->sendTemplateMsg($wxuser, $content, $url);
                }
            } else {
                // 没有图片封面时 发送模板消息
                $this->sendTemplateMsg($wxuser, $content, $url);
            }
        }

        $patient = $wxuser->patient;
        if ($patient instanceof Patient) {
            QuickPassService::auditorReply($patient);
        }

        echo "ok";
        return self::BLANK;
    }

    private function sendTemplateMsg($wxuser, $content, $url) {
        $title = "医生助理";

        XContext::setValue('is_filter_blacklist', false);
        XContext::setValue('is_filter_doubtlist', false);
        PushMsgService::sendNoticeToWxUserBySystem($wxuser, $title, $content, $url);
    }
}
