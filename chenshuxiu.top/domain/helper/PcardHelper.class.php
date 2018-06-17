<?php

class PcardHelper
{

    public static function getFieldDesc ($field) {
        return isset(self::$fieldDescs[$field]) ? self::$fieldDescs[$field] : '';
    }

    public static function getFields () {
        return self::$fieldDescs;
    }

    private static $fieldDescs = array(
        'patientid' => 'patientid',
        'doctorid' => 'doctorid',
        'diseaseid' => 'diseaseid',
        'patient_name' => 'patient_name',
        'groupstr4doctor' => '基于医生的患者分组,钱英',
        'create_doc_date' => '建档日期',
        'out_case_no' => '院内病历号',
        'patientcardno' => '院内就诊卡号',
        'patientcard_id' => '院内患者ID',
        'bingan_no' => '院内病案号',
        'fee_type' => '费用类型',
        'complication' => '诊断',
        'first_happen_date' => '首发时间',
        'first_visit_date' => '首次就诊时间',
        'last_incidence_date' => '上次发病日期',
        'has_update' => '有更新',
        'lastpipeid' => '最后流id',
        'lastpipe_createtime' => '最后一次用户行为时间',
        'send_pmsheet_status' => '患者核对用药情况',
        'status' => '就诊卡状态',
        'auditstatus' => '审核状态',
        'auditorid' => 'auditorid',
        'auditremark' => '审核备注',
        'audittime' => '审核通过时间',
        'create_patientid' => 'create_patientid');
}
