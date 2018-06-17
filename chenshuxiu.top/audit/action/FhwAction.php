<?php

class FhwAction extends AuditBaseAction
{
    private $diseaseid = 8;

    // 测试函数
    public function doDebug () {

        Debug::addNotice("addNotice1");

        Debug::trace("--trace--");
        Debug::trace("--info--");

        Debug::addNotice("addNotice2");

        Debug::sql("--sql--");
        Debug::warn("--warn 第一行 第二行 --");
        Debug::error("--error--");

        Debug::addNotice("addNotice3");

        DBC::requireTrue(false, '我抛出的异常消息');

        return self::blank;
    }

    public function doInfo () {
        $sql = "select id from patients limit 10 ";
        $patientids = XCache::getValue($sql, 120, function() use($sql){
            return Dao::queryValues($sql);
        }, 'php');

//        print_r($patientids);

        return self::SUCCESS;
    }

    public function doInfopost () {
        $addresss = XRequest::getValue('hospital', []);

        print_r($addresss);
        exit;

        return self::SUCCESS;
    }

    public function doTestDataJson () {
        $list = [
            'itemtitles' => [
                '完成率'
            ],
            'itemX' => [
                '2018-03-01','2018-03-02','2018-03-03','2018-03-04','2018-03-05','2018-03-06','2018-03-07', '2018-03-08','2018-03-09','2018-03-10','2018-03-11','2018-03-12','2018-03-13','2018-03-14'
            ],
            'itemV' => [
                40, 30, 20, 10, 50, 80, 70, 40, 30, 20, 10, 50, 80, 70
            ],
        ];

        XContext::setValue('json', $list);

        return self::TEXTJSON;
    }

    public function doOpTaskDataJson () {
        /*
        肿瘤-基本信息填写
        肿瘤-血常规收集
        肿瘤-血常规收集(治疗)
        肿瘤-血常规收集(观察)
        肿瘤-化疗方案收集
        肿瘤-用药核对
        肿瘤-定期复查
        肿瘤-定期随访

        count(if(a.status=0,true,null)) / count(a.id) * 100 as 'status_0_cnt',
        count(if(a.status=2,true,null)) / count(a.id) * 100 as 'status_2_cnt',
        count(if(a.status=1,true,null)) / count(a.id) * 100 as 'status_1_cnt'
         */
        $date_range = XRequest::getValue('date_range', '');

        if (!empty($date_range)) {
            $arr = explode('至', $date_range);
            $from_date = $arr[0];
            $to_date = $arr[1];
        } else {
            // 如果没选日期，默认给一个月
            $from_date = date("Y-m-d", strtotime("-1 month"));
            $to_date = date('Y-m-d');
        }

        $optask_first_plantime_cond = " and a.first_plantime >= '{$from_date}' AND a.first_plantime < '{$to_date}' ";

        $optasktplids = [493488746,440624196,445430496,458705906,334289746,445440206,577224776,270243896];

        $xAxis = [];
        $date = $from_date;
        while (strtotime($date) < strtotime($to_date)) {
            $xAxis[] = $date;
            $date = date('Y-m-d', strtotime($date) + 3600 * 24);
        }

        foreach ($optasktplids as $i => $optasktplid) {
            $sql = "select left(a.first_plantime, 10) as 'first_plantime',
                count(if(a.status=1,if(b.code='finish',true,null),null)) / count(a.id) * 100 as 'status_1_cnt'
                from optasks a
                left join opnodes b on b.id = a.opnodeid
                where 1 = 1 {$optask_first_plantime_cond} and a.optasktplid = {$optasktplid}
                group by left(a.first_plantime, 10) ";
            $list = Dao::queryRows($sql);

            $optasktpltitle = Dao::queryValue("select title from optasktpls where id = {$optasktplid} ");

            $serieVs = [];
            foreach ($xAxis as $xAxi) {
                $flag = 0;
                foreach ($list as $item) {
                    if ($item['first_plantime'] == $xAxi) {
                        $flag = 1;
                        break;
                    }
                }
                if ($flag == 1) {
                    $serieVs[] = $item['status_1_cnt'];
                } else {
                    $serieVs[] = -10;
                }
            }

            $series[] = [
                'name' => $optasktpltitle,
                'type' => 'line',
                'stack' => $optasktpltitle,
                'data' => $serieVs
            ];
        }

        $legend = [
            '肿瘤-基本信息填写',
            '肿瘤-血常规收集',
            '肿瘤-血常规收集(治疗)',
            '肿瘤-血常规收集(观察)',
            '肿瘤-化疗方案收集',
            '肿瘤-用药核对',
            '肿瘤-定期复查',
            '肿瘤-定期随访'
        ];

        $data = [
            'legend' => $legend,
            'xAxis' => $xAxis,
            'series' => $series
        ];

        XContext::setValue('json', $data);

        return self::TEXTJSON;
    }

