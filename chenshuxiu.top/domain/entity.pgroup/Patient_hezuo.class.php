<?php

/*
 * Patient_hezuo
 */

class Patient_hezuo extends Entity
{
    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'patientid'    //patientid
        , 'startdate'    //入组时间
        , 'enddate'    //出组时间
        , 'status'    //1：正在组中；2：顺利出组；3：不活跃退出；4：停换药退出；5：主动退出；
            //6: 合作患者重复扫非合作医生码；7:取关退出；(状态6、状态7 由Lilly_patient_check.php置状态）
        , 'company'    //合作公司
        , 'pgroup_subtypestrs'    //医生勾选的培训课分类
        , 'drug_monthcnt_when_create'    //入组时，已服药时长（月）
        , 'remark'    //备注
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array('patientid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();
        $this->_belongtos["patient"] = array("type" => "Patient", "key" => "patientid");
    }

    // $row = array();
    // $row["patientid"] = $patientid;
    // $row["startdate"] = $startdate;
    // $row["enddate"] = $enddate;
    // $row["status"] = $status;
    // $row["company"] = $company;
    // $row["pgroup_subtypestrs"] = $pgroup_subtypestrs;
    // $row["remark"] = $remark;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "Patient_hezuo::createByBiz row cannot empty");

        $default = array();
        $default["patientid"] = 0;
        $default["startdate"] = '';
        $default["enddate"] = '';
        $default["status"] = 0;
        $default["company"] = '';
        $default["pgroup_subtypestrs"] = '';
        $default["drug_monthcnt_when_create"] = 1;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getDayCntFromCreate($d = "") {
        if ("" == $d) {
            $d = date("Y-m-d", time());
        }
        $createtime = strtotime($this->createtime);
        $createdate = date("Y-m-d", $createtime);

        $diff = XDateTime::getDateDiff($d, $createdate);
        return $diff;
    }

    public function goInto() {
        $this->status = 1;
        $this->enddate = "0000-00-00";
    }

    public function goOut($status) {
        $this->status = $status;
        $this->enddate = date("Y-m-d", time());

        //mgtgroup 逻辑
        $patient = $this->patient;
        $mgtgrouptpl = MgtGroupTplDao::getByEname("lilly");
        $mgtgroup = MgtGroupDao::getByPatientMgtGroupTpl($patient, $mgtgrouptpl);
        if ($mgtgroup instanceof MgtGroup) {
            $mgtgroup->status = $status;
            $mgtgroup->enddate = date("Y-m-d", time());
        } else {
            Debug::warn("礼来项目出组时mgtgroup不存在。patientid[{$patient->id}]");
        }
        $patient->mgtgrouptplid = 0;

        // 提交一下工作单元，再做移动微信组操作
        $unitofwork = BeanFinder::get("UnitOfWork");
        $unitofwork->commitAndInit();
        $this->deleteGroup();
        $this->addGroupAfterOut();

        if (2 == $status) {
            $this->sendmsg("autoout");
        }
    }

    public function getStatusStr() {
        $arr = array(
            "1" => "正在项目中",
            "2" => "顺利出项目",
            "3" => "不活跃退出",
            "4" => "停换药退出",
            "5" => "主动退出",
            "6" => "扫非合作医生退出",
            "7" => "取关退出",
        );
        return $arr[$this->status];
    }

    //移除微信礼来 tag
    public function deleteGroup() {
        $wxusers = WxUserDao::getListByPatient($this->patient);

        foreach ($wxusers as $wxuser) {
            if (1 == $wxuser->wxshopid && 1 == $wxuser->subscribe) {
                WxApi::DeleteGroup($wxuser, 134);
            }
        }
    }

    //添加微信礼来 tag
    public function addGroup() {
        $wxusers = WxUserDao::getListByPatient($this->patient);

        foreach ($wxusers as $wxuser) {
            if (1 == $wxuser->wxshopid && 1 == $wxuser->subscribe) {
                WxApi::MvWxuserToGroup($wxuser, 134);
            }
        }
    }

    //添加微信礼来 tag
    public function addGroupAfterOut() {
        $canIntoMenzhen = $this->patient->canIntoMenzhen();

        if ($canIntoMenzhen) {
            $groupid = 141;
        } else {
            $groupid = 142;
        }

        $wxusers = WxUserDao::getListByPatient($this->patient);

        foreach ($wxusers as $wxuser) {
            if (1 == $wxuser->wxshopid && 1 == $wxuser->subscribe) {
                WxApi::MvWxuserToGroup($wxuser, $groupid);
            }
        }
    }

    public function needAutoOut() {
        $diff = $this->getDayCntFromCreate();
        return $diff >= 7 * 26;
    }

