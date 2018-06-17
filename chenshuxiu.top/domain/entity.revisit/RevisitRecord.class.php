<?php
// RevisitRecord
// 复诊记录

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701
class RevisitRecord extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // doctorid,主动冗余
            'thedate',  // 复诊当日
            'scheduleid',  // scheduleid,出诊当日,可空,暂时不用
            'revisittktid',  // revisittktid
            'patientmedicinepkgid',  // patientmedicinepkgid
            'content',  // 文本内容
            'typestr',  // 就诊类型 门诊/住院/出院
            'issend',  // 有没有推送
            'sendtime'); // 推送时间
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'scheduleid',
            'revisittktid',
            'patientmedicinepkgid');
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

        $this->_belongtos["schedule"] = array(
            "type" => "Schedule",
            "key" => "scheduleid");
        $this->_belongtos["revisittkt"] = array(
            "type" => "RevisitTkt",
            "key" => "revisittktid");
        $this->_belongtos["patientmedicinepkg"] = array(
            "type" => "PatientMedicinePkg",
            "key" => "patientmedicinepkgid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["scheduleid"] = $scheduleid;
    // $row["revisittktid"] = $revisittktid;
    // $row["patientmedicinepkgid"] = $patientmedicinepkgid;
    // $row["typestr"] = $typestr;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "RevisitRecord::createByBiz row cannot empty");

        if ($row["patientid"] == null) {
            $row["patientid"] = 0;
        }

        if ($row["doctorid"] == null) {
            $row["doctorid"] = 0;
        }

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["doctorid"] = 0;
        $default["thedate"] = date('Y-m-d');
        $default["scheduleid"] = 0;
        $default["revisittktid"] = 0;
        $default["patientmedicinepkgid"] = 0;
        $default["content"] = '';
        $default["typestr"] = '';
        $default["issend"] = 0;
        $default["sendtime"] = '0000-00-00 00:00:00';

        $row += $default;
        $revisitrecord = new self($row);

        // #4719
//
//        // 佛祖保佑，门诊记录算做了一次检查，更新监测任务
//        BeanFinder::get("UnitOfWork")->commitAndInit();
//
//        $doctor = $revisitrecord->doctor;
//        $patient = $revisitrecord->patient;
//        $pcard = $patient->getPcardByDoctorid($doctor->id);
//        PADRMonitorService::updateMonitorByDoctorForRevisitRecord($patient, $pcard->diseaseid, $revisitrecord);

        return $revisitrecord;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function isEmpty () {
        $revisittkt = $this->revisittkt;
        $patientmedicinepkg = $this->patientmedicinepkg;
        if (false == $revisittkt instanceof RevisitTkt && false == $patientmedicinepkg instanceof PatientMedicinePkg) {
            return true;
        } else {
            return false;
        }
    }

    public function set_issend () {
        $this->issend = 1;
        $this->sendtime = date('Y-m-d H:i:s');
    }

    public function getAllPatientRemark () {
        return PatientRemarkDao::getListByRevisitrecordid($this->id);
    }

    public function getSymptom () {
        $patientremarks = $this->getAllPatientRemark();
        $content = '';
        foreach ($patientremarks as $patientremark) {
            if ($patientremark->content == '') {
                continue;
            }

            if ($patientremark->name != '') {
                $content .= $patientremark->name . ' : ';
            }
            $content .= $patientremark->content . "\n";
        }

        return nl2br($content);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