    public function dead_peroid ($doctorid) {
        $sql = "select count(distinct a.id)
                from patients a
                inner join patientrecords b on b.patientid = a.id
                where a.doctorid = {$doctorid} and b.type = 'staging' and b.json_content like '%\"stage\":\"IV\"%' ";
        $peroidcnt = Dao::queryValue($sql);

        $sql = "select count(distinct a.id)
                from patients a
                inner join patientrecords b on b.patientid = a.id
                where a.doctorid = {$doctorid} and b.type = 'dead' ";
        $deadcnt = Dao::queryValue($sql);

        $list = [
            "死亡" => $deadcnt,
            "晚期" => $peroidcnt
        ];

        return $list;
    }

    public function disease ($doctorid) {
        $sql = "select c.name,count(a.id) as cnt
                from patients a
                inner join doctordiseaserefs b on b.diseaseid = a.diseaseid and b.doctorid = a.doctorid
                inner join diseases c on c.id = b.diseaseid
                where a.doctorid = 477
                group by b.diseaseid ";
        $rows = Dao::queryRows($sql);

        $list = [];
        foreach ($rows as $row) {
            $list["{$row['name']}"] = $row['cnt'];
        }

        return $list;
    }

    public function age ($doctorid) {
        $sql = "select (YEAR(NOW()) - YEAR(birthday)) div 10 as 'range',count(*) as cnt
                from patients
                where doctorid = {$doctorid} and birthday <> '0000-00-00' and is_test = 0 
                group by (YEAR(NOW()) - YEAR(birthday)) div 10 ";
        $rows = Dao::queryRows($sql);

        $all_cnt = Dao::queryValue("select count(*) from patients where doctorid = {$doctorid} and birthday <> '0000-00-00' and is_test = 0 ");

        $list = [];
        $cnt_30 = 0;
        $cnt_80 = 0;
        $list["<30"] = 0;
        foreach ($rows as $row) {
            switch ($row['range']) {
                case 0:
                    $cnt_30 += $row['cnt'];
                    break;
                case 1:
                    $cnt_30 += $row['cnt'];
                    break;
                case 2:
                    $cnt_30 += $row['cnt'];
                    break;
                case 3:
                    $list["30-40"] = [
                        'cnt' => $row['cnt'],
                        'rate' => round($row['cnt'] / $all_cnt, 2) * 100 . "%"
                    ];
                    break;
                case 4:
                    $list["40-50"] = [
                        'cnt' => $row['cnt'],
                        'rate' => round($row['cnt'] / $all_cnt, 2) * 100 . "%"
                    ];
                    break;
                case 5:
                    $list["50-60"] = [
                        'cnt' => $row['cnt'],
                        'rate' => round($row['cnt'] / $all_cnt, 2) * 100 . "%"
                    ];
                    break;
                case 6:
                    $list["60-70"] = [
                        'cnt' => $row['cnt'],
                        'rate' => round($row['cnt'] / $all_cnt, 2) * 100 . "%"
                    ];
                    break;
                case 7:
                    $list["70-80"] = [
                        'cnt' => $row['cnt'],
                        'rate' => round($row['cnt'] / $all_cnt, 2) * 100 . "%"
                    ];
                    break;
                case 8:
                    $cnt_80  += $row['cnt'];
                    break;
                case 9:
                    $cnt_80  += $row['cnt'];
                    break;
                case 10:
                    $cnt_80  += $row['cnt'];
                    break;
                case 11:
                    $cnt_80  += $row['cnt'];
                    break;
                case 12:
                    $cnt_80  += $row['cnt'];
                    break;
                default:
                    break;
            }
        }
        $list["<30"] = [
            'cnt' => $cnt_30,
            'rate' => round($cnt_30 / $all_cnt, 2) * 100 . "%"
        ];
        $list[">80"] = [
            'cnt' => $cnt_80,
            'rate' => round($cnt_80 / $all_cnt, 2) * 100 . "%"
        ];

        return $list;
    }

    // 性别分布
    public function patientcnt ($doctorid) {
        /*
            $data['patientcnt'] = [
                'all' => 500,
                'man' => 300,
                'women' => 150,
                'unknown' => 50
            ];
         * */
        $sql = "select sex,count(*) as cnt
                from patients
                where doctorid = {$doctorid}
                group by sex ";
        $rows = Dao::queryRows($sql);

        $list = [];
        $list["unknown"] = 0;
        $sum = 0;
        foreach ($rows as $row) {
            $sum += $row['cnt'];

            switch ($row['sex']) {
                case 0:
                    $list['unknown'] = $row['cnt'];
                    break;
                case 1:
                    $list['man'] = $row['cnt'];
                    break;
                case 2:
                    $list['women'] = $row['cnt'];
                    break;
                default:
                    break;
            }
        }
        $list['all'] = $sum;

        
        return $list;
    }

