<?php
/*
 * LetterDao
 */
class LetterDao extends Dao
{
    // 名称: getOneByObj
    // 备注: 根据实体对象（WxMsgTxt或PatientNote） 查询 letters 表中的记录，并返回其实体对象
    // 创建:
    // 修改:
    public static function getOneByObj(Entity $obj) {
        $bind = [];
        $cond = " and objtype=:objtype and objid=:objid";
        $bind[':objtype'] = get_class($obj);
        $bind[':objid'] = $obj->id;

        return Dao::getEntityByCond("Letter", $cond, $bind);
    }

}