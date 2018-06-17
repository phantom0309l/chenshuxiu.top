<?php

class PaperService
{
    //取adhd_iv量表的数据
    public static function getAdhd_ivData (Paper $paper) {
        $count = array(
            0 => array(
                '无' => 0,
                '偶尔' => 0,
                '常常' => 0,
                '总是' => 0),
            1 => array(
                '无' => 0,
                '偶尔' => 0,
                '常常' => 0,
                '总是' => 0),
            2 => array(
                '无' => 0,
                '偶尔' => 0,
                '常常' => 0,
                '总是' => 0));

        foreach ($paper->getAnswers() as $a) {
            if ($a->xquestion->type != "Radio") {
                continue;
            }

            $arr = explode("_", $a->xquestion->ename);
            $section_index = $arr[1];
            if ($section_index > 2) {
                continue;
            }
            $ref = XAnswerOptionRef::getOneByXAnswer($a);
            if ($ref instanceof XAnswerOptionRef) {
                $content = $ref->content;
                $count[$section_index][$content] += 1;
            }
        }
        return array(
            "scores" => $paper->xanswersheet->score,
            "count" => $count);
    }

    //取hygtfzbchjss量表的数据
    public static function getHygtfzbchjssData (Paper $paper) {
        $data = [0, 0, 0, 0, 0, 0];
        $xanswersheetid = $paper->xanswersheetid;

        $sql = " select sum(a.score)
        from xoptions a
        inner join xansweroptionrefs b on b.xoptionid=a.id
        inner join xanswers c on c.id=b.xanswerid
        inner join xquestions d on d.id=c.xquestionid
        where c.xanswersheetid={$xanswersheetid}
        and d.ename in ('section_A', 'section_B') ";
        $data[0] = Dao::queryValue($sql);

        $sql = " select sum(a.score)
        from xoptions a
        inner join xansweroptionrefs b on b.xoptionid=a.id
        inner join xanswers c on c.id=b.xanswerid
        inner join xquestions d on d.id=c.xquestionid
        where c.xanswersheetid={$xanswersheetid}
        and d.ename in ('section_C', 'section_D', 'section_E') ";
        $data[1] = Dao::queryValue($sql);

        $sql = " select sum(a.score)
        from xoptions a
        inner join xansweroptionrefs b on b.xoptionid=a.id
        inner join xanswers c on c.id=b.xanswerid
        inner join xquestions d on d.id=c.xquestionid
        where c.xanswersheetid={$xanswersheetid}
        and d.ename in ('section_A', 'section_B', 'section_C', 'section_D', 'section_E') ";
        $data[2] = Dao::queryValue($sql);

        $sql = " select sum(a.score)
        from xoptions a
        inner join xansweroptionrefs b on b.xoptionid=a.id
        inner join xanswers c on c.id=b.xanswerid
        inner join xquestions d on d.id=c.xquestionid
        where c.xanswersheetid={$xanswersheetid}
        and d.ename='section_2' ";
        $data[3] = Dao::queryValue($sql);

        $sql = " select count(a.id)
        from xoptions a
        inner join xansweroptionrefs b on b.xoptionid=a.id
        inner join xanswers c on c.id=b.xanswerid
        inner join xquestions d on d.id=c.xquestionid
        where c.xanswersheetid={$xanswersheetid}
        and d.ename='section_4' and a.score>0 ";
        $data[4] = Dao::queryValue($sql);

        $sql = " select count(a.id)
        from xoptions a
        inner join xansweroptionrefs b on b.xoptionid=a.id
        inner join xanswers c on c.id=b.xanswerid
        inner join xquestions d on d.id=c.xquestionid
        where c.xanswersheetid={$xanswersheetid}
        and d.ename='section_4' and a.score>1 ";
        $data[5] = Dao::queryValue($sql);

        return $data;
    }

    //取hygtfzlbchjjz量表的数据
    public static function getHygtfzlbchjjzData (Paper $paper) {
        $data = [0, 0, 0, 0, 0];
        $xanswersheetid = $paper->xanswersheetid;

        $sql = " select sum(a.score)
        from xoptions a
        inner join xansweroptionrefs b on b.xoptionid=a.id
        inner join xanswers c on c.id=b.xanswerid
        inner join xquestions d on d.id=c.xquestionid
        where c.xanswersheetid={$xanswersheetid}
        and d.ename='section_4' ";
        $data[0] = Dao::queryValue($sql);

        $sql = " select sum(a.score)
        from xoptions a
        inner join xansweroptionrefs b on b.xoptionid=a.id
        inner join xanswers c on c.id=b.xanswerid
        inner join xquestions d on d.id=c.xquestionid
        where c.xanswersheetid={$xanswersheetid}
        and d.ename='section_16' ";
        $data[1] = Dao::queryValue($sql);

        $sql = " select sum(a.score)
        from xoptions a
        inner join xansweroptionrefs b on b.xoptionid=a.id
        inner join xanswers c on c.id=b.xanswerid
        inner join xquestions d on d.id=c.xquestionid
        where c.xanswersheetid={$xanswersheetid}
        and d.ename='section_19' ";
        $data[2] = Dao::queryValue($sql);

        $sql = " select sum(a.score)
        from xoptions a
        inner join xansweroptionrefs b on b.xoptionid=a.id
        inner join xanswers c on c.id=b.xanswerid
        inner join xquestions d on d.id=c.xquestionid
        where c.xanswersheetid={$xanswersheetid}
        and substring_index(d.ename, '_', -1)>=1
        and substring_index(d.ename, '_', -1)<=24 ";
        $data[3] = Dao::queryValue($sql);

        $sql = " select sum(a.score)
        from xoptions a
        inner join xansweroptionrefs b on b.xoptionid=a.id
        inner join xanswers c on c.id=b.xanswerid
        inner join xquestions d on d.id=c.xquestionid
        where c.xanswersheetid={$xanswersheetid}
        and d.ename='section_D' ";
        $data[4] = Dao::queryValue($sql);

        return $data;
    }

