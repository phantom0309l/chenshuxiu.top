<?php

/**
 * TableNameCreator
 * @desc		表名生成器,根据需要可以继承
 * @remark		依赖类: 无
 * @copyright	(c)2012 xwork.
 * @file		TableNameCreator.class.php
 * @author		shijianping <shijpcn@qq.com>
 * @date		2012-02-26
 */
class TableNameCreator
{

    public function getTableName ($entityClassName, $tableno = 0, $database = '') {

        $pre = '';
        // // 2017016 by sjp : 修改了框架,不用拼这个数据库名了
        // if ($database && $database != '_defaultdb_' && $database !=
        // DaoBase::get_defaultdb_name()) {
        // $pre = $database . '.';
        // }

        $fix = '';
        if ($tableno > 0) {
            $fix = $tableno;
        }

        return $pre . strtolower($entityClassName) . "s$fix";
    }
}