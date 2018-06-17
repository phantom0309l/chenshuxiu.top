<?php
// BedTktLogMgrAction
class BedTktLogMgrAction extends AuditBaseAction
{
    public function doList4BedtktHtml() {
        $bedtktid = XRequest::getValue('bedtktid', '');
        DBC::requireNotEmpty($bedtktid, 'bedtktid is null');

        $bedtkt = Dao::getEntityById('BedTkt', $bedtktid);
        DBC::requireNotEmpty($bedtkt, 'bedtkt is null');

        $cond = " AND bedtktid = :bedtktid AND type != 'status_change' ORDER BY id DESC";;
        $bind = [];
        $bind[':bedtktid'] = $bedtktid;
        $bedtktlogs = Dao::getEntityListByCond("BedTktLog", $cond, $bind);

        XContext::setValue('bedtkt', $bedtkt);
        XContext::setValue('bedtktlogs', $bedtktlogs);
        return self::SUCCESS;
    }

}
