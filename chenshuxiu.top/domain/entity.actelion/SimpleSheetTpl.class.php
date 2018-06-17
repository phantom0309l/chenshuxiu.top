<?php

/*
 * SimpleSheetTpl
 */
class SimpleSheetTpl extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'title'    //title
        ,'ename'    //ename
        ,'content'    //json配置
        ,'createauditorid'    //创建人
        ,'remark'    //
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            );
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["createauditor"] = array ("type" => "Auditor", "key" => "createauditorid" );

    }

    // $row = array(); 
    // $row["title"] = $title;
    // $row["ename"] = $ename;
    // $row["content"] = $content;
    // $row["createauditorid"] = $createauditorid;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "SimpleSheetTpl::createByBiz row cannot empty");

        $default = array();
        $default["title"] = '';
        $default["ename"] = '';
        $default["content"] = '';
        $default["createauditorid"] = 1;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    // 获取答卷数
    public function getSimpleSheetCnt () {
        $sql = "select count(*) from simplesheets where simplesheettplid = {$this->id} ";

        return Dao::queryValue($sql);
    }
}
