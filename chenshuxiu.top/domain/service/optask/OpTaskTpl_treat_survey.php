<?php

// 当前治疗情况调查
class OpTaskTpl_treat_survey extends OpTaskTplBase
{

    // 钩子实现: to_finish_before, 完成时 #4720
    public static function to_finish_before (OpTask $optask, OpNodeFlow $opnodeflow, $auditorid = 0, $exArr = []) {
        // 如果患者有医嘱用药则向患者发送 信息核对 #4722
        $patientmedicinetargets = PatientMedicineTargetDao::getListByPatient($optask->patient);
        if (count($patientmedicinetargets) > 0) {
            $row = [];
            $row["wxuserid"] = $optask->wxuserid;
            $row["userid"] = $optask->userid;
            $row["patientid"] = $optask->patientid;
            $row["type"] = 'first_drug_check';
            $row["is_fill"] = 0;
            $patientcollection = PatientCollection::createByBiz($row);

            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/patientcollection/medicinecheck?patientcollectionid=" . $patientcollection->id; // #4722

            $first = [
                "value" => "根据您的医嘱情况有以下信息需要与您进行核对，提交后我们会第一时间进行处理。",
                "color" => ""];

            $keywords = [
                [
                    "value" => $optask->patient->name,
                    "color" => "#ff6600"],
                [
                    "value" => $optask->patient->doctor->name . "诊后随访团队",
                    "color" => "#ff6600"],
                [
                    "value" => "医嘱信息",
                    "color" => "#ff6600"]];
            $remark = "请点击详情进行信息核对。";
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToWxUserBySystem($optask->wxuser, "info_check_notice", $content, $url);
        }

        // 将患者的诊断信息导入到运营备注
        $patientcollection = $optask->obj;
        $json_content = json_decode($patientcollection->json_content, true);
        $data = $json_content['step1'];
        $list['title'] = $data['content'];

        $row = [];
        $row['patientid'] = $optask->patientid;
        $row['type'] = 'diagnose';
        $row['code'] = 'nmo';
        $row['create_auditorid'] = $auditorid;
        $row['thedate'] = $data['thedate'];
        $row['json_content'] = json_encode($list, JSON_UNESCAPED_UNICODE);
        $patientrecord = PatientRecord::createByBiz($row);
    }
}
