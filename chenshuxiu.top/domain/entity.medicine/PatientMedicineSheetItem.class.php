<?php
/*
 * PatientMedicineSheetItem
 */
class PatientMedicineSheetItem extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'patientmedicinesheetid',  // patientmedicinesheetid
            'medicineid',  // medicineid
            'drug_dose',  // 药物剂量
            'target_drug_dose',  // 目标剂量
            'drug_frequency',  // 用药频率
            'target_drug_frequency',  // 目标用药频率
            'drug_hour',  // 用药时间(用餐前后)
            'target_drug_hour',  // 目标用药时间(用餐前后)
            'ismark',  // 标记需要审核,如果确认无误后,可以置为0
            'status',  // 状态,0:需要确认,1:确认正确服用,2:确认错误服用
            'drug_date',  // 用药日期/停药日期
            'auditorid',  // auditorid
            'auditlog',//运营修改日志
            'auditremark',//运营备注
            'createby', //创建人
        ); 
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientmedicinesheetid',
            'medicineid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["patientmedicinesheet"] = array(
            "type" => "PatientMedicineSheet",
            "key" => "patientmedicinesheetid");
        $this->_belongtos["medicine"] = array(
            "type" => "Medicine",
            "key" => "medicineid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["patientmedicinesheetid"] = $patientmedicinesheetid;
    // $row["medicineid"] = $medicineid;
    // $row["drug_dose"] = $drug_dose;
    // $row["ismark"] = $ismark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientMedicineSheetItem::createByBiz row cannot empty");

        $default = array();
        $default["patientmedicinesheetid"] = 0;
        $default["medicineid"] = 0;
        $default["drug_dose"] = '';
        $default["target_drug_dose"] = '';
        $default["drug_frequency"] = '';
        $default["target_drug_frequency"] = '';
        $default["drug_hour"] = '';
        $default["target_drug_hour"] = '';
        $default["ismark"] = 0;
        $default["drug_date"] = date('Y-m-d');
        $default["status"] = 0;
        $default["auditorid"] = 0;
        $default["createby"] = '';
        $default["auditlog"] = '';
        $default["auditremark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getStatusDesc () {
        return self::$statusDescArray[$this->status];
    }

    public function getDrugDate() {
        return  $this->drug_date != '0000-00-00' ? $this->drug_date : substr($this->createtime, 0, 10);
    }

    public function getCreator() {
        if ($this->createby == 'Auditor') {
            return '运营';
        }

        return '患者';
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    public static $statusDescArray = array(
        0 => '待确认',
        1 => '正确',
        2 => '错误',
        3 => '停药',
    );

}