    public static function getLossAppetiteScore (Paper $paper) {
        $answers = $paper->getAnswers();
        $score = 0;
        foreach ($answers as $a) {
            if ($a->pos == 7) {
                $ref = XAnswerOptionRef::getOneByXAnswer($a);
                if ($ref instanceof XAnswerOptionRef) {
                    $xoption = XOption::getById($ref->xoptionid);
                    $score += $xoption->score;
                }
            }
        }
        $score += self::getBMIScore();
        return $score;
    }

    private static function getBMIScore (Paper $paper) {
        $BMI = self::getBMI($paper);
        $score = 0;
        if ($BMI <= 16) {
            $score = 5;
        }
        if ($BMI > 16 && $BMI <= 18) {
            $score = 3;
        }
        if ($BMI > 18 && $BMI <= 20) {
            $score = 1;
        }
        return $score;
    }

    private static function getBMI (Paper $paper) {
        $answers = $paper->getAnswers();
        $weight = 0;
        $height = 0;
        $BMI = 0;
        foreach ($answers as $a) {
            if ($a->pos == 1) {
                $height = $a->content / 100;
            }
            if ($a->pos == 2) {
                $weight = $a->content;
                break;
            }
        }
        if ($height > 0) {
            $BMI = $weight / ($height * $height);
        }
        return round($BMI);
    }

    //获取ASD孤独症量表结果数据
    public static function getASDPaperResult(Paper $paper, $type){
        $func = "getASDPaperResultOf{$type}";
        if(method_exists("PaperService", $func)){
            Debug::trace("=========func[{$func}]=============");
            return call_user_func("PaperService::{$func}", $paper);
        }else{
            return null;
        }
    }

    //汉语沟通发展表:词汇及手势 HYGT_HAND
    public static function getASDPaperResultOfHYGT_HAND(Paper $paper){
        $s1 = 0; //早期手势
        $s2 = 0; //后期手势
        $s3 = 0; //动作手势总分
        $s4 = 0; //短语理解
        $s5 = 0; //词汇理解
        $s6 = 0; //词汇表达

        //背景资料题目号
        $bg_pos_arr = array(490,491,492,493,494,495,496);

        //存放背景数据分组
        $bg_arr = [];

        $xanswersheet = $paper->xanswersheet;
        $xanswers = $xanswersheet->getAnswers();

        foreach($xanswers as $a){
            $pos = $a->pos;
            if(strpos($pos, '.')){
                continue;
            }
            //早期手势
            if($pos >= 446 && $pos <= 461){
                $s1 += $a->score;
            }

            //后期手势
            if($pos >= 462 && $pos <= 488){
                $s2 += $a->score;
            }

            //短语理解
            if($pos >= 4 && $pos <= 30){
                $s4 += $a->score;
            }

            //词汇理解，第35-445题得分不为0的『题目数』
            //词汇表达，第35-445题得分为2的『题目数』
            if($pos >= 35 && $pos <= 445){
                $score = $a->score;
                if($score > 0){
                    $s5++;
                }
                if($score == 2){
                    $s6++;
                }
            }

            //背景资料获取
            if(in_array($pos, $bg_pos_arr)){
                $temp = [];
                $temp["question"] = $a->getQuestionCtr()->getQaHtmlQuestionContent();
                $temp["answer"] = $a->getQuestionCtr()->getQaHtmlAnswerContent();
                $bg_arr[] = $temp;
            }
        }

        //动作手势总分
        $s3 = $s1 + $s2;

        return array(
            "bg" => $bg_arr,
            "base" => array(
                array(
                    "desc" => "早期手势得分",
                    "score" => $s1
                ),
                array(
                    "desc" => "后期手势得分",
                    "score" => $s2
                ),
                array(
                    "desc" => "动作手势总分",
                    "score" => $s3
                ),
                array(
                    "desc" => "短语理解得分",
                    "score" => $s4
                ),
                array(
                    "desc" => "词汇理解得分",
                    "score" => $s5
                ),
                array(
                    "desc" => "词汇表达得分",
                    "score" => $s6
                ),
            )
        );
    }

