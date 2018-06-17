<?php

class Patient_hezuoMgrAction extends AuditBaseAction
{

    //患者入合作
    public function doAddJson() {
        $patientid = XRequest::getValue("patientid", 0);

        $patient = Patient::getById($patientid);

        $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid("Lilly", $patientid);
        if($patient_hezuo instanceof Patient_hezuo){
            echo "hadCreate";
            return self::BLANK;
        }

        if($patient instanceof Patient){
            //生成patient_hezuo
            $row = array();
            $row["patientid"] = $patient->id;
            $row["startdate"] = date("Y-m-d");
            $row["company"] = "Lilly";
            $row["status"] = 1;
            $patient_hezuo = Patient_hezuo::createByBiz($row);

            //此处患者变更至微信（礼来）分组
            $createwxuser = $patient->createuser->createwxuser;
            WxApi::MvWxuserToGroup($createwxuser, 134);
            PushMsgService::sendTxtMsgWhenPassSunflower($createwxuser);

            //生成mgtgroup
            $row = array();
            $row["wxuserid"] = $patient->createuser->createwxuserid;
            $row["userid"] = $patient->createuserid;
            $row["patientid"] = $patient->id;

            $mgtgrouptpl = MgtGroupTplDao::getByEname("lilly");
            $row["mgtgrouptplid"] = $mgtgrouptpl->id;
            $row["objtype"] = get_class($patient_hezuo);
            $row["objid"] = $patient_hezuo->id;
            $row["startdate"] = date("Y-m-d");
            $row["status"] = 1;
            $cnt = MgtGroupDao::getCntByPatient($patient);
            $row["pos"] = $cnt + 1;
            MgtGroup::createByBiz($row);

            //设置所属管理组
            $patient->mgtgrouptplid = $mgtgrouptpl->id;

            $doctor = $patient->doctor;
            $first_patient_hezuo = $this->getPateint_hezuoByDoctorid($doctor->id);
            $doctor_hezuo = Doctor_hezuoDao::getOneByCompanyDoctorid("Lilly", $doctor->id, " and status = 1 order by id ");

            //之前没有入过合作患者，此次是当前医生第一个合作患者入组
            if(false ==  $first_patient_hezuo instanceof Patient_hezuo){
                //找到合作医生
                if($doctor_hezuo instanceof Doctor_hezuo){
                    //记录下合作医生的第一个合作患者入组时间
                    $date = date("Y-m-d");
                    $doctor_hezuo->first_patient_date = $date;

                    if($doctor_hezuo->canSendFirstPatientMsg()){
                        //入第一个合作患者，给礼来接口推送提醒消息
                        $content = "{first: '您好，您有一位患者',keywords: ['{$patient->name}', '{$date}'],remark: '点此查看'}";
                        $lillyservice = new LillyService();
                        $send_status = $lillyservice->sendTemplate(1, $doctor_hezuo->doctor_code, $content);

                        if(200 == $send_status){
                            Debug::warn("礼来合作医生{$doctor->name}入组第一个患者，返回status:[{$send_status}]推送至礼来接口的提醒消息成功！");
                        }else {
                            Debug::warn("礼来合作医生{$doctor->name}入组第一个患者，返回status:[{$send_status}]推送至礼来接口的提醒消息失败！");
                        }
                    }
                }
            }

            if($doctor_hezuo instanceof Doctor_hezuo){
                if($doctor_hezuo->isSuggestCourses()){
                    $patient_hezuo->pgroup_subtypestrs = "PracticalTraining,ABCTraining,AdvancedTraining";
                    echo "hadSuggestCourses";
                    return self::BLANK;
                }
            }
        }
        echo "ok";
        return self::BLANK;
    }

    private function getPateint_hezuoByDoctorid($doctorid)
    {
        $sql = "select b.* from patients a
            inner join patient_hezuos b on b.patientid=a.id
            where a.doctorid = :doctorid order by b.id";
        $bind = array(
            ':doctorid' => $doctorid);

        return Dao::loadEntity('Patient_hezuo', $sql, $bind);
    }

    //选行为训练课程
    public function doChoicePgroupsJson() {
        $patientid = XRequest::getValue("patientid", 0);
        $pgroup_subtypestrs = XRequest::getValue("pgroup_subtypestrs", "");
        $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid("Lilly", $patientid);

        if($patient_hezuo instanceof Patient_hezuo && !empty($pgroup_subtypestrs)){
            $patient_hezuo->pgroup_subtypestrs = $pgroup_subtypestrs;
        }

        echo "ok";
        return self::BLANK;
    }

    //设置已服择思达时长（月）
    public function doSetDrugMonthcntJson() {
        $patientid = XRequest::getValue("patientid", 0);
        $drug_monthcnt_when_create = XRequest::getValue("drug_monthcnt_when_create", 1);
        $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid("Lilly", $patientid);

        if($patient_hezuo instanceof Patient_hezuo){
            $patient_hezuo->drug_monthcnt_when_create = $drug_monthcnt_when_create;
        }

        echo "ok";
        return self::BLANK;
    }

    // 出组
    public function doOutJson() {
        $patientid = XRequest::getValue("patientid", 0);
        $status = XRequest::getValue("status", 0);
        $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid("Lilly", $patientid);
        $patient_hezuo->goOut($status);

        echo "ok";
        return self::BLANK;
    }

}