    // os
    public function os ($doctorid, $startdate, $enddate) {
        $enddate .= " 23:59:59";
        $sql = "select b.name as 'patient_name',c.name as 'disease_name',a.thedate as 'dead_date',e.json_content
                from patientrecords a
                inner join patientrecords e on e.patientid = a.patientid
                inner join patients b on b.id = a.patientid
                inner join diseases c on c.id = b.diseaseid
                inner join doctordiseaserefs d on d.diseaseid = b.diseaseid and d.doctorid = b.doctorid
                where a.type = 'dead' and e.type = 'diagnose' and b.doctorid = {$doctorid} and a.createtime > '{$startdate}' and a.createtime < '{$enddate}' ";
        $rows = Dao::queryRows($sql);

        $list = [];
        foreach ($rows as $row) {
            $item = json_decode($row['json_content'], true);

            $zhenduan_date = $item['thedate'];
            if ($zhenduan_date) {
                $list[] = [
                    "patient_name" => $row['patient_name'],
                    "disease_name" => $row['disease_name'],
                    "zhenduan_date" => $zhenduan_date,
                    "dead_date" => $row['dead_date'],
                    "os" => round((strtotime($row['dead_date']) - strtotime($zhenduan_date)) / (3600 * 24 * 30), 1)
                ];
            }
        }

        

        return $list;
    }

    // 不良反应
    public function untowardeffect ($doctorid, $startdate, $enddate) {
        $enddate .= " 23:59:59";
        $sql = "select b.name as 'patient_name',c.name as 'disease_name',a.json_content
                from patientrecords a
                inner join patients b on b.id = a.patientid
                inner join diseases c on c.id = b.diseaseid
                where a.type = 'untoward_effect' and b.doctorid = {$doctorid} and a.createtime >= '{$startdate}' and a.createtime <= '{$enddate}' 
                order by a.patientid ";
        $rows = Dao::queryRows($sql);

        $list = [];
        foreach ($rows as $row) {
            $item = json_decode($row['json_content'], true);

            if ($item['degree'] > 0) {
                $list[] = [
                    "patient_name" => $row['patient_name'],
                    "disease_name" => $row['disease_name'],
                    "name" => $item['name'],
                    "degree" => $item['degree']
                ];
            }
        }

        return $list;
    }

    public function doweek () {
        $patientcnt = $this->patientcnt(477);
        $age = $this->age(477);
        $disease = $this->disease(477);
        $dead_peroid = $this->dead_peroid(477);
        $untowardeffect = $this->untowardeffect(477, '2018-03-01', '2018-05-10');
        $os = $this->os(477, '2018-01-01', '2018-05-10');

        $data = [
            "startdate" => '2017-01-01',
            "enddata" => '2018-01-01',
            "untowardeffect" => $untowardeffect,
            "os" => $os,
            "patientcnt" => $patientcnt,
            "age" => $age,
            "disease" => $disease,
            "dead_peroid" => $dead_peroid
        ];

        XContext::setValue('json', $data);

        return self::TEXTJSON;
    }

    public function doday () {
        $startdate = "2018-06-06";
        $enddate = "2018-06-07";

        $emphasiss = $this->emphasis(477, $startdate, $enddate);
        $patientids_emphasis = array_keys($emphasiss);

        $adverses = $this->adverses(477, $startdate, $enddate);
        $patientids_adverse = array_keys($adverses);

        $patientids = array_merge($patientids_emphasis, $patientids_adverse);
        $actives = $this->actives(477, $startdate, $enddate, $patientids);

        // 将重点患者和严重不良反应相同患者合并
        $emphasis_adverses_all = [];
        $commons = [];
        foreach ($emphasiss as $patientid => $emphasis) {
            if (array_key_exists($patientid, $adverses)) {
                $commons[] = $patientid;
            } else {
                $emphasis_adverses_all["{$patientid}"] = $emphasis;
            }
        }
        foreach ($adverses as $patientid => $adverse) {
            if (!in_array($patientid, $commons)) {
                $emphasis_adverses_all["{$patientid}"] = $adverse;
            }
        }
        foreach ($commons as $patientid) {
            $emphasis_adverses_all["{$patientid}"] = [
                'patientid' => $patientid,
                'name' => $emphasiss["{$patientid}"]['name'],
                'age' => $emphasiss["{$patientid}"]['age'],
                'sex' => $emphasiss["{$patientid}"]['sex'],
                'disease_name' => $emphasiss["{$patientid}"]['disease_name'],
                'content' => $emphasiss["{$patientid}"]['content'] . ";" .$adverses["{$patientid}"]['content']
            ];
        }

        // 合并活跃患者
        foreach ($actives as $patientid => $active) {
            $emphasis_adverses_all["{$patientid}"] = $active;
        }

        $data = [
            'emphasis' => array_values($emphasiss),
            'adverses' => array_values($adverses),
            'all' => array_values($emphasis_adverses_all)
        ];



        XContext::setValue('json', $data);

        return self::TEXTJSON;
    }

