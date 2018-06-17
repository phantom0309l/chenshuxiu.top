<?php

/*
 * PaperTpl
 */
class PaperTpl extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'xquestionsheetid',  // 问卷id
            'groupstr',  // 分组英文名
            'ename',  // 量表英文名称
            'title',  // 标题
            'brief',  // 摘要
            'content',  // 内容
            'status'  // 状态
            );

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["xquestionsheet"] = array(
            "type" => "XQuestionSheet",
            "key" => "xquestionsheetid");
    }

    // $row = array();
    // $row["xquestionsheetid"] = $xquestionsheetid;
    // $row["ename"] = $ename;
    // $row["title"] = $title;
    // $row["brief"] = $brief;
    // $row["content"] = $content;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PaperTpl::createByBiz row cannot empty");

        $default = array();
        $default["xquestionsheetid"] = 0;
        $default["groupstr"] = '';
        $default["ename"] = '';
        $default["title"] = '';
        $default["brief"] = '';
        $default["content"] = '';
        $default["status"] = 1;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // getDiseasePaperTplRefs
    public function getDiseasePaperTplRefs () {
        return DiseasePaperTplRefDao::getListByPaperTpl($this);
    }

    // PaperTplCallback
    public function PaperTplCallback (XQuestionSheet $sheet) {
        $this->xquestionsheetid = $sheet->id;
    }

    public function getPaperCnt () {
        return PaperDao::getCntByPaperTpl($this);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
