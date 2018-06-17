<?php
// ShopAddressMgrAction
class ShopAddressMgrAction extends AuditBaseAction
{
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);

        $cond = " order by id desc ";
        $shopAddresss = Dao::getEntityListByCond4Page("ShopAddress", $pagesize, $pagenum, $cond);

        $countSql = "select count(id) as cnt from shopaddresss where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/ShopAddressmgr/list";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue('shopAddresss', $shopAddresss);
        XContext::setValue("pagelink", $pagelink);
        return self::SUCCESS;
    }

    public function doListForPatient () {
        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);

        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);

        DBC::requireNotEmpty($patient, "患者不能为空");

        $cond = " and patientid = :patientid order by id desc ";
        $bind = [
            ':patientid' => $patientid
        ];
        $shopAddresss = Dao::getEntityListByCond4Page("ShopAddress", $pagesize, $pagenum, $cond, $bind);

        $countSql = "select count(id) as cnt from shopaddresss where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/ShopAddressmgr/listforpatient?patientid={$patientid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue('shopAddresss', $shopAddresss);
        XContext::setValue('patient', $patient);
        XContext::setValue("pagelink", $pagelink);
        return self::SUCCESS;
    }

    public function doAdd () {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);

        DBC::requireNotEmpty($patient, "患者不能为空");

        XContext::setValue('patient', $patient);

        return self::SUCCESS;

    }

    public function doAddPost () {
        $patientid = XRequest::getValue('patientid', 0);
        $patient = Patient::getById($patientid);

        DBC::requireNotEmpty($patient, "患者不能为空");

        $shopaddress_place = XRequest::getValue('shopaddress', []);
        $shopaddress_place = PatientAddressService::fixNull($shopaddress_place);
        $linkman_name = XRequest::getValue('linkman_name', '');
        $linkman_mobile = XRequest::getValue('linkman_mobile', '');
        $content = XRequest::getValue('content', '');
        $postcode = XRequest::getValue('postcode', '');

        $row = [];
        $row["patientid"] = $patientid;
        $row["linkman_name"] = $linkman_name;
        $row["linkman_mobile"] = $linkman_mobile;
        $row["xprovinceid"] = $shopaddress_place['xprovinceid'];
        $row["xcityid"] = $shopaddress_place['xcityid'];
        $row["xcountyid"] = $shopaddress_place['xcountyid'];
        $row["content"] = $content;
        $row["postcode"] = $postcode;
        $shopaddress = ShopAddress::createByBiz($row);

        XContext::setJumpPath('/shopaddressmgr/listforpatient?patientid=' . $patientid);
    }

    public function doModify () {
        $shopaddressid = XRequest::getValue('shopaddressid', 0);
        $shopaddress = ShopAddress::getById($shopaddressid);

        DBC::requireNotEmpty($shopaddress, "shopaddress is null");

        XContext::setValue('shopaddress', $shopaddress);

        return self::SUCCESS;

    }

    public function doModifyPost () {
        $shopaddressid = XRequest::getValue('shopaddressid', 0);
        $shopaddress = ShopAddress::getById($shopaddressid);

        DBC::requireNotEmpty($shopaddress, "shopaddress is null");

        $shopaddress_place = XRequest::getValue('shopaddress', []);
        $shopaddress_place = PatientAddressService::fixNull($shopaddress_place);
        $linkman_name = XRequest::getValue('linkman_name', '');
        $linkman_mobile = XRequest::getValue('linkman_mobile', '');
        $content = XRequest::getValue('content', '');
        $postcode = XRequest::getValue('postcode', '');

        $shopaddress->linkman_name = $linkman_name;
        $shopaddress->linkman_name = $linkman_name;
        $shopaddress->linkman_mobile = $linkman_mobile;
        $shopaddress->xprovinceid = $shopaddress_place['xprovinceid'];
        $shopaddress->xcityid = $shopaddress_place['xcityid'];
        $shopaddress->xcountyid = $shopaddress_place['xcountyid'];
        $shopaddress->content = $content;
        $shopaddress->postcode = $postcode;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/shopaddressmgr/modify?shopaddressid=" . $shopaddressid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doDeleteJson () {
        $shopaddressid = XRequest::getValue('shopaddressid', 0);
        $shopaddress = ShopAddress::getById($shopaddressid);

        DBC::requireNotEmpty($shopaddress, "shopaddress is null");

        $sql = "select count(*) from shoporders where shopaddressid = :shopaddressid ";
        $bind = [
            ':shopaddressid' => $shopaddressid
        ];
        $cnt = Dao::queryValue($sql, $bind);

        if ($cnt > 0) {
            echo "地址已经被使用，不能删除";
        } else {
            $shopaddress->remove();
            echo "success";
        }

        return self::BLANK;
    }
}