    public function emphasis ($doctorid, $startdate, $enddate) {
        /*
         $emphasis = [
            "1232121212" => [
                'name' => 'xxx',
                'age' => 23,
                'sex' => '男',
                'disease_name' => '肺癌',
                'content' => 'dddddddddd'
            ]
        ];
         * */
        $sql = "select a.patientid, b.name, c.name as 'disease_name', if(b.sex = 1, '男', '女') as sex, year(now()) - year(b.birthday) as age, group_concat(a.title) as 'content'
                from patienttodaymarks a
                inner join patients b on b.id = a.patientid
                inner join diseases c on c.id = b.diseaseid
                where b.doctorid = {$doctorid} and a.createtime >= '2018-06-06' and a.createtime < '2018-06-07'
                group by a.patientid ";
        $rows = Dao::queryRows($sql);

        $list = [];
        foreach ($rows as $row) {
            $list["{$row['patientid']}"] = [
                'patientid' => $row['patientid'],
                'name' => $row['name'],
                'sex' => $row['sex'],
                'age' => $row['age'],
                'disease_name' => $row['disease_name'],
                'content' => $row['content']
            ];
        }

        return $list;
    }

    public function adverses ($doctorid, $startdate, $enddate) {
        $sql = "select a.patientid, b.name, c.name as 'disease_name',if(b.sex = 1, '男', '女') as sex, year(now()) - year(b.birthday) as age, group_concat(a.json_content SEPARATOR '|') as 'content'
                from patientrecords a
                inner join patients b on b.id = a.patientid
                inner join diseases c on c.id = b.diseaseid
                where b.doctorid = 477 and a.createtime >= '2018-05-06' and a.createtime < '2018-06-07' 
                and (a.json_content like '%\"degree\":\"4\"%' or a.json_content like '%\"degree\":\"3\"%')
                group by a.patientid ";
        $rows = Dao::queryRows($sql);

        /*
        [patientid] => 123254925
        [name] => 张海棠
        [disease_name] => 结直肠癌
        [sex] => 女
        [age] => 51
        [content] => {"name":"粒细胞","degree":"3","relate_chemo":"507192936"},{"name":"HGB","degree":"4","relate_chemo":"507192936"}
         * */

        $list = [];
        foreach ($rows as $row) {
            $contents = explode('|' ,$row['content']);

            $contentlist = [];
            foreach ($contents as $content) {
                $arr = json_decode($content, true);
                $contentlist[] = $arr['name'] . $arr['degree'] . "级";
            }
            $contentstr = implode(' ', $contentlist);

            $list["{$row['patientid']}"] = [
                'patientid' => $row['patientid'],
                'name' => $row['name'],
                'sex' => $row['sex'],
                'age' => $row['age'],
                'disease_name' => $row['disease_name'],
                'content' => $contentstr
            ];
        }

        return $list;
    }

    public function actives ($doctorid, $startdate, $enddate, $patientids) {
        $sql = "select a.patientid, b.name, c.name as 'disease_name',if(b.sex = 1, '男', '女') as sex, year(now()) - year(b.birthday) as age
                from pipes a
                inner join patients b on b.id = a.patientid
                inner join diseases c on c.id = b.diseaseid
                where a.objtype in ('WxTxtMsg','WxPicMsg','Paper') and b.doctorid = 477 and a.createtime >= '2018-04-06' and a.createtime < '2018-04-07'
                group by a.patientid ";
        $rows = Dao::queryRows($sql);

        $list = [];
        foreach ($rows as $row) {
            if (!in_array($row['patientid'], $patientids)) {
                $list["{$row['patientid']}"] = [
                    'patientid' => $row['patientid'],
                    'name' => $row['name'],
                    'sex' => $row['sex'],
                    'age' => $row['age'],
                    'disease_name' => $row['disease_name']
                ];
            }
        }

        return $list;
    }
}
