<?php

/*
 * Paper
 */
class Paper extends Entity
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
            'papertplid',  // papertplid
            'groupstr',  // groupstr
            'ename',  // 量表英文名称
            'xanswersheetid',  // xanswersheetid
            'objtype',
            'objid',
            'writer');  // 填写人
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'papertplid',
            'xanswersheetid');
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
        $this->_belongtos["papertpl"] = array(
            "type" => "PaperTpl",
            "key" => "papertplid");
        $this->_belongtos["xanswersheet"] = array(
            "type" => "XAnswerSheet",
            "key" => "xanswersheetid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["papertplid"] = $papertplid;
    // $row["groupstr"] = $groupstr;
    // $row["ename"] = $ename;
    // $row["xanswersheetid"] = $xanswersheetid;
    // $row["writer"] = $writer;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Paper::createByBiz row cannot empty");

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
        $default["papertplid"] = 0;
        $default["groupstr"] = '';
        $default["ename"] = '';
        $default["xanswersheetid"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["writer"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    /*
     * adhd_iv conners dyspnea eq5d ird medicine_parent sf36 sgrq sideeffect
     * zhouxingchi
     */
    public function getAnswerSheet () {
        $xquestionsheetid = $this->papertpl->xquestionsheetid;
        if ($xquestionsheetid < 1) {
            return null;
        }
        return $this->xanswersheet;
        // return XAnswerSheet::getBy3params($xquestionsheetid,
        // get_class($this), $this->id);
    }

    public function hasAnswerSheet () {
        $answersheet = $this->getAnswerSheet();
        return $answersheet instanceof XAnswerSheet;
    }

    public function getQsheetNameFix () {
        $arr = PaperDao::getQSheetNameArray();
        return $arr[$this->ename];
    }

    public function getContent () {
        return "量表:" . $this->ename;
    }

    public function getAnswers () {
        return XAnswer::getListByXAnswerSheet($this->xanswersheetid);
    }

    public function getLastAnswer () {
        $answers = $this->getAnswers();

        $answer = array_pop($answers);
        return $answer;
    }

    public function getRemarkByQsheetName () {
        $name = $this->ename;

        // diseaseid = 2
        $str = $this->papertpl->title;

        if ($name == "adhd_iv") {
            $str = $this->xanswersheet->score . "分";
        } elseif ($name == "medicine_parent") {
            $a = $this->getMissedMedicine();
            $day = $a['day'];
            if (! empty($day)) {
                $str = "停药{$day}天";
            }
        } elseif ($name == "sideeffect") {
            $str = $this->getSideeffectSerious() . "项重、极重";
        } elseif ($name == "conners") {
            $str = "";
        }

        return $str;
    }

    public function getSumOfConners () {
        $answers = $this->getAnswers();
        $score1 = 0;
        $score2 = 0;
        $score3 = 0;
        $score4 = 0;
        $score5 = 0;
        $score6 = 0;
        $scoreMap = array(
            "无" => 0,
            "稍有" => 1,
            "相当多" => 2,
            "很多" => 3);
        $arrs = array(
            "score1" => array(
                2,
                8,
                14,
                19,
                20,
                21,
                22,
                23,
                27,
                33,
                34,
                39),
            "score2" => array(
                10,
                25,
                31,
                37),
            "score3" => array(
                32,
                41,
                43,
                44,
                48),
            "score4" => array(
                4,
                5,
                11,
                13),
            "score5" => array(
                12,
                16,
                24,
                47),
            "score6" => array(
                4,
                7,
                11,
                13,
                14,
                25,
                31,
                33,
                37,
                38));
        foreach ($answers as $a) {
            $score = 0;
            $pos = $a->pos;
            $ref = XAnswerOptionRef::getOneByXAnswer($a);
            if ($ref instanceof XAnswerOptionRef) {
                $content = $ref->content;
                $score = $scoreMap[$content];
            }
            foreach ($arrs as $key => $value) {
                if (in_array($pos, $value)) {
                    $$key += $score;
                }
            }
        }

        return array(
            $score1,
            $score2,
            $score3,
            $score4,
            $score5,
            $score6);
    }

    // 获取重、极重的项数
    public function getSideeffectSerious () {
        $answers = $this->getAnswers();
        $num = 0;
        $scoreMap = array(
            "正常" => 0,
            "轻" => 1,
            "中" => 2,
            "重" => 3,
            "极重" => 4);
        foreach ($answers as $a) {
            $ref = XAnswerOptionRef::getOneByXAnswer($a);
            if ($ref instanceof XAnswerOptionRef) {
                $content = $ref->content;
                if ($scoreMap[$content] >= 3) {
                    $num ++;
                }
            }
        }
        return $num;
    }

    // 获取漏服药列表
    public function getMissedMedicine () {
        $answers = $this->getAnswers();
        $name = "";
        $day = "";
        $date = "";
        foreach ($answers as $a) {
            $xquestion = $a->xquestion;
            $ename = $xquestion->ename;
            if ($ename == "drugname") {
                $ref = XAnswerOptionRef::getOneByXAnswer($a);
                if ($ref instanceof XAnswerOptionRef) {
                    $name = $ref->content;
                } else {
                    $name = $a->content;
                }
                $date = date('Y-m-d', strtotime($a->createtime));
            }
            if ($ename == "missday") {
                $day = $a->content;
            }
        }

        return array(
            "name" => $name,
            "day" => $day,
            "date" => $date);
    }
    // 获取用药剂量
    public function getMedicineDose () {
        $answers = $this->getAnswers();
        $dose = array();
        foreach ($answers as $a) {
            $xquestion = $a->xquestion;
            $ename = $xquestion->ename;
            if ($ename == "drugdose") {
                $k = preg_match_all('/[0-9]{2}/', $a->content, $dose);
                if ($dose[0][0] > 100) {
                    $dose[0][0] = 1;
                }
                break;
            }
        }

        return $dose[0][0];
    }
    // 获取用药名称
    public function getMedicineName () {
        $answers = $this->getAnswers();
        $name = "";
        foreach ($answers as $a) {
            $xquestion = $a->xquestion;
            $ename = $xquestion->ename;
            if ($ename == "drugname") {
                $ref = XAnswerOptionRef::getOneByXAnswer($a);
                if ($ref instanceof XAnswerOptionRef) {
                    $name = $ref->content;
                } else {
                    $name = $a->content;
                }
                break;
            }
        }

        return $name;
    }

    public function isOutPaper ($pgroup) {
        if ($pgroup->outpapertplid == $this->papertplid) {
            return true;
        }
        return false;
    }

    // 复制量表,级联复制答卷
    public function copyOne ($auditor) {
        $row = array();
        $row["wxuserid"] = $this->wxuserid;
        $row["userid"] = $this->userid;
        $row["patientid"] = $this->patientid;
        $row["doctorid"] = $this->doctorid; // done pcard fix
        $row["papertplid"] = $this->papertplid;
        $row["groupstr"] = $this->groupstr;
        $row["ename"] = $this->ename;
        $row["xanswersheetid"] = 0;
        $row["writer"] = '医助:' . $auditor->name;

        $paperNew = Paper::createByBiz($row);

        // 复制答卷
        $this->copyXAnswerSheetTo($paperNew);

        return $paperNew;
    }

    // 级联复制答卷
    public function copyXAnswerSheetTo (Paper $paperNew) {
        $xanswersheet = $this->xanswersheet;
        if ($xanswersheet instanceof XAnswerSheet) {
            $xanswersheet->copyOne($paperNew);
        }
    }

    // 回调接口
    public function PaperCallback (XAnswerSheet $sheet) {
        $this->set4lock('xanswersheetid', $sheet->id);
    }

    public function getScore () {
        if ($this->papertplid == 130883366) {
            return $this->getLCQScore();
        } else {
            return '';
        }
    }

    // #5831 计算LCQ分数
    public function getLCQScore () {
        $paper = $this;

        $lcqs = [
            'shengli' => [
                'pos' => [1, 2, 3, 9, 10, 11, 14, 15],
                'divisor' => 8,
                'sum' => 0
            ],
            'xinli' => [
                'pos' => [4, 5, 6, 12, 13, 16, 17],
                'divisor' => 7,
                'sum' => 0
            ],
            'shehui' => [
                'pos' => [7, 8, 18, 19],
                'divisor' => 4,
                'sum' => 0
            ]
        ];

        if ($paper->hasAnswerSheet()) {
            foreach ($paper->getAnswerSheet()->getAnswers() as $xanswer) {
                foreach ($lcqs as $title => $lcq) {
                    if (in_array($xanswer->pos, $lcq['pos'])) {
                        $lcqs["{$title}"]['sum'] += $xanswer->score;
                    }
                }
            }
        }

        $sum = 0;
        foreach ($lcqs as $title => $lcq) {
            $sum += round($lcqs["{$title}"]['sum'] / $lcq['divisor'], 1);
        }

        return $sum;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getWriterByStr ($writer) {
        $name = '';
        switch ($writer) {
            case father:
                $name = XConst::$Ships[1];
                break;
            case mother:
                $name = XConst::$Ships[2];
                break;
            case teacher:
                $name = XConst::$Ships[3];
                break;
            case other:
                $name = XConst::$Ships[9];
                break;
            default:
                $name = $writer;
                break;
        }
        return $name;
    }
}
