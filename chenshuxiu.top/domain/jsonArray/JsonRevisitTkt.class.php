<?php

class JsonRevisitTkt
{
    // jsonArray
    public static function jsonArray(RevisitTkt $revisitTkt, Doctor $doctor) {
        $arr = array();

        $arr["revisittktid"] = $revisitTkt->id;
        $arr["createtime"] = $revisitTkt->createtime;
        $arr["createbystr"] = $revisitTkt->getCreatebyStr();
        $arr["status"] = $revisitTkt->status;
        $arr["remark"] = $revisitTkt->auditremark;

        $checkuptpls_tkt = $revisitTkt->getCheckupTpls();

        foreach ($checkuptpls_tkt as $a) {
            $arr["checkuptpls_tkt"][] = JsonCheckupTpl::jsonArrayTkt($a);
        }

        $patient = $revisitTkt->patient;

        $arr["scheduleid"] = $revisitTkt->scheduleid;
        $arr["schedule"] = JsonSchedule::jsonArray($revisitTkt->schedule);
        $arr["patientid"] = $revisitTkt->patientid;
        $arr["patient"] = array();

        $baodaotime = '';
        $tagNamesStr = '';
        $mobile = '';

        // TODO by sjp : 这样处理
        if ($patient instanceof Patient) {
            $arr["patient"] = JsonPatient::jsonArrayForPad_List($patient, $doctor);

            $baodaotime = $patient->createtime;
            $tagNamesStr = $patient->getTagNamesStr("Disease");
            $mobile = $patient->getMobiles();
        }

        $patientremark = "未知";

        if ('' != $revisitTkt->patient_content) {
            $patientremark = $revisitTkt->patient_content;
        }

        if (false == empty($arr["checkuptpls_tkt"])) {
            $patientremark = '';
            foreach ($checkuptpls_tkt as $checkuptpl_tkt) {
                $patientremark .= "{$checkuptpl_tkt->title} ";
            }
        }

        $arr['cells'] = array(
            array(
                "k" => "入组：",
                "v" => $baodaotime),
            array(
                "k" => "诊断：",
                "v" => $tagNamesStr),
            array(
                "k" => "手机号：",
                "v" => $mobile),
            array(
                "k" => "复诊目的：",
                "v" => $patientremark));

        return $arr;
    }

    // jsonArray4Ipad
    public static function jsonArray4Ipad(RevisitTkt $revisitTkt) {
        $arr = JsonRevisitTkt::jsonArrayForIpad_imp($revisitTkt);

        $arr["patient_content"] = $revisitTkt->patient_content ? $revisitTkt->patient_content : '未知';

        // 重新处理
        $checkuptplids = explode(',', $revisitTkt->checkuptplids);

        $tmp = array();
        foreach ($checkuptplids as $checkuptplid) {
            $checkuptplid = trim($checkuptplid);
            if (empty($checkuptplid)) {
                continue;
            }
            $checkuptpl = Dao::getEntityById('CheckupTpl', $checkuptplid);
            $tmp[$checkuptplid] = $checkuptpl->title;
        }
        $arr["checkuptpl_items_str"] = implode("、", $tmp);
        $arr["checkuptplids"] = $tmp;

        return $arr;
    }

    // jsonArray4Ipad_List
    public static function jsonArray4Ipad_List(RevisitTkt $revisitTkt) {
        $arr = JsonRevisitTkt::jsonArray4Ipad($revisitTkt);
        $arr["patient"] = JsonPatient::jsonArray4Ipad($revisitTkt->patient, $revisitTkt->doctor);
        unset($arr["checkuptplids"]); // 用不到

        return $arr;
    }

