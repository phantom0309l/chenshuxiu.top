<?php

// ServiceProductMgrAction
class ServiceProductMgrAction extends AuditBaseAction
{

    public function doList() {
        $pagenum = XRequest::getValue('pagenum', 1);
        $pagesize = 50;

        $serviceproducts = ServiceProductDao::getList($pagesize, $pagenum);

        //获得分页
        $cnt = Dao::queryValue("SELECT count(*) FROM serviceproducts");
        $url = "/serviceproductmgr/list";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue('serviceproducts', $serviceproducts);
        return self::SUCCESS;
    }

    public function doAdd() {
        return self::SUCCESS;
    }

    public function doAddJson() {
        $pictureid = XRequest::getValue('pictureid', 0);
        $type = XRequest::getValue('type');
        $title = XRequest::getValue('title');
        $short_title = XRequest::getValue('short_title');
        $price = XRequest::getValue('price');
        $item_cnt = XRequest::getValue('item_cnt');
        $content = XRequest::getValue('content');
        $status = XRequest::getValue('status');

        if (empty($type)) {
            $this->returnError('请选择类型');
        } elseif (empty($title)) {
            $this->returnError('请填写标题');
        } elseif (empty($short_title)) {
            $this->returnError('请填写短标题');
        } elseif (empty($price)) {
            $this->returnError('请填写价格');
        } elseif (empty($item_cnt)) {
            $this->returnError('请填写服务项数量');
        }

        $row = array();
        $row["pictureid"] = $pictureid;
        $row["type"] = $type;
        $row["title"] = $title;
        $row["short_title"] = $short_title;
        $row["content"] = $content;
        $row["price"] = $price * 100;
        $row["item_cnt"] = $item_cnt;
        $row["status"] = $status;
        $serviceproduct = ServiceProduct::createByBiz($row);

        return self::TEXTJSON;
    }

    public function doModify() {
        $serviceproductid = XRequest::getValue('serviceproductid', 0);
        $serviceproduct = ServiceProduct::getById($serviceproductid);
        if (false == $serviceproduct instanceof ServiceProduct) {
            $this->returnError('商品不存在');
        }

        XContext::setValue('serviceproduct', $serviceproduct);
        return self::SUCCESS;
    }

    public function doModifyJson() {
        $serviceproductid = XRequest::getValue('serviceproductid', 0);
        $serviceproduct = ServiceProduct::getById($serviceproductid);
        if (false == $serviceproduct instanceof ServiceProduct) {
            $this->returnError('商品不存在');
        }

        $pictureid = XRequest::getValue('pictureid', 0);
        $type = XRequest::getValue('type');
        $title = XRequest::getValue('title');
        $short_title = XRequest::getValue('short_title');
        $price = XRequest::getValue('price');
        $item_cnt = XRequest::getValue('item_cnt');
        $content = XRequest::getValue('content');
        $status = XRequest::getValue('status', 0);

        if (empty($type)) {
            $this->returnError('请选择类型');
        } elseif (empty($title)) {
            $this->returnError('请填写标题');
        } elseif (empty($short_title)) {
            $this->returnError('请填写短标题');
        } elseif (empty($price)) {
            $this->returnError('请填写价格');
        } elseif (empty($item_cnt)) {
            $this->returnError('请填写服务项数量');
        }

        $serviceproduct->pictureid = $pictureid;
        $serviceproduct->type = $type;
        $serviceproduct->title = $title;
        $serviceproduct->short_title = $short_title;
        $serviceproduct->price = $price * 100;
        $serviceproduct->item_cnt = $item_cnt;
        $serviceproduct->content = $content;
        $serviceproduct->status = $status;

        return self::TEXTJSON;
    }

    public function doDeleteJson() {
        $serviceproductid = XRequest::getValue('serviceproductid', 0);
        $serviceproduct = ServiceProduct::getById($serviceproductid);
        if (false == $serviceproduct instanceof ServiceProduct) {
            $this->returnError('商品不存在');
        }

        $serviceproduct->remove();
        return self::TEXTJSON;
    }
}
