<?php
// CheckupTpl
// 检查报告模板-关联一个问卷

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701
class CheckupTpl extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'diseaseid',  // 疾病
            'doctorid',  // 医生
            'xquestionsheetid',  // 问卷id
            'groupstr',  // 检查分组名称
            'ename',  // 检查英文名称
            'title',  // 标题
            'brief',  // 摘要
            'content',  // 内容
            'is_in_tkt',  // 是否显示在预约中
            'is_in_admin',  // 是否有问卷
            'is_selected',  // 医生开检查清单时是否默认选中
            'pos',  // 序号
            'status'); // 状态
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'xquestionsheetid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["xquestionsheet"] = array(
            "type" => "XQuestionSheet",
            "key" => "xquestionsheetid");
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
    }

    // $row = array();
    // $row["xquestionsheetid"] = $xquestionsheetid;
    // $row["groupstr"] = $groupstr;
    // $row["ename"] = $ename;
    // $row["title"] = $title;
    // $row["brief"] = $brief;
    // $row["content"] = $content;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "CheckupTpl::createByBiz row cannot empty");

        $default = array();
        $default["diseaseid"] = 0;
        $default["doctorid"] = 0;
        $default["xquestionsheetid"] = 0;
        $default["groupstr"] = '';
        $default["ename"] = '';
        $default["title"] = '';
        $default["brief"] = '';
        $default["content"] = '';
        $default["is_in_tkt"] = 0;
        $default["is_in_admin"] = 0;
        $default["is_selected"] = 0;
        $default["content"] = '';
        $default["pos"] = 9;
        $default["status"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 避免污染patient,函数写在了这里
    public function getTableChartOfPatient (Patient $patient, $version = 2) {
        $sql = " select count(*)
            from checkups
            where status = 0
            and patientid = :patientid
            and checkuptplid = :checkuptplid ";

        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':checkuptplid'] = $this->id;

        $cnt = Dao::queryValue($sql, $bind);

        $arr = array();
        if ($cnt > 0) {
            $arr['title'] = $this->title;
            $arr['ischartshow'] = 1;
            $arr['istableshow'] = 1;
            $arr['chart'] = $this->getChartOfPatient($patient);

            // 新版有不同
            if ($version > 2) {
                $arr['table'] = $this->getTableOfPatient4Ipad($patient);
            } else {
                $arr['table'] = $this->getTableOfPatient($patient);
            }
        }

        return $arr;
    }

    // getTableOfPatient
    public function getTableOfPatient (Patient $patient) {
        $checkups = CheckupDao::getListByPatientCheckupTpl($patient, $this);
        $checkups = array_reverse($checkups);
        $questions = $this->xquestionsheet->getQuestions();

        $table = array();

        $temp = array();
        $temp[]['title'] = "日期";

        foreach ($questions as $i => $q) {
            if ($q->isMultText()) {
                foreach ($q->getMultTitles() as $t) {
                    $temp[]['title'] = "{$q->content}-{$t}";
                }
            } else {
                $temp[]['title'] = "{$q->content}";
            }
        }
        $table['titles'] = $temp;

        $temp = array();
        foreach ($checkups as $a) {
            $temp = array();
            if ($a->xanswersheetid == 0) {
                continue;
            }
            $temp['cells'][] = $a->check_date;

            foreach ($questions as $i => $q) {
                $xanswer = $a->xanswersheet->getAnswer($q->id);
                // 有答案
                if ($xanswer instanceof XAnswer) {
                    foreach ($xanswer->getQuestionCtr()->getAnswerContents() as $t) {
                        $temp['cells'][] = "{$t}";
                    }
                } else {
                    if ($q->isMultText()) {
                        foreach ($q->getMultTitles() as $t) {
                            $temp['cells'][] = " ";
                        }
                    } else {
                        $temp['cells'][] = " ";
                    }
                }
            }
            $table['items'][] = $temp;
        }

        return $table;
    }

    // getTableOfPatient
    public function getTableOfPatient4Ipad (Patient $patient) {
        $checkups = CheckupDao::getListByPatientCheckupTpl($patient, $this);
        $checkups = array_reverse($checkups);
        $questions = $this->xquestionsheet->getQuestions();

        $table = array();

        $temp = array();
        $temp[] = "日期";

        foreach ($questions as $i => $q) {
            if ($q->isMultText()) {
                foreach ($q->getMultTitles() as $t) {
                    $temp[] = "{$q->content}-{$t}";
                }
            } else {
                $temp[] = "{$q->content}";
            }
        }
        $table['titles'] = $temp;
        $table['rows'] = array();

        $temp = array();
        foreach ($checkups as $a) {
            $temp = array();
            if ($a->xanswersheetid == 0) {
                continue;
            }
            $temp[] = $a->check_date;

            foreach ($questions as $i => $q) {
                $xanswer = $a->xanswersheet->getAnswer($q->id);
                // 有答案
                if ($xanswer instanceof XAnswer) {
                    foreach ($xanswer->getQuestionCtr()->getAnswerContents() as $t) {
                        $temp[] = "{$t}";
                    }
                } else {
                    if ($q->isMultText()) {
                        foreach ($q->getMultTitles() as $t) {
                            $temp[] = " ";
                        }
                    } else {
                        $temp[] = " ";
                    }
                }
            }
            $table['rows'][] = $temp;
        }

        return $table;
    }

    // getChartOfPatient
    public function getChartOfPatient ($patient) {
        $enames = $this->getXquestionEnamesForChart();
        $unit = $this->getUnit();

        $name = array();
        foreach ($enames as $key => $value) {
            $name[] = $key;
        }

        $response = array(
            'type' => 'line',
            'x' => array(),
            'unit' => "{$unit}",
            'lines' => array());

        $checkups = CheckupDao::getListByPatientidCheckuptplid_last7($patient->id, $this->id);
        $checkups = array_reverse($checkups);

        if (empty($checkups)) {
            $response = array();
        }

        foreach ($checkups as $checkup) {
            $response['x'][] = "{$checkup->check_date}";

            $xanswersheetid = $checkup->xanswersheetid;
            $xanswersheet = XAnswerSheet::getById($xanswersheetid);

            if (false == $xanswersheet instanceof XAnswerSheet) {
                continue;
            }

            $i = 0;
            foreach ($enames as $key => $value) {
                Debug::trace(" and xquestionsheetid={$xanswersheet->xquestionsheetid} and ename='{$value}'");

                $cond = " and xquestionsheetid=:xquestionsheetid and ename=:ename ";

                $bind = [];
                $bind[':xquestionsheetid'] = $xanswersheet->xquestionsheetid;
                $bind[':ename'] = $value;

                $xquestion = Dao::getEntityByCond("XQuestion", $cond, $bind);

                if (false == $xquestion instanceof XQuestion) {
                    // debug
                    Debug::warn("XQuestion not exist: and xquestionsheetid={$xanswersheet->xquestionsheetid} and ename='{$value}' ");
                    continue;
                }

                $cond = " and xanswersheetid=:xanswersheetid and xquestionid=:xquestionid";

                $bind = [];
                $bind[':xanswersheetid'] = $xanswersheetid;
                $bind[':xquestionid'] = $xquestion->id;

                $xanswer = Dao::getEntityByCond("XAnswer", $cond, $bind);

                if ('' == $xanswer->content) {
                    $val = "0";
                } else {
                    $val = "{$xanswer->content}";
                }

                $response['lines'][$i]['y'][] = $val;
                $response['lines'][$i ++]['name'] = $key;
            }
        }

        return $response;
    }

    public function getXquestionEnamesForChart () {
        $arr = array();

        // $arr['肺功能']['FVC'] = 'fvc';
        // $arr['肺功能']['TLC'] = 'tlc';
        // $arr['肺功能']['DLco'] = 'dlco';
        // $arr['肺功能']['FEV1'] = 'fev1';

        $arr['肺功能']['FEV1%'] = 'fev1per';
        $arr['肺功能']['FVC%'] = 'fvcper';
        $arr['肺功能']['TLC%'] = 'tlcper';
        $arr['肺功能']['DLco%'] = 'dlcoper';

        $arr['KL-6']['KL6'] = 'kl6';

        $arr['6min']['距离'] = 'juli';

        $arr['HRCT']['G'] = 'g';
        $arr['HRCT']['R'] = 'r';
        $arr['HRCT']['H'] = 'h';

        $str = isset($arr[$this->title]) ? $arr[$this->title] : "";
        return $str;
    }

    // 获取单位
    public function getUnit () {
        $units = array();
        $units['肺功能'] = "%";
        $units['KL-6'] = "U/ml";
        $units['6min'] = "m";
        $units['HRCT'] = "";

        $str = isset($units[$this->title]) ? $units[$this->title] : "";
        return $str;
    }

    // 回调接口
    public function CheckupTplCallback (XQuestionSheet $sheet) {
        $this->set4lock('xquestionsheetid', $sheet->id);
    }

    // TODO rework 有用处吗?
    public function testCallback (XQuestionSheet $sheet) {
        $this->CheckupTplCallback($sheet);
    }

    public function getContentNl2br () {
        return nl2br($this->content);
    }

    // 复制检查报告模板,级联复制问卷
    public function copyOne ($diseaseid, $doctorid) {
        // 复制检查报告模板
        $row = array();
        $row['doctorid'] = $doctorid;
        $row['groupstr'] = $this->groupstr;
        $row['title'] = $this->title;
        $row['brief'] = $this->brief;
        $row['content'] = $this->content;
        $row['diseaseid'] = $diseaseid;
        $row['xquestionsheetid'] = '';
        $row['ename'] = $this->ename;
        $row['status'] = $this->status;
        $row['pos'] = $this->pos;

        $checkuptplNew = self::createByBiz($row);

        // 复制问卷
        $this->copyXQuestionSheetTo($checkuptplNew);

        return $checkuptplNew;
    }

    // 级联复制问卷
    public function copyXQuestionSheetTo (CheckupTpl $checkuptplNew) {
        $xquestionsheet = $this->xquestionsheet;
        if ($xquestionsheet instanceof XQuestionSheet) {
            $xquestionsheet->copyOne($checkuptplNew);
        }
    }

    // 获取指定问题名称的最新答案
    public function getLatestAnswerByQuestionEnameAndPatientid ($ename, $patientid) {
        $cond = ' AND patientid=:patientid AND checkuptplid=:checkuptplid ORDER BY id DESC ';
        $bind = array(
            ':patientid' => $patientid,
            ':checkuptplid' => $this->id);
        $checkup = Dao::getEntityByCond('Checkup', $cond, $bind);

        if (! $checkup) {
            return null;
        }

        $xanswersheet = $checkup->xanswersheet;
        if (! $xanswersheet) {
            return null;
        }
        $xquestionsheet = $xanswersheet->xquestionsheet;
        $xquestion = $xquestionsheet->getQuestionByEname($ename);
        $answer = $xanswersheet->getAnswer($xquestion->id);

        return $answer;
    }

    public function getCheckupCnt () {
        $cond = " and checkuptplid = {$this->id} ";
        return CheckupDao::getCheckupCnt($cond);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // toArrayCtr
    public static function toArrayCtr ($checkuptpls, $ishasnone = 1) {
        if ($ishasnone) {
            $arr = array();
            $arr[0] = "不选择";
        }

        foreach ($checkuptpls as $a) {
            $arr[$a->id] = $a->title;
        }

        return $arr;
    }

    public static function getList4TktByDoctor ($doctor) {
        return CheckupTplDao::getListByDoctorAndDiseaseid_isInTkt_isInAdmin($doctor, null, 1);
    }
}