    //汉语沟通发展表:词汇及句子 HYGT_JUZI
    public static function getASDPaperResultOfHYGT_JUZI(Paper $paper){
        $s1 = 0; //动词得分
        $s2 = 0; //方向词得分
        $s3 = 0; //量词得分
        $s4 = 0; //词汇总分
        $s5 = 0; //句子复杂度得分

        //背景资料题目号
        $bg_pos_arr = array(840,841,842,843,844,845,846);

        //存放背景数据分组
        $bg_arr = [];

        $xanswersheet = $paper->xanswersheet;
        $xanswers = $xanswersheet->getAnswers();

        foreach($xanswers as $a){
            $pos = $a->pos;
            if(strpos($pos, '.')){
                continue;
            }
            //动词得分
            if($pos >= 73 && $pos <= 266){
                $s1 += $a->score;
            }

            //方向词得分
            if($pos >= 672 && $pos <= 692){
                $s2 += $a->score;
            }

            //量词得分
            if($pos >= 726 && $pos <= 745){
                $s3 += $a->score;
            }

            //词汇总分
            if($pos >= 1 && $pos <= 799){
                $s4 += $a->score;
            }

            //句子复杂度得分
            if($pos >= 813 && $pos <= 839){
                $s5 += $a->score;
            }

            //背景资料获取
            if(in_array($pos, $bg_pos_arr)){
                $temp = [];
                $temp["question"] = $a->getQuestionCtr()->getQaHtmlQuestionContent();
                $temp["answer"] = $a->getQuestionCtr()->getQaHtmlAnswerContent();
                $bg_arr[] = $temp;
            }
        }

        return array(
            "bg" => $bg_arr,
            "base" => array(
                array(
                    "desc" => "动词得分",
                    "score" => $s1
                ),
                array(
                    "desc" => "方向词得分",
                    "score" => $s2
                ),
                array(
                    "desc" => "量词得分",
                    "score" => $s3
                ),
                array(
                    "desc" => "词汇总分",
                    "score" => $s4
                ),
                array(
                    "desc" => "句子复杂度得分",
                    "score" => $s5
                ),
            )
        );
    }

    //社交反应量表结果数据 SRS
    public static function getASDPaperResultOfSRS(Paper $paper){
        //知觉
        $arr1 = array(2,7,25,32,45,52,54,56);
        //认知
        $arr2 = array(5,10,15,17,30,40,42,44,48,58,59,62);
        //沟通
        $arr3 = array(12,13,16,18,19,21,22,26,33,35,36,37,38,41,46,47,51,53,55,57,60,61);
        //动机
        $arr4 = array(1,3,6,9,11,23,27,34,43,64,65);
        //行为方式
        $arr5 = array(4,8,14,20,24,28,29,31,39,49,50,63);

        $score = 0; //总分
        $score1 = 0; //知觉得分
        $score2 = 0; //认知得分
        $score3 = 0; //沟通得分
        $score4 = 0; //动机得分
        $score5 = 0; //行为方式得分

        $xanswersheet = $paper->xanswersheet;
        $xanswers = $xanswersheet->getAnswers();

        $score = $xanswersheet->score;

        foreach($xanswers as $a){
            $pos = $a->pos;
            if(strpos($pos, '.')){
                continue;
            }
            if(in_array($pos, $arr1)){
                $score1 += $a->score;
                continue;
            }
            if(in_array($pos, $arr2)){
                $score2 += $a->score;
                continue;
            }
            if(in_array($pos, $arr3)){
                $score3 += $a->score;
                continue;
            }
            if(in_array($pos, $arr4)){
                $score4 += $a->score;
                continue;
            }
            if(in_array($pos, $arr5)){
                $score5 += $a->score;
                continue;
            }
        }

        return array(
            array(
                "desc" => "知觉",
                "score" => $score1
            ),
            array(
                "desc" => "认知",
                "score" => $score2
            ),
            array(
                "desc" => "沟通",
                "score" => $score3
            ),
            array(
                "desc" => "动机",
                "score" => $score4
            ),
            array(
                "desc" => "行为方式",
                "score" => $score5
            ),
            array(
                "desc" => "总分",
                "score" => $score
            ),
        );
    }

