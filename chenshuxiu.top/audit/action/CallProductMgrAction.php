<?php
// CallProductMgrAction
class CallProductMgrAction extends AuditBaseAction
{

    public function doList () {
        $status = XRequest::getValue('status', 1);

        $cond = "";
        $bind = [];

        $cond .= " and status=:status ";
        $bind[":status"] = $status;

        $cond .= ' order by pos, title_pinyin ';

        $callProducts = Dao::getEntityListByCond('CallProduct', $cond, $bind);

        XContext::setValue('callProducts', $callProducts);

        return self::SUCCESS;
    }

    public function doOne () {
        $callproductid = XRequest::getValue('callproductid', 0);

        $callProduct = CallProduct::getById($callproductid);

        $callProduct->resetTitle_pinyin();

        XContext::setValue('callProduct', $callProduct);

        return self::SUCCESS;
    }

    public function doAdd () {
        $objtype = XRequest::getValue('objtype', 'Doctor');
        $objid = XRequest::getValue('objid', 0);

        $obj = Dao::getEntityById($objtype, $objid);

        if ($obj instanceof Entity) {
            $callProduct = CallProductDao::getCallProductByObjtypeObjid($objtype, $objid);
            if ($callProduct instanceof CallProduct) {
                XContext::setJumpPath("/CallProductMgr/one?callproductid={$callProduct->id}");
                return self::SUCCESS;
            }
        }

        XContext::setValue('objtype', $objtype);
        XContext::setValue('objid', $objid);
        XContext::setValue('obj', $obj);

        return self::SUCCESS;
    }

    public function doAddPost () {
        $objtype = XRequest::getValue('objtype', '');
        $objid = XRequest::getValue('objid', 0);
        $title = XRequest::getValue('title', '');
        $content = XRequest::getValue('content', '');
        $price_yuan = XRequest::getValue('price_yuan', 0);
        $market_price_yuan = XRequest::getValue('market_price_yuan', 0);
        $pack_unit = XRequest::getValue('pack_unit', '');

        $row = array();
        $row["objtype"] = $objtype;
        $row["objid"] = $objid;
        $row["title"] = $title;
        $row["content"] = $content;
        $row["price"] = $price_yuan * 100;
        $row["market_price"] = $market_price_yuan * 100;
        $row["pack_unit"] = $pack_unit;
        $row["pos"] = Dao::queryValue("select max(pos) from callproducts") + 1;
        $callProduct = CallProduct::createByBiz($row);

        XContext::setJumpPath("/CallProductMgr/one?callproductid={$callProduct->id}");
        return self::SUCCESS;
    }

    public function doModify () {
        $callproductid = XRequest::getValue('callproductid', 0);

        $callProduct = CallProduct::getById($callproductid);

        XContext::setValue('callProduct', $callProduct);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $callproductid = XRequest::getValue('callproductid', 0);

        $title = XRequest::getValue('title', '');
        $content = XRequest::getUnSafeValue('content', '');
        $price_yuan = XRequest::getValue('price_yuan', 0);
        $market_price_yuan = XRequest::getValue('market_price_yuan', 0);
        $pack_unit = XRequest::getValue('pack_unit', '');
        $status = XRequest::getValue('status', 0);
        $service_percent = XRequest::getValue('service_percent', 0);

        $callProduct = CallProduct::getById($callproductid);
        $callProduct->title = $title;
        $callProduct->content = $content;
        $callProduct->price = $price_yuan * 100;
        $callProduct->market_price = $market_price_yuan * 100;
        $callProduct->pack_unit = $pack_unit;
        $callProduct->service_percent = $service_percent;
        $callProduct->status = $status;

        $callProduct->resetTitle_pinyin();

        XContext::setJumpPath("/CallProductMgr/one?callproductid={$callProduct->id}");
        return self::SUCCESS;
    }

    public function doPosModifyPost () {
        $posArray = XRequest::getValue('pos', array());

        foreach ($posArray as $callproductid => $pos) {
            $entity = CallProduct::getById($callproductid);
            $entity->pos = $pos;
        }

        XContext::setJumpPath("/CallProductMgr/list");
        return self::SUCCESS;
    }
}
