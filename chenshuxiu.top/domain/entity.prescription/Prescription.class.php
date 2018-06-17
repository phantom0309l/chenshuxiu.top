<?php

/*
 * Prescription 电子处方
 */
class Prescription extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'shoporderid',  // shoporderid
            'doctorid',  // doctorid
            'doctor_is_audit',  // 医生是否审核。0：未审核，1：已审核，2：已拒绝，3：系统自动拒绝
            'doctor_time_audit',  // 医生审核时间
            'sys_time_audit',  // 系统审核时间
            'doctor_remark',  // 医生备注
            'yishiid',  // 执业医师
            'yaoshiid_audit',  // 审核药师
            'yaoshiid_send',  // 发货药师
            'type',  // 处方类型 1-普通 2-麻醉药品与第一类精神药品 3-第二类精神药品
            'yishi_remark',  // 医师备注
            'audit_remark',  // 审核备注
            'content',  // 药方内容汇总
            'hospital_name',  // 医院
            'department_name',  // 科室
            'patient_name',  // 患者姓名
            'patient_sex',  // 性别, 值为1时是男性，值为2时是女性，值为0时是未知
            'patient_birthday',  // 患者生日
            'fee_type',  // 费用类型
            'time_confirm',  // 审核时间
            'time_audit',  // 审核时间
            'time_send',  // 发货时间
            'md5str',  // md5串
            'status',  // 状态 0-待医师确认 1-医师已确认 2-医师已拒绝 11-药师已复核 12-药师已拒绝 21-已配药
            'remark',  // 运营备注
            'chufang_cfbh'); // 海南处方系统处方编号
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["shoporder"] = array(
            "type" => "ShopOrder",
            "key" => "shoporderid");
        $this->_belongtos["yishi"] = array(
            "type" => "YiShi",
            "key" => "yishiid");
        $this->_belongtos["yaoshi_audit"] = array(
            "type" => "YiShi",
            "key" => "yaoshiid_audit");
        $this->_belongtos["yaoshi_send"] = array(
            "type" => "YiShi",
            "key" => "yaoshiid_send");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["shoporderid"] = $shoporderid;
    // $row["yishiid"] = $yishiid;
    // $row["type"] = $type;
    // $row["content"] = $content;
    // $row["hospital_name"] = $hospital_name;
    // $row["department_name"] = $department_name;
    // $row["patient_name"] = $name;
    // $row["patient_sex"] = $patient_sex;
    // $row["patient_birthday"] = $patient_birthday;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Prescription::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["shoporderid"] = 0;
        $default["doctorid"] = 0;
        $default["doctor_is_audit"] = 0;
        $default["doctor_time_audit"] = '0000-00-00 00:00:00';
        $default["sys_time_audit"] = '0000-00-00 00:00:00';
        $default["doctor_remark"] = '';
        $default["yishiid"] = 0;
        $default["yaoshiid_audit"] = 0;
        $default["yaoshiid_send"] = 0;
        $default["type"] = 1;
        $default["yishi_remark"] = '';
        $default["audit_remark"] = '';
        $default["content"] = '';
        $default["hospital_name"] = '';
        $default["department_name"] = '';
        $default["patient_name"] = '';
        $default["patient_sex"] = 0;
        $default["patient_birthday"] = '0000-00-00';
        $default["fee_type"] = '自费';
        $default["time_confirm"] = '0000-00-00 00:00:00';
        $default["time_audit"] = '0000-00-00 00:00:00';
        $default["time_send"] = '0000-00-00 00:00:00';
        $default["md5str"] = '';
        $default["status"] = 0;
        $default["remark"] = '';
        $default["chufang_cfbh"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function passBySys () {
        $this->doctor_is_audit = 1;
        $this->sys_time_audit = date("Y-m-d H:i:s");

        $shoporder = $this->shoporder;
        if($shoporder instanceof ShopOrder){
            $shoporder->pass();
        }
    }

    public function refuseBySys () {
        $this->doctor_is_audit = 3;
        $this->sys_time_audit = date("Y-m-d H:i:s");

        $shoporder = $this->shoporder;
        if($shoporder instanceof ShopOrder){
            $shoporder->refuseBySys();
        }
    }

    public function passByDoctor () {
        $this->doctor_is_audit = 1;
        $this->doctor_time_audit = date("Y-m-d H:i:s");

        $shoporder = $this->shoporder;
        if($shoporder instanceof ShopOrder){
            $shoporder->pass();
        }
    }

    public function refuseByDoctor () {
        $this->doctor_is_audit = 2;
        $this->doctor_time_audit = date("Y-m-d H:i:s");

        $shoporder = $this->shoporder;
        if($shoporder instanceof ShopOrder){
            $shoporder->refuse();
        }

        $patient = $this->patient;
        if($patient instanceof Patient){
            //给运营发送通知
            $doctor_name = $this->doctor instanceof Doctor ? $this->doctor->name : '';

            $content = "\n{$doctor_name}医生拒绝了{$patient->name}的续方申请。\n\n拒绝原因：{$this->doctor_remark}";
            PushMsgService::sendMsgToAuditorBySystem("Prescription", 1, $content);

            //生成医生审核拒绝任务
            OpTaskService::createPatientOpTask($patient, 'Prescription:refuse', $this, '', 1);
        }
    }

    public function getPrescriptionItems () {
        return PrescriptionItemDao::getPrescriptionItemsByPrescription($this);
    }

    public function getTypeDesc () {
        $arr = [];
        $arr[0] = '未知';
        $arr[1] = '普通';
        $arr[2] = '麻醉药品与第一类精神药品';
        $arr[3] = '第二类精神药品';
        return $arr[$this->type];
    }

    public function getStatusDesc () {
        $arr = [];
        $arr[0] = '待医师确认';
        $arr[1] = '医师已确认';
        $arr[2] = '医师已拒绝';
        $arr[11] = '药师已复核';
        $arr[12] = '药师已拒绝';
        $arr[21] = '已配药';
        return $arr[$this->status];
    }

    public function getPatient_sexDesc () {
        $arr = [];
        $arr[0] = '未知';
        $arr[1] = '男';
        $arr[2] = '女';
        return $arr[$this->patient_sex];
    }

    // getPatientAgeStr
    public function getPatientAgeStr () {
        $birth = $this->patient_birthday;
        list ($by, $bm, $bd) = explode('-', $birth);
        $cm = date('n');
        $cd = date('j');
        $age = date('Y') - $by - 1;
        if ($cm > $bm || $cm == $bm && $cd > $bd) {
            $age ++;
        }

        if ($age > 100) {
            $age = "";
        }

        return $age;
    }
}
