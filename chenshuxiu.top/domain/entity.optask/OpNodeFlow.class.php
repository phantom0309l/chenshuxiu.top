<?php

/*
 * OpNodeFlow
 */
class OpNodeFlow extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'from_opnodeid',  // 起点节点
            'to_opnodeid',  // 终点节点
            'type',  // 类型 timeout:挂起超时 manual:手动
                'content'); // 节点流转说明
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'from_opnodeid',
            'to_opnodeid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["from_opnode"] = array(
            "type" => "OpNode",
            "key" => "from_opnodeid");
        $this->_belongtos["to_opnode"] = array(
            "type" => "OpNode",
            "key" => "to_opnodeid");
    }

    // $row = array();
    // $row["from_opnodeid"] = $from_opnodeid;
    // $row["to_opnodeid"] = $to_opnodeid;
    // $row["type"] = $type;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "OpNodeFlow::createByBiz row cannot empty");

        $default = array();
        $default["from_opnodeid"] = 0;
        $default["to_opnodeid"] = 0;
        $default["type"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    public static function getTypeArr () {
        $arr = [
            'timeout' => '挂起超时',
            // 'auto' => '自动',
            'manual' => '手动'];

        return $arr;
    }

    public static function getOneTypeArr () {
        $arr = [
            // 'auto' => '自动',
            'manual' => '手动'];

        return $arr;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getTypeStr () {
        $arr = self::getTypeArr();

        return $arr[$this->type];
    }
}
