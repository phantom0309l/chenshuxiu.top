<?php

/*
 * ClinicalTest
 */

class ClinicalTest extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'auditorid'    //创建者
        , 'title'    //标题
        , 'list_title'    //列表标题
        , 'brief'    //简介
        , 'content'    //内容
        , 'status'    //状态
        , 'remark'    //备注
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array();
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

    }

    // $row = array(); 
    // $row["auditorid"] = $auditorid;
    // $row["title"] = $title;
    // $row["list_title"] = $list_title;
    // $row["brief"] = $brief;
    // $row["content"] = $content;
    // $row["status"] = $status;
    // $row["remark"] = $remark;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "ClinicalTest::createByBiz row cannot empty");

        $default = array();
        $default["auditorid"] = 0;
        $default["title"] = '';
        $default["list_title"] = '';
        $default["brief"] = '';
        $default["content"] = '';
        $default["status"] = 0;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getStatusStr() {
        return $this->status ? '有效' : '无效';
    }

}
