<?php
// Rpt_date_dbMgrAction
class Rpt_date_dbMgrAction extends AuditBaseAction
{

    public function doList () {
        $rpt_date_dbs = Dao::getEntityListByCond('Rpt_date_db', 'order by id desc', [], 'statdb');

        XContext::setValue('rpt_date_dbs', $rpt_date_dbs);
        return self::SUCCESS;
    }
}
