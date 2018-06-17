<?php

class JsonBedTkt
{
    // forDwx
    public static function jsonArrayForDwx (BedTkt $bedtkt) {
        $patient = $bedtkt->patient;

        // 血常规照片
        $blood_pictures = $bedtkt->getWxPicMsgs();
        $blood_picture_urls = [];
        foreach ($blood_pictures as $a) {
            $blood_picture_urls[] = $a->getThumbUrl(1500, 1500);
        }

        // 肝肾功照片
        $liver_pictures = $bedtkt->getLiverPictures();
        $liver_picture_urls = [];
        foreach ($liver_pictures as $a) {
            $liver_picture_urls[] = $a->getThumbUrl(1500, 1500);
        }

        // 住院证照片
        $bedtkt_pictures = $bedtkt->getBedTktPictures();
        $bedtkt_picture_urls = [];
        foreach ($bedtkt_pictures as $a) {
            $bedtkt_picture_urls[] = $a->getThumbUrl(1500, 1500);
        }

        $bedtktconfig = BedTktConfigDao::getAllowByDoctoridType($bedtkt->doctorid, $bedtkt->typestr);
        DBC::requireNotEmpty($bedtktconfig, "{$bedtkt->doctor->name} 没有开启住院预约");

        $list = [];
        $configs = json_decode($bedtktconfig->content, true);

        $arr = [];

        $arr['patietnid'] = $bedtkt->patientid;
        $arr['patientname'] = $patient->name;
        $arr['agestr'] = $patient->getAgeStr();
        $arr['diseasename'] = $patient->disease->name;
        $arr['plan_date'] = $bedtkt->plan_date;
        $arr['doctor_audit_time'] = $bedtkt->doctor_audit_time;
        $arr['auditor_remark'] = $bedtkt->auditor_remark;
        $arr['doctor_remark'] = $bedtkt->doctor_remark;
        $arr['blood_pictures'] = $blood_picture_urls;
        $arr['liver_pictures'] = $liver_picture_urls;
        $arr['bedtkt_pictures'] = $bedtkt_picture_urls;
        $arr['extra_info'] = json_decode($bedtkt->extra_info, true);

        if ($configs['is_xindiantu_show']) {
            foreach ($bedtkt->getXindiantuPictures() as $a) {
                $arr['xindiantu'][] = $a->getThumbUrl(1500, 1500);
            }
        }
        if ($configs['is_xueshuantanlitu_show']) {
            foreach ($bedtkt->getXueshuantanlituPictures() as $a) {
                $arr['xueshuantanlitu'][] = $a->getThumbUrl(1500, 1500);
            }
        }
        if ($configs['is_fengshimianyijiancha_show']) {
            foreach ($bedtkt->getFengshimianyijianchaPictures() as $a) {
                $arr['fengshimianyijiancha'][] = $a->getThumbUrl(1500, 1500);
            }
        }
        if ($configs['is_shuqianqitajiancha_show']) {
            foreach ($bedtkt->getShuqianqitajianchaPictures() as $a) {
                $arr['shuqianqitajiancha'][] = $a->getThumbUrl(1500, 1500);
            }
        }

        return $arr;
    }

    // jsonArray
    public static function jsonArray (BedTkt $bedtkt) {
        $patient = $bedtkt->patient;

        $arr = array();

        $arr['id'] = $bedtkt->id;
        $arr['submit_time'] = $bedtkt->submit_time;
        $arr['notify_time'] = $bedtkt->notify_time;
        $arr['auditor_operate_time'] = $bedtkt->audit_time; // todo 运营审核时间
        $arr['mobile'] = $bedtkt->patient->getMobiles();
        $arr['fee_type'] = $bedtkt->fee_type;
        $arr['plan_date'] = $bedtkt->plan_date;
        $arr['confirm_date'] = $bedtkt->confirm_date;
        $arr['status'] = $bedtkt->status;
        $arr['extra_info'] = json_decode($bedtkt->extra_info, true);

        $tmp = BedTkt::TYPESTR_PATIENT_STATUS;
        $arr['title'] = $tmp[$bedtkt->status];

        $arr['patient_status'] = $bedtkt->status_by_patient;
        $arr['patient_status_desc'] = $bedtkt->getPatientStatusDesc();
        $arr['patient_remark'] = $bedtkt->patient_remark;
        $arr['is_open'] = ($bedtkt->status == BedTkt::WILL_AUDITOR_STATUS || $bedtkt->status == BedTkt::AUDITOR_PASS_STATUS) ? 1 : 0;

        $arr['patientid'] = $patient->id;
        $arr['name'] = $patient->name;
        $arr['sex'] = $patient->getSexStr();
        $arr['age'] = $patient->getAgeStr();
        $arr['address'] = $patient->getXprovinceXcityStr();
        $arr['disease_name'] = $patient->disease->name;

        $arr['lastlog_thedate'] = '无';
        $arr['lastlog_color'] = '8f949a';
        $arr['lastlog_title'] = '无记录';
        $arr['lastlog_content'] = '暂无相应历史记录';

        $bedtktlog = $bedtkt->getLastLog();
        if ($bedtktlog instanceof BedTktLog) {
            $bedtktlog_arr = JsonBedTktLog::jsonArray($bedtktlog);
            $arr['lastlog_thedate'] = $bedtktlog_arr['lastlog_thedate'];
            $arr['lastlog_color'] = $bedtktlog_arr['lastlog_color'];
            $arr['lastlog_title'] = $bedtktlog_arr['lastlog_title'];
            $arr['lastlog_content'] = $bedtktlog_arr['lastlog_content'];
            if ($bedtktlog->auditor) {
                $arr['auditor_name'] = $bedtktlog->auditor->name;
            } else {
                $arr['auditor_name'] = '';
            }
        }

        return $arr;
    }
}
