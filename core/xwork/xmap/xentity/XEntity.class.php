<?php

/**
 * XEntity
 * @desc		阉割版的Entity,可以没有version,createtime,updatetime
 * @remark		依赖类: EntityBase;
 * @copyright	(c)2012 xwork.
 * @file		XEntity.class.php
 * @author		shijianping <shijpcn@qq.com>
 * @date		2012-02-26
 */
class XEntity extends EntityBase
{
    // 生成insert命令
    public function getInsertCommand () {
        return XMapper::getInsertCommand($this);
    }

    // 生成update命令
    public function getUpdateCommand () {
        return XMapper::getUpdateCommand($this);
    }

    // 取得删除实体的sql语句，包括删除的修正sql语句
    public function getDeleteCommand () {
        return XMapper::getDeleteCommand($this);
    }
}