<?php

/*
 * OpNode
 */
class OpNode extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'optasktplid',  // optasktplid
            'code',  // 唯一code
            'is_hang_up',  // 是否有挂起功能，1：能挂起，0：不能挂起
            'is_show_next_plantime',  // 是否显示下一次计划日期
            'title',  // 节点名
            'content' // 节点说明
);
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'optasktplid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["optasktpl"] = array(
            "type" => "OpTaskTpl",
            "key" => "optasktplid");
    }

    // $row = array();
    // $row["optasktplid"] = $optasktplid;
    // $row["code"] = $code;
    // $row["is_hang_up"] = $is_hang_up;
    // $row["is_show_next_plantime"] = $is_show_next_plantime;
    // $row["title"] = $title;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "OpNode::createByBiz row cannot empty");

        $default = array();
        $default["optasktplid"] = 0;
        $default["code"] = '';
        $default["is_hang_up"] = 1;
        $default["is_show_next_plantime"] = 0;
        $default["title"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getOpNodeFlowCnt () {
        $opnodeflows = OpNodeFlowDao::getListByOpNode($this);

        return count($opnodeflows);
    }
}
