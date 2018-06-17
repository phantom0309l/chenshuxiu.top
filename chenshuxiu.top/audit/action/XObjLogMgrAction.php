<?php
// XObjLogMgrAction
class XObjLogMgrAction extends AuditBaseAction
{

    // 实体监控日志列表
    public function doList () {
        $xunitofworkid = XRequest::getValue('xunitofworkid', 0);

        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);

        XContext::setValue('xunitofworkid', $xunitofworkid);
        XContext::setValue('objtype', $objtype);
        XContext::setValue('objid', $objid);

        // 不同维度
        if ($xunitofworkid > 0) {
            //1526470813871382425
            DBC::requireTrue(strlen($xunitofworkid) == 19, "xunitofworkid:$xunitofworkid 非法");
            $tableno = XUnitOfWork::getTablenoByXunitofworkid($xunitofworkid);
            XContext::setValue('tableno', $tableno);

            $dbconf = [];
            $dbconf['database'] = 'xworkdb';
            $dbconf['tableno'] = $tableno;

            $xunitofwork = XUnitOfWork::getById($xunitofworkid, $dbconf);
            XContext::setValue('xunitofwork', $xunitofwork);

            $objtypeobjids = XObjLogDao::getObjtypeObjidArrayByXunitofworkid($xunitofworkid);
            XContext::setValue('objtypeobjids', $objtypeobjids);

            $xobjlogs = XObjLogDao::getListByXunitofworkid($xunitofworkid);
        } else {
            $xobjlogs = XObjLogDao::getListByObjtypeObjid($objtype, $objid);

            $entity = Dao::getEntityById($objtype, $objid);
            XContext::setValue('entity', $entity);
        }

        XContext::setValue('xobjlogs', $xobjlogs);

        return self::SUCCESS;
    }

    // 还原实体
    public function doReCreateEntityPost () {
        $objtype = XRequest::getValue("objtype", "all");
        $objid = XRequest::getValue('objid', 0);

        $preMsg = "实体已还原";

        $xobjlogs = XObjLogDao::getListByObjtypeObjid($objtype, $objid);

        // 最小版本
        $xobjlog1 = array_shift($xobjlogs);
        // 最大版本
        $xobjlog2 = $xobjlog1;
        if (false == empty($xobjlogs)) {
            $xobjlog2 = array_pop($xobjlogs);
        }

        $row = XObjLog::getSnapByObj($objtype, $objid, 10000);
        $row['id'] = $objid;
        $row['createtime'] = $xobjlog1->createtime;
        $row['updatetime'] = $xobjlog2->createtime;

        $objtype::createByBiz($row);

        $unitofwork = BeanFinder::get("UnitOfWork");
        $unitofwork->commitAndInit();
        $unitofwork = BeanFinder::get("UnitOfWork");

        $table = strtolower($objtype) . "s";

        $sql = "update {$table} set version = :version where id = :id ";
        $bind = [];
        $bind[':version'] = $xobjlog2->objver + 1;
        $bind[':id'] = $xobjlog2->objid;

        Dao::executeNoQuery($sql, $bind, 'fcqxdb');

        $lastXobjlog = XObjLogDao::getLastOneByObjtypeObjid($objtype, $objid);

        $sql = "update xobjlogs{$lastXobjlog->randno} set objver = :objver, type=4 where id = :id ";
        $bind = [];
        $bind[':objver'] = $xobjlog2->objver + 1;
        $bind[':id'] = $lastXobjlog->id;

        Dao::executeNoQuery($sql, $bind, 'xworkdb');

        $sql = "update xobjlogs{$lastXobjlog->randno_fix} set objver = :objver, type=4 where id = :id ";
        $bind = [];
        $bind[':objver'] = $xobjlog2->objver + 1;
        $bind[':id'] = $lastXobjlog->id;

        Dao::executeNoQuery($sql, $bind, 'xworkdb');

        XContext::setJumpPath("/xobjlogmgr/list?objtype={$objtype}&objid={$objid}&preMsg=" . urlencode($preMsg));

        return self::BLANK;
    }
}