    //长处和困难问卷（家长版）SDQ
    private static function getASDPaperResultOfSDQ(Paper $paper){
        //情绪问题
        $arr1 = array(3,8,13,16,24);
        //行为问题
        $arr2 = array(5,7,12,18,22);
        //多动问题
        $arr3 = array(2,10,15,21,25);
        //伙伴问题
        $arr4 = array(6,11,14,19,23);
        //亲社会行为
        $arr5 = array(1,4,9,17,20);

        $score1 = 0; //情绪问题得分
        $score2 = 0; //行为问题得分
        $score3 = 0; //多动问题得分
        $score4 = 0; //伙伴问题得分
        $score5 = 0; //亲社会行为得分
        //困难总分：情绪+行为+多动+伙伴
        $score = 0;

        $xanswersheet = $paper->xanswersheet;
        $xanswers = $xanswersheet->getAnswers();
        foreach($xanswers as $a){
            $pos = $a->pos;
            if(strpos($pos, '.')){
                continue;
            }
            if(in_array($pos, $arr1)){
                $score1 += $a->score;
                continue;
            }
            if(in_array($pos, $arr2)){
                $score2 += $a->score;
                continue;
            }
            if(in_array($pos, $arr3)){
                $score3 += $a->score;
                continue;
            }
            if(in_array($pos, $arr4)){
                $score4 += $a->score;
                continue;
            }
            if(in_array($pos, $arr5)){
                $score5 += $a->score;
                continue;
            }
        }
        $score = $score1 + $score2 + $score3 + $score4;

        //评价的获取
        //情绪问题 常模 0-3
        $result_str1 = self::getASDPaperOfSDQResultStr($score1, 3);
        //行为问题 常模 0-2
        $result_str2 = self::getASDPaperOfSDQResultStr($score2, 2);
        //多动问题 常模 0-5
        $result_str3 = self::getASDPaperOfSDQResultStr($score3, 5);
        //伙伴问题 常模 0-2
        $result_str4 = self::getASDPaperOfSDQResultStr($score4, 2);
        //亲社会行为 常模 10-6
        $result_str5 = self::getASDPaperOfSDQResultStr($score5, 6, false);

        //困难 常模 0-13
        $result_str = "正常";
        if($score >= 14 && $score <= 16){
            $result_str = "临界值";
        }else if($score > 16){
            $result_str = "异常";
        }

        return array(
            array(
                'desc' => '情绪问题',
                'changmo' => '0-3',
                'score' => $score1,
                'result_str' => $result_str1
            ),
            array(
                'desc' => '行为问题',
                'changmo' => '0-2',
                'score' => $score2,
                'result_str' => $result_str2
            ),
            array(
                'desc' => '多动问题',
                'changmo' => '0-5',
                'score' => $score3,
                'result_str' => $result_str3
            ),
            array(
                'desc' => '伙伴问题',
                'changmo' => '0-2',
                'score' => $score4,
                'result_str' => $result_str4
            ),
            array(
                'desc' => '困难总分',
                'changmo' => '0-13',
                'score' => $score,
                'result_str' => $result_str
            ),
            array(
                'desc' => '亲社会行为',
                'changmo' => '10-6',
                'score' => $score5,
                'result_str' => $result_str5
            ),
        );
    }

    private static function getASDPaperOfSDQResultStr($score, $value, $isgt = true){
        $str = "正常";
        if($isgt){
            if($score == $value+1){
                $str = "临界值";
            }else if($score > $value+1){
                $str = "异常";
            }
        }else{
            if($score == $value-1){
                $str = "临界值";
            }else if($score < $value-1){
                $str = "异常";
            }
        }
        return $str;
    }

    //社会交流量表 SCQ
    private static function getASDPaperResultOfSCQ(Paper $paper){
        $xanswersheet = $paper->xanswersheet;
        $xanswers = $xanswersheet->getAnswers();

        $score = $xanswersheet->score;

        $temp = array();
        foreach($xanswers as $a){
            $pos = $a->pos;
            if(strpos($pos, '.')){
                continue;
            }
            $xoption = $a->getTheXOption();
            for ($i=0; $i < 10; $i++) {
                $n = ($pos-1)%10;
                if($i == $n){
                    if(empty($temp[$i])){
                        $temp[$i] = array();
                    }
                    $temp[$i][] = $pos;
                    $temp[$i][] = $xoption->content;
                    break;
                }
            }
            //$first = array_shift($temp);
            //array_push($temp, $first);
        }
        return array(
            'score' => $score,
            'answers' => $temp
        );
    }

    //12项一般健康问卷 GHQ12
    private static function getASDPaperResultOfGHQ12(Paper $paper){
        $xanswersheet = $paper->xanswersheet;
        $score = $xanswersheet->score;

        return array(
            'score' => $score
        );
    }

    //广泛性焦虑量表 GAD7
    private static function getASDPaperResultOfGAD7(Paper $paper){
        $xanswersheet = $paper->xanswersheet;
        $score = $xanswersheet->score;

        $result_str = "没有广泛性焦虑";
        if($score >= 5 && $score <= 9){
            $result_str = "轻度广泛性焦虑";
        }else if($score >= 10 && $score <= 14){
            $result_str = "中度广泛性焦虑";
        }else if($score >= 15){
            $result_str = "严重广泛性焦虑";
        }

        return array(
            'score' => $score,
            'result_str' => $result_str
        );
    }

    //9条目健康问卷 PHQ9
    private static function getASDPaperResultOfPHQ9(Paper $paper){
        $xanswersheet = $paper->xanswersheet;
        $score = $xanswersheet->score;

        $result_str = "没有抑郁";
        if($score >= 5 && $score <= 9){
            $result_str = "轻度抑郁";
        }else if($score >= 10 && $score <= 14){
            $result_str = "中度抑郁";
        }else if($score >= 15 && $score <= 19){
            $result_str = "中重度抑郁";
        }else if($score >= 20){
            $result_str = "重度抑郁";
        }

        return array(
            'score' => $score,
            'result_str' => $result_str
        );
    }

