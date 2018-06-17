<?php

class CdrMeetingMgrAction extends AuditBaseAction
{
    // 库存列表
    public function doListForLilly () {
        $pagesize = XRequest::getValue("pagesize", 50);
        $pagenum = XRequest::getValue("pagenum", 1);

        $patientid = XRequest::getValue("patientid", 0);

        $type = XRequest::getValue("type", "connect_ok");

        $left_date = XRequest::getValue("left_date", date("Y-m-d", time() - 30*86400));
        $right_date = XRequest::getValue("right_date", date("Y-m-d"));

        $cond = '';
        $bind = [];

        //患者筛选
        if($patientid > 0){
            $cond .= " and a.patientid = :patientid ";
            $bind[":patientid"] = $patientid;
        }

        if($type == "connect_ok"){
            $cond .= " and a.cdr_status in (1,28) ";
        }

        if($type == "other"){
            $cond .= " and a.cdr_status not in (1,28) ";
        }

        //生成日期筛选
        if($left_date > 0){
            $cond .= " and a.createtime >= :left_date ";
            $bind[":left_date"] = $left_date;
        }

        //生成日期筛选
        if($right_date > 0){
            $cond .= " and a.createtime < :right_date ";
            $right_date_new = date("Y-m-d", strtotime($right_date) + 86400);
            $bind[":right_date"] = $right_date_new;
        }

        //获得实体
        $sql = "select a.*
                    from cdrmeetings a
                    inner join patient_hezuos b on b.patientid = a.patientid
                    where 1 = 1 {$cond} order by a.id desc";
        $cdrMeetings = Dao::loadEntityList4Page("CdrMeeting", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("cdrMeetings", $cdrMeetings);

        //获得分页
        $countSql = "select count(a.id)
                    from cdrmeetings a
                    inner join patient_hezuos b on b.patientid = a.patientid
                    where 1 = 1 {$cond} order by a.id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/cdrmeetingmgr/listforlilly?left_date={$left_date}&right_date={$right_date}&patientid={$patientid}&type={$type}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("patientid", $patientid);
        XContext::setValue("left_date", $left_date);
        XContext::setValue("right_date", $right_date);
        XContext::setValue("type", $type);

        return self::SUCCESS;
    }

    public function doOneCdrMeetingHtml () {
        $id = XRequest::getValue('id',0);
        $cdrmeeting = CdrMeeting::getById($id);
        DBC::requireTrue($cdrmeeting instanceof CdrMeeting , "id为{$id}的cdrmeeting不存在");

        $cdr_json = $cdrmeeting->cdr_json;
        if (empty($cdr_json)) {
            $recordFileName = str_replace('.mp3','',$cdrmeeting->cdr_record_file);
            $result = TianRunService::asrDownload($recordFileName);
            $resultObj = json_decode($result);
            if ($resultObj->result == 'success') {
                $cdrmeeting->cdr_json = $result;
                $cdrmeeting->cdr_json_back = $result;
            }
        }else {
            $result = $cdrmeeting->cdr_json;
            $resultObj = json_decode($result);
        }

        if($cdrmeeting->wxuser instanceof  WxUser) {
            $default_wxuser_header = $cdrmeeting->wxuser->getHeadImgPictureSrc(100,100);
        }else {
            $default_wxuser_header = Config::getConfig("img_uri") . '/static/img/audit/default_wxuser_header.png';
        }

        XContext::setValue('default_wxuser_header',$default_wxuser_header);
        XContext::setValue("resultObj",$resultObj);
        XContext::setValue('cdrmeeting', $cdrmeeting);
        return self::SUCCESS;
    }

    public function doChangeCdrJsonPost () {
        $text = XRequest::getValue('text','');
        $cdrMeetingId = XRequest::getValue('cdr_meeting_id', 0);
        $index = XRequest::getValue('index',0);

        $data = [];
        $data['errcode'] = -1;
        $data['errmsg'] = "cdr_json未发生修改";

        if(!empty($text)) {
            $cdrMeeting = CdrMeeting::getById($cdrMeetingId);

            if($cdrMeeting instanceof CdrMeeting) {
                $cdr_json =$cdrMeeting->cdr_json;
                $cdr_json_obj = json_decode($cdr_json);
                $cdr_json_ori = $cdr_json_obj->msg->data[$index]->text;

                if (!empty($cdr_json) && $cdr_json_ori != $text) {
                    $cdr_json_obj->msg->data[$index]->text = $text;
                    $cdrMeeting->cdr_json = json_encode($cdr_json_obj,JSON_UNESCAPED_UNICODE);
                    $data['status'] = ErrCode::ok;
                    $data['errcode'] = 0;
                    $data['errmsg'] = "cdr_json修改成功";
                    $data['cdr_json_text'] = $text;
                }
            }
        }

        XContext::setValue("json", $data);
        return self::TEXTJSON;
    }
}
