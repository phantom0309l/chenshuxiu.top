<?php

/*
 * Doctor_hezuo
 */
class Doctor_hezuo extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'company',  // 合作公司
            'doctor_code',  // 合作公司医生编码
            'first_patient_date',  // 入第一个患者时间
            'can_send_msg',  // 是否发送消息 存储的是一个十进制的数
                            // 转为二进制后:
                            // 左起第1位：标志第一个病人选择加入项目后是否发送通知。
                            // 左起第2位：标志是否给合作医生发送调研问卷。
                            // 左起第3位：标志是否给合作医生每两周推送患者报告。
            'is_suggest_courses',  // 0：初始化；1：了解行为训练课程的内容，并愿意推荐。
            'is_clicked_agree',  // 医生是否点击了sunflower知情同意 0:未点击 1:已点击
            'starttime',  // 合作医生开通时间
            'doctorid',  // 方寸医生doctorid
            'status',  // 是否开通合作，默认不开通 值为0
            'name',  // 医生姓名
            'sex',  // 性别, 值为1时是男性，值为2时是女性，值为0时是未知
            'title1',  // 技术职称
            'title2',  // 行政职称
            'hospital_name',  // hospital_name
            'hospital_name_2',  // hospital_name_2
            'department',  // 部门科室
            'marketer_name',  // 市场人员名
            'city_name_bymarketer',  // 城市名
            'area_bymarketer',  // 大区
            'json'); // 其他相关个性字段
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
    }

    // $row = array();
    // $row["company"] = $company;
    // $row["doctor_code"] = $doctor_code;
    // $row["doctorid"] = $doctorid;
    // $row["name"] = $name;
    // $row["sex"] = $sex;
    // $row["title1"] = $title1;
    // $row["title2"] = $title2;
    // $row["hospital_name"] = $hospital_name;
    // $row["department"] = $department;
    // $row["json"] = $json;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Doctor_hezuo::createByBiz row cannot empty");

        $default = array();
        $default["company"] = '';
        $default["doctor_code"] = '';
        $default["first_patient_date"] = '0000-00-00';
        $default["can_send_msg"] = 1;
        $default["is_suggest_courses"] = 0;
        $default["is_clicked_agree"] = 0;
        $default["starttime"] = '0000-00-00 00:00:00';
        $default["doctorid"] = 0;
        $default["status"] = 0;
        $default["name"] = '';
        $default["sex"] = 0;
        $default["title1"] = '';
        $default["title2"] = '';
        $default["hospital_name"] = '';
        $default["hospital_name_2"] = '';
        $default["department"] = '';
        $default["marketer_name"] = '';
        $default["city_name_bymarketer"] = '';
        $default["area_bymarketer"] = '';
        $default["json"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getDayCntFromCreate ($d = "") {
        if ("" == $d) {
            $d = date("Y-m-d", time());
        }
        $createtime = strtotime($this->createtime);
        $createdate = date("Y-m-d", $createtime);

        $diff = XDateTime::getDateDiff($d, $createdate);
        return $diff;
    }

    public function agree () {
        $this->is_clicked_agree = 1;
    }

    public function pass () {
        $this->status = 1;
        if ("0000-00-00 00:00:00" == $this->starttime) {
            $this->starttime = date("Y-m-d H:m:s", time());
        }

        //如果没有配置市场人员，开通的时候配置成王春生
        if(0 == $this->doctor->auditorid_market){
            $this->doctor->auditorid_market = 10003;
        }

        $this->noticeLilly();
    }

    public function noticeLilly () {
        $lillyservice = new LillyService();
        $send_status = $lillyservice->sendDoctorList($this->doctor_code);

        $content = "";
        if (200 == $send_status) {
            $content = "开通礼来合作医生{$this->name}，推送至礼来接口的提醒消息成功！";
        } else {
            $content = "开通礼来合作医生{$this->name}，推送至礼来接口的提醒消息失败！错误码：{$send_status}";
        }
        Debug::warn($content);
        PushMsgService::sendMsgToAuditorBySystem('Sunflower', 1, $content);
    }

    public function close () {
        $this->status = 0;
    }

    public function isPassed () {
        return 1 == $this->status && $this->doctorid > 0;
    }

    //合作医生已经核对并绑定，则可以自动通过
    public function canAutoPass () {
        return $this->doctorid > 0;
    }

    public function isSuggestCourses () {
        return 1 == $this->is_suggest_courses;
    }

    public function canSendFirstPatientMsg () {
        return $this->getConfigStatusByPos(1);
    }

    public function canPushSurveyMsg () {
        return $this->getConfigStatusByPos(2);
    }

    public function canSendNoticeMsg () {
        return $this->getConfigStatusByPos(3);
    }

    private function getConfigStatusByPos ($pos) {
        $can_send_msg = $this->can_send_msg;
        // 转换为二进制
        $can_send_msg_bin = str_pad(decbin($can_send_msg), 3, '0', STR_PAD_LEFT);

        return 1 == substr($can_send_msg_bin, $pos-1, 1);
    }

    public function getSameNameDoctorCnt () {
        $doctors = DoctorDao::getListByName($this->name);
        return count($doctors);
    }
    // ====================================
    // ----------- static method ----------
    // ====================================
}
