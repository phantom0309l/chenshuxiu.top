<?php

class PaperToEchartsService
{

    // app端SNAP-IV量表展示数据
    public static function getResultOfAdhd_ivForApp ($patientid, $num, $writer = 'all') {
        $result = array();
        $papers = PaperDao::getList_adhd_iv($patientid, $num, $writer);
        $patient = Patient::getById($patientid);
        $doctor = $patient->getMasterDoctor();
        if ($doctor instanceof Doctor) {
            $result = self::getResult($papers, $doctor);
        }
        return $result;
    }

    // web端SNAP-IV量表展示数据
    public static function getResultOfAdhd_ivForWeb ($patientid, $num, $writer = 'all') {
        $result = array();
        $papers = PaperDao::getList_adhd_iv($patientid, $num, $writer);
        $patient = Patient::getById($patientid);
        $doctor = $patient->getMasterDoctor();
        if ($doctor instanceof Doctor) {
            $result = self::getResult($papers, $doctor);
            $arr = array();
            if (self::isScoreRule($doctor)) {
                $arr['legend'] = array(
                    "注意",
                    "多动+冲动",
                    "对立违抗",
                    "总分");
                $arr['item1_type'] = "line";
                $arr['item2_type'] = "line";
                $arr['item3_type'] = "line";
                $arr['yAxis'] = array(
                    0 => array(
                        'type' => 'value',
                        'name' => '分数',
                        'max' => 4,
                        'axisLabel' => array(
                            'formatter' => '{value}')));
                $arr['scores_yAxisIndex'] = 0;

            } else {
                $arr['legend'] = array(
                    "注意(常常+总是)",
                    "多动+冲动(常常+总是)",
                    "总分");
                $arr['item1_type'] = "bar";
                $arr['item2_type'] = "bar";
                $arr['yAxis'] = array(
                    0 => array(
                        'type' => 'value',
                        'name' => '项数',
                        'max' => 12,
                        'axisLabel' => array(
                            'formatter' => '{value}')),
                    1 => array(
                        'type' => 'value',
                        'name' => '分数',
                        'axisLabel' => array(
                            'formatter' => '{value}')));
                $arr['scores_yAxisIndex'] = 1;
            }
            $result += $arr;
            $result['scores_type'] = "line";
        }
        return $result;
    }

    // web端 QCD量表 展示数据
    public static function getResultOfQcdForWeb ($patientid, $num) {
        $result = array();
        $papers = PaperDao::getListByPatientid($patientid," and ename='QCD' order by id desc limit {$num} ");
        $result = self::getQcdResult($papers);

        return $result;
    }

    // 备注:获取渲染conners评估图表的数据
    public static function getConnersChartData ($patientid, $num = "all") {
        if ($num == "all") {
            $cond = " and ename = 'conners' and patientid = :patientid order by id ";
        } else {
            $num = intval($num);
            $cond = " and ename = 'conners' and patientid = :patientid order by id desc limit {$num} ";
        }

        $bind = [];
        $bind[':patientid'] = $patientid;

        $papers = Dao::getEntityListByCond("Paper", $cond, $bind);

        $arr = array();
        $arr['score1'] = array();
        $arr['score2'] = array();
        $arr['score3'] = array();
        $arr['score4'] = array();
        $arr['score5'] = array();
        $arr['score6'] = array();
        $arr['created_at'] = array();

        foreach ($papers as $a) {
            list ($score1, $score2, $score3, $score4, $score5, $score6) = $a->getSumOfConners();
            $arr['score1'][] = $score1;
            $arr['score2'][] = $score2;
            $arr['score3'][] = $score3;
            $arr['score4'][] = $score4;
            $arr['score5'][] = $score5;
            $arr['score6'][] = $score6;
            $arr['created_at'][] = Date('m-d', strtotime($a->createtime));
        }
        if ($num !== "all") {
            foreach ($arr as $k => $v) {
                $arr[$k] = array_reverse($v);
            }
        }
        return $arr;
    }

    // 备注:获取治疗反应问卷体重图表数据
    public static function getZlfywjWeightChartData ($patientid, $num) {
        $num = intval($num);
        $cond = "and ename = 'zlfywj' and patientid = :patientid order by id desc limit {$num}";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $papers = Dao::getEntityListByCond("Paper", $cond, $bind);

        $arr = array();

        foreach ($papers as $a) {
            $answers = $a->getAnswers();
            foreach ($answers as $b) {
                $xquestion = $b->xquestion;
                $ename = $xquestion->ename;
                if ($ename == "weight") {
                    $arr['createday'][] = substr($a->getCreateDay(), 5);
                    $arr['weight'][] = is_numeric($b->content) ? $b->content : 0;
                    break;
                }
            }
        }

        foreach ($arr as $k => $v) {
            $arr[$k] = array_reverse($v);
        }
        return $arr;
    }

    // 备注:获取用药剂量图表数据
    public static function getDoseChartData ($patientid, $num) {
        $cond = "and ename = 'medicine_parent' and patientid = :patientid order by id desc";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $papers = Dao::getEntityListByCond("Paper", $cond, $bind);

        $arr = array();
        $arr['dose'] = array();
        $arr['name'] = array();
        $arr['created_at'] = array();
        $i = 1;

        foreach ($papers as $a) {
            if ($i > $num) {
                break;
            }
            $dose = $a->getMedicineDose();
            if (! empty($dose)) {
                $arr['dose'][] = $dose;
                $arr['name'][] = $a->getMedicineName();
                $arr['created_at'][] = Date('m-d', strtotime($a->createtime));
                $i ++;
            }
        }
        foreach ($arr as $k => $v) {
            $arr[$k] = array_reverse($v);
        }
        return $arr;
    }

