<?php
// RevisitTktConfigMgrAction
class RevisitTktConfigMgrAction extends AuditBaseAction
{

    public function doOne () {
        $doctorid = XRequest::getValue("doctorid", 0);
        $diseaseid = XRequest::getValue("diseaseid", 0);
        $doctor = Doctor::getById($doctorid);
        $disease = Disease::getById($diseaseid);
        if (false == $disease instanceof Disease) {
            $diseases = $doctor->getDiseases();
            $disease = array_shift($diseases);
        }
        DBC::requireNotEmpty($doctor, "医生为空，doctorid{$doctorid}");
        DBC::requireNotEmpty($disease, "疾病为空，diseaseid{$diseaseid}");

        $revisittktconfig = RevisitTktConfigDao::getByDoctorDisease($doctor, $disease);

        if( false == $revisittktconfig instanceof RevisitTktConfig ){
            $row = [];
            $row['doctorid'] = $doctorid;
            $row['diseaseid'] = $disease->id;

            $revisittktconfig = RevisitTktConfig::createByBiz($row);
        }
        $diseases = $doctor->getDiseases();

        XContext::setValue("doctor", $doctor);
        XContext::setValue("diseases", $diseases);
        XContext::setValue("diseaseid", $disease->id);
        XContext::setValue("revisittktconfig", $revisittktconfig);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $revisittktconfigid = XRequest::getValue("revisittktconfigid", 0);
        $copy2otherdisease = XRequest::getValue("copy2otherdisease", 0);

        $status = XRequest::getValue("status", 0);

        $isuse_out_case_no = XRequest::getValue("isuse_out_case_no", 0);
        $ismust_out_case_no = XRequest::getValue("ismust_out_case_no", 0);

        $isuse_patientcardno = XRequest::getValue("isuse_patientcardno", 0);
        $ismust_patientcardno = XRequest::getValue("ismust_patientcardno", 0);

        $isuse_patientcard_id = XRequest::getValue("isuse_patientcard_id", 0);
        $ismust_patientcard_id = XRequest::getValue("ismust_patientcard_id", 0);

        $isuse_bingan_no = XRequest::getValue("isuse_bingan_no", 0);
        $ismust_bingan_no = XRequest::getValue("ismust_bingan_no", 0);

        $isuse_treat_stage = XRequest::getValue("isuse_treat_stage", 0);
        $ismust_treat_stage = XRequest::getValue("ismust_treat_stage", 0);

        $isuse_patient_content = XRequest::getValue("isuse_patient_content", 0);
        $ismust_patient_content = XRequest::getValue("ismust_patient_content", 0);

        $create_optask_not_ontime_status = XRequest::getValue("create_optask_not_ontime_status", 0);

        $remind_status = XRequest::getValue("remind_status", 0);
        $remind_pre_day_cnt = XRequest::getValue("remind_pre_day_cnt", 0);
        $remind_notice = XRequest::getValue("remind_notice", '');
        $remind_issend_miss = XRequest::getValue("remind_issend_miss", 0);

        $confirm_status = XRequest::getValue("confirm_status", 0);
        $confirm_pre_day_cnt = XRequest::getValue("confirm_pre_day_cnt", 0);
        $confirm_notice = XRequest::getValue("confirm_notice", '');
        $confirm_content_yes = XRequest::getValue("confirm_content_yes", '');
        $confirm_issend_miss = XRequest::getValue("confirm_issend_miss", 0);

        $is_mark_his_notice = XRequest::getValue("is_mark_his_notice", 0);
        $mark_his_notice = XRequest::getValue("mark_his_notice", '');
        $unmark_his_notice = XRequest::getValue("unmark_his_notice", '');

        $revisittktconfig = RevisitTktConfig::getById($revisittktconfigid);
        $revisittktconfigArr = [$revisittktconfig];
        if ($copy2otherdisease) {
            $revisittktconfigArr = Dao::getEntityListByCond('RevisitTktConfig', ' AND doctorid=:doctorid', [":doctorid"=>$revisittktconfig->doctor->id]);
        }

        foreach ($revisittktconfigArr as $a) {
            $a->status = $status;

            $a->isuse_out_case_no = $isuse_out_case_no;
            $a->ismust_out_case_no = $ismust_out_case_no;

            $a->isuse_patientcardno = $isuse_patientcardno;
            $a->ismust_patientcardno = $ismust_patientcardno;

            $a->isuse_patientcard_id = $isuse_patientcard_id;
            $a->ismust_patientcard_id = $ismust_patientcard_id;

            $a->isuse_bingan_no = $isuse_bingan_no;
            $a->ismust_bingan_no = $ismust_bingan_no;

            $a->isuse_treat_stage = $isuse_treat_stage;
            $a->ismust_treat_stage = $ismust_treat_stage;

            $a->isuse_patient_content = $isuse_patient_content;
            $a->ismust_patient_content = $ismust_patient_content;

            $a->create_optask_not_ontime_status = $create_optask_not_ontime_status;

            $a->remind_status = $remind_status;
            $a->remind_pre_day_cnt = $remind_pre_day_cnt;
            $a->remind_notice = $remind_notice;
            $a->remind_issend_miss = $remind_issend_miss;

            $a->confirm_status = $confirm_status;
            $a->confirm_pre_day_cnt = $confirm_pre_day_cnt;
            $a->confirm_notice = $confirm_notice;
            $a->confirm_content_yes = $confirm_content_yes;
            $a->confirm_issend_miss = $confirm_issend_miss;

            $a->is_mark_his_notice = $is_mark_his_notice;
            $a->mark_his_notice = $mark_his_notice;
            $a->unmark_his_notice = $unmark_his_notice;
        }

        XContext::setJumpPath("/revisittktconfigmgr/one?doctorid={$revisittktconfig->doctorid}&diseaseid={$revisittktconfig->diseaseid}&preMsg=".urlencode("复诊设置保存成功"));
        return self::BLANK;
    }
}
