<?php
// ShopProductPictureMgrAction
class ShopProductPictureMgrAction extends AuditBaseAction
{

    public function doList () {
        $shopproductid = XRequest::getValue("shopproductid", 0);

        $shopProduct = ShopProduct::getById($shopproductid);
        $shopProductPictures = $shopProduct->getShopProductPictures();

        XContext::setValue("shopProduct", $shopProduct);
        XContext::setValue("shopProductPictures", $shopProductPictures);

        return self::SUCCESS;
    }

    public function doAddPost () {
        $shopproductid = XRequest::getValue("shopproductid", 0);
        $pictureid = XRequest::getValue("pictureid", 0);

        $shopProduct = ShopProduct::getById($shopproductid);

        $row = array();
        $row["shopproductid"] = $shopproductid;
        $row["pictureid"] = $pictureid;
        $row["pos"] = ShopProductPictureDao::getMaxPosByShopProduct($shopProduct) + 1;

        ShopProductPicture::createByBiz($row);

        XContext::setJumpPath("/ShopProductPictureMgr/list?shopproductid=" . $shopproductid);

        return self::SUCCESS;
    }

    public function doDeletePost () {
        $shopproductpictureid = XRequest::getValue("shopproductpictureid", 0);

        $shopProductPicture = ShopProductPicture::getById($shopproductpictureid);

        $shopProductPicture->remove();

        XContext::setJumpPath("/ShopProductPictureMgr/list?shopproductid=" . $shopProductPicture->shopproductid);

        return self::SUCCESS;
    }
}