    private static function getQcdResult ($papers) {
        $arr = array();
        $arr['date'] = array();
        $arr['scores'] = array();

        foreach (array_reverse($papers) as $a) {
            $arr['date'][] = substr($a->getCreateDay(), 5);
            $arr['scores'][] = $a->xanswersheet->score;
        }
        return $arr;
    }

    private static function getResult ($papers, $doctor) {
        $arr = array();
        $arr['item1'] = array();
        $arr['item2'] = array();
        $arr['item3'] = array();
        $arr['scores'] = array();
        $arr['created_at'] = array();
        $arr['urls'] = array();

        foreach ($papers as $a) {
            list ($item1, $item2, $item3, $score, $url) = self::getFixAdhd_ivData($doctor, $a);
            $arr['item1'][] = $item1;
            $arr['item2'][] = $item2;
            $arr['item3'][] = $item3;
            $arr['scores'][] = $score;
            $arr['urls'][] = $url;
            $arr['created_at'][] = Date('m-d', strtotime($a->createtime));
        }

        foreach ($arr as $k => $v) {
            $arr[$k] = array_reverse($v);
        }
        return $arr;
    }

    private static function getFixAdhd_ivData (Doctor $doctor, Paper $paper) {
        $version = PhoneUtil::getVersion();
        if ($version && $version < 1.29) {
            return self::getAdhd_ivDataByTermRule($paper);
        }
        return self::isScoreRule($doctor) ? self::getAdhd_ivDataByScoreRule($paper) : self::getAdhd_ivDataByTermRule($paper);
    }

    private static function isScoreRule (Doctor $doctor) {
        return $doctor->hospitalid == 89;
    }

    // 根据项数规则得到展示数据
    private static function getAdhd_ivDataByTermRule (Paper $paper) {
        $total = 0;
        $arr = array();

        $data = self::getBaseDataOfAdhd_iv($paper);
        foreach ($data as $k => $v) {
            $total += $v["scores"];
            foreach ($v["term"] as $i => $j) {
                if ($i > 1) {
                    $arr[$k] += $j;
                }
            }
        }

        // 注意（常常+总是）的填写项数
        $item1 = $arr[0];
        // 多动+冲动（常常+总是）的填写项数
        $item2 = $arr[1] + $arr[2];
        // 对立违抗（常常+总是）的填写项数
        $item3 = $arr[3];
        // 总分

        $url = UrlFor::dmAppAnswerSheet($paper->xanswersheetid);

        return array(
            $item1,
            $item2,
            $item3,
            $total,
            $url);
    }

    // 根据得分规则得到展示数据
    private static function getAdhd_ivDataByScoreRule (Paper $paper) {
        $data = self::getBaseDataOfAdhd_iv($paper);
        // 注意 得分
        $item1 = number_format($data[0]["scores"] / $data[0]["questionscnt"], 2);
        // 多动+冲动 得分
        $item2 = number_format(($data[1]["scores"] + $data[2]["scores"]) / ($data[1]["questionscnt"] + $data[2]["questionscnt"]), 2);
        // 对立违抗 得分
        $item3 = number_format($data[3]["scores"] / $data[3]["questionscnt"], 2);
        // 总分
        $total = number_format(($item1 + $item2 + $item3) / 3, 2);
        $url = UrlFor::dmAppAnswerSheet($paper->xanswersheetid);

        return array(
            $item1,
            $item2,
            $item3,
            $total,
            $url);
    }

    // 根据项数规则得到展示数据
    private static function getBaseDataOfAdhd_iv (Paper $paper) {
        $answers = $paper->getAnswers();
        $scoreMap = array(
            "无" => 0,
            "偶尔" => 1,
            "常常" => 2,
            "总是" => 3);

        $items = array(
            "0" => array(
                "questionscnt" => 9,
                "scores" => 0,
                "term" => array(
                    "0" => 0,
                    "1" => 0,
                    "2" => 0,
                    "3" => 0)),
            "1" => array(
                "questionscnt" => 5,
                "scores" => 0,
                "term" => array(
                    "0" => 0,
                    "1" => 0,
                    "2" => 0,
                    "3" => 0)),
            "2" => array(
                "questionscnt" => 4,
                "scores" => 0,
                "term" => array(
                    "0" => 0,
                    "1" => 0,
                    "2" => 0,
                    "3" => 0)),
            "3" => array(
                "questionscnt" => 8,
                "scores" => 0,
                "term" => array(
                    "0" => 0,
                    "1" => 0,
                    "2" => 0,
                    "3" => 0)));

        foreach ($answers as $a) {
            $ref = XAnswerOptionRef::getOneByXAnswer($a);
            if ($ref instanceof XAnswerOptionRef) {
                $content = $ref->content;

                $arr = explode("_", $a->xquestion->ename);
                $section_index = $arr[1];

                $answer_score = $scoreMap[$content];

                $items[$section_index]["term"][$answer_score] ++;
                $items[$section_index]["scores"] += $answer_score;
            }
        }

        return $items;
    }

}
