<?php

/*
 * PatientPgroupRef
 */
class PatientPgroupRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'doctorid',  // doctorid
            'diseaseid',  // diseaseid
            'pgroupid',  // pgroupid
            'typestr',
            'startdate',  // 入组时间
            'enddate',  // 出组时间
            'status',
            'pos',
            'paperid',
            'iseffect',
            'hasdonetask',
            'hasundotask',
            'remark');
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientid',
            'doctorid',
            'pgroupid');
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
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
        $this->_belongtos["pgroup"] = array(
            "type" => "Pgroup",
            "key" => "pgroupid");
        $this->_belongtos["paper"] = array(
            "type" => "Paper",
            "key" => "paperid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["pgroupid"] = $pgroupid;
    // $row["startdate"] = $startdate;
    // $row["enddate"] = $enddate;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientPgroupRef::createByBiz row cannot empty");

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
        $default["diseaseid"] = 0;
        $default["pgroupid"] = 0;
        $default["typestr"] = "";

        $time = time();
        $default["startdate"] = date("Y-m-d", $time);
        $pgroupid = $row['pgroupid'];
        $pgroup = Pgroup::getById($pgroupid);
        if ($pgroup instanceof Pgroup) {
            $daycnt = $pgroup->daycnt;
            if ($daycnt) {
                $endtime = $time + 86400 * ($daycnt + 1);
                $default["enddate"] = date("Y-m-d", $endtime);
            } else {
                $default["enddate"] = '0000-00-00';
            }
        } else {
            $default["enddate"] = '0000-00-00';
        }
        $default["status"] = 1;
        $default["pos"] = 0;
        $default["paperid"] = 0;
        $default["iseffect"] = 0;
        $default["hasdonetask"] = 0;
        $default["hasundotask"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function isComplete () {
        return $this->status == 2;
    }

    public function pass () {
        $this->status = 1;
    }

    // 两种完成出组的情况： 2:完整出组，0:不完整出组
    // 完整出组是指每课都有课后作业提交
    // 不完整出组指至少有一课没有提交课后作业
    public function complete () {
        $status = $this->isDoneAllHwk() ? 2 : 0;
        $this->status = $status;
        $this->enddate = date("Y-m-d", time());
    }

    // 判断是不是做了每一课的作业
    public function isDoneAllHwk () {
        $studyplans = StudyPlanDao::getListByPatientpgrouprefidObjcode($this->id, "hwk");
        foreach ($studyplans as $a) {
            $done_cnt = $a->done_cnt;
            if ($done_cnt == 0) {
                return false;
            }
        }
        return true;
    }

    public function createStudyPlans () {
        $fiveIds = array();
        $wxuser = $this->wxuser;
        $patient = $this->patient;
        if ($wxuser instanceof WxUser) {
            $fiveIds = $wxuser->get5id();
        } else
            if ($patient instanceof Patient) {
                $fiveIds = $patient->get5id();
            }
        $patientpgrouprefid = $this->id;

        $course = $this->pgroup->course;
        $courselessonrefs = CourseLessonRefDao::getListByCourse($course);

        $startdate = date("Y-m-d");
        foreach ($courselessonrefs as $i => $a) {
            $open_duration = $a->lesson->open_duration;
            $enddate = $this->getDateStr($startdate, $open_duration);
            $lessonid = $a->lessonid;

            $row = array();
            $row += $fiveIds;
            $row['patientpgrouprefid'] = $patientpgrouprefid;
            $row["objtype"] = "Lesson";
            $row["objid"] = $lessonid;
            $row["objcode"] = "read";
            $row["startdate"] = $startdate;
            $row["enddate"] = $enddate;
            $row["done_cnt"] = 0;
            $studyplan = StudyPlan::createByBiz($row);

            $row = array();
            $row += $fiveIds;
            $row['patientpgrouprefid'] = $patientpgrouprefid;
            $row["objtype"] = "Lesson";
            $row["objid"] = $lessonid;
            $row["objcode"] = "test";
            $row["startdate"] = $startdate;
            $row["enddate"] = $enddate;
            $row["done_cnt"] = 0;
            $studyplan = StudyPlan::createByBiz($row);

            $row = array();
            $row += $fiveIds;
            $row['patientpgrouprefid'] = $patientpgrouprefid;
            $row["objtype"] = "Lesson";
            $row["objid"] = $lessonid;
            $row["objcode"] = "hwk";
            $row["startdate"] = $startdate;
            $row["enddate"] = $enddate;
            $row["done_cnt"] = 0;
            $studyplan = StudyPlan::createByBiz($row);

            $startdate = $enddate;
        }
    }

    private function getDateStr ($createtime, $d = 0) {
        $time = strtotime($createtime) + $d * 86400;
        return date("Y-m-d", $time);
    }

    // 判断当前是不是还在学习计划中
    public function isInStudyPlan ($d = "") {
        if ($d == "") {
            $d = date("Y-m-d H:i:s");
        }
        $studyplan = StudyPlanDao::getOneByPatientpgrouprefidObjcode($this->id, "hwk", " order by id desc");
        $enddate = $studyplan->enddate;
        $dtime = strtotime($d);
        $endtime = strtotime($enddate);

        return $dtime < $endtime;
    }

    // 获取当前正在进行的计划
    public function getCurrentStudyPlan ($objcode = "hwk") {
        $d = date("Y-m-d");
        $studyplan = StudyPlanDao::getOneByPatientpgrouprefidObjcode($this->id, $objcode, " and startdate <= '{$d}' and enddate > '{$d}' order by id asc");
        return $studyplan;
    }

    // 是不是最后一天作业日
    public function isLastHwkDate ($d = "") {
        if ($d == "") {
            $d = date("Y-m-d");
        }
        $studyplan = StudyPlanDao::getOneByPatientpgrouprefidObjcode($this->id, "hwk", " order by id desc");
        $enddate = $studyplan->enddate;

        $time = strtotime($enddate) - 86400;
        $enddate = date("Y-m-d", $time);

        return $enddate == $d;
    }

    // 获取当前课所处的阶段
    // patientpgroupref status为1时进行调用
    // 0: 初始化状态，还没有看文章
    // 1: 已经看了文章，但没有做巩固
    // 2: 已经做完了巩固，但没有做作业
    // 3: 已做作业
    public function getCurrentLessonStep () {
        $studyplan_read = $this->getCurrentStudyPlan("read");

        // 0: 初始化状态，还没有看文章
        $done_cnt_read = $studyplan_read->done_cnt;
        if ($done_cnt_read == 0) {
            return 0;
        }

        // 1: 已经看了文章，但没有做巩固
        $studyplan_test = $this->getCurrentStudyPlan("test");
        $done_cnt_test = $studyplan_test->done_cnt;
        if ($done_cnt_test == 0) {
            return 1;
        }

        // 2: 已经做完了巩固，但没有做作业
        $studyplan_hwk = $this->getCurrentStudyPlan("hwk");
        $done_cnt_hwk = $studyplan_hwk->done_cnt;
        if ($done_cnt_hwk == 0) {
            return 2;
        }

        // 3: 已做作业
        return 3;
    }

    // 今天做没做作业
    public function isDoneHwkOfToday () {
        $studyplan = $this->getCurrentStudyPlan("hwk");
        if ($studyplan instanceof StudyPlan) {
            $study = StudyDao::getOneByStudyplanidThedate($studyplan->id, date("Y-m-d"));
            return $study instanceof Study;
        } else {
            return false;
        }
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
