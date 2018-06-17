<?php
/*
 * Rpt_date_dbDao
 */
class Assistant_PatientTagTpl_RefDao extends Dao
{
    public function getListByAssitantId($assistantid) {
        $cond = " AND assistantid=:assistantid";
        $bind = [
            ':assistantid' => $assistantid,
        ];
        $apRefs = Dao::getEntityListByCond('Assistant_PatientTagTpl_Ref', $cond, $bind);

        return $apRefs;
    }

}
