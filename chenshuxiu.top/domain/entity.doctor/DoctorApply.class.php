<?php
/*
 * DoctorApply
 */
class DoctorApply extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'name'    //姓名
        ,'hospital_name'    //所属医院
        ,'department'    //部门科室
        ,'mobile'    //医生手机号
        ,'status'    //状态
        ,'remark'    //备注
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array();
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
    }

    // $row = array();
// $row["name"] = $name;
// $row["hospital_name"] = $hospital_name;
// $row["department"] = $department;
// $row["mobile"] = $mobile;
// $row["status"] = $status;
// $row["remark"] = $remark;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"DoctorApply::createByBiz row cannot empty");

        $default = array();
        $default["name"] = '';
        $default["hospital_name"] = '';
        $default["department"] = '';
        $default["mobile"] = '';
        $default["status"] =  0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================

}
