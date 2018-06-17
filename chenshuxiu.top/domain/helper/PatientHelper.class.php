<?php

class PatientHelper
{

    public static function getFieldDesc ($field) {
        return isset(self::$fieldDescs[$field]) ? self::$fieldDescs[$field] : '';
    }

    public static function getFields () {
        return self::$fieldDescs;
    }

    private static $fieldDescs = array(
        'name' => '姓名',
        'prcrid' => '身份证号',
        'sex' => '性别',
        'birthday' => '生日',
        'nation' => '民族',
        'marry_status' => '婚姻状况',
        'education' => '文化程度',
        'career' => '职业',
        'income' => '家庭收入',
        'postcode' => '邮编',
        'past_main_history' => '自身免疫病',
        'past_other_history' => '其他疾病',
        'family_history' => '家族病史',
        'menstruation_history' => '月经史',
        'childbearing_history' => '生育史',
        'allergy_history' => '过敏史',
        'mobile' => '电话',
        'other_contacts' => '备用联系人',
        'email' => '邮箱',
        'remark' => '导数据用的备注');
}