    //SNAP-IV 评估问卷
    private static function getASDPaperResultOfSNAPIV(Paper $paper){
        //注意缺陷
        $arr1 = array(1,2,3,4,5,6,7,8,9);
        //多动冲动
        $arr2 = array(10,11,12,13,14,15,16,17,18);
        //对立违抗
        $arr3 = array(19,20,21,22,23,24,25,26);
        //品行障碍
        $arr4 = array(27,28,29,30,31,32,33,34,35,36,37,38,39,40);
        //情绪问题
        $arr5 = array(41,42,43,44,45,46,47);

        $score1 = 0; //注意缺陷
        $score2 = 0; //多动冲动
        $score3 = 0; //对立违抗
        $score4 = 0; //品行障碍
        $score5 = 0; //情绪问题

        //阳性条目判定
        //得分大于等于2为阳性条目
        $bad_answer_cnt1 = 0;
        $bad_answer_cnt2 = 0;
        $bad_answer_cnt3 = 0;
        $bad_answer_cnt4 = 0;
        $bad_answer_cnt5 = 0;

        $xanswersheet = $paper->xanswersheet;
        $xanswers = $xanswersheet->getAnswers();
        foreach($xanswers as $a){
            $pos = $a->pos;
            if(strpos($pos, '.')){
                continue;
            }
            if(in_array($pos, $arr1)){
                $score1 += $a->score;
                if($a->score >= 2){
                    $bad_answer_cnt1 += 1;
                }
                continue;
            }
            if(in_array($pos, $arr2)){
                $score2 += $a->score;
                if($a->score >= 2){
                    $bad_answer_cnt2 += 1;
                }
                continue;
            }
            if(in_array($pos, $arr3)){
                $score3 += $a->score;
                if($a->score >= 2){
                    $bad_answer_cnt3 += 1;
                }
                continue;
            }
            if(in_array($pos, $arr4)){
                $score4 += $a->score;
                if($a->score >= 2){
                    $bad_answer_cnt4 += 1;
                }
                continue;
            }
            if(in_array($pos, $arr5)){
                $score5 += $a->score;
                if($a->score >= 2){
                    $bad_answer_cnt5 += 1;
                }
                continue;
            }
        }

        $result_str1 = "阴性";
        $result_str2 = "阴性";
        $result_str3 = "阴性";
        $result_str4 = "阴性";
        $result_str5 = "阴性";
        if($bad_answer_cnt1 >= 6){
            $result_str1 = "阳性";
        }
        if($bad_answer_cnt2 >= 6){
            $result_str2 = "阳性";
        }
        if($bad_answer_cnt3 >= 4){
            $result_str3 = "阳性";
        }
        if($bad_answer_cnt4 >= 3){
            $result_str4 = "阳性";
        }
        if($bad_answer_cnt5 >= 3){
            $result_str5 = "阳性";
        }

        return array(
            array(
                'desc' => '注意缺陷',
                'bad_answer_cnt' => $bad_answer_cnt1,
                'score' => $score1,
                'result_str' => $result_str1
            ),
            array(
                'desc' => '多动冲动',
                'bad_answer_cnt' => $bad_answer_cnt2,
                'score' => $score2,
                'result_str' => $result_str2
            ),
            array(
                'desc' => '对立违抗',
                'bad_answer_cnt' => $bad_answer_cnt3,
                'score' => $score3,
                'result_str' => $result_str3
            ),
            array(
                'desc' => '品行障碍',
                'bad_answer_cnt' => $bad_answer_cnt4,
                'score' => $score4,
                'result_str' => $result_str4
            ),
            array(
                'desc' => '情绪问题',
                'bad_answer_cnt' => $bad_answer_cnt5,
                'score' => $score5,
                'result_str' => $result_str5
            ),
        );
    }

