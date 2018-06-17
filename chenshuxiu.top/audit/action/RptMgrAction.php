<?php

class RptMgrAction extends AuditBaseAction
{

    // 首页
    public function doDefault () {
        return self::SUCCESS;
    }

    // 首页
    public function doIndex () {
        return self::SUCCESS;
    }

    // 报表
    public function dorpt_date_patient_list () {
        $list = dao::getEntityListByCond("Rpt_date_patient", 'order by id desc limit 100', [], 'statdb');
        XContext::setValue("list", $list);
        return self::SUCCESS;
    }

    // 日报表详情
    public function dorpt_date_patient_one () {
        $rpt_date_patient_id = XRequest::getValue("rpt_date_patient_id", 0);

        $rpt_date_patient = Rpt_date_patient::getById($rpt_date_patient_id, 'statdb');
        XContext::setValue("rpt_date_patient", $rpt_date_patient);

        return self::SUCCESS;
    }

    // 某日的快照
    public function dorpt_patient_list_of_date () {
        $thedate = XRequest::getValue("thedate", date("Y-m-d"));
        $istest = XRequest::getValue("istest", 0);

        $beforeOneDay = date('Y-m-d', strtotime($thedate) - 86400);

        XContext::setValue("thedate", $thedate);
        XContext::setValue("istest", $istest);

        $rpt_date_patient = Rpt_date_patientDao::getByThedate($thedate);
        XContext::setValue("rpt_date_patient", $rpt_date_patient);

        $list = Rpt_patientDao::getListByDate($thedate, $istest, " order by id desc limit 100 ");
        $list0 = Rpt_patientDao::getListByDate($beforeOneDay, $istest, "  order by id desc limit 100 ");

        $day_rpt_array = array();
        foreach ($list0 as $a) {
            $day_rpt_array[$a->patientid] = $a;
        }

        XContext::setValue("list", $list);
        XContext::setValue("day_rpt_array", $day_rpt_array);

        return self::SUCCESS;
    }

    // 人的历史
    public function dorpt_patient_list_of_patient () {
        $patientid = XRequest::getValue("patientid", 0);

        $patient = Patient::getById($patientid);
        XContext::setValue("patient", $patient);

        $list = Rpt_patientDao::getListByPatient($patientid);
        XContext::setValue("list", $list);
        return self::SUCCESS;
    }

    public function dorpt_week_patient_statistic () {
        $list = Rpt_week_patientDao::getEntityListByCond("Rpt_week_patient", '', [], 'statdb');
        $rpt_last = Rpt_week_patientDao::getEntityByCond("Rpt_week_patient", " order by id desc", [], 'statdb');

        $monday = $rpt_last->mondaydate;
        $sunday = date('Y-m-d', strtotime("$monday + 6 days"));

        $monday = date('Y-m-d', strtotime("$monday + 7 days"));
        $sunday = date('Y-m-d', strtotime("$sunday + 7 days"));

        $thisday = date('Y-m-d', time());
        $date = array();
        $arr = array();

        do {
            $arr = Rpt_week_patient::getRptRowByDate($monday, $sunday);
            array_push($date, $arr);
            $monday = date('Y-m-d', strtotime("$monday + 7 days"));
            $sunday = date('Y-m-d', strtotime("$sunday + 7 days"));
        } while ($monday <= $thisday);

        XContext::setValue("list", $list);
        XContext::setValue("date", $date);
        return self::SUCCESS;
    }

    // 报到30-180天患者缓解率
    public function doHuanjieRatio () {
        $thedate_huanjie = XRequest::getValue("thedate_huanjie", date('Y-m-d'));
        $thedate_scale = XRequest::getValue("thedate_scale", date('Y-m-d'));
        $redmine = 3076;
        XContext::setValue("thedate_huanjie", $thedate_huanjie);
        XContext::setValue("thedate_scale", $thedate_scale);
        XContext::setValue("redmine", $redmine);
        return self::SUCCESS;
    }

    // 报到30-180天患者缓解率
    public function doHuanjieRatioJson () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $thedate_huanjie = XRequest::getValue("thedate_huanjie", date('Y-m-d'));
        $thedate_scale = XRequest::getValue("thedate_scale", date('Y-m-d'));
        $baodaocnt_left = XRequest::getValue("baodaocnt_left", 30);

