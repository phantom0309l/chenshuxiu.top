<?php
// CheckupTpl
// 检查报告模板-关联一个问卷

// owner by xuzhe
// create by xuzhe
// review by sjp 20160701

class CheckupTplMenu extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'diseaseid',  // 疾病
            'doctorid',  // 医生
            'content' ,  // 内容 json
            'simple_content'  //
        );

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
    }

    // $row = array();
    // $row["diseaseid"] = $content;
    // $row["doctorid"] = $content;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "CheckupTpl::createByBiz row cannot empty");

        $default = array();
        $default["diseaseid"] = 0;
        $default["doctorid"] = 0;
        $default["content"] = '';
        $default["simple_content"] = '';

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    public function getContentArray () {
        return json_decode($this->content, true);
    }
}
