<?php

/*
 * 备选项
 */
class XOption extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'xquestionid',  // 问题id
            'content',  // 内容
            'score',  // score
            'checked',  // 是否默认选中
            'havesub',  // 是否有子模块
            'showenames',  // 显示的enames
            'hideenames',  // 隐藏的enames
            'status'); // 状态
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'xquestionid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["xquestion"] = array(
            "type" => "XQuestion",
            "key" => "xquestionid");
    }

    public static function getCheckedDescArray () {
        $arr = array();
        $arr[0] = '不选中';
        $arr[1] = '选中';

        return $arr;
    }

    public function getCheckedDesc () {
        $arr = self::getCheckedDescArray();
        return $arr[$this->checked];
    }

    public static function getHaveSubDescArray () {
        $arr = array();
        $arr[0] = '没子模块';
        $arr[1] = '有子模块';

        return $arr;
    }

    public function getHaveSubDesc () {
        $arr = self::getHaveSubDescArray();
        return $arr[$this->havesub];
    }

    public function getHavesubStr () {
        return $this->havesub ? 'havesub' : '';
    }

    public function getShowenamesFix () {
        return self::strFix($this->showenames);
    }

    public function getHideenamesFix () {
        return self::strFix($this->hideenames);
    }

    public static function strFix ($str) {
        $arr = explode(',', $str);
        $arrFix = array();
        foreach ($arr as $v) {
            if (false == empty($v)) {
                $v = trim($v);
                $arrFix[] = $v;
            }
        }

        return implode(',', $arrFix);
    }

    // 获取子问题ename array
    private $subEnameArray = null;
    // 获取子问题ename array
    public function getSubEnameArray () {
        if ($this->subEnameArray !== null) {
            return $this->subEnameArray;
        }

        $str = $this->showenames . "," . $this->hideenames;
        $arr = explode(',', $str);
        foreach ($arr as $i => $a) {
            $a = trim($a);
            $arr[$i] = $a;
            if (empty($a)) {
                unset($arr[$i]);
            }
        }

        $arr = array_unique($arr);

        $this->subEnameArray = $arr;

        return $arr;
    }

    // 获取显示用的 EnameArray
    private $showEnameArray = null;
    // 获取显示用的 EnameArray
    public function getShowEnameArray () {
        if ($this->showEnameArray !== null) {
            return $this->showEnameArray;
        }

        $str = $this->showenames;
        $arr = explode(',', $str);
        foreach ($arr as $i => $a) {
            $a = trim($a);
            $arr[$i] = $a;
            if (empty($a)) {
                unset($arr[$i]);
            }
        }

        $arr = array_unique($arr);

        $this->showEnameArray = $arr;

        return $arr;
    }

    // 复制一个备选项至新问题
    public function copyOne ($xquestionNew) {
        $row = array();
        $row['xquestionid'] = $xquestionNew->id;
        $row['content'] = $this->content;
        $row['score'] = $this->score;
        $row['checked'] = $this->checked;
        $row['havesub'] = $this->havesub;
        $row['showenames'] = $this->showenames;
        $row['hideenames'] = $this->hideenames;
        $row['status'] = $this->status;

        return self::createByBiz($row);
    }

    // $row = array();
    // $row["xquestionid"] = $xquestionid;
    // $row["content"] = $content;
    // $row["score"] = $score;
    // $row["checked"] = $checked;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "XOption::createByBiz row cannot empty");

        $default = array();
        $default["xquestionid"] = 0;
        $default["content"] = '';
        $default["score"] = 0;
        $default["checked"] = 0;
        $default["havesub"] = 0;
        $default["showenames"] = '';
        $default["hideenames"] = '';
        $default["status"] = 1;

        $row += $default;

        $row['showenames'] = FUtil::filterInvisibleChar($row['showenames']); 
        $row['hideenames'] = FUtil::filterInvisibleChar($row['hideenames']); 

        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    // 数组转换
    public static function toArray4HtmlCtr (array $xoptions) {
        $arr = array();
        foreach ($xoptions as $a) {
            $arr[$a->id] = $a->content;
        }

        return $arr;
    }

    // 数组转换,有显隐的属性
    public static function toFixArray4HtmlCtr (array $xoptions) {
        $arr = [];
        $class1 = "";

        if (false == $xoptions[0]->xquestion instanceof XQuestion) {
            return $arr;
        }

        if ($xoptions[0]->xquestion->isSelect()) {
            $class1 = "sheet-question-select-option";
        } else if ($xoptions[0]->xquestion->isSingleChoice()) {
            $class1 = "sheet-question-radio";
        } else if ($xoptions[0]->xquestion->isMultChoice()) {
            $class1 = "sheet-question-checkbox-option";
        }
        foreach ($xoptions as $a) {
            $arr[$a->id] = "class=\"{$class1} {$a->getHavesubStr()}\" data-showgroup=\"{$a->showenames}\" data-hidegroup=\"{$a->hideenames}\"";
        }

        return $arr;
    }

    public static function toFixArray4HtmlCtrCheckupTpl (array $xoptions) {
        $arr = array();
        foreach ($xoptions as $a) {
            $arr[$a->id] = " {$a->getHavesubStr()}\" data-showgroup=\"{$a->showenames}\" data-hidegroup=\"{$a->hideenames}";
        }

        return $arr;
    }

    // 答案列表 of 答卷
    public static function getArrayOfXquestion (XQuestion $xquestion) {
        $cond = " AND xquestionid=:xquestionid ";

        $bind = [];
        $bind[':xquestionid'] = $xquestion->id;

        return Dao::getEntityListByCond('XOption', $cond, $bind);
    }

    // 答案列表 of 答卷
    public static function getListByXquestionid ($xquestionid) {
        $cond = " AND xquestionid=:xquestionid ";

        $bind = [];
        $bind[':xquestionid'] = $xquestionid;

        return Dao::getEntityListByCond('XOption', $cond, $bind);
    }

    public static function getByXQuestionidAndContent ($xquestionid, $content) {
        $cond = " AND xquestionid=:xquestionid AND content=:content ";

        $bind = [];
        $bind[':xquestionid'] = $xquestionid;
        $bind[':content'] = $content;

        return Dao::getEntityByCond('XOption', $cond, $bind);
    }
}