        $sql = "select id
            from patients
            where datediff(:thedate_huanjie, createtime) >= :baodaocnt_left and datediff(:thedate_huanjie, createtime) <=180
            and diseaseid=1 and doubt_type=0 and doctorid not in (10,11)";
        $bind = [];
        $bind[":baodaocnt_left"] = $baodaocnt_left;
        $bind[":thedate_huanjie"] = $thedate_huanjie;
        $ids = Dao::queryValues($sql, $bind);
        $i = 0;
        $huanjiecnt1 = 0;
        $huanjiecnt2 = 0;
        $data = array();
        $data["total"] = count($ids);
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 200) {
                $i = 0;
                $unitofwork->commitAndRelease();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            $patient = Patient::getById($id);
            $doctor = $patient->doctor;
            if (false == $doctor instanceof Doctor) {
                continue;
            }
            if ($patient instanceof Patient) {
                $status = $this->getHuanjieStatus($patient);
                if ($status == 1) {
                    $huanjiecnt1 ++;
                }
                if ($status == 2) {
                    $huanjiecnt2 ++;
                }
            }
        }
        $data["huanjiecnt1"] = $huanjiecnt1;
        $data["huanjiecnt2"] = $huanjiecnt2;
        $data["huanjiecnt"] = $huanjiecnt1 + $huanjiecnt2;

        $sql1 = "select count(*)
                from papers
                where ename='adhd_iv' and (userid > 20000 or userid < 10000)
                and doctorid not in (10,11,0) and createtime < :thedate_scale";
        $bind = [];
        $bind[":thedate_scale"] = date("Y-m-d", strtotime($thedate_scale) + 86400);
        $scalecnt = Dao::queryValue($sql1, $bind);
        $data["scalecnt"] = $scalecnt;

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }
    // status 0 ：没有填写或者未缓解
    // 1:评估平均分小于等于一分
    // 2:总分最高分比最低分高20%（含）以上的患者
    private function getHuanjieStatus ($patient) {
        $status = 0;
        $doctor = $patient->doctor;
        $num = $this->getQuestionNum($doctor);

        $score = array();
        $sql = "select a.* from papers a
                inner join xanswersheets b on b.id = a.xanswersheetid
                where a.patientid = :patientid and a.ename='adhd_iv' and b.score > 0 order by a.id asc";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $papers = Dao::loadEntityList("Paper", $sql, $bind);
        foreach ($papers as $a) {
            $r = $a->xanswersheet->score / $num;
            if ($r <= 1) {
                $status = 1;
                break;
            } else {
                $score[] = $a->xanswersheet->score;
            }
        }
        if (count($score) == 0) {
            return $status;
        }

        if ($status == 0) {
            $max = max($score);
            $min = min($score);
            $max_pos = 0;
            $min_pos = 0;
            foreach ($score as $i => $value) {
                if ($value == $max) {
                    $max_pos = $i;
                }
                if ($value == $min) {
                    $min_pos = $i;
                }
            }

            if ($max_pos < $min_pos) {
                if ($min <= ($max * 0.8)) {
                    $status = 2;
                }
            }
        }
        return $status;
    }

    private function getQuestionNum (Doctor $doctor) {
        $flag = $doctor->useAdhd_ivOf26();
        return $flag == true ? 26 : 18;
    }

    // 入组患者维度统计
    public function doSunflowerForPatient () {
        $startdate = XRequest::getValue("startdate", "2017-07-12");
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));
        $redmine = 4929;
        XContext::setValue("startdate", $startdate);
        XContext::setValue("enddate", $enddate);
        XContext::setValue("redmine", $redmine);
        return self::SUCCESS;
    }

    // 入组患者维度统计
    public function doSunflowerForPatientOutput () {
        $startdate = XRequest::getValue("startdate", "2017-07-12");
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));
        $data = [
            'startdate' => $startdate,
            'enddate' => $enddate,
        ];

        $this->createExportJobAndTriggerNSQ('sunflowerforpatient', $data);
        return self::TEXTJSON;
    }

    //方寸儿童管理服务平台KPI数据导出
    public function doADHD_KPI () {
        $startdate_default = date("Y-m-d", time() - 24*7*86400 - 3*86400);
        $enddate_default = date("Y-m-d", time() - 8*7*86400 - 0*86400);
        $startdate = XRequest::getValue("startdate", $startdate_default);
        $enddate = XRequest::getValue("enddate", $enddate_default);
        $redmine = 5627;
        XContext::setValue("startdate", $startdate);
        XContext::setValue("enddate", $enddate);
        XContext::setValue("redmine", $redmine);
        return self::SUCCESS;
    }

    public function doADHD_KPIOutputJson () {
        $this->result = array(
            'errno' => 0,
            'errmsg' => '',
            'data' => '');

        $startdate_default = date("Y-m-d", time() - 24*7*86400 - 3*86400);
        $enddate_default = date("Y-m-d", time() - 8*7*86400 - 0*86400);
        $startdate = XRequest::getValue("startdate", $startdate_default);
        $enddate = XRequest::getValue("enddate", $enddate_default);

        $data = [
            'startdate' => $startdate,
            'enddate' => $enddate,
        ];
        $this->createExportJobAndTriggerNSQ('ADHD_KPI', $data);
        return self::TEXTJSON;
    }

    private function createExportJobAndTriggerNSQ($type, $data){
        $myauditor = $this->myauditor;
        DBC::requireNotEmpty($data, '数据不能为空');
        $cnt = Export_JobDao::getActiveJobCntByAuditorid($myauditor->id);
        DBC::requireTrue($cnt < 2, '同时只能运行2个导出任务');

        $row = [];
        $row['type'] = $type;
        $row['data'] = json_encode($data, JSON_UNESCAPED_UNICODE);
        $row['auditorid'] = $myauditor->id;

        $export_job = Export_Job::createByBiz($row);
        $job = Job::getInstance();
        $job->doBackground('export_data', $export_job->id);
        return $export_job;
    }

    // 礼来市场维度统计
    public function doSunflowerForMarketer () {
        $startdate = XRequest::getValue("startdate", "2017-07-12");
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));
        $redmine = 3862;
        XContext::setValue("startdate", $startdate);
        XContext::setValue("enddate", $enddate);
        XContext::setValue("redmine", $redmine);
        return self::SUCCESS;
    }

    // 礼来市场维度统计
    public function doSunflowerForMarketerOutput () {
        $startdate = XRequest::getValue("startdate", "2017-07-12");
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));

        $sql = "select a.id
            from doctor_hezuos a
            inner join doctors b on b.id=a.doctorid
            where a.status=1 and b.hospitalid!=5
            and a.starttime >= :startdate and a.starttime < :enddate";

        $bind = [];
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));

        $ids = Dao::queryValues($sql, $bind);
        $arr = array();
        $temp = array();
        $data = array();
        foreach ($ids as $id) {
            $arr[] = $this->getDataForSunflower($id);
        }

        $cnt = 18;
        foreach ($arr as $k => $v) {
            for($i=0; $i<$cnt; $i++){
                if($i < 3){
                    $temp[$v[1].'_'.$v[2]][$i] = $v[$i];
                }else {
                    if(3 == $i){
                        $temp[$v[1].'_'.$v[2]][$i]++;
                        continue;
                    }
                    if(4 == $i){
                        if($v[18] != '0000-00-00'){
                            $temp[$v[1].'_'.$v[2]][$i]++;
                        }else {
                            if(false == isset($temp[$v[1].'_'.$v[2]][$i])){
                                $temp[$v[1].'_'.$v[2]][$i] = 0 ;
                            }
                        }
                        continue;
                    }
                    $temp[$v[1].'_'.$v[2]][$i] += $v[$i];
                }
            }
        }

        foreach ($temp as $k => $v) {
            $temp2 = array();
            for($i=0; $i<$cnt; $i++){
                $temp2[] = $v[$i];
            }
            $temp2[$cnt] = 0==$temp2[$cnt-1] ? "0" : round($temp2[$cnt-3]/$temp2[$cnt-1]). "（{$temp2[$cnt-3]}/" . "{$temp2[$cnt-1]}）";
            $data[] = $temp2;
        }

        $headarr = array(
            "大区",
            "城市",
            "代表姓名",
            "开通的医生总数",
            "有患者入组的医生数",
            "入组患者的总数",
            "顺利出组的患者总数",
            "不活跃退出的患者总数",
            "停换药退出的患者总数",
            "主动退出的患者总数",
            "扫非合作医生退出的患者总数",
            "取关的患者总数",
            "出组的患者总数",
            "扫码微信号数",
            "扫码未报到的微信号总数",
            "AE 的数量",
            "PC 的数量",
            "入组患者电话数",
            "电话中AE 发生率");
        ExcelUtil::createForWeb($data, $headarr);
    }

    // 合作医生维度统计
    public function doSunflowerForDoctor () {
        $startdate = XRequest::getValue("startdate", "2017-07-12");
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));
        $redmine = 3907;
        XContext::setValue("startdate", $startdate);
        XContext::setValue("enddate", $enddate);
        XContext::setValue("redmine", $redmine);
        return self::SUCCESS;
    }

    // 合作医生维度统计
    public function doSunflowerForDoctorOutput () {
        $startdate = XRequest::getValue("startdate", "2017-07-12");
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));

        $sql = "select a.id
            from doctor_hezuos a
            inner join doctors b on b.id=a.doctorid
            where a.status=1 and b.hospitalid!=5
            and a.starttime >= :startdate and a.starttime < :enddate";

        $bind = [];
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));

        $ids = Dao::queryValues($sql, $bind);
        $data = array();
        $cnt = 18;
        foreach ($ids as $id) {
            $arr = $this->getDataForSunflower($id);
            $arr[$cnt] = 0==$arr[$cnt-1] ? "0" : round($arr[$cnt-3]/$arr[$cnt-1]). "（{$arr[$cnt-3]}/" . "{$arr[$cnt-1]}）";
            $data[] = $arr;
        }

        $headarr = array(
            "大区",
            "城市",
            "代表姓名",
            "开通医生",
            "开通时间",
            "入组患者的总数",
            "顺利出组的患者总数",
            "不活跃退出的患者总数",
            "停换药退出的患者总数",
            "主动退出的患者总数",
            "扫非合作医生退出的患者总数",
            "取关的患者总数",
            "出组的患者总数",
            "扫码微信号数",
            "扫码未报到的微信号总数",
            "AE 的数量",
            "PC 的数量",
            "入组患者电话数",
            "电话中AE 发生率");
        ExcelUtil::createForWeb($data, $headarr);
    }

    private function getDataForSunflower ($doctor_hezuoid) {
        $doctor_hezuo = Doctor_hezuo::getById($doctor_hezuoid);
        $doctor = $doctor_hezuo->doctor;
        $doctorid = $doctor->id;

        $arr = array();
        $arr[] = $doctor_hezuo->area_bymarketer;
        $arr[] = $doctor_hezuo->city_name_bymarketer;
        $arr[] = $doctor_hezuo->marketer_name;
        $arr[] = $doctor_hezuo->name;
        $arr[] = $doctor_hezuo->starttime;
        $arr[] = $this->getCntByCompanyDoctorid("Lilly", $doctorid);
        $arr[] = $this->getCntByCompanyDoctorid("Lilly", $doctorid, " and a.status=2 ");
        $arr[] = $this->getCntByCompanyDoctorid("Lilly", $doctorid, " and a.status=3 ");
        $arr[] = $this->getCntByCompanyDoctorid("Lilly", $doctorid, " and a.status=4 ");
        $arr[] = $this->getCntByCompanyDoctorid("Lilly", $doctorid, " and a.status=5 ");
        $arr[] = $this->getCntByCompanyDoctorid("Lilly", $doctorid, " and a.status=6 ");
        $arr[] = $this->getCntByCompanyDoctorid("Lilly", $doctorid, " and a.status=7 ");
        $arr[] = $this->getCntByCompanyDoctorid("Lilly", $doctorid, " and a.status>1 ");
        $arr[] = $this->getScanCnt($doctorid);
        $arr[] = $this->getScanNotBaodaoCnt($doctorid);
        $arr[] = $this->getAECntByDoctorid($doctorid);
        $arr[] = $this->getPCCntByDoctorid($doctorid);
        $arr[] = $this->getMeetingCntByDoctorid($doctorid);
        $arr[] = $doctor_hezuo->first_patient_date;
        return $arr;
    }

    private function getScanCnt ($doctorid) {
        $sql = " select count(a.id) as cnt
            from wxusers a
            inner join doctor_hezuos b on b.doctorid=a.doctorid
            where b.doctorid = {$doctorid} and a.createtime>b.starttime ";
        return Dao::queryValue($sql);
    }

    private function getScanNotBaodaoCnt ($doctorid) {
        $sql = " select count(a.id) as cnt
            from wxusers a
            inner join users b on b.id=a.userid
            inner join doctor_hezuos c on c.doctorid=a.doctorid
            where a.doctorid = {$doctorid} and b.patientid=0
            and a.createtime>c.starttime ";
        return Dao::queryValue($sql);
    }

    private function getAECntByDoctorid ($doctorid) {
        $sql = " select count(a.id) as cnt
            from patients a
            inner join papers b on b.patientid=a.id
            where a.first_doctorid = {$doctorid} and (b.papertplid=275143816 or b.papertplid=312586776) ";
        return Dao::queryValue($sql);
    }

    private function getPCCntByDoctorid ($doctorid) {
        $sql = " select count(a.id) as cnt
            from patients a
            inner join papers b on b.patientid=a.id
            where a.first_doctorid = {$doctorid} and (b.papertplid=275209326 or b.papertplid=312586776) ";
        return Dao::queryValue($sql);
    }

    private function getCntByCompanyDoctorid ($company, $doctorid, $condEx="") {
        $sql = " select count(a.id) as cnt
            from patient_hezuos a
            inner join patients b on b.id=a.patientid
            where a.company = :company and b.first_doctorid = :doctorid {$condEx}";
        $bind = [];
        $bind[":company"] = $company;
        $bind[":doctorid"] = $doctorid;
        return Dao::queryValue($sql, $bind);
    }

    private function getMeetingCntByDoctorid ($doctorid) {
        $sql = " select count(a.id) as cnt
        from cdrmeetings a
        inner join patients b on b.id=a.patientid
        inner join patient_hezuos c on c.patientid=b.id
        where b.doctorid={$doctorid} and a.cdr_end_time - a.cdr_bridge_time > 0 ";

        return Dao::queryValue($sql);
    }

    public function doPatientDrugState () {
        $startdate = XRequest::getValue("startdate", "2017-06-01");
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));
        $type = XRequest::getValue("type", "all");
        $redmine = 4285;
        XContext::setValue("startdate", $startdate);
        XContext::setValue("enddate", $enddate);
        XContext::setValue("redmine", $redmine);
        XContext::setValue("type", $type);
        return self::SUCCESS;
    }

    public function doPatientDrugStateJson () {
        $startdate = XRequest::getValue("startdate", "2017-07-12");
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));
        $type = XRequest::getValue("type", "all");

        $data = [];

        $nodes = [];
        $nodes[0]["name"] = "总数";
        for($i=1; $i<=7; $i++){
            $nodes[]["name"] = "在服(" . $i . ")";
            $nodes[]["name"] = "不服(" . $i . ")";
            $nodes[]["name"] = "未知(" . $i . ")";
            if(1 != $i){
                $nodes[]["name"] = "停服(" . $i . ")";
            }
        }

        $links = [];
        $bind = [];
        $bind[":startdate"] = "{$startdate}";
        $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));

        $cond = "";
        if("sunflower" == $type){
            $cond = " and a.id in ( select patientid from patient_hezuos where status=1 ) ";
        }
        if("notsunflower" == $type){
            $cond = " and a.id not in ( select patientid from patient_hezuos where status=1 ) ";
        }

        $sql = " select
        b.state as state,
        count(a.id) as cnt
        from patients a
        inner join patientdrugstates b on b.patientid=a.id
        where a.is_test=0 and b.pos=1 {$cond}
        and a.createtime > :startdate
        and a.createtime < :enddate
        group by b.state order by b.state ";
        $arr = Dao::queryRows($sql, $bind);

        foreach ($arr as $k => $v) {
            $temp = [];
            if("ondrug" == $v["state"]){
                $temp["target"] = "在服(1)";
            }
            if("nodrug" == $v["state"]){
                $temp["target"] = "不服(1)";
            }
            if("stopdrug" == $v["state"]){
                $temp["target"] = "停服(1)";
            }
            if("unknown" == $v["state"]){
                $temp["target"] = "未知(1)";
            }
            $temp["source"] = "总数";
            $temp["value"] = $v["cnt"];
            $links[] = $temp;
        }

        $states = [
            "ondrug" => "在服",
            "nodrug" => "不服",
            "stopdrug" => "停服",
            "unknown" => "未知"
        ];

        for($i=1; $i<7; $i++){
            $j = $i+1;
            foreach ($states as $k => $v) {
                $temp = [];
                $sql = " select
                b.state as state,
                count(a.id) as cnt
                from patients a
                inner join patientdrugstates b on b.patientid=a.id
                where a.is_test=0 and b.pos={$j} {$cond}
                and a.id in (
                    select
                    a.id
                    from patients a
                    inner join patientdrugstates b on b.patientid=a.id
                    where b.pos={$i} and state='{$k}'
                    and a.createtime > :startdate
                    and a.createtime < :enddate
                    )
                group by b.state order by b.state ";
                $arr = Dao::queryRows($sql, $bind);

                $ondrug["target"] = "在服(" . $j . ")";
                $ondrug["source"] = $v . "(" . $i .")";
                $ondrug["value"] = 0;
                $nodrug["target"] = "不服(" . $j . ")";
                $nodrug["source"] = $v . "(" . $i .")";
                $nodrug["value"] = 0;
                $stopdrug["target"] = "停服(" . $j . ")";
                $stopdrug["source"] = $v . "(" . $i .")";
                $stopdrug["value"] = 0;
                $unknown["target"] = "未知(" . $j . ")";
                $unknown["source"] = $v . "(" . $i .")";
                $unknown["value"] = 0;

                foreach ($arr as $m => $n) {
                    $temp = [];
                    if("ondrug" == $n["state"]){
                        $ondrug["value"] = $n["cnt"];
                    }
                    if("nodrug" == $n["state"]){
                        $nodrug["value"] = $n["cnt"];
                    }
                    if("stopdrug" == $n["state"]){
                        $stopdrug["value"] = $n["cnt"];
                    }
                    if("unknown" == $n["state"]){
                        $unknown["value"] = $n["cnt"];
                    }
                }
                $links[] = $ondrug;
                $links[] = $nodrug;
                $links[] = $stopdrug;
                $links[] = $unknown;
            }

        }

        $data["nodes"] = $nodes;
        $data["links"] = $links;
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    public function doStopDrugData () {
        $startdate = XRequest::getValue("startdate", "2017-06-01");
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));
        $redmine = 5091;
        XContext::setValue("startdate", $startdate);
        XContext::setValue("enddate", $enddate);
        XContext::setValue("redmine", $redmine);
        return self::SUCCESS;
    }

    public function doStopDrugDataJson () {
        $startdate = XRequest::getValue("startdate", "2017-06-01");
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));

        $sql = " select a.id from patient_hezuos a
        inner join drugitems b on b.patientid=a.patientid
        where b.medicineid=2 and b.type=0
        and a.createtime >= :startdate and a.createtime <= :enddate
        group by a.id ";

        $bind = [];
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));

        $ids = Dao::queryValues($sql, $bind);

        $data = array();
        foreach ($ids as $id) {
            $patient_hezuo = Patient_hezuo::getById($id);
            $patient = $patient_hezuo->patient;
            $patientid = $patient->id;

            $drugitems = DrugItemDao::getListStopByPatientidMedicineid($patientid, 2);
            $patientmedicineref = PatientMedicineRefDao::getByPatientidMedicineid($patientid, 2);

            $temp = array();
            $temp[0] = $patientid;
            $temp[1] = $patient->getCreateDay();
            $temp[2] = $patient->doctor->name;
            $temp[3] = $patient_hezuo->getCreateDay();
            $temp[4] = '';
            $temp[5] = $patientmedicineref->getStopDrugTypeStr();
            $temp[6] = '';
            $temp[7] = '';
            foreach ($drugitems as $k => $drugitem) {
                $temp[4] .= $drugitem->record_date . '|';
                $temp[6] .= $drugitem->content . '|';

                if($drugitems[$k+1] instanceof DrugItem){
                    $nextstopdate = $drugitems[$k+1]->record_date;
                    $drugitem_again = DrugItemDao::getByPatientid($patientid, " and medicineid=2 and type!=0 and record_date>'{$drugitem->record_date}' and record_date<'{$nextstopdate}' ");
                }else {
                    $drugitem_again = DrugItemDao::getByPatientid($patientid, " and medicineid=2 and type!=0 and record_date>'{$drugitem->record_date}' ");
                }

                $temp[7] .= $drugitem_again instanceof DrugItem ? $drugitem_again->record_date . '|' : '0000-00-00 00:00:00|';
            }
            $temp[8] = $patient_hezuo->getStatusStr();
            $data[] = $temp;
        }

        $headarr = array(
            "PatientId",
            "报到日期",
            "医生姓名",
            "入组日期",
            "择思达用药记录为0的日期",
            "停药原因",
            "备注内容",
            "择思达恢复不为0的日期",
            "当前状态");
        ExcelUtil::createForWeb($data, $headarr);
    }

    public function doDrugRadio () {
        $startdate = XRequest::getValue("startdate", "2017-06-01");
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));
        $week = XRequest::getValue("week", 4);
        $redmine = 5288;
        XContext::setValue("startdate", $startdate);
        XContext::setValue("enddate", $enddate);
        XContext::setValue("week", $week);
        XContext::setValue("redmine", $redmine);
        return self::SUCCESS;
    }

    public function doDrugRadioOutput () {
        $startdate = XRequest::getValue("startdate", "2017-06-01");
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));
        $week = XRequest::getValue("week", 4);

        $optasktpl_week = OpTaskTplDao::getOneByUnicode("baseDrug_{$week}_week:");

        $sql = " select id from optasks where optasktplid = {$optasktpl_week->id} and createtime >= :startdate and createtime <= :enddate ";

        $bind = [];
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));

        $ids = Dao::queryValues($sql, $bind);

        $data = array();
        foreach ($ids as $id) {
            $optask = OpTask::getById($id);
            $patient = $optask->patient;

            if(false == $patient instanceof Patient || 1 == $patient->is_test){
                continue;
            }

            $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid("Lilly", $patient->id);

            if(false == $patient_hezuo instanceof Patient_hezuo){
                continue;
            }

            $doctor = $patient->doctor;
            $patientid = $patient->id;

            // 拿到生成任务+10天内最后一条择思达用药记录
            $optask_createtime = $optask->createtime;
            $endtime = date('Y-m-d H:i:s', strtotime($optask_createtime) + 86400 * 10);
            $zsdDrugitem = $this->getLastZsdDrugitemBetweenTimes($patientid, $optask_createtime, $endtime);
            $patientmedicineref = PatientMedicineRefDao::getByPatientidMedicineid($patient->id, 2);

            $temp = array();
            $temp[] = $patient->id;
            $temp[] = $patient->doctor->name;
            $doctor_hezuo = Doctor_hezuoDao::getOneByCompanyDoctorid("Lilly", $patient->doctorid);
            if($doctor_hezuo instanceof Doctor_hezuo){
                $temp[] = $doctor_hezuo->marketer_name;
            }else {
                $temp[] = "";
            }
            $temp[] = $patient->getCreateDay();
            $temp[] = $patient->getDayCntFromBaodao();
            $temp[] = $patient_hezuo->getCreateDay();

            $firstZsdDrugitem = DrugItemDao::getFirstValidByPatientidMedicineid($patientid, 2, 1);
            $lastZsdDrugitem = DrugItemDao::getLastByPatientid($patientid, 2, 1);
            $firstAEPC = PaperDao::getAEPCByPatientAscOrderById($patient, true);
            $lastAEPC = PaperDao::getAEPCByPatientAscOrderById($patient, false);
            $temp[] = $firstZsdDrugitem->createtime;
            $temp[] = $lastZsdDrugitem->createtime;
            $temp[] = $firstAEPC->createtime;
            $temp[] = $lastAEPC->createtime;
            $temp[] = PaperDao::getAEPCCntByPatient($patient);

            $temp[] = $optask->getCreateDay();

            if($zsdDrugitem instanceof DrugItem){
                $pipe = $this->getPipeBetweenTimes($patientid, $optask_createtime, $endtime);

                $temp[] = "是";
                // 生成任务日期+10天内有择思达用药记录的患者，在任务生成后到更新期间是否有运营发送的消息/量表/电话
                if($pipe instanceof Pipe){
                    $temp[] = "是";
                } else {
                    $temp[] = "否";
                }

                // 择思达记录是否为0（取该时间区间内距今最近一次用药情况）
                if( 0 == $zsdDrugitem->value){
                    $temp[] = "是";
                } else {
                    $temp[] = "否";
                }
            } else {
                $temp[] = "否";
                $temp[] = "";
                $temp[] = "";
            }

            $temp[] = $patientmedicineref instanceof PatientMedicineRef ? $patientmedicineref->getStopDrugTypeStr() : "";
            $temp[] = $patientmedicineref instanceof PatientMedicineRef ? $patientmedicineref->remark : "";
            $temp[] = $patient_hezuo->getStatusStr();
            $temp[] = $patient_hezuo->enddate;

            $unsubscribe_reason = $this->getUnsubscribeReason($patient);
            $temp[] = $unsubscribe_reason;
            $data[] = $temp;
        }

        $headarr = array(
            "patientID",
            "所属医生",
            "所属礼来代表",
            "报到日期",
            "报到时长（距今）",
            "入项目日期",
            "第一条择思达用药记录日期",
            "最后一条择思达用药记录日期",
            "第一条AE或PC或AEPC创建日期",
            "最后一条AE或PC或AEPC创建日期",
            "截止目前AE/AEPC/PC创建总个数",
            "生成{$week}周基础用药提醒日期",
            "生成任务+10天内是否有择思达用药记录",
            "生成任务日期+10天内有择思达用药记录的患者，在任务生成后到更新期间是否有运营发送的消息/量表/电话",
            "择思达记录是否为0（取该时间区间内距今最近一次用药情况）",
            "停药 原因（遵医嘱停药/自行停药）",
            "停药备注",
            "当前在项目中的状态",
            "出组日期",
            "取关原因",
        );
        ExcelUtil::createForWeb($data, $headarr);
    }

    private function getLastZsdDrugitemBetweenTimes ($patientid, $startime, $endtime) {
        $cond = " and patientid = :patientid and createtime > :starttime and createtime < :endtime ";

        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':starttime'] = $startime;
        $bind[':endtime'] = $endtime;

        $cond .= " and medicineid=2 ORDER BY id desc ";

        return Dao::getEntityByCond("DrugItem", $cond, $bind);
    }

    private function getPipeBetweenTimes ($patientid, $startime, $endtime) {
        $cond = " and patientid = :patientid and createtime > :starttime and createtime < :endtime
        and ((objtype='PushMsg' and objcode='byAuditor') or objtype='Paper' or objtype='CdrMeeting') ";

        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':starttime'] = $startime;
        $bind[':endtime'] = $endtime;

        return Dao::getEntityByCond("Pipe", $cond, $bind);
    }

    private function getUnsubscribeReason (Patient $patient) {
        $str = "";
        $optask = OpTaskDao::getOneByPatientUnicode($patient, 'follow:outSunflower', false);
        if($optask instanceof OpTask){
            $optaskopnodelog = OpTaskOpNodeLogDao::getLastOneByOpTaskid($optask->id);
            if($optaskopnodelog instanceof OpTaskOpNodeLog){
                $str = $optaskopnodelog->opnode->title;
            }
        }

        return $str;
    }

    public function doOpNodeLogData () {
        $startdate = XRequest::getValue("startdate", date('Y-m-d', strtotime(date('Y-m-d')) - 86400 * 28));
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));
        $optasktplid = XRequest::getValue("optasktplid", 0);
        $status = XRequest::getValue("status", -1);
        $redmine = 5324;

        $mydisease = $this->mydisease;

        $cond = " and status=1 order by objtype, code, subcode ";

        $optasktpls = Dao::getEntityListByCond('OpTaskTpl', $cond);

        if ($mydisease instanceof Disease) {
            $temp = [];
            foreach ($optasktpls as $optasktpl) {
                if (0 == $optasktpl->diseaseids || in_array($mydisease->id, $optasktpl->getDiseaseIdArr())) {
                    $temp[] = $optasktpl;
                }
            }
            $optasktpls = $temp;
        }

        XContext::setValue("optasktpls", $optasktpls);

        XContext::setValue("startdate", $startdate);
        XContext::setValue("enddate", $enddate);
        XContext::setValue("optasktplid", $optasktplid);
        XContext::setValue("status", $status);
        XContext::setValue("redmine", $redmine);
        return self::SUCCESS;
    }

    public function doOpNodeLogDataOutput () {
        $optasktplid = XRequest::getValue("optasktplid", 0);
        $status = XRequest::getValue("status", -1);
        $startdate = XRequest::getValue("startdate", date('Y-m-d', strtotime(date('Y-m-d')) - 86400 * 28));
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));

        $sql = " select id from opnodes where optasktplid = :optasktplid ";

        $bind = [];
        $bind[":optasktplid"] = $optasktplid;

        $opnodeids = Dao::queryValues($sql, $bind);

        $sql = " select id from optasks where optasktplid = :optasktplid and plantime >= :startdate and plantime < :enddate ";

        $bind = [];
        $bind[":optasktplid"] = $optasktplid;
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));

        if($status > -1){
            $sql .= " and status = :status ";
            $bind[":status"] = $status;
        }

        $ids = Dao::queryValues($sql, $bind);

        $data = array();
        foreach ($ids as $id) {
            $optask = OpTask::getById($id);
            $patient = $optask->patient;

            if(false == $patient instanceof Patient || 1 == $patient->is_test){
                continue;
            }

            $temp = array();
            $optask_createday = $optask->getCreateDay();
            $patient_createday = $patient->getCreateDay();
            $temp[] = $optask_createday;
            $temp[] = $optask->donetime;
            $temp[] = $optask->getLevelStr();
            $temp[] = $patient->id;
            $temp[] = $patient_createday;
            $temp[] = XDateTime::getDateDiff($optask_createday, $patient_createday);

            $has_opnodeids = $this->getHasOpNodeIds($optask->id);

            foreach ($opnodeids as $k => $opnodeid) {
                if(0 == $k){
                    //根节点，都写有
                    $temp[] = '有';
                    continue;
                }
                if(in_array($opnodeid, $has_opnodeids)){
                    $temp[] = '有';
                }else {
                    $temp[] = '无';
                }
            }
            $data[] = $temp;
        }

        $headarr = array(
            "任务创建时间",
            "任务关闭时间",
            "任务优先级",
            "patientid",
            "报到时间",
            "创建任务时患者的报到时长",
        );
        foreach ($opnodeids as $k => $opnodeid) {
            $opnode = OpNode::getById($opnodeid);
            $headarr[] = $opnode->title;
        }
        ExcelUtil::createForWeb($data, $headarr);
    }

    private function getHasOpNodeIds ($optaskid) {
        $sql = " select opnodeid from optaskopnodelogs where optaskid = :optaskid ";

        $bind = [];
        $bind[":optaskid"] = $optaskid;

        return Dao::queryValues($sql, $bind);
    }

    public function doPipeLevel () {
        $fromdate = XRequest::getValue("fromdate", date('Y-m-d', strtotime(date('Y-m-d')) - 86400 * 7));
        $todate = XRequest::getValue("todate", date('Y-m-d'));
        $mydisease = $this->mydisease;

        $diff = XDateTime::getDateDiff($todate, $fromdate);
        DBC::requireTrue($diff<60, "查询数据过大，请选择的时间段小于等于60天！");

        $redmine = 5422;
        $data = [];

        if($mydisease instanceof Disease && 1 == $mydisease->id){
            $data['pipelevel_data'] = $this->getPipelevelData($fromdate, $todate);
        }

        $data['optask_data'] = $this->getOptaskData($fromdate, $todate, $mydisease);

        XContext::setValue("fromdate", $fromdate);
        XContext::setValue("todate", $todate);
        XContext::setValue("redmine", $redmine);
        XContext::setValue("data", $data);
        return self::SUCCESS;
    }

    public function doGetOptaskDetail () {
        $fromdate = XRequest::getValue("fromdate", "");
        $todate = XRequest::getValue("todate", "");
        $type_str = XRequest::getValue("type_str", "");
        $worktime_str = XRequest::getValue("worktime_str", "");
        $level_str = XRequest::getValue("level_str", "urgent");
        $mydisease = $this->mydisease;

        $data = [];
        $arr = $this->getOptaskRows($fromdate, $todate, $mydisease);

        foreach ($arr as $k => $v) {
            $temp = array();
            $patientid = $v["patientid"];
            $createtime = $v["createtime"];
            $donetime = $v["donetime"];
            $level = $v["level"];
            $replytime = $v["replytime"];

            $isInType = $this->isInType($replytime, $donetime, $type_str);
            $worktime_str2 = $this->getWorkTimeStr($createtime);
            $level_str2 = 2 == $level ? 'not_urgent' : 'urgent';

            if($isInType && $worktime_str == $worktime_str2 && $level_str == $level_str2){
                $temp[] = $patientid;
                $temp[] = $createtime;
                $temp[] = $replytime;
                $temp[] = $donetime;
                $data[] = $temp;
            }
        }
        $headarr = array(
            "patientid",
            "任务创建时间",
            "任务当天首次回复时间",
            "任务关闭时间"
        );
        ExcelUtil::createForWeb($data, $headarr);
    }

    private function getPipelevelData ($fromdate, $todate) {
        $pipelevel_data = [];

        $sql = "select is_urgent, is_urgent_fix, count(id) as cnt
            from pipelevels
            where createtime > :fromdate and createtime < :todate
            group by is_urgent, is_urgent_fix";

        $bind = [];
        $bind[":fromdate"] = $fromdate;
        $bind[":todate"] = date("Y-m-d", (strtotime($todate) + 86400));
        $arr = Dao::queryRows($sql, $bind);

        //紧急数
        $is_urgent_cnt = 0;
        //不紧急数
        $not_is_urgent_cnt = 0;
        //紧急修正不紧急数
        $is_urgent_fix_cnt = 0;
        //不紧急修正紧急数
        $not_is_urgent_fix_cnt = 0;

        foreach ($arr as $k => $v) {
            if(2 == $v['is_urgent']){
                $is_urgent_cnt += $v['cnt'];
                if(1 == $v['is_urgent_fix']){
                    $is_urgent_fix_cnt += $v['cnt'];
                }
            }
            if(1 == $v['is_urgent']){
                $not_is_urgent_cnt += $v['cnt'];
                if(2 == $v['is_urgent_fix']){
                    $not_is_urgent_fix_cnt += $v['cnt'];
                }
            }
        }

        $pipelevel_data['is_urgent_cnt'] = $is_urgent_cnt;
        $pipelevel_data['not_is_urgent_cnt'] = $not_is_urgent_cnt;
        $pipelevel_data['is_urgent_fix_cnt'] = $is_urgent_fix_cnt;
        $pipelevel_data['not_is_urgent_fix_cnt'] = $not_is_urgent_fix_cnt;

        $pipelevel_data["recall"] = $is_urgent_cnt + $not_is_urgent_fix_cnt > 0 ? number_format(round($is_urgent_cnt/($is_urgent_cnt+$not_is_urgent_fix_cnt), 4)*100) : 0;
        $pipelevel_data["precision"] = $is_urgent_cnt > 0 ? number_format(round(($is_urgent_cnt-$is_urgent_fix_cnt)/$is_urgent_cnt, 4)*100) : 0;
        $pipelevel_data["accuracy"] = $is_urgent_cnt + $not_is_urgent_cnt > 0 ? number_format(round(($is_urgent_cnt+$not_is_urgent_cnt-$is_urgent_fix_cnt-$not_is_urgent_fix_cnt) / ($is_urgent_cnt+$not_is_urgent_cnt), 4)*100) : 0;
        return $pipelevel_data;
    }

    private function getOptaskRows ($fromdate, $todate, $disease) {
        $cond = "";
        $bind = [];
        if($disease instanceof Disease){
            $cond = " and a.diseaseid = :diseaseid ";
            $bind[":diseaseid"] = $disease->id;
        }

        $optasktpl = OpTaskTplDao::getOneByUnicode("PatientMsg:message");

        $sql = "select
            a.patientid as patientid,
            a.createtime as createtime,
            a.donetime as donetime,
            a.level as level,
            if(min(b.createtime) is null, '0000-00-00 00:00:00', min(b.createtime)) as replytime
            from optasks a
            left join pushmsgs b on b.patientid=a.patientid
            and left(b.createtime, 10)=left(a.createtime, 10) and b.createtime>a.createtime
            and b.send_by_objtype='Auditor' and b.send_by_objid>1
            where a.optasktplid = :optasktplid and a.level in (2, 4)
            and a.createtime > :fromdate and a.createtime < :todate {$cond}
            group by a.id";

        $bind[":optasktplid"] = $optasktpl->id;
        $bind[":fromdate"] = $fromdate;
        $bind[":todate"] = date("Y-m-d", (strtotime($todate) + 86400));

        return Dao::queryRows($sql, $bind);
    }

    private function getOptaskData ($fromdate, $todate, $disease) {
        $optask_data = [];

        $pie = [
            ["name" => "小于30分", value => 0],
            ["name" => "30分到2小时之间", value => 0],
            ["name" => "大于2小时", value => 0],
        ];
        $optask_data["urge_pie"] = $pie;
        $optask_data["noturge_pie"] = $pie;

        $optask_sub_arr = [
            "firstreplytime" => [
                "urgent" => [
                    "time" => 0,
                    "cnt" => 0
                ],
                "not_urgent" => [
                    "time" => 0,
                    "cnt" => 0
                ]
            ],
            "donetime" => [
                "urgent" => [
                    "time" => 0,
                    "cnt" => 0
                ],
                "not_urgent" => [
                    "time" => 0,
                    "cnt" => 0
                ]
            ],
            "notreplycnt_data" => [
                "urgent" => 0,
                "not_urgent" => 0
            ],
            "notdonecnt_data" => [
                "urgent" => 0,
                "not_urgent" => 0
            ]
        ];
        $optask_arr = [
            "worktime" => $optask_sub_arr,
            "workbefore" => $optask_sub_arr,
            "workafter" => $optask_sub_arr,
            "not_worktime" => $optask_sub_arr
        ];

        $arr = $this->getOptaskRows($fromdate, $todate, $disease);
        foreach ($arr as $k => $v) {
            $patientid = $v["patientid"];
            $createtime = $v["createtime"];
            $donetime = $v["donetime"];
            $level = $v["level"];
            $replytime = $v["replytime"];

            $worktime_str = $this->getWorkTimeStr($createtime);
            $level_str = 2 == $level ? 'not_urgent' : 'urgent';
            $level_str_pie = 2 == $level ? 'noturge_pie' : 'urge_pie';


            //首次回复时间
            if('0000-00-00 00:00:00' != $replytime){
                $num = strtotime($replytime) - strtotime($createtime);
                $optask_arr[$worktime_str]["firstreplytime"][$level_str]["time"] += $num;
                $optask_arr[$worktime_str]["firstreplytime"][$level_str]["cnt"]++;
                if($this->isBettweenTwoNums(0, 1800, $num)){
                    $optask_data[$level_str_pie][0]["value"]++;
                }
                if($this->isBettweenTwoNums(1800, 7200, $num)){
                    $optask_data[$level_str_pie][1]["value"]++;
                }
                if($this->isBettweenTwoNums(7200, -1, $num)){
                    $optask_data[$level_str_pie][2]["value"]++;
                }
            }

            //任务关闭时间
            if('0000-00-00 00:00:00' != $donetime){
                $optask_arr[$worktime_str]["donetime"][$level_str]["time"] += strtotime($donetime) - strtotime($createtime);
                $optask_arr[$worktime_str]["donetime"][$level_str]["cnt"]++;
            }

            //当天未回复数
            if('0000-00-00 00:00:00' == $replytime){
                $optask_arr[$worktime_str]["notdonecnt_data"][$level_str]++;
            }

            //未关闭数
            if('0000-00-00 00:00:00' == $donetime){
                $optask_arr[$worktime_str]["notreplycnt_data"][$level_str]++;
            }
        }

        foreach ($optask_arr as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                if('firstreplytime' == $k2 || 'donetime' == $k2){
                    foreach ($v2 as $k3 => $v3) {
                        $optask_data[$k2][$k1][$k3] = $v3["cnt"] > 0 ? number_format(round($v3["time"]/($v3["cnt"]*3600), 4), 2) : 0;
                    }
                }
                if('notreplycnt_data' == $k2 || 'notdonecnt_data' == $k2){
                    $optask_data[$k2][$k1] = $v2;
                }
            }
        }
        return $optask_data;
    }

    //第一个参数表示区间左起点； 第二个参数表示区间右起点(-1表示无穷大)； 第三个参数表示要判断的数字
    private function isBettweenTwoNums ($min, $max, $num) {
        if($min <= $num && ($num < $max || $max < 0)){
            return true;
        }
        return false;
    }

    private function isInType($replytime, $donetime, $type_str){
        //首次回复时间
        if("firstreplytime" == $type_str && '0000-00-00 00:00:00' != $replytime){
            return true;
        }

        //任务关闭时间
        if("donetime" == $type_str && '0000-00-00 00:00:00' != $donetime){
            return true;
        }

        //当天未回复数
        if("notdonecnt_data" == $type_str && '0000-00-00 00:00:00' == $replytime){
            return true;
        }

        //未关闭数
        if("notreplycnt_data" == $type_str && '0000-00-00 00:00:00' == $donetime){
            return true;
        }
        return false;
    }

    private function getWorkTimeStr($createtime){
        if($this->isworktime($createtime)){
            return 'worktime';
        }
        if($this->isworkbefore($createtime)){
            return 'workbefore';
        }
        if($this->isworkafter($createtime)){
            return 'workafter';
        }
        return 'not_worktime';
    }

    //工作日[10点, 19:30)
    private function isworktime ($createtime) {
        $createday = substr($createtime, 0, 10);
        $hour = date('H',strtotime($createtime));
        $minute = date('i',strtotime($createtime));
        $num = date('Hi',strtotime($createtime));

        if(false == FUtil::isHoliday($createday)){
            if($this->isBettweenTwoNums("1000", "1930", $num)){
                return true;
            }
        }

        return false;
    }

    //工作日[0点, 10点)
    private function isworkbefore ($createtime) {
        $createday = substr($createtime, 0, 10);
        $frontday = date('Y-m-d', strtotime($createday) - 86400);
        $num = date('Hi',strtotime($createtime));

        if(false == FUtil::isHoliday($createday) && false == FUtil::isHoliday($frontday)){
            if($this->isBettweenTwoNums("0000", "1000", $num)){
                return true;
            }
        }

        return false;
    }

    //工作日[19:30, 24点)
    private function isworkafter ($createtime) {
        $createday = substr($createtime, 0, 10);
        $nextday = date('Y-m-d', strtotime($createday) + 86400);
        $num = date('Hi',strtotime($createtime));

        if(false == FUtil::isHoliday($createday) && false == FUtil::isHoliday($nextday)){
            if($this->isBettweenTwoNums("1930", "2400", $num)){
                return true;
            }
        }

        return false;
    }

    public function doOptaskDataForADHD () {
        $type = XRequest::getValue("type", 'twomonth');
        $fromdate = XRequest::getValue("fromdate", date('Y-m-d', strtotime(date('Y-m-d')) - 86400 * 7));
        $todate = XRequest::getValue("todate", date('Y-m-d'));

        $redmine = 5840;
        $types = [
            "twomonth" => "0~55天",
            "twomonthlater" => "56天及以上",
            "sunflower" => "礼来项目",
            "liuyuan" => "六院项目",
        ];

        $diff = XDateTime::getDateDiff($todate, $fromdate);
        DBC::requireTrue($diff<60, "查询数据过大，请选择的时间段小于等于60天！");

        $data = [];

        //礼来项目
        if("sunflower" == $type){
            $data = $this->getDataForADHDSunflower($fromdate, $todate);
        }

        //六院管理项目
        if("liuyuan" == $type){
            $data = $this->getDataForADHDLiuyuan($fromdate, $todate);
        }

        //常规患者 报到两个月内
        if("twomonth" == $type){
            $data = $this->getDataForADHDTwoMonth($fromdate, $todate);
        }

        //常规患者 报到两个月以上
        if("twomonthlater" == $type){
            $data = $this->getDataForADHDTwoMonthLater($fromdate, $todate);
        }

        XContext::setValue("type", $type);
        XContext::setValue("types", $types);
        XContext::setValue("fromdate", $fromdate);
        XContext::setValue("todate", $todate);
        XContext::setValue("redmine", $redmine);
        XContext::setValue("data", $data);
        return self::SUCCESS;
    }

    private function getDataForADHDSunflower ($fromdate, $todate) {
        $levels = [5];
        $data = [];
        foreach ($levels as $level) {
            $data = $this->getDataByLevelAndDates($level, $fromdate, $todate);
        }
        $data = $this->getCaculData($data);
        return $data;
    }

    private function getDataForADHDLiuyuan ($fromdate, $todate) {
        $levels = [4, 2];
        $data = [];

        $mgtgrouptpl = MgtGroupTplDao::getByEname("pkuh6");
        $cond_patient = " and mgtgrouptplid = :mgtgrouptplid ";
        $bind = [];
        $bind[':mgtgrouptplid'] = $mgtgrouptpl->id;

        foreach ($levels as $level) {
            $arr = $this->getDataByLevelAndDates($level, $fromdate, $todate, $cond_patient, $bind);
            $data[] = $this->getCaculData($arr);
        }
        $data = $this->getFinalData($data);
        return $data;
    }

    private function getDataForADHDTwoMonth ($fromdate, $todate) {
        $levels = [4, 2];
        $data = [];

        $cond_patient = " and mgtgrouptplid=0 ";
        $bind = [];
        $baodaoday = date('Y-m-d', strtotime("-55 days"));
        $cond_patient .= " and createtime > :baodaoday ";
        $bind[':baodaoday'] = $baodaoday;
        foreach ($levels as $level) {
            $arr = $this->getDataByLevelAndDates($level, $fromdate, $todate, $cond_patient, $bind);
            $data[] = $this->getCaculData($arr);
        }
        $data = $this->getFinalData($data);
        return $data;
    }

    private function getDataForADHDTwoMonthLater ($fromdate, $todate) {
        $levels = [4, 2];
        $data = [];

        $cond_patient = " and mgtgrouptplid=0 ";
        $bind = [];
        $baodaoday = date('Y-m-d', strtotime("-55 days"));
        $cond_patient .= " and createtime <= :baodaoday ";
        $bind[':baodaoday'] = $baodaoday;

        foreach ($levels as $level) {
            $arr = $this->getDataByLevelAndDates($level, $fromdate, $todate, $cond_patient, $bind);
            $data[] = $this->getCaculData($arr);
        }
        $data = $this->getFinalData($data);
        return $data;
    }

    private function getDataByLevelAndDates ($level, $fromdate, $todate, $cond_patient = "", $bind = []){
        $arr = [
            "sum_times" => 0,
            "max_apply_times" => 0,
            "cnt" => 0,
            "apply_good_cnt" => 0,
            "apply_cnt" => 0,
            "not_apply_cnt" => 0,
        ];
        $data = [
            "time1" => [
                "name" => "工作日【0，10）",
                "values" => $arr,
            ],
            "time2" => [
                "name" => "工作日【10，12）",
                "values" => $arr,
            ],
            "time3" => [
                "name" => "工作日【12，13）",
                "values" => $arr,
            ],
            "time4" => [
                "name" => "工作日【13，17）",
                "values" => $arr,
            ],
            "time5" => [
                "name" => "工作日【17，17：30）",
                "values" => $arr,
            ],
            "time6" => [
                "name" => "工作日【17：30，19）",
                "values" => $arr,
            ],
            "time7" => [
                "name" => "工作日【19，24）",
                "values" => $arr,
            ],
            "time8" => [
                "name" => "非工作日",
                "values" => $arr,
            ],
            "worktime" => [
                "name" => "工作时间",
                "values" => $arr,
            ],
        ];

        $optasktpl = OpTaskTplDao::getOneByUnicode("PatientMsg:message");

        //有效患者
        $cond = " and a.patientid in (select id from patients where ( status = 1 or (status = 0 and auditstatus = 0) ) {$cond_patient}) ";

        $sql = "select
            a.patientid as patientid,
            a.createtime as createtime,
            a.donetime as donetime,
            a.level as level,
            if(min(b.createtime) is null, '0000-00-00 00:00:00', min(b.createtime)) as replytime
            from optasks a
            left join pushmsgs b on b.patientid=a.patientid
            and b.createtime>a.createtime and b.createtime<a.donetime
            and b.send_by_objtype='Auditor' and b.send_by_objid>1
            where a.level = :level and a.optasktplid = :optasktplid
            and a.createtime > :fromdate and a.createtime < :todate
            and a.diseaseid = 1 and a.status = 1 {$cond}
            group by a.id";

        $bind[":level"] = $level;
        $bind[":optasktplid"] = $optasktpl->id;
        $bind[":fromdate"] = $fromdate;
        $bind[":todate"] = date("Y-m-d", (strtotime($todate) + 86400));

        $rows = Dao::queryRows($sql, $bind);

        foreach ($rows as $k => $v) {
            $patientid = $v["patientid"];
            $createtime = $v["createtime"];
            $donetime = $v["donetime"];
            $level = $v["level"];
            $replytime = $v["replytime"];

            $applytime = strtotime($replytime) - strtotime($createtime);

            $timetype = $this->getTimeType($createtime);

            $applytime > 0 ? $data[$timetype]["values"]["sum_times"] += $applytime : 0;
            $data[$timetype]["values"]["max_apply_times"] = $data[$timetype]["values"]["max_apply_times"] > $applytime ? $data[$timetype]["values"]["max_apply_times"] : $applytime;
            $data[$timetype]["values"]["cnt"] += 1;
            $this->isGoodRepply($level, $applytime) ? $data[$timetype]["values"]["apply_good_cnt"] += 1 : 0;
            $applytime > 0 ? $data[$timetype]["values"]["apply_cnt"] += 1 : 0;
            $applytime < 0 ? $data[$timetype]["values"]["not_apply_cnt"] += 1 : 0;

            //工作时间的定义：工作日【10，12）+工作日【13，17）+工作日【17：30，19）
            if('time2'==$timetype || 'time4'==$timetype || 'time6'==$timetype){
                $applytime > 0 ? $data['worktime']["values"]["sum_times"] += $applytime : 0;
                $data['worktime']["values"]["max_apply_times"] = $data['worktime']["values"]["max_apply_times"] > $applytime ? $data['worktime']["values"]["max_apply_times"] : $applytime;
                $data['worktime']["values"]["cnt"] += 1;
                $this->isGoodRepply($level, $applytime) ? $data['worktime']["values"]["apply_good_cnt"] += 1 : 0;
                $applytime > 0 ? $data['worktime']["values"]["apply_cnt"] += 1 : 0;
                $applytime < 0 ? $data['worktime']["values"]["not_apply_cnt"] += 1 : 0;
            }
        }

        return $data;
    }

    private function isGoodRepply ($level, $applytime) {
        $arr = [
            2 => 3600,
            4 => 600,
            5 => 1800,
        ];
        foreach ($arr as $k => $v) {
            if($level == $k){
                if($applytime > 0 && $applytime < $v){
                    return true;
                }
            }
        }
        return false;
    }

    private function getTimeType ($createtime) {
        $createday = substr($createtime, 0, 10);
        $num = date('Hi',strtotime($createtime));

        $arr = [
            "time1" => [0000, 1000],
            "time2" => [1000, 1200],
            "time3" => [1200, 1300],
            "time4" => [1300, 1700],
            "time5" => [1700, 1730],
            "time6" => [1730, 1900],
            "time7" => [1900, 2400],
            "time8" => [0000, 2400],
        ];
        foreach ($arr as $k => $v) {
            if(false == FUtil::isHoliday($createday)){
                if($this->isBettweenTwoNums($v[0], $v[1], $num)){
                    return $k;
                }
            }else {
                return "time8";
            }
        }

        return "time8";
    }

    private function getCaculData ($data) {
        foreach ($data as $k => $item) {
            $data[$k]["values"]["average"] = $data[$k]["values"]["apply_cnt"] > 0 ? number_format(round($data[$k]["values"]["sum_times"] / $data[$k]["values"]["apply_cnt"] / 3600, 2), 2) : 0;
            $data[$k]["values"]["max_apply_times"] = number_format(round($data[$k]["values"]["max_apply_times"] / 3600, 2), 2);
            $data[$k]["values"]["apply_rate"] = $data[$k]["values"]["apply_cnt"] > 0 ? (number_format(round($data[$k]["values"]["apply_good_cnt"] / $data[$k]["values"]["apply_cnt"], 2), 2) * 100)."%"  : "0%";
        }
        return $data;
    }

    private function getFinalData ($data) {
        $finaldata = $data[0];
        $arr2 = $data[1];

        foreach ($finaldata as $k => $v) {
            $finaldata[$k]["values"]["average"] = "<span class='red'>" . $finaldata[$k]["values"]["average"] . "</span> / <span class='green'>" . $arr2[$k]["values"]["average"] . "</span>";
            $finaldata[$k]["values"]["max_apply_times"] = "<span class='red'>" . $finaldata[$k]["values"]["max_apply_times"] . "</span> / <span class='green'>" . $arr2[$k]["values"]["max_apply_times"] . "</span>";
            $finaldata[$k]["values"]["cnt"] = "<span class='red'>" . $finaldata[$k]["values"]["cnt"] . "</span> / <span class='green'>" . $arr2[$k]["values"]["cnt"] . "</span>";
            $finaldata[$k]["values"]["not_apply_cnt"] = "<span class='red'>" . $finaldata[$k]["values"]["not_apply_cnt"] . "</span> / <span class='green'>" . $arr2[$k]["values"]["not_apply_cnt"] . "</span>";
            $finaldata[$k]["values"]["apply_rate"] = "<span class='red'>" . $finaldata[$k]["values"]["apply_rate"] . "</span> / <span class='green'>" . $arr2[$k]["values"]["apply_rate"] . "</span>";
        }

        return $finaldata;
    }

    public function doShopOrder () {
        $first = XRequest::getValue('first', 'other');
        $startdate = XRequest::getValue("startdate", date('Y-m-d', strtotime(date('Y-m-d')) - 86400 * 28));
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));
        $redmine = 5874;

        XContext::setValue("first", $first);
        XContext::setValue("startdate", $startdate);
        XContext::setValue("enddate", $enddate);
        XContext::setValue("redmine", $redmine);
        return self::SUCCESS;
    }

    public function doShopOrderOutput () {
        $mydisease = $this->mydisease;
        $first = XRequest::getValue('first', 'other');
        $startdate = XRequest::getValue("startdate", date('Y-m-d', strtotime(date('Y-m-d')) - 86400 * 7));
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));

        $cond = '';
        $bind = [];

        if ($first == 'first') {
            // 首单
            $cond .= " and a.pos = 1 ";
        } elseif ($first == 'other') {
            // 非首单
            $cond .= " and a.pos > 1 ";
        }

        $cond .= " and a.time_pay >= :startdate ";
        $bind[":startdate"] = $startdate;

        $cond .= " and a.time_pay < :enddate ";
        $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));

        if($mydisease instanceof Disease){
            $cond .= " and c.diseaseid = :diseaseid ";
            $bind[":diseaseid"] = $mydisease->id;
        }

        $cond .= " and a.amount > a.refund_amount  ";
        //获得实体
        $sql = "select distinct a.*
                    from shoporders a
                    inner join doctors b on b.id = a.the_doctorid
                    inner join doctordiseaserefs c on c.doctorid = b.id
                    where 1 = 1 {$cond} order by a.time_pay desc, a.id desc";
        $shopOrders = Dao::loadEntityList4Page("ShopOrder", $sql, $pagesize, $pagenum, $bind);

        $data = [];
        foreach ($shopOrders as $k => $shopOrder) {
            $temp = array();

            $temp[] = $shopOrder->patientid;
            $temp[] = $shopOrder->patient->getDayCntFromBaodao($shopOrder->time_pay);
            $temp[] = $shopOrder->patient->doctor->name;
            $temp[] = $shopOrder->getTypeDesc();
            $temp[] = $shopOrder->time_pay;
            $temp[] = $shopOrder->pos;
            $data[] = $temp;
        }
        $headarr = array(
            "patient ID",
            "本次下单时患者的报到时长",
            "所属医生",
            "订单类型",
            "支付日期",
            "当次为第几单"
        );
        ExcelUtil::createForWeb($data, $headarr);
    }

    public function doShopOrderOfDoctorForMarket () {
        $year = XRequest::getValue("year", date('Y'));
        $menzhen_offset_daycnt = XRequest::getValue("menzhen_offset_daycnt", '-1');
        $areaid = XRequest::getValue("areaid", '0');
        $xprovinceid = XRequest::getValue("xprovinceid", '0');
        $auditorid_market = XRequest::getValue("auditorid_market", '0');
        $months = XDateTime::getMonthArrByYear($year);

        $areas = [
            0 => '全部',
            1 => '东北',
            2 => '华北',
            3 => '西南',
            4 => '西北',
            5 => '华东',
            6 => '华中',
            7 => '华南',
        ];

        $cond = "";
        $bind = [];
        if($menzhen_offset_daycnt == 0){
            $cond .= " and a.menzhen_offset_daycnt = 0 ";
        }

        if($menzhen_offset_daycnt > 0){
            $cond .= " and a.menzhen_offset_daycnt > 0 ";
        }

        if($xprovinceid){
            $cond .= " and f.id = :xprovinceid ";
            $bind[":xprovinceid"] = $xprovinceid;
        }

        if($auditorid_market){
            $cond .= " and a.auditorid_market = :auditorid_market ";
            $bind[":auditorid_market"] = $auditorid_market;
        }

        $sql_else = "";
        foreach ($months as $key => $month) {
            $sql_else .= "sum(if(left(b.createtime, 7)='{$month}', b.amount, 0)) as '{$month}交易额',";
            $sql_else .= "sum(if(left(b.createtime, 7)='{$month}', b.refund_amount, 0)) as '{$month}退款额',";
        }

        $sql = "select
        f.id as 'xproviniceid',
        f.name as '省份',
        e.name as '医院',
        a.name as '医生',
        if(a.menzhen_offset_daycnt > 0, '是', '否') as '当前是否开通门诊',
        {$sql_else}
        sum(if(left(b.createtime, 4)='{$year}', b.amount, 0)) as '{$year}总交易额',
        sum(if(left(b.createtime, 4)='{$year}', b.refund_amount, 0)) as '{$year}总退款额'
        from doctors a
        left join shoporders b on b.the_doctorid=a.id and b.is_pay=1
        inner join doctordiseaserefs c on c.doctorid=a.id and c.diseaseid=1
        inner join hospitals e on e.id=a.hospitalid and e.id!=5
        inner join xprovinces f on f.id=e.xprovinceid
        where 1=1 and a.status=1
        {$cond}
        group by a.id
        order by f.name, e.name";

        $arr = Dao::queryRows($sql, $bind);

        $data = [];
        $rowspanarr = [];
        foreach ($arr as $k => $v) {
            $area = $this->getAreaByXprovinceid($v['xproviniceid']);
            //不是全部大区 且 不是当前选择的大区 跳过
            if($areaid > 0 && $area != $areas[$areaid]){
                continue;
            }
            $data[$area][$v["省份"]][$v["医院"]][] = $v;
        }

        foreach ($data as $area => $provinces) {
            $rowspanarr[$area]["cnt"] ++;
            foreach ($provinces as $province => $hospitals) {
                $rowspanarr[$area]["cnt"]++;
                $rowspanarr[$area][$province]["cnt"]++;
                foreach ($hospitals as $hospital => $doctors) {
                    $doctorcnt = count($doctors);
                    $rowspanarr[$area]["cnt"]++;
                    $rowspanarr[$area][$province]["cnt"]++;
                    $rowspanarr[$area][$province][$hospital]["cnt"]++;
                    $rowspanarr[$area]["cnt"] += $doctorcnt;
                    $rowspanarr[$area][$province]["cnt"] += $doctorcnt;
                    $rowspanarr[$area][$province][$hospital]["cnt"] += $doctorcnt;
                }
            }
        }

        XContext::setValue("data", $data);
        XContext::setValue("rowspanarr", $rowspanarr);
        XContext::setValue("year", $year);
        XContext::setValue("menzhen_offset_daycnt", $menzhen_offset_daycnt);
        XContext::setValue("areaid", $areaid);
        XContext::setValue("xprovinceid", $xprovinceid);
        XContext::setValue("auditorid_market", $auditorid_market);
        XContext::setValue("months", $months);
        XContext::setValue("areas", $areas);
        return self::SUCCESS;
    }

    public function doShopOrderOfXprovinceForMarket () {
        $year = XRequest::getValue("year", date('Y'));
        $months = XDateTime::getMonthArrByYear($year);

        $cond = "";

        foreach ($months as $key => $month) {
            $cond .= "sum(if(left(b.createtime, 7)='{$month}', b.amount, 0)) as '{$month}交易额',";
            $cond .= "sum(if(left(b.createtime, 7)='{$month}', b.refund_amount, 0)) as '{$month}退款额',";
        }

        $sql = "select
        a.id as 'xproviniceid',
        a.name as '省份',
        $cond
        sum(if(left(b.createtime, 4)='{$year}', b.amount, 0)) as '{$year}总交易额',
        sum(if(left(b.createtime, 4)='{$year}', b.refund_amount, 0)) as '{$year}总退款额'
        from xprovinces a
        left join (
        select
        a.the_doctorid as the_doctorid,
        left(a.createtime, 7) as createtime,
        a.amount as amount,
        a.refund_amount as refund_amount,
        c.xprovinceid as xprovinceid
        from shoporders a
        inner join doctors b on b.id=a.the_doctorid
        inner join hospitals c on c.id=b.hospitalid and c.id!=5 and c.xprovinceid>0
        inner join doctordiseaserefs d on d.doctorid=b.id and d.diseaseid=1
        where a.is_pay=1
        ) b on b.xprovinceid=a.id
        where 1=1
        group by a.id
        order by a.name";

        $arr = Dao::queryRows($sql);

        $data = ["东北"=>[], "华北"=>[], "西南"=>[], "西北"=>[], "华东"=>[], "华中"=>[], "华南"=>[]];

        foreach ($arr as $k => $v) {
            $area = $this->getAreaByXprovinceid($v['xproviniceid']);
            $data[$area][] = $v;
        }

        XContext::setValue("data", $data);
        XContext::setValue("year", $year);
        XContext::setValue("months", $months);
        return self::SUCCESS;
    }

    private function getAreaByXprovinceid ($xprovinceid) {
        $area = '';
        $arr = [
            110000 => '华北',120000 => '华北',130000 => '华北',140000 => '华北',150000 => '东北',
            210000 => '东北',220000 => '东北',230000 => '东北',310000 => '华东',320000 => '华东',
            330000 => '华东',340000 => '华东',350000 => '华南',360000 => '华中',370000 => '东北',
            410000 => '华北',420000 => '华中',430000 => '华中',440000 => '华南',450000 => '华南',
            460000 => '其它',500000 => '西南',510000 => '西南',520000 => '西南',530000 => '西南',
            540000 => '其它',610000 => '西北',620000 => '西北',630000 => '西北',640000 => '西北',
            650000 => '西北',710000 => '其它',810000 => '其它',820000 => '其它'
        ];
        $area = $arr[$xprovinceid];
        return $area;
    }

    // 患教文章统计
    // 备注：基于 courseid=680895016 的 lesson统计
    // 分别统计 推送的 WxUser数量  打开文章的WxUser数量  看过两次以上的 WxUser数量
    public function doPatientEduByADHDTag () {
        $temp = [];
        $course = Course::getById(680895016);
        $lessons = LessonDao::getListByCourse($course);

        foreach ($lessons as $key=>$lesson) {
            $temp[$key]['lessonid'] = $lesson->id;
            $temp[$key]['lessonTitle'] = $lesson->title;
            $temp[$key]['WxUserCnt'] = PatientEduRecordDao:: getWxUserCntByCourseAndLesson($course, $lesson);
            $temp[$key]['WxUserCntByRead'] = PatientEduRecordDao::getWxUserCntByCourseAndLesson($course, $lesson, true);
            $temp[$key]['WxUserCntByReadTimes'] = PatientEduRecordDao::getWxUserCntByCourseAndLesson($course, $lesson, true, 2);
            $temp[$key]['WxUserCntByReadAndWeek'] = PatientEduRecordDao::getWxUserCntByCourseAndLesson($course, $lesson, false, 0, 3);
            $temp[$key]['readTimes'] = PatientEduRecordService::getViewCntByCourseAndLesson($course, $lesson);
        }

        XContext::setValue('PatientEduCount', $temp);
        XContext::setValue('redmine', 6175);

        return self::SUCCESS;
    }
}