    //Conners 儿童行为问卷（父母版）
    private static function getASDPaperResultOfConners(Paper $paper){
        //品行问题 12
        $arr1 = array(2,8,14,19,20,21,22,23,27,33,34,39);
        //学习问题 4
        $arr2 = array(10,25,31,37);
        //心身障碍 5
        $arr3 = array(32,41,43,44,48);
        //冲动-多动 4
        $arr4 = array(4,5,11,13);
        //焦虑 4
        $arr5 = array(12,16,24,47);
        //多动指数 10
        $arr6 = array(4,7,11,13,14,25,31,33,37,38);

        $score1 = 0; //品行问题
        $score2 = 0; //学习问题
        $score3 = 0; //心身障碍
        $score4 = 0; //冲动-多动
        $score5 = 0; //焦虑
        $score6 = 0; //多动指数

        $xanswersheet = $paper->xanswersheet;

        $xanswers = $xanswersheet->getAnswers();
        foreach($xanswers as $a){
            $pos = $a->pos;
            if(strpos($pos, '.')){
                continue;
            }
            if(in_array($pos, $arr1)){
                $score1 += $a->score;
                //continue;
            }
            if(in_array($pos, $arr2)){
                $score2 += $a->score;
                //continue;
            }
            if(in_array($pos, $arr3)){
                $score3 += $a->score;
                //continue;
            }
            if(in_array($pos, $arr4)){
                $score4 += $a->score;
                //continue;
            }
            if(in_array($pos, $arr5)){
                $score5 += $a->score;
                //continue;
            }
            if(in_array($pos, $arr6)){
                $score6 += $a->score;
                //continue;
            }
        }
        $score1 = round($score1/12, 2);
        $score2 = round($score2/4, 2);
        $score3 = round($score3/5, 2);
        $score4 = round($score4/4, 2);
        $score5 = round($score5/4, 2);
        $score6 = round($score6/10, 2);

        $score = $score1 + $score2 + $score3 + $score4 + $score5 + $score6;

        $result_str1 = "";
        $result_str2 = "";
        $result_str3 = "";
        $result_str4 = "";
        $result_str5 = "";
        $result_str6 = "";
        $data = self::getASDConnersOfNormalRange($paper);
        if($data != null){
            $score_avg_arr = array($score1, $score2, $score3, $score4, $score5, $score6);
            list($result_str1, $result_str2, $result_str3, $result_str4, $result_str5, $result_str6) = self::getASDPaperOfConnersResultStr($score_avg_arr, $data);
        }
        return array(
            array(
                'desc' => '品行问题',
                'score' => $score1,
                'result_str' => $result_str1
            ),
            array(
                'desc' => '学习问题',
                'score' => $score2,
                'result_str' => $result_str2
            ),
            array(
                'desc' => '心身障碍',
                'score' => $score3,
                'result_str' => $result_str3
            ),
            array(
                'desc' => '冲动-多动',
                'score' => $score4,
                'result_str' => $result_str4
            ),
            array(
                'desc' => '焦虑',
                'score' => $score5,
                'result_str' => $result_str5
            ),
            array(
                'desc' => '多动指数',
                'score' => $score6,
                'result_str' => $result_str6
            ),
            array(
                'desc' => '总分',
                'score' => $score,
                'result_str' => ''
            ),
        );
    }

    private static function getASDPaperOfConnersResultStr($score_avg_arr, $data){
        $temp = array();
        foreach($data as $i => $a){
            $x = $a['X'];
            $sd = $a['SD'];
            $left_v = $x - 2*$sd;
            $right_v = $x + 2*$sd;

            $v = $score_avg_arr[$i];
            $str = "异常";
            if($v <= $right_v && $v >= $left_v ){
                $str = "正常";
            }
            $temp[] = $str;
        }
        return $temp;
    }

