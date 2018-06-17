<?php

class LillyReportMgrAction extends AuditBaseAction
{
    private $arr_day = [
        [0, 28],
        [29, 56],
        [57, 84],
        [85, 112],
        [113, 140],
        [141, 168],
    ];

    // 首页
    public function doIndex () {
        return self::SUCCESS;
    }

    // 医生入组数/周（第4页）
    public function doPage4ForDoctor () {
        return self::SUCCESS;
    }

    // 医生入组数/周（第4页）数据接口
    public function doPage4ForDoctorJson () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $sql = "select
        left(a.starttime, 7) as month,
        count(a.id) as cnt
        from doctor_hezuos a
        inner join doctors b on b.id=a.doctorid
        where a.status=1 and b.hospitalid!=5
        and a.starttime < :thedate
        group by left(a.starttime, 7)";

        $bind = [];
        $bind[":thedate"] = date("Y-m-d", strtotime($thedate) + 86400);

        $arr = Dao::queryRows($sql, $bind);

        $months = $this->getLillyMonths($thedate);

        $data = [];
        foreach ($months as $i => $month) {
            $data[$i][0] = $month;
            $data[$i][1] = 0;
            foreach ($arr as $k => $v) {
                if($month == $v["month"]){
                    $data[$i][1] = $v["cnt"];
                }
            }
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    // 患者入组数/周（第4页）
    public function doPage4ForPatient () {
        return self::SUCCESS;
    }

    // 患者入组数/周（第4页）数据接口
    public function doPage4ForPatientJson () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $sql = "select
        left(a.createtime, 7) as month,
        count(a.id) as cnt
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        where b.status=1 and b.is_test=0 and c.hospitalid!=5
        and a.createtime < :thedate
        group by left(a.createtime, 7)";

        $bind = [];
        $bind[":thedate"] = date("Y-m-d", strtotime($thedate) + 86400);

        $arr = Dao::queryRows($sql, $bind);

        $months = $this->getLillyMonths($thedate);

        $data = [];
        foreach ($months as $i => $month) {
            $data[$i][0] = $month;
            $data[$i][1] = 0;
            foreach ($arr as $k => $v) {
                if($month == $v["month"]){
                    $data[$i][1] = $v["cnt"];
                }
            }
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    // 入组医生总数（第5页）
    public function doPage5ForDoctor () {
        return self::SUCCESS;
    }

    // 入组医生总数（第5页）数据接口
    public function doPage5ForDoctorJson () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $sql = "select
        left(a.starttime, 7) as month,
        count(a.id) as cnt
        from doctor_hezuos a
        inner join doctors b on b.id=a.doctorid
        where a.status=1 and b.hospitalid!=5
        and a.starttime < :thedate
        group by left(a.starttime, 7)";

        $bind = [];
        $bind[":thedate"] = date("Y-m-d", strtotime($thedate) + 86400);

        $arr = Dao::queryRows($sql, $bind);

        $months = $this->getLillyMonths($thedate);

        $data = [];
        $temp = 0;
        foreach ($months as $i => $month) {
            $data[$i][0] = $month;
            $data[$i][1] = $temp;
            foreach ($arr as $k => $v) {
                if($month == $v["month"]){
                    $temp += $v["cnt"];
                    $data[$i][1] = $temp;
                }
            }
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    // 入组患者总数（第5页）
    public function doPage5ForPatient () {
        return self::SUCCESS;
    }

    // 入组患者总数（第5页）数据接口
    public function doPage5ForPatientJson () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $sql = "select
        left(a.createtime, 7) as month,
        count(a.id) as cnt
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        where b.status=1 and b.is_test=0 and c.hospitalid!=5
        and a.createtime < :thedate
        group by left(a.createtime, 7)";

        $bind = [];
        $bind[":thedate"] = date("Y-m-d", strtotime($thedate) + 86400);

        $arr = Dao::queryRows($sql, $bind);

        $months = $this->getLillyMonths($thedate);

        $data = [];
        $temp = 0;
        foreach ($months as $i => $month) {
            $data[$i][0] = $month;
            $data[$i][1] = $temp;
            foreach ($arr as $k => $v) {
                if($month == $v["month"]){
                    $temp += $v["cnt"];
                    $data[$i][1] = $temp;
                }
            }
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    private function getLillyMonths ($thedate) {
        $arr = [];
        for ($month = '2017-07'; strtotime($thedate) > strtotime($month); $month = date("Y-m",strtotime("+1 month",strtotime($month)))) {
            $arr[] = $month;
        }
        return $arr;
    }

    // 新老患者比例（第6页）
    public function doPage6ForPie () {
        return self::SUCCESS;
    }

    // 新老患者比例（第6页）数据接口
    public function doPage6ForPieJson () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $sql = "select
        a.drug_monthcnt_when_create as month,
        count(a.id) as cnt
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        where c.hospitalid!=5 and b.is_test=0 and b.status=1
        and a.createtime < :thedate
        group by a.drug_monthcnt_when_create";

        $bind = [];
        $bind[":thedate"] = date("Y-m-d", strtotime($thedate) + 86400);

        $arr = Dao::queryRows($sql, $bind);

        $data = [];

        $data[0]['name'] = '新患者';
        $data[0]['value'] = $arr[0]['cnt'];
        $data[1]['name'] = '老患者';
        $data[1]['value'] = - $arr[0]['cnt'];
        foreach ($arr as $k => $v) {
            $data[1]['value'] += $v["cnt"];
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    // 入组新老患者分布（第6页）
    public function doPage6ForBar () {
        return self::SUCCESS;
    }

    // 入组新老患者分布（第6页）数据接口
    public function doPage6ForBarJson () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $sql = "select
        a.drug_monthcnt_when_create as month,
        count(a.id) as cnt
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        where c.hospitalid!=5 and b.is_test=0 and b.status=1
        and a.createtime < :thedate
        group by a.drug_monthcnt_when_create";

        $bind = [];
        $bind[":thedate"] = date("Y-m-d", strtotime($thedate) + 86400);

        $arr = Dao::queryRows($sql, $bind);

        $data = [];
        foreach ($arr as $k => $v) {
            $data[$k][] = '服药' . $v["month"] . '个月';
            $data[$k][] = $v["cnt"];
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    // 医生活跃性分析（第7页）
    public function doPage7ForPie () {
        return self::SUCCESS;
    }

    // 医生活跃性分析（第7页）数据接口
    public function doPage7ForPieJson () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $sql = "select
        b.doctorid as doctorid,
        count(a.id) as cnt
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        where b.status=1 and b.is_test=0 and c.hospitalid!=5
        and a.createtime < :thedate
        group by b.doctorid";

        $bind = [];
        $bind[":thedate"] = date("Y-m-d", strtotime($thedate) + 86400);

        $arr = Dao::queryRows($sql, $bind);

        $sql = "select count(a.id)
            from doctor_hezuos a
            inner join doctors b on b.id=a.doctorid
            where a.status=1 and b.hospitalid!=5
            and a.starttime < :thedate";

        $bind = [];
        $bind[":thedate"] = date("Y-m-d", (strtotime($thedate) + 86400));

        $cnt = Dao::queryValue($sql, $bind);

        $data = [
            ['name' => '0患者','value' => $cnt,],
            ['name' => '1患者','value' => 0,],
            ['name' => '2~4患者','value' => 0,],
            ['name' => '5~9患者','value' => 0,],
            ['name' => '10(含)以上','value' => 0,],
        ];

        foreach ($arr as $k => $v) {
            if(1 == $v['cnt']){
                $data[1]['value']++;
            }
            if(2 <= $v['cnt'] && $v['cnt'] <= 4){
                $data[2]['value']++;
            }
            if(5 <= $v['cnt'] && $v['cnt'] <= 9){
                $data[3]['value']++;
            }
            if(10 <= $v['cnt']){
                $data[4]['value']++;
            }
            $data[0]['value']--;
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    // 活跃医生入组患者数分析（第7页）
    public function doPage7ForBar () {
        return self::SUCCESS;
    }

    // 活跃医生入组患者数分析（第7页）数据接口
    public function doPage7ForBarJson () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $sql = "select
        b.doctorid as doctorid,
        count(a.id) as cnt
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        where b.status=1 and b.is_test=0 and c.hospitalid!=5
        and a.createtime < :thedate
        group by b.doctorid";

        $bind = [];
        $bind[":thedate"] = date("Y-m-d", strtotime($thedate) + 86400);

        $arr = Dao::queryRows($sql, $bind);

        $data = [['1患者',0,],['2~4患者',0,],['5~9患者',0,],['10(含)以上',0,],];

        foreach ($arr as $k => $v) {
            if(1 == $v['cnt']){
                $data[0][1]++;
            }
            if(2 <= $v['cnt'] && $v['cnt'] <= 4){
                $data[1][1]++;
            }
            if(5 <= $v['cnt'] && $v['cnt'] <= 9){
                $data[2][1]++;
            }
            if(10 <= $v['cnt']){
                $data[3][1]++;
            }
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    // 患者自评量表完成率（第9页）
    public function doPage9 () {
        return self::SUCCESS;
    }

    // 患者自评量表完成率（第9页）数据接口
    public function doPage9Json () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $arr_day = $this->arr_day;

        $data = [
            ['首月', 0, 0],
            ['次月', 0, 0],
            ['三个月', 0, 0],
            ['四个月', 0, 0],
            ['五个月', 0, 0],
            ['六个月', 0, 0],
        ];

        foreach ($arr_day as $k => $v) {
            $cnt_all = $this->getPatientCntByEnddate($thedate, $v);
            $cnt_donepaper = $this->getPatientCntDonePaperByEnddate($thedate, $v);
            $data[$k][1] = $cnt_all;
            $data[$k][2] = 0 == $cnt_all ? 0 : number_format(round($cnt_donepaper/$cnt_all, 2)*100);
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    // 患者行为训练完成率（第10页）
    public function doPage10 () {
        return self::SUCCESS;
    }

    // 患者行为训练完成率（第10页）数据接口
    public function doPage10Json () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $arr_day = $this->arr_day;

        $data = [
            ['首月', 0, 0],
            ['次月', 0, 0],
            ['三个月', 0, 0],
            ['四个月', 0, 0],
            ['五个月', 0, 0],
            ['六个月', 0, 0],
        ];

        foreach ($arr_day as $k => $v) {
            $cnt_all = $this->getPatientCntByEnddate($thedate, $v);
            $cnt_donehwk = $this->getPatientCntDoneHwkByEnddate($thedate, $v);
            $data[$k][1] = $cnt_all;
            $data[$k][2] = 0 == $cnt_all ? 0 : number_format(round($cnt_donehwk/$cnt_all, 2)*100);
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    // 患者主动提问的活跃度（第11页）
    public function doPage11 () {
        return self::SUCCESS;
    }

    // 患者主动提问的活跃度（第11页）数据接口
    public function doPage11Json () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $arr_day = $this->arr_day;

        $data = [
            ['首月', 0, 0],
            ['次月', 0, 0],
            ['三个月', 0, 0],
            ['四个月', 0, 0],
            ['五个月', 0, 0],
            ['六个月', 0, 0],
        ];

        foreach ($arr_day as $k => $v) {
            $cnt_all = $this->getPatientCntByEnddate($thedate, $v);
            $cnt_havewxmsg = $this->getPatientCntHaveWxmsgByEnddate($thedate, $v);
            $data[$k][1] = $cnt_all;
            $data[$k][2] = 0 == $cnt_all ? 0 : number_format(round($cnt_havewxmsg/$cnt_all, 2)*100);
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    private function getPatientCntByEnddate ($enddate, $arr) {
        $sql = "select
        count(b.id) as cnt
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        where b.status=1 and b.is_test=0 and c.hospitalid!=5
        and (a.enddate = '0000-00-00' or datediff(a.enddate, a.startdate) >= :fromday)
        and datediff(:enddate, a.createtime) > :day
        and a.createtime < :enddate";

        $bind = [];
        $bind[":fromday"] = $arr[0];
        $bind[":day"] = $arr[1];
        $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));

        return Dao::queryValue($sql, $bind);
    }

    private function getPatientCntDonePaperByEnddate ($enddate, $arr) {
        $sql = "select count(*)
        from (
        select
        a.patientid as patientid
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        inner join papers d on d.patientid=a.patientid
        where b.status=1 and b.is_test=0 and c.hospitalid!=5
        and (a.enddate = '0000-00-00' or datediff(a.enddate, a.startdate) >= :fromday)
        and (d.papertplid=100996299 or d.papertplid=179607836)
        and datediff(d.createtime, a.createtime) >= :fromday
        and datediff(d.createtime, a.createtime) <= :today
        and datediff(:enddate, a.createtime) > :day
        and a.createtime < :enddate
        and d.createtime < :enddate
        group by a.patientid) t";

        $bind = [];
        $bind[":fromday"] = $arr[0];
        $bind[":today"] = $arr[1];
        $bind[":day"] = $arr[1];
        $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));

        return Dao::queryValue($sql, $bind);
    }

    private function getPatientCntDoneHwkByEnddate ($enddate, $arr) {
        $sql = "select count(*)
        from (
        select a.patientid as patientid
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        inner join studyplans d on d.patientid=a.patientid
        where b.status=1 and b.is_test=0 and c.hospitalid!=5
        and (a.enddate = '0000-00-00' or datediff(a.enddate, a.startdate) >= :fromday)
        and d.objcode='hwk'
        and datediff(d.createtime, a.createtime) >= :fromday
        and datediff(d.createtime, a.createtime) <= :today
        and datediff(:enddate, a.createtime) > :day
        and a.createtime < :enddate
        and d.createtime < :enddate
        group by a.patientid) t";

        $bind = [];
        $bind[":fromday"] = $arr[0];
        $bind[":today"] = $arr[1];
        $bind[":day"] = $arr[1];
        $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));

        return Dao::queryValue($sql, $bind);
    }

    private function getPatientCntHaveWxmsgByEnddate ($enddate, $arr) {
        $sql = "select count(*)
        from (
        select a.patientid as patientid
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        inner join pipes d on d.patientid=a.patientid
        where b.status=1 and b.is_test=0 and c.hospitalid!=5
        and (a.enddate = '0000-00-00' or datediff(a.enddate, a.startdate) >= :fromday)
        and d.objtype in ('WxTxtMsg', 'WxVoiceMsg', 'WxPicMsg')
        and datediff(d.createtime, a.createtime) >= :fromday
        and datediff(d.createtime, a.createtime) <= :today
        and datediff(:enddate, a.createtime) > :day
        and a.createtime < :enddate
        and d.createtime < :enddate
        group by a.patientid) t";

        $bind = [];
        $bind[":fromday"] = $arr[0];
        $bind[":today"] = $arr[1];
        $bind[":day"] = $arr[1];
        $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));

        return Dao::queryValue($sql, $bind);
    }

    // 出组率（第13页）
    public function doPage13ForOutradio () {
        return self::SUCCESS;
    }

    // 出组率（第13页）数据接口
    public function doPage13ForOutradioJson () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        // 在组中
        $sql = "select
        count(a.id) as cnt
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        where c.hospitalid!=5 and b.is_test=0 and b.status=1 and a.startdate < :thedate
        and (a.status=1 or (a.status>1 and a.enddate >= :thedate))";

        $bind = [];
        $bind[":thedate"] = date("Y-m-d", strtotime($thedate) + 86400);

        $cnt_in = Dao::queryValue($sql, $bind);

        // 已出组
        $sql = "select
        count(a.id) as cnt
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        where c.hospitalid!=5 and b.is_test=0 and b.status=1 and a.startdate < :thedate
        and (a.status>1 and a.enddate < :thedate)";

        $bind = [];
        $bind[":thedate"] = date("Y-m-d", strtotime($thedate) + 86400);

        $cnt_out = Dao::queryValue($sql, $bind);

        $data = [];

        $data[0]['name'] = '在组中';
        $data[0]['value'] = $cnt_in;
        $data[1]['name'] = '已出组';
        $data[1]['value'] = $cnt_out;

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    // 出组率明细（第13页）
    public function doPage13ForOutradioDetail () {
        return self::SUCCESS;
    }

    // 出组率明细（第13页）数据接口
    public function doPage13ForOutradioDetailJson () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $data = [
            0 => array( 'name' => '顺利出组', 'value' => 0 ),
            1 => array( 'name' => '不活跃退出', 'value' => 0 ),
            2 => array( 'name' => '停换药退出', 'value' => 0 ),
            3 => array( 'name' => '主动退出', 'value' => 0 ),
            4 => array( 'name' => '扫非合作医生退出', 'value' => 0 ),
            5 => array( 'name' => '取关退出', 'value' => 0 ),
        ];

        foreach ($data as $k => $v) {
            $sql = "select
            count(a.id) as cnt
            from patient_hezuos a
            inner join patients b on b.id=a.patientid
            inner join doctors c on c.id=b.doctorid
            where c.hospitalid!=5
            and a.status = :status and a.enddate < :thedate
            and b.is_test=0 and b.status=1 and a.startdate < :thedate";

            $bind = [];
            $bind[":status"] = $k + 2;
            $bind[":thedate"] = date("Y-m-d", strtotime($thedate) + 86400);

            $data[$k]['value'] = Dao::queryValue($sql, $bind);
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    // 每月依从性（第14页）
    public function doPage14 () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));
        $redmine = 5902;

        $arr_day = $this->arr_day;
        $arr = [0, 0, 0, 0, 0, 0];

        $data = [
            'allcnt' => [
                'title' => '满足相应入项目时长的患者数',
                'values' => $arr
            ],
            'outcnt' => [
                'title' => '在相应时间内退出项目的患者数',
                'values' => $arr
            ],
            'outcnt_notactive' => [
                'title' => '不活跃退出患者数',
                'values' => $arr
            ],
            'outcnt_scanother' => [
                'title' => '扫非合作医生二维码退出患者数',
                'values' => $arr
            ],
            'outcnt_unsubscribe' => [
                'title' => '取关退出患者数',
                'values' => $arr
            ],
            'outcnt_stopdrugbyself' => [
                'title' => '自行停换药退出患者数',
                'values' => $arr
            ],
            'outcnt_stopdrugbydoctor' => [
                'title' => '遵医嘱停换药退出患者数',
                'values' => $arr
            ],
            'outcnt_stopdrugbyother' => [
                'title' => '未知原因停换药退出患者数',
                'values' => $arr
            ],
            'cnt_updatezsd' => [
                'title' => '相应时间+2周内收集到用药信息的人数',
                'values' => $arr
            ],
            'cnt_updatedrugzsd' => [
                'title' => '相应时间+2周内收集到用药信息且在服药的人数',
                'values' => $arr
            ],
            'cnt_maybedrug' => [
                'title' => '预估服药人数',
                'values' => $arr
            ],
            'cnt_stopdrugbyself' => [
                'title' => '相应时间+2周内收集到用药信息且已自行停药的人数',
                'values' => $arr
            ],
            'cnt_stopdrugbydoctor' => [
                'title' => '相应时间+2周内收集到用药信息且已遵医嘱停药的人数',
                'values' => $arr
            ],
            'cnt_stopdrugbyother' => [
                'title' => '相应时间+2周内收集到用药信息且已未知原因停药的人数	',
                'values' => $arr
            ],
            'cnt_updatedrugrate' => [
                'title' => '遵医嘱服药率',
                'values' => $arr
            ]
        ];

        foreach ($arr_day as $k => $v) {
            $drugdata = $this->getDrugData($thedate, $v);
            foreach ($drugdata as $v2) {
                $data['allcnt']['values'][$k]++;
                if($v2['isupdate']){
                    $data['cnt_updatezsd']['values'][$k]++;
                }
                if($v2['isdrug']){
                    $data['cnt_updatedrugzsd']['values'][$k]++;
                }
                if($v2['maybedrug']){
                    $data['cnt_maybedrug']['values'][$k]++;
                }
                if($v2['isnotdrug'] && 2 == $v2['type']){
                    $data['cnt_stopdrugbyself']['values'][$k]++;
                }
                if($v2['isnotdrug'] && 1 == $v2['type']){
                    $data['cnt_stopdrugbydoctor']['values'][$k]++;
                }
                if($v2['isnotdrug'] && 0 == $v2['type']){
                    $data['cnt_stopdrugbyother']['values'][$k]++;
                }
            }
            $outdata = $this->getOutData($thedate, $v);
            foreach ($outdata as $v3) {
                $data['outcnt']['values'][$k]++;
                if(3 == $v3['status']){
                    $data['outcnt_notactive']['values'][$k]++;
                }
                if(6 == $v3['status']){
                    $data['outcnt_scanother']['values'][$k]++;
                }
                if(7 == $v3['status']){
                    $data['outcnt_unsubscribe']['values'][$k]++;
                }
                if(4 == $v3['status'] && 2 == $v3['type']){
                    $data['outcnt_stopdrugbyself']['values'][$k]++;
                }
                if(4 == $v3['status'] && 1 == $v3['type']){
                    $data['outcnt_stopdrugbydoctor']['values'][$k]++;
                }
                if(4 == $v3['status'] && 0 == $v3['type']){
                    $data['outcnt_stopdrugbyother']['values'][$k]++;
                }
            }
            $temp = $data['allcnt']['values'][$k] - $data['cnt_stopdrugbyother']['values'][$k] - $data['allcnt']['outcnt_stopdrugbyother'][$k];
            $data['cnt_updatedrugrate']['values'][$k] = $temp > 0 ? number_format(round(($data['cnt_updatedrugzsd']['values'][$k])/$temp, 4)*100) .'%' : 0 . '%';
        }

        XContext::setValue("redmine", $redmine);
        XContext::setValue("thedate", $thedate);
        XContext::setValue("data", $data);
        return self::SUCCESS;
    }

    // 每月依从性（第14页）
    public function doPage14Detail () {
        $type = XRequest::getValue("type", 'cnt_stopdrugbyother');
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));
        $k = XRequest::getValue("k", 0);

