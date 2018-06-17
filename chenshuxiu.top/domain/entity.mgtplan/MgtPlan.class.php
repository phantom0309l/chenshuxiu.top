<?php
/*
 * MgtPlan
 */
class MgtPlan extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    //加入管理计划
    const type_join = 'join';

    public static function getKeysDefine()
    {
        return  array(
        'ename'    //英文名称
        ,'title'    //标题
        ,'brief'    //摘要
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array();
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["ename"] = $ename;
    // $row["title"] = $title;
    // $row["brief"] = $brief;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"MgtPlan::createByBiz row cannot empty");

        $default = array();
        $default["ename"] = '';
        $default["title"] = '';
        $default["brief"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getAllId () {
        $sql = "select id from mgtplans ";

        $ids = Dao::queryValues($sql);
        $ids[] = 0;

        return $ids;
    }

}
