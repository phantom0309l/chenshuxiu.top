<?php

/*
 * ReportTpl
 */

class ReportTpl extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'title'    //标题
        , 'brief'    //摘要
        , 'content'    //模板内容
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array();
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

    }

    // $row = array(); 
    // $row["title"] = $title;
    // $row["brief"] = $brief;
    // $row["content"] = $content;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "ReportTpl::createByBiz row cannot empty");

        $default = array();
        $default["title"] = '';
        $default["brief"] = '';
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

}
