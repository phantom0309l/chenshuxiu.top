<?php
// FitPageTplItemDao

// owner by fhw
// create by fhw
// review by sjp 20160628

class FitPageTplItemDao extends Dao
{
    // 名称: getListByFitPageTpl
    // 备注:
    // 创建:
    // 修改:
    public static function getListByFitPageTpl (FitPageTpl $fitpagetpl) {
        $cond = " and fitpagetplid = :fitpagetplid order by pos , id ";
        $bind = [];
        $bind[':fitpagetplid'] = $fitpagetpl->id;

        return Dao::getEntityListByCond('FitPageTplItem', $cond, $bind);
    }
}