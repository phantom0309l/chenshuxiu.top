<?php
// ShopProductTypeMgrAction
class ShopProductTypeMgrAction extends AuditBaseAction
{

    public function doList () {
        $shopProductTypes = Dao::getEntityListByCond('ShopProductType', ' order by pos ');

        XContext::setValue('shopProductTypes', $shopProductTypes);
        return self::SUCCESS;
    }

    public function doAddPost () {
        $name = XRequest::getValue('name', '');
        $diseasegroupid = XRequest::getValue('diseasegroupid', '');

        $row = array();
        $row["id"] = Dao::queryValue("select max(id) from shopproducttypes") + 1;
        $row["pos"] = Dao::queryValue("select max(pos) from shopproducttypes") + 1;
        $row["diseasegroupid"] = $diseasegroupid;
        $row["name"] = $name;
        ShopProductType::createByBiz($row);

        XContext::setJumpPath("/ShopProductTypeMgr/list");
        return self::SUCCESS;
    }

    public function doModify () {
        $shopproducttypeid = XRequest::getValue('shopproducttypeid', 0);
        $shopProductType = ShopProductType::getById($shopproducttypeid);

        XContext::setValue('shopProductType', $shopProductType);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $shopproducttypeid = XRequest::getValue('shopproducttypeid', 0);
        $diseasegroupid = XRequest::getValue('diseasegroupid', 0);
        $name = XRequest::getValue('name', '');
        $pos = XRequest::getValue('pos', 0);

        $shopProductType = ShopProductType::getById($shopproducttypeid);
        $shopProductType->name = $name;
        $shopProductType->pos = $pos;
        $shopProductType->diseasegroupid = $diseasegroupid;

        XContext::setJumpPath("/ShopProductTypeMgr/list");
        return self::SUCCESS;
    }
}