    public function needNotActiveOut() {
        $diff = $this->getDayCntFromCreate();
        //在患者报到28天的时候，要先判断是否出组
        if ($diff == 28) {
            $fromdate = date('Y-m-d', strtotime($this->createtime) + 7 * 86400);
            $enddate = XDateTime::now();
            $isFinishedDrugAndPaper = $this->isFinishedDrugAndPaperImp($fromdate, $enddate);
            if ($isFinishedDrugAndPaper) {
                return false;
            }
        }

        $isFinishedDrugAndPaper = $this->isFinishedDrugAndPaper();
        if ($isFinishedDrugAndPaper) {
            return false;
        }
        $diff = $this->getDayCntFromCreate();
        $arr = [7 + 21, 28 + 21, 56 + 21, 84 + 21, 112 + 21, 140 + 21, 168 + 21];
        //暂停系统消息（只推送文章），并标记不活跃状态
        if (in_array($diff, $arr)) {
            return true;
        }
        return false;
    }

    //检查催用药评估后，患者某一段时间内是否完成全部的用药评估。
    public function isFinishedDrugAndPaper() {
        $diff = $this->getDayCntFromCreate();
        //拿到最后一次催用药，催评估与入项目时间的差值
        // $max_urge_day = array_reduce($arr0, function($v, $w) use ($diff) {$v = $v < $diff && $diff <= $w ? $v : $w; return $v;}, 0);
        $theday = $this->getNearlyDayCnt();
        echo "\n====截止到现在最后一次催用药，催评估与入项目时间的差值：{$theday}===";

        //拿到最后一次催用药，催评估的日期
        $fromdate = date('Y-m-d', strtotime($this->createtime) + $theday * 86400);
        $enddate = XDateTime::now();

        return $this->isFinishedDrugAndPaperImp($fromdate, $enddate);
    }

    //拿到 $diff 落在 $arr 数组某一区域的最左边界值（左开右闭）
    public function getNearlyDayCnt() {
        $diff = $this->getDayCntFromCreate();
        $arr = [168, 140, 112, 84, 56, 28, 7, 0];
        for ($i = 0; $i < count($arr); $i++) {
            if ($diff >= $arr[$i]) {
                return $arr[$i];
            }
        }
        return 0;
    }

    //检查催用药评估后，患者某一段时间内是否完成全部的用药评估。
    public function isFinishedDrugAndPaperImp($fromdate, $enddate) {
        //如果最后一次催评估，催用药后到现在，患者填写了（SNAP-IV评估，QCD评估，用药）的完成情况
        $finishDrug = $this->isFinishedDrug($fromdate, $enddate);
        $finishAdhdPaper = $this->isFinishedAdhdPaper($fromdate, $enddate);
        $finishQcdPaper = $this->isFinishedQcdPaper($fromdate, $enddate);

        return $finishDrug && $finishAdhdPaper && $finishQcdPaper;
    }

    public function isFinishedDrug($fromdate, $enddate) {
        $drugsheet = DrugSheetDao::getOneByPatientid($this->patientid, " and thedate>='{$fromdate}' and thedate<'{$enddate}' ");

        return $drugsheet instanceof DrugSheet;
    }

    public function isFinishedAdhdPaper($fromdate, $enddate) {
        $adhd_papertpl = PaperTplDao::getByEname("adhd_iv");
        $paper_adhd = PaperDao::getLastByPatientidPapertplid($this->patientid, $adhd_papertpl->id, " and createtime>='{$fromdate}' and createtime<'{$enddate}' ");

        return $paper_adhd instanceof Paper;
    }

    public function isFinishedQcdPaper($fromdate, $enddate) {
        $QCD_papertpl = PaperTplDao::getByEname("QCD");
        $paper_QCD = PaperDao::getLastByPatientidPapertplid($this->patientid, $QCD_papertpl->id, " and createtime>='{$fromdate}' and createtime<'{$enddate}' ");

        return $paper_QCD instanceof Paper;
    }

    private function sendmsg($type) {
        $patient = $this->patient;
        $user = $patient->createuser;
        $wxuser = $user->createwxuser;
        if ($wxuser instanceof WxUser && 1 == $wxuser->wxshopid && 1 == $wxuser->subscribe) {
            $doctor_name = $patient->doctor->name;
            $str = "向日葵关爱行动";
            $content = "";

            if ('autoout' == $type) {
                $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'lilly_autoout');
            }

            if ('notactiveout' == $type) {
                $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'lilly_notactiveout');
            }

            $first = array(
                "value" => "",
                "color" => "#ff6600");
            $keywords = array(
                array(
                    "value" => $str,
                    "color" => "#aaa"),
                array(
                    "value" => $content,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content);
        }
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
