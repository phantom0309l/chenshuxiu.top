<?php

/*
 * Tmp_fengyun
 */
class Tmp_fengyun extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'content',  // 提交的内容
        ); //
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, __METHOD__ . " row cannot empty");

        $default = array();
        $default["content"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================
}