    // jsonArrayForDwx
    public static function jsonArrayForDwx(RevisitTkt $revisitTkt, Doctor $doctor) {
        $arr = array();

        $arr["revisittktid"] = $revisitTkt->id;
        DBC::requireNotEmpty($revisitTkt->patient, "revisittkt[{$revisitTkt->id}]指向的患者不存在");
        $arr["patient"] = JsonPatient::jsonArrayBase($revisitTkt->patient);
        $arr["scheduleid"] = $revisitTkt->scheduleid;
        $arr["revisitrecordid"] = $revisitTkt->revisitrecordid;
        $arr["createday"] = substr($revisitTkt->createtime, 0, 10);
        $arr["is_mark_his"] = $revisitTkt->is_mark_his;
        $arr["thedate"] = $revisitTkt->thedate;
        $arr["treat_stage"] = $revisitTkt->treat_stage;

        //$arr["patient_confirm_status"] = $revisitTkt->getPatient_confirm_statusStr();
        $arr["patient_confirm_status"] = $revisitTkt->patient_confirm_status;

        $desc_arr = $revisitTkt->getDescArr();
        $arr["status_desc"] = $desc_arr[0];
        $arr["status_desc_bg_color"] = $desc_arr[1];

        $arr["checkuptplids"] = explode(',', $revisitTkt->checkuptplids);

        $patient_content = "未知";

        if ('' != $revisitTkt->patient_content) {
            $patient_content = $revisitTkt->patient_content;
        }

        $arr["patient_content"] = $patient_content; // 就诊目的

        $pcard = $revisitTkt->patient->getPcardByDoctorOrMasterPcard($doctor);

        $arr["out_case_no"] = $pcard->out_case_no;
        $arr["patientcardno"] = $pcard->patientcardno;
        $arr["patientcard_id"] = $pcard->patientcard_id;
        $arr["bingan_no"] = $pcard->bingan_no;
        $arr["patient_content"] = $patient_content;
        $arr["createbystr"] = $revisitTkt->getCreatebyStr();
        $arr["remark"] = $revisitTkt->auditremark;

        //MARK: - #5352 需求扩展，想在方寸管理端 动态 隐藏、显示 配置内容
        $revisitTktConfig = RevisitTktConfigDao::getByDoctorDisease($revisitTkt->doctor, $revisitTkt->patient->disease);
        $use_configs = [];
        if ($revisitTktConfig->isuse_treat_stage == 1) {
            $use_configs[] = [
                'key' => 'treat_stage',
                'title' => '手术',
                'value' => $revisitTkt->treat_stage,
            ];
        }
        if ($revisitTktConfig->isuse_patientcardno == 1) {
            $use_configs[] = [
                'key' => 'patientcardno',
                'title' => '就诊卡号',
                'value' => $pcard->patientcardno,
            ];
        }
        if ($revisitTktConfig->isuse_bingan_no == 1) {
            $use_configs[] = [
                'key' => 'bingan_no',
                'title' => '病案号',
                'value' => $pcard->bingan_no,
            ];
        }
        if ($revisitTktConfig->isuse_out_case_no == 1) {
            $use_configs[] = [
                'key' => 'out_case_no',
                'title' => '病历号',
                'value' => $pcard->out_case_no,
            ];
        }
        if ($revisitTktConfig->isuse_patientcard_id == 1) {
            $use_configs[] = [
                'key' => 'patientcard_id',
                'title' => '患者ID',
                'value' => $pcard->patientcard_id,
            ];
        }
        if ($revisitTktConfig->isuse_patient_content == 1) {
            $use_configs[] = [
                'key' => 'patient_content',
                'title' => '期望解决的问题',
                'value' => $patient_content,
            ];
        }
        $arr['use_configs'] = $use_configs;

        return $arr;
    }

    // jsonArrayForIpad
    public static function jsonArrayForIpad(RevisitTkt $revisitTkt) {
        $arr = JsonRevisitTkt::jsonArrayForIpad_imp($revisitTkt);

        $arr["patientremark"] = $revisitTkt->patient_content ? $revisitTkt->patient_content : '未知';
        $arr["remark"] = $revisitTkt->auditremark;
        $arr["checkuptplids"] = explode(',', $revisitTkt->checkuptplids);

        return $arr;
    }

    // jsonArrayForIpad_imp
    public static function jsonArrayForIpad_imp(RevisitTkt $revisitTkt) {
        $arr = array();

        $arr["revisittktid"] = $revisitTkt->id;
        $arr["patientid"] = $revisitTkt->patientid;
        $arr["scheduleid"] = $revisitTkt->scheduleid;
        $arr["revisitrecordid"] = $revisitTkt->revisitrecordid;
        $arr["thedate"] = $revisitTkt->thedate;
        $arr["createbystr"] = $revisitTkt->getCreatebyStr();

        return $arr;
    }
}
