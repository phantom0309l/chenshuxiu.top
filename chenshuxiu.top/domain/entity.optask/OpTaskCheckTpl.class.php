<?php

/*
 * OpTaskCheckTpl
 */

class OpTaskCheckTpl extends Entity
{
    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'xquestionsheetid'    //xquestionsheetid
        , 'ename'    //ename
        , 'title'    //标题
        , 'content'    //内容
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array();
    }

    protected function init_belongtos() {
        $this->_belongtos = array();
        $this->_belongtos["xquestionsheet"] = array(
            "type" => "XQuestionSheet",
            "key" => "xquestionsheetid");
    }

    // $row = array();
    // $row["xquestionsheetid"] = $xquestionsheetid;
    // $row["title"] = $title;
    // $row["content"] = $content;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "OpTaskCheckTpl::createByBiz row cannot empty");

        $default = array();
        $default["xquestionsheetid"] = 0;
        $default["ename"] = '';
        $default["title"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getCheckHtml () {
        $xquestions = XQuestion::getArrayOfXQuestionSheet($this->xquestionsheet);

        var_dump($xquestions);

    }

    // 回调接口
    public function CheckupTplCallback (XQuestionSheet $sheet) {
        $this->set4lock('xquestionsheetid', $sheet->id);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // OpTaskCheckTpl 与 AuditorGroup  Ename 的映射关系
    public static function getOpTaskCheckTplAndAuditorGroupMap () {
        $config = [
            [
                'opTaskCheckTplEname'   =>  'ADHD_quality_checked',
                'AuditorGroupEname'     =>  'ADHD_auditor'
            ]
        ];

        return $config;
    }

    // 获取 Ename 对应的 limit
    public static function getLimitByEname ($ename) {
        $config = [
            'ADHD_auditor'  =>  4
        ];

        return $config[$ename];
    }

    public static function getOpTaskCheckTplEnameByAuditorGroupEname ($auditorGroupEname) {
        $config = self::getOpTaskCheckTplAndAuditorGroupMap();
        foreach ($config as $item) {
           if($item['AuditorGroupEname'] == $auditorGroupEname){
               return $item['opTaskCheckTplEname'];
           }
        }

        return 0;
    }
}