    private static function getASDConnersOfNormalRange($paper){
        $arr = array(
            array(
                'age_arr' => array(3,4,5),
                1 => array(
                    array(
                        'X' => 0.48,
                        'SD' => 0.36
                    ),
                    array(
                        'X' => 0.64,
                        'SD' => 0.53
                    ),
                    array(
                        'X' => 0.15,
                        'SD' => 0.24
                    ),
                    array(
                        'X' => 0.58,
                        'SD' => 0.56
                    ),
                    array(
                        'X' => 0.43,
                        'SD' => 0.37
                    ),
                    array(
                        'X' => 0.55,
                        'SD' => 0.43
                    ),
                ),
                2 => array(
                    array(
                        'X' => 0.39,
                        'SD' => 0.27
                    ),
                    array(
                        'X' => 0.49,
                        'SD' => 0.42
                    ),
                    array(
                        'X' => 0.11,
                        'SD' => 0.20
                    ),
                    array(
                        'X' => 0.47,
                        'SD' => 0.44
                    ),
                    array(
                        'X' => 0.40,
                        'SD' => 0.35
                    ),
                    array(
                        'X' => 0.45,
                        'SD' => 0.35
                    ),
                ),
            ),
            array(
                'age_arr' => array(6,7,8),
                1 => array(
                    array(
                        'X' => 0.41,
                        'SD' => 0.32
                    ),
                    array(
                        'X' => 0.61,
                        'SD' => 0.49
                    ),
                    array(
                        'X' => 0.17,
                        'SD' => 0.26
                    ),
                    array(
                        'X' => 0.58,
                        'SD' => 0.47
                    ),
                    array(
                        'X' => 0.34,
                        'SD' => 0.38
                    ),
                    array(
                        'X' => 0.52,
                        'SD' => 0.38
                    ),
                ),
                2 => array(
                    array(
                        'X' => 0.34,
                        'SD' => 0.29
                    ),
                    array(
                        'X' => 0.51,
                        'SD' => 0.46
                    ),
                    array(
                        'X' => 0.15,
                        'SD' => 0.23
                    ),
                    array(
                        'X' => 0.41,
                        'SD' => 0.45
                    ),
                    array(
                        'X' => 0.32,
                        'SD' => 0.32
                    ),
                    array(
                        'X' => 0.40,
                        'SD' => 0.34
                    ),
                ),
            ),
            array(
                'age_arr' => array(9,10,11),
                1 => array(
                    array(
                        'X' => 0.41,
                        'SD' => 0.37
                    ),
                    array(
                        'X' => 0.66,
                        'SD' => 0.53
                    ),
                    array(
                        'X' => 0.18,
                        'SD' => 0.27
                    ),
                    array(
                        'X' => 0.51,
                        'SD' => 0.47
                    ),
                    array(
                        'X' => 0.34,
                        'SD' => 0.36
                    ),
                    array(
                        'X' => 0.5,
                        'SD' => 0.39
                    ),
                ),
                2 => array(
                    array(
                        'X' => 0.35,
                        'SD' => 0.29
                    ),
                    array(
                        'X' => 0.53,
                        'SD' => 0.50
                    ),
                    array(
                        'X' => 0.19,
                        'SD' => 0.28
                    ),
                    array(
                        'X' => 0.39,
                        'SD' => 0.41
                    ),
                    array(
                        'X' => 0.30,
                        'SD' => 0.32
                    ),
                    array(
                        'X' => 0.39,
                        'SD' => 0.34
                    ),
                ),
            ),
            array(
                'age_arr' => array(12,13,14),
                1 => array(
                    array(
                        'X' => 0.45,
                        'SD' => 0.38
                    ),
                    array(
                        'X' => 0.78,
                        'SD' => 0.61
                    ),
                    array(
                        'X' => 0.25,
                        'SD' => 0.36
                    ),
                    array(
                        'X' => 0.53,
                        'SD' => 0.55
                    ),
                    array(
                        'X' => 0.37,
                        'SD' => 0.41
                    ),
                    array(
                        'X' => 0.53,
                        'SD' => 0.45
                    ),
                ),
                2 => array(
                    array(
                        'X' => 0.37,
                        'SD' => 0.33
                    ),
                    array(
                        'X' => 0.61,
                        'SD' => 0.57
                    ),
                    array(
                        'X' => 0.27,
                        'SD' => 0.37
                    ),
                    array(
                        'X' => 0.39,
                        'SD' => 0.43
                    ),
                    array(
                        'X' => 0.38,
                        'SD' => 0.39
                    ),
                    array(
                        'X' => 0.42,
                        'SD' => 0.39
                    ),
                ),
            ),
            array(
                'age_arr' => array(15,16,17),
                1 => array(
                    array(
                        'X' => 0.44,
                        'SD' => 0.4
                    ),
                    array(
                        'X' => 0.78,
                        'SD' => 0.64
                    ),
                    array(
                        'X' => 0.25,
                        'SD' => 0.32
                    ),
                    array(
                        'X' => 0.51,
                        'SD' => 0.53
                    ),
                    array(
                        'X' => 0.34,
                        'SD' => 0.39
                    ),
                    array(
                        'X' => 0.51,
                        'SD' => 0.43
                    ),
                ),
                2 => array(
                    array(
                        'X' => 0.37,
                        'SD' => 0.34
                    ),
                    array(
                        'X' => 0.65,
                        'SD' => 0.55
                    ),
                    array(
                        'X' => 0.28,
                        'SD' => 0.37
                    ),
                    array(
                        'X' => 0.37,
                        'SD' => 0.41
                    ),
                    array(
                        'X' => 0.32,
                        'SD' => 0.34
                    ),
                    array(
                        'X' => 0.41,
                        'SD' => 0.38
                    ),
                ),
            ),
        );

        $patient = $paper->patient;
        if(false == $patient instanceof Patient){
            return null;
        }

        $sex = $patient->sex;
        if($sex == 0){
            return null;
        }

        $age = $patient->getAgeStr();

        if($age == ""){
            return null;
        }

        if($age < 3){
            return null;
        }

        if($age > 17){
            return null;
        }

        foreach($arr as $a){
            $age_arr = $a['age_arr'];
            if(in_array($age, $age_arr)){
                return $a[$sex];
            }
        }
        return null;
    }