        $arr_day = $this->arr_day;
        $v = $arr_day[$k];

        $data = [];
        $patientids = [];
        if('cnt_stopdrugbyother' == $type){
            $drugdata = $this->getDrugData($thedate, $v);
            foreach ($drugdata as $arr) {
                if($arr['isnotdrug'] && 0 == $arr['type']){
                    $patientids[] = $arr['patientid'];
                }
            }
        }
        if('outcnt_stopdrugbyother' == $type){
            $outdata = $this->getOutData($thedate, $v);
            foreach ($outdata as $arr) {
                if(4 == $arr['status'] && 0 == $arr['type']){
                    $patientids[] = $arr['patientid'];
                }
            }
        }

        foreach ($patientids as $patientid) {
            $temp = [];
            $temp[] = $patientid;
            $data[] = $temp;
        }
        $headarr = array(
            "patientid"
        );
        ExcelUtil::createForWeb($data, $headarr);
    }

    private function getDrugData ($enddate, $arr) {
        $sql = "select
		a.patientid as patientid,
		if(e.id is not null, 1, 0) as isupdate,
		if(e.id is not null and e.value>0, 1, 0) as isdrug,
		if(e.id is not null and e.value=0, 1, 0) as isnotdrug,
		if(d.stop_drug_type is not null, d.stop_drug_type, 0) as type,
		if(f.id is not null, 1, 0) as maybedrug
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        left join (
            select * from patientmedicinerefs where medicineid=2 group by patientid
            ) d on d.patientid=a.patientid
        left join (
            select a.*
            from drugitems a
            inner join (
                select a.patientid as patientid, max(a.record_date) as record_date
                from drugitems a
                inner join patient_hezuos b on b.patientid=a.patientid
                and datediff(a.record_date, b.startdate) >= :fromday
                and datediff(a.record_date, b.startdate) <= :today
                where medicineid=2
                group by a.patientid
                order by a.record_date desc
                ) b on b.patientid=a.patientid and b.record_date=a.record_date and a.medicineid=2
            group by a.patientid
            ) e on e.patientid=a.patientid
        left join (
            select a.* from drugitems a
            inner join patient_hezuos b on b.patientid=a.patientid
            where a.value > 0 and a.medicineid = 2 and datediff(a.record_date, b.startdate) >= :fromday group by a.patientid
        ) f on f.patientid=a.patientid
        where b.is_test=0 and b.status=1 and c.hospitalid!=5
        and a.drug_monthcnt_when_create=1
        and datediff(:enddate, a.startdate) >= :fromday";

        $bind = [];
        $bind[":fromday"] = $arr[1];
        $bind[":today"] = $arr[1]+14;
        $bind[":enddate"] = $enddate;

        return Dao::queryRows($sql, $bind);
    }

    private function getOutData ($enddate, $arr) {
        $sql = "select
		a.patientid as patientid,
		a.status as status,
		if(d.stop_drug_type is not null, d.stop_drug_type, 0) as type
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        left join (
            select * from patientmedicinerefs where medicineid=2 group by patientid
            ) d on d.patientid=a.patientid
        where b.is_test=0 and b.status=1 and c.hospitalid!=5
        and a.status>1 and a.drug_monthcnt_when_create=1
        and datediff(a.enddate, a.startdate) < :fromday
        and a.enddate <= :enddate";

        $bind = [];
        $bind[":fromday"] = $arr[1];
        $bind[":enddate"] = $enddate;

        return Dao::queryRows($sql, $bind);
    }

    // AE 上报率（第19页）
    public function doPage19 () {
        return self::SUCCESS;
    }

    // AE 上报率（第19页）数据接口
    public function doPage19Json () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $data = [];
        for ($i = 0; $i < 24; $i++) {
            $j = $i + 1;
            $data[$i][0] = 'W' . $j;
            $AEcnt = $this->getAECntByEnddateAndWeek($thedate, $i);
            $patientcnt = $this->getPatientCntByEnddateAndWeek($thedate, $i);
            $data[$i][1] =  0 == $patientcnt ? 0 : number_format(round($AEcnt/$patientcnt, 2)*100);
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    private function getPatientCntByEnddateAndWeek ($enddate, $week) {
        $sql = "select
        sum(if(DATEDIFF(:enddate, a.createtime)/7 > :weekand1, 1, 0)) as '超过1周'
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        where b.status=1 and b.is_test=0 and c.hospitalid!=5";

        $bind = [];
        $bind[":weekand1"] = $week+1;
        $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));

        return Dao::queryValue($sql, $bind);
    }

    private function getAECntByEnddateAndWeek ($enddate, $week) {
        $sql = "select
        sum(if(DATEDIFF(d.createtime, a.createtime)/7 >= :week and DATEDIFF(d.createtime, a.createtime)/7 < :weekand1, 1, 0)) as cnt
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        inner join papers d on d.patientid=a.patientid
        where b.status=1 and b.is_test=0 and c.hospitalid!=5
        and d.papertplid in (275143816, 312586776)
        and d.createtime < :enddate";

        $bind = [];
        $bind[":week"] = $week;
        $bind[":weekand1"] = $week+1;
        $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));

        return Dao::queryValue($sql, $bind);
    }

    // AE 上报率明细（第19页）
    public function doPage19Detail () {
        $thedate = XRequest::getValue("thedate", date('Y-m-d'));

        $data = [];
        $data[0][0] = '';
        $data[1][0] = 'AE上报数';
        $data[2][0] = '患者数';
        $data[3][0] = 'AE上报';
        for ($i = 1; $i <= 24; $i++) {
            $data[0][$i] = 'W' . $i;
            $AEcnt = $this->getAECntByEnddateAndWeek($thedate, $i-1);
            $patientcnt = $this->getPatientCntByEnddateAndWeek($thedate, $i-1);
            $data[1][$i] =  $AEcnt;
            $data[2][$i] =  $patientcnt;
            $data[3][$i] =  0 == $patientcnt ? '0%' : number_format(round($AEcnt/$patientcnt, 2)*100).'%';
        }

        XContext::setValue("thedate", $thedate);
        XContext::setValue("data", $data);
        return self::SUCCESS;
    }

    public function doResponseStatistic () {
        $fromdate = XRequest::getValue("fromdate", date('Y-m-16', strtotime('-1 month')));
        $todate = XRequest::getValue("todate", date('Y-m-15'));
        $redmine = 4929;

        $data = [];
        $data['needServiceCnt'] = count($this->getNeedServicePatientids($fromdate, $todate));
        $data['haveServiceCnt'] = count($this->getHaveServicePatientids($fromdate, $todate));
        $data['needAnswerCnt'] = count($this->getNeedAnswerPatientids($fromdate, $todate));
        $data['haveAnswerCnt'] = count($this->getHaveAnswerPatientids($fromdate, $todate));
        XContext::setValue("fromdate", $fromdate);
        XContext::setValue("todate", $todate);
        XContext::setValue("redmine", $redmine);
        XContext::setValue("data", $data);
        return self::SUCCESS;
    }

    public function doResponseStatisticDetail () {
        $type = XRequest::getValue("type", 'needService');
        $fromdate = XRequest::getValue("fromdate", date('Y-m-16', strtotime('-1 month')));
        $todate = XRequest::getValue("todate", date('Y-m-15'));

        $data = [];
        $patientids = [];
        if('needService' == $type){
            $patientids = $this->getNeedServicePatientids($fromdate, $todate);
        }
        if('haveService' == $type){
            $patientids = $this->getHaveServicePatientids($fromdate, $todate);
        }
        if('needAnswer' == $type){
            $patientids = $this->getNeedAnswerPatientids($fromdate, $todate);
        }
        if('haveAnswer' == $type){
            $patientids = $this->getHaveAnswerPatientids($fromdate, $todate);
        }

        foreach ($patientids as $patientid) {
            $temp = [];
            $temp[] = $patientid;
            $data[] = $temp;
        }
        $headarr = array(
            "patientid"
        );
        ExcelUtil::createForWeb($data, $headarr);
    }

    private function getNeedServicePatientids ($fromdate, $todate) {
        $sql = "select
        a.id as patientid
        from patients a
        inner join doctor_hezuos b on b.doctorid=a.doctorid
        inner join patientmedicinerefs c on c.patientid=a.id
        inner join doctors d on d.id=a.doctorid
        where a.status = 1 and b.status = 1 and a.is_test = 0 and d.hospitalid != 5
        and c.medicineid = 2 and left(c.createtime, 10) = left(a.createtime, 10)
        and a.createtime > b.starttime
        and left(a.createtime, 10) >= :fromdate
        and left(a.createtime, 10) < :todate
        group by a.id";

        $bind = [];
        $bind[":fromdate"] = $fromdate;
        $bind[":todate"] = date("Y-m-d", (strtotime($todate) + 86400));

        return Dao::queryValues($sql, $bind);
    }

    private function getHaveServicePatientids ($fromdate, $todate) {
        $data = [];
        $patientids = $this->getNeedServicePatientids($fromdate, $todate);
        foreach ($patientids as $key => $patientid) {
            if($this->haveService($patientid)){
                $data[] = $patientid;
            }
        }
        return $data;
    }

    private function haveService ($patientid) {
        $patient = Patient::getById($patientid);
        $baodaotime = $patient->createtime;
        $nearlyworkday = $this->getNearlyWorkDay($patient->getCreateDay());

        $cond = " and cdr_answer_time > 0 and patientid = :patientid and createtime > :baodaotime and left(createtime, 10) <= :nearlyworkday ";
        $bind = [];

        $bind[':patientid'] = $patientid;
        $bind[':baodaotime'] = $baodaotime;
        $bind[':nearlyworkday'] = $nearlyworkday;

        $cdrmeeting = Dao::getEntityByCond('CdrMeeting', $cond, $bind);
        return $cdrmeeting instanceof CdrMeeting;
    }

    private function getNearlyWorkDay ($date) {
        $nextday = date("Y-m-d", (strtotime($date) + 86400));
        if(false == FUtil::isHoliday($nextday)){
            return $nextday;
        }else{
            return $this->getNearlyWorkDay($nextday);
        }
    }

    private function getNeedAnswerPatientids ($fromdate, $todate) {
        $data = $this->getDataByDate($fromdate, $todate);

        $patientids = [];
        foreach ($data as $k => $v) {
            if($this->isworktime($v["starttime"])){
                $patientids[] = $v["patientid"];
            }
        }
        return $patientids;
    }

    private function getHaveAnswerPatientids ($fromdate, $todate) {
        $data = $this->getDataByDate($fromdate, $todate);

        $patientids = [];
        foreach ($data as $k => $v) {
            if($this->isworktime($v["starttime"]) && $v["bridgetime"] > 0){
                $patientids[] = $v["patientid"];
            }
        }
        return $patientids;
    }

    private function getDataByDate ($fromdate, $todate) {
        $sql = "select
        p.patientid as patientid,
        c.cdr_answer_time as starttime,
        c.cdr_bridge_time as bridgetime
        from
        (select a.*
        from patient_hezuos a
        inner join patients b on b.id=a.patientid
        inner join doctors c on c.id=b.doctorid
        where c.hospitalid!=5 and b.is_test=0
        and a.createtime < :todate) p
        left join cdrmeetings c on c.patientid=p.patientid
        where c.cdr_call_type=1 and c.cdr_answer_time>0
        and c.createtime > p.createtime
        and (p.status=1 or (p.status>1 and c.createtime < p.enddate))
        and c.cdr_end_time-c.cdr_answer_time>25
        and c.createtime > :fromdate
        and c.createtime < :todate";

        $bind = [];
        $bind[":fromdate"] = $fromdate;
        $bind[":todate"] = date("Y-m-d", (strtotime($todate) + 86400));

        return Dao::queryRows($sql, $bind);
    }

    private function isworktime ($time) {
        $date = date('Y-m-d H:i:s', $time);
        $day = substr($date, 0, 10);
        $num = date('Hi',strtotime($date));

        if(false == FUtil::isHoliday($day)){
            if(1000 <= $num && $num < 1930){
                return true;
            }
        }
        return false;
    }
}
