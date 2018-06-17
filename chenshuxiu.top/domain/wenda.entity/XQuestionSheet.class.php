<?php

/*
 * XQuestionSheet
 */
class XQuestionSheet extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'sn',  // 唯一码,英文编码
            'title',  // 标题,一般不需要前台显示,由obj自己显示问卷信息
            'objtype',  // 关联对象type,三元式
            'objid',  // 关联对象id,三元式
            'objcode',  // 关联对象子分类编码,三元式
            'ishidepos',  // 隐藏序号
            'status',  // 状态
            'auditorid'); // auditorid
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'objtype',
            'objid',
            'objcode',
            'auditorid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    public static function getIshideposDescArray () {
        $arr = array();
        $arr[0] = '显示';
        $arr[1] = '隐藏';

        return $arr;
    }

    public function isOfGantong () {
        $sn = $this->sn;
        if (substr($sn, 0, strpos($sn, '[')) == "gantong") {
            return true;
        } else {
            return false;
        }
    }

    public function getIshideposDesc () {
        $arr = self::getIshideposDescArray();
        return $arr[$this->ishidepos];
    }

    // 做该量表的答卷数
    public function getAnswerSheetCnt () {
        return XAnswerSheet::getCntOfXQuestionSheet($this);
    }

    // 条目列表
    public function getItemList () {
        return $this->getQuestions();
    }

    // 问题列表
    private $questions = null;
    // 问题列表
    public function getQuestions ($issimple = 0) {
        if ($this->questions === null) {
            $this->questions = XQuestion::getArrayOfXQuestionSheet($this);
        }

        if (0 == $issimple) {
            return $this->questions;
        }

        $questions = array();
        foreach ($this->questions as $a) {
            if ($a->issimple) {
                $questions[] = $a;
            }
        }
        return $questions;
    }

    // 问题数目
    public function getQuestionCnt () {
        return XQuestion::getCntOfXQuestionSheet($this);
    }

    // 最大问题序号
    public function getMaxQuestionPos () {
        return XQuestion::getMaxPosOfXQuestionSheet($this);
    }

    // 试卷第一题 简写
    public function getFirstQuestion () {
        return $this->getFirstXQuestion();
    }

    // 试卷第一题
    public function getFirstXQuestion () {
        return XQuestion::getFirstOneByXQuestionSheet($this);
    }

    // 获取第pos题
    public function getQuestionByPos ($pos) {
        return XQuestion::getByQuestionsheetidPos($this->id, $pos);
    }

    public function getQuestionByEname ($ename) {
        return XQuestion::getByEnameAndXQuestionSheetid($ename, $this->id);
    }

    // 隐藏的ename array
    private $subEnameArray = null;

    // 是否默认隐藏
    public function isDefaultHideEname ($ename) {
        $arr = $this->getSubEnameArray();
        $hasMinus = false;
        foreach ($arr as $one) {
            if (strstr($one, "---") !== false) {
                $hasMinus = true;
                break;
            }
        }
        if (!$hasMinus) {
            return in_array($ename, $arr);
        }

        //处理---范围问题
        $questionEnames = [];
        foreach ($this->getQuestions() as $question) {
           $questionEnames[] = $question->ename;
        }
        $ranges = [];
        foreach ($arr as $one) {
            if (strstr($one, "---") !== false) {
                list($start, $end) = explode("---", $one);
                $startIndex = array_search($start, $questionEnames);
                $endIndex = array_search($end, $questionEnames);
                //ename不存在就忽略这个配置,报警让运营去检查未生效的原因
                if ($startIndex === false || $endIndex === false) {
                    Debug::warn("量表二级联动范围配置的问题ename有错 {$one}");
                    continue;
                }
                $ranges[] = ["min"=>$startIndex, "max"=>$endIndex];
            }
        }
        $isHide = in_array($ename, $arr);
        if (!$isHide) {
            $idx = array_search($ename, $questionEnames);
            foreach ($ranges as $range) {
                //在连续范围内
                if ($idx >= $range['min'] && $idx <= $range['max']) {
                    $isHide = true;
                    break;
                }
            }
        }
        return $isHide;
    }

    // 获取默认隐藏的enames
    public function getSubEnameArray () {
        if ($this->subEnameArray !== null) {
            return $this->subEnameArray;
        }

        $arr = array();

        $questions = $this->getQuestions();
        foreach ($questions as $q) {
            $arr1 = $q->getSubEnameArray();
            $arr = array_merge($arr, $arr1);
        }

        $arr = array_unique($arr);

        $this->subEnameArray = $arr;

        return $arr;
    }

    // 获取父问题
    public function getParentQuestionByQuestion (XQuestion $question) {
        $questions = $this->getQuestions();
        foreach ($questions as $q) {
            foreach ($q->getSubQuestions() as $subq) {
                if ($subq->id == $question->id) {
                    return $q;
                }
            }
        }
        return null;
    }

    // 存在子问题
    public function hasHideSubQuestions () {
        $sql = "select sum(a.havesub) as havsub_cnt
                from xoptions a
                inner join xquestions b on b.id=xquestionid
                inner join xquestionsheets c on c.id=b.xquestionsheetid
                where c.id = :xquestionsheetid
                group by c.id";

        $bind = [];
        $bind[':xquestionsheetid'] = $this->id;

        $cnt = Dao::queryValue($sql, $bind);
        return $cnt > 0;
    }

    // 生成显隐配置的json串
    public function getShowHideOptionJsonStr () {
        $arr = array();
        foreach ($this->getQuestions() as $q) {
            if ($q->isChoice()) {
                foreach ($q->getOptions() as $opt) {
                    if ($opt->havesub) {
                        $arr[$opt->id] = array(
                            'showenames' => $opt->getShowenamesFix(),
                            'hideenames' => $opt->getHideenamesFix());
                    }
                }
            }
        }

        return json_encode($arr);
    }

    public function copyOne ($checkuptplNew) {
        $row = array();
        $row['sn'] = $this->sn;
        $row['title'] = $this->title;
        $row['objtype'] = $this->objtype;
        $row['objid'] = $checkuptplNew->id;
        $row['objcode'] = $this->objcode;
        $row['ishidepos'] = $this->ishidepos;
        $row['status'] = $this->status;
        $row['auditorid'] = $this->auditorid;

        $xquestionsheetNew = XQuestionSheet::createByBiz($row);
        // 修改检查报告模板问卷id
        $checkuptplNew->CheckupTplCallback($xquestionsheetNew);

        // 复制问题
        $questions = $this->getQuestions();
        foreach ($questions as $question) {
            $question->copyOne($xquestionsheetNew);
        }
    }

    // $row = array();
    // $row["sn"] = $sn;
    // $row["title"] = $title;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["objcode"] = $objcode;
    // $row["ishidepos"] = $ishidepos;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "XQuestionSheet::createByBiz row cannot empty");

        $default = array();
        $default["sn"] = '';
        $default["title"] = '';
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["objcode"] = ''; // 避免没填
        $default["ishidepos"] = 1;
        $default["status"] = 1;
        $default["auditorid"] = 0;

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    // 获取问卷 by 三元式 : objtype + objid + objcode
    public static function getBy3params ($objtype, $objid, $objcode = '') {
        $cond = " AND objtype=:objtype AND objid=:objid";
        $bind = [];
        $bind[':objtype'] = $objtype;
        $bind[':objid'] = $objid;
        if ($objcode) {
            $cond .= ' AND objcode=:objcode';
            $bind[':objcode'] = $objcode;
        }
        return Dao::getEntityByCond('XQuestionSheet', $cond, $bind);
    }

    // 获取问卷身上有多少个答卷
    public function getXAnswerSheetCnt () {
        $cond = " and xquestionsheetid = :xquestionsheetid ";
        $bind = [];
        $bind[':xquestionsheetid'] = $this->id;

        $xanswersheets = Dao::getEntityListByCond('XAnswerSheet', $cond, $bind);
        return count($xanswersheets);
    }

    // 获取问卷 by 关联对象 : obj + objcode
    public static function getByObj (EntityBase $obj, $objcode = '') {
        $objtype = get_class($obj);
        $objid = $obj->id;
        return self::getBy3params($objtype, $objid, $objcode);
    }

    // 获取问卷 by sn
    public static function getBySn ($sn) {
        $cond = "AND sn=:sn";

        $bind = array(
            ':sn' => $sn);

        return Dao::getEntityByCond('XQuestionSheet', $cond, $bind);
    }

    public static function isAnswered (User $user, XQuestionSheet $xquestionsheet) {
        $cond = " and userid=:userid and xquestionsheetid=:xquestionsheetid ";

        $bind = [];
        $bind[':userid'] = $user->id;
        $bind[':xquestionsheetid'] = $xquestionsheet->id;

        $tmp = Dao::getEntityByCond("XAnswerSheet", $cond, $bind);

        if ($tmp instanceof XAnswerSheet) {
            return true;
        } else {
            return false;
        }
    }

    // 患者答卷统计
    public static function getXQuestionSheetSumOfPatient ($patientid) {
        $sql = "select a.id as xquestionsheetid,a.title,count(*) as cnt
from xquestionsheets a
inner join xanswersheets b on b.xquestionsheetid=a.id
where b.patientid=:patientid and (a.title not like '%要点巩固%' and a.title not like '%天%' and a.title not like '%作业%')
group by a.id
order by title
        ";

        $bind = array(
            ":patientid" => $patientid);

        return Dao::queryRows($sql, $bind);
    }
}