    //儿童睡眠节律报告
    private static function getASDPaperResultOfSleepRhy(Paper $paper){
        $xanswersheet = $paper->xanswersheet;

        //工作日
        //早晨醒来的时刻
        $time_wake_wd_a = XAnswer::getXAnswerByXQuestionename($paper, 'time_wake_wd');
        $time_wake_wd = $time_wake_wd_a->content;

        //完全清醒的时刻
        $time_wake_wide_wd_a = XAnswer::getXAnswerByXQuestionename($paper, 'time_wake_wide_wd');
        $time_wake_wide_wd = $time_wake_wide_wd_a->content;

        //夜间上床的时刻
        $time_bed_wd_a = XAnswer::getXAnswerByXQuestionename($paper, 'time_bed_wd');
        $time_bed_wd = $time_bed_wd_a->content;

        //关灯时刻
        $time_lightoff_wd_a = XAnswer::getXAnswerByXQuestionename($paper, 'time_lightoff_wd');
        $time_lightoff_wd = $time_lightoff_wd_a->content;

        //入睡需要的时长
        $sleepneed_length_wd_a = XAnswer::getXAnswerByXQuestionename($paper, 'sleepneed_length_wd');
        $sleepneed_length_wd = $sleepneed_length_wd_a->content;

        //在床上时长=早晨醒来的时刻-夜间上床的时刻
        $bed_length_wd = self::getHourDiff($time_bed_wd, $time_wake_wd);

        //入睡时刻 = 关灯时刻+入睡需要的时长
        $time_sleep_wd = self::getTimeStr($time_lightoff_wd, $sleepneed_length_wd);

        //睡眠时长=早晨醒来的时刻-入睡时刻
        $sleep_length_wd = self::getHourDiff($time_sleep_wd, $time_wake_wd);

        //睡眠惰性=完全清醒的时刻-早晨醒来的时刻
        $sleep_inertia_wd = self::getHourDiff($time_wake_wide_wd, $time_wake_wd, true);
        $sleep_inertia_wd = ceil($sleep_inertia_wd*60);

        //休息日
        //早晨醒来的时刻
        $time_wake_rd_a = XAnswer::getXAnswerByXQuestionename($paper, 'time_wake_rd');
        $time_wake_rd = $time_wake_rd_a->content;

        //完全清醒的时刻
        $time_wake_wide_rd_a = XAnswer::getXAnswerByXQuestionename($paper, 'time_wake_wide_rd');
        $time_wake_wide_rd = $time_wake_wide_rd_a->content;

        //夜间上床的时刻
        $time_bed_rd_a = XAnswer::getXAnswerByXQuestionename($paper, 'time_bed_rd');
        $time_bed_rd = $time_bed_rd_a->content;

        //关灯时刻
        $time_lightoff_rd_a = XAnswer::getXAnswerByXQuestionename($paper, 'time_lightoff_rd');
        $time_lightoff_rd = $time_lightoff_rd_a->content;

        //入睡需要的时长
        $sleepneed_length_rd_a = XAnswer::getXAnswerByXQuestionename($paper, 'sleepneed_length_rd');
        $sleepneed_length_rd = $sleepneed_length_rd_a->content;

        //在床上时长=早晨醒来的时刻-夜间上床的时刻
        $bed_length_rd = self::getHourDiff($time_bed_rd, $time_wake_rd);

        //入睡时刻 = 关灯时刻+入睡需要的时长
        $time_sleep_rd = self::getTimeStr($time_lightoff_rd, $sleepneed_length_rd);

        //睡眠时长=早晨醒来的时刻-入睡时刻
        $sleep_length_rd = self::getHourDiff($time_sleep_rd, $time_wake_rd);

        //睡眠惰性=完全清醒的时刻-早晨醒来的时刻
        $sleep_inertia_rd = self::getHourDiff($time_wake_wide_rd, $time_wake_rd, true);
        $sleep_inertia_rd = ceil($sleep_inertia_rd*60);

        //平均睡眠时长=(工作日睡眠时长*5+休息日睡眠时长*2)/7
        if( empty($sleep_length_wd) && empty($sleep_length_rd) ){
            $sleep_length = "";
        }else{
            $sleep_length = ($sleep_length_wd*5 + $sleep_length_rd*2)/7;
            $sleep_length = round($sleep_length,2);
        }

        //M/E 得分=17--26选择题分值相加
        $score_me = 0;
        $xanswersheet = $paper->xanswersheet;

        $xanswers = $xanswersheet->getAnswers();
        foreach($xanswers as $a){
            $pos = $a->pos;
            if(strpos($pos, '.')){
                continue;
            }
            if($pos >= 28 && $pos <= 37){
                $score_me += $a->score;
            }
        }

        //睡眠类型
        $sleep_type = "早睡早醒型";
        if($score_me > 23 && $score_me < 33){
            $sleep_type = "中间型";
        }else if($score_me >= 33){
            $sleep_type = "晚睡晚醒型";
        }

        return array(
            'bed_length_wd' => $bed_length_wd,
            'time_sleep_wd' => $time_sleep_wd,
            'sleep_length_wd' => $sleep_length_wd,
            'sleep_inertia_wd' => $sleep_inertia_wd,
            'bed_length_rd' => $bed_length_rd,
            'time_sleep_rd' => $time_sleep_rd,
            'sleep_length_rd' => $sleep_length_rd,
            'sleep_inertia_rd' => $sleep_inertia_rd,
            'sleep_length' => $sleep_length,
            'score_me' => $score_me,
            'sleep_type' => $sleep_type
        );
    }

    //获取今天和昨天两个时刻的时间差，返回小时数
    //当 $is_same_day = false,即两个时刻分别属于昨天和今天
    //$lhourstr 昨天的时刻
    //$rhourstr 今天的时刻

    //当 $is_same_day = true, 即两个时刻属于今天，同一天
    //$lhourstr 较大的时刻
    //$rhourstr 较小的时刻

    private static function getHourDiff($lhourstr, $rhourstr, $is_same_day = false){
        if(empty($lhourstr) || empty($rhourstr)){
            return "";
        }
        $time1 = strtotime($lhourstr);
        $time2 = strtotime($rhourstr);
        if($is_same_day){
            $time = ($time1 - $time2);
        }else{
            $time = 86400 - ($time1 - $time2);
        }
        $h0 = floor($time/3600);
        $h1 = round((floor($time/60) % 60)/60, 2);
        $h = $h0 + $h1;
        return $h;
    }

    //获取类似『19:30』的时刻字符串
    private static function getTimeStr($timestr, $minute){
        if(empty($timestr)){
            return "";
        }
        $time = strtotime($timestr);
        $time = $time + $minute*60;
        return date("H:i:s", $time);
    }

}
