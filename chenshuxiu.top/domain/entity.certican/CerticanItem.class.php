<?php

/*
 * CerticanItem
 */
class CerticanItem extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
             'certicanid'    //qinyan_certicanid
            ,'plan_date'    //计划日期
            ,'drug_dose'    //服药剂量
            ,'adverse_content'    //不良反应描述
            ,'is_wbc'    //是否验血 1:是，0:否
            ,'wbc'    //验血wbc：从运营备注里读取数值
            ,'is_white'    //是否注射升白针  1:是，0:否
            ,'white_dose'    //升白针剂量
            ,'is_platelet'    //是否注射升血小板针 1:是，0:否
            ,'platelet_dose'    //升血小板针剂量
            ,'is_fill'    //是否填写
            ,'fill_time'    //填写时间
        );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'certicanid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["certican"] = array ("type" => "Certican", "key" => "certicanid" );
    }

    // $row = array(); 
    // $row["certicanid"] = $certicanid;
    // $row["plan_date"] = $plan_date;
    // $row["drug_dose"] = $drug_dose;
    // $row["adverse_content"] = $adverse_content;
    // $row["is_wbc"] = $is_wbc;
    // $row["wbc"] = $wbc;
    // $row["is_white"] = $is_white;
    // $row["white_dose"] = $white_dose;
    // $row["is_platelet"] = $is_platelet;
    // $row["platelet_dose"] = $platelet_dose;
    // $row["is_fill"] = $is_fill;
    // $row["fill_time"] = $fill_time;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "CerticanItem::createByBiz row cannot empty");

        $default = array();
        $default["certicanid"] =  0;
        $default["plan_date"] = '';
        $default["drug_dose"] = '';
        $default["adverse_content"] = '';
        $default["is_wbc"] =  0;
        $default["wbc"] = '';
        $default["is_white"] =  0;
        $default["white_dose"] = '';
        $default["is_platelet"] =  0;
        $default["platelet_dose"] = '';
        $default["is_fill"] =  0;
        $default["fill_time"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function fixWbc () {
        // 从patientrecord中获取血常规wbc  baixibao
        $patientrecord = PatientRecordDao::getPatientidTypeThedate($this->certican->patientid, 'wbc_checkup', $this->plan_date);
        $wbc = "";
        if ($patientrecord instanceof PatientRecord) {
            $json = json_decode($patientrecord->json_content, true);

            $wbc .= "wbc:" . $json['baixibao'];
            $wbc .= "<br>血红蛋白:" . $json['xuehongdanbai'];
            $wbc .= "<br>中性粒:" . $json['zhongxingli'];
            $wbc .= "<br>血小板:" . $json['xuexiaoban'];
        }

        $this->wbc = $wbc;
    }
}
