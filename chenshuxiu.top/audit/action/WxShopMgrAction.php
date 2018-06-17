<?php

class WxShopMgrAction extends AuditBaseAction
{

    // 列表
    public function doList () {
        $wxshops = Dao::getEntityListByCond("WxShop");

        XContext::setValue("wxshops", $wxshops);
        return self::SUCCESS;
    }

    //
    public function doWxConditional () {
        $wxshopid = XRequest::getValue('wxshopid', 0);
        $showurlencode = XRequest::getValue('showurlencode', 0);
        $wxshop = WxShop::getById($wxshopid);
        $wxmenu = new WxMenu();
        $menusData = WxMenu::getConditionalMenus($wxshopid, true);

        $result = array();
        foreach ($menusData as $i => $menuData) {
            if (! $showurlencode) {
                $menuData = $this->redirectUriDecodeOrEncode($menuData, true);
            }
            $groupid = $menuData["matchrule"]["group_id"] ?? '0';
            $wxgroup = WxGroupDao::getOneByWxshopidGroupid($wxshopid, $groupid);
            $name = $wxgroup instanceof WxGroup ? $wxgroup->name : "";
            $temp = array();
            $temp["num"] = $i;
            $temp["value"] = json_encode($menuData, JSON_UNESCAPED_UNICODE);
            $temp["groupid"] = $groupid;
            $temp["name"] = $name;
            $result[] = $temp;
        }

        XContext::setValue("wxshop", $wxshop);
        XContext::setValue("result", $result);
        XContext::setValue("showurlencode", $showurlencode);
        return self::SUCCESS;
    }

    //
    public function doWxConditionalCreateJson () {
        $result = array();
        $wxshopid = XRequest::getValue('wxshopid', 0);
        $showurlencode = XRequest::getValue('showurlencode', 0);
        $menuArrStr = XRequest::getUnSafeValue('menuArrStr', '');

        $menuArr = json_decode($menuArrStr, true);

        if (! $showurlencode) {
            $menuArr = $this->redirectUriDecodeOrEncode($menuArr, false);
        }

        $menuArrStr = json_encode($menuArr, JSON_UNESCAPED_UNICODE);
        $wxmenu = new WxMenu();
        $result = $wxmenu->createConditional($wxshopid, $menuArrStr);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    // 获取菜单
    public function doWxMenu () {
        $wxshopid = XRequest::getValue('wxshopid', 0);
        $showurlencode = XRequest::getValue('showurlencode', 0);
        $wxshop = WxShop::getById($wxshopid);
        $wxmenu = new WxMenu();
        $menuData = $wxmenu->getMenu($wxshopid);
        if (! $showurlencode) {
            $menuData = $this->redirectUriDecodeOrEncode($menuData, true);
        }
        $menuData = json_encode($menuData, JSON_UNESCAPED_UNICODE);
        XContext::setValue("wxshop", $wxshop);
        XContext::setValue("menuData", $menuData);
        XContext::setValue("showurlencode", $showurlencode);
        return self::SUCCESS;
    }

    // 微信创建菜单提交
    public function doWxMenuCreateJson () {
        $result = array();
        $wxshopid = XRequest::getValue('wxshopid', 0);
        $showurlencode = XRequest::getValue('showurlencode', 0);
        $menuArrStr = XRequest::getUnSafeValue('menuArrStr', '');

        $menuArr = json_decode($menuArrStr, true);

        if (! $showurlencode) {
            $menuArr = $this->redirectUriDecodeOrEncode($menuArr, false);
        }

        $menuArrStr = json_encode($menuArr, JSON_UNESCAPED_UNICODE);
        $wxmenu = new WxMenu();
        $result = $wxmenu->createMenu($wxshopid, $menuArrStr);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        return self::BLANK;
    }

    private function redirectUriDecodeOrEncode ($menuData, $isdecode = true) {
        $buttons = $menuData["button"];
        foreach ($buttons as $k1 => $button) {
            $sub_buttons = $button["sub_button"];
            if (count($sub_buttons) > 0) {
                // 是二级菜单
                foreach ($sub_buttons as $k2 => $sub_button) {
                    $menuData["button"][$k1]["sub_button"][$k2] = $this->setButtonUrl($sub_button, $isdecode);
                }
            } else {
                // 是一级菜单
                $menuData["button"][$k1] = $this->setButtonUrl($button, $isdecode);
            }
        }
        return $menuData;
    }

    private function setButtonUrl ($button, $isdecode = true) {
        if ($button['type'] == "view") {
            $url = $button["url"];
            $decode_url = $this->getDecodeOrEncodeUrl($url, $isdecode);
            if ($decode_url) {
                $button["url"] = $decode_url;
            }
        }
        return $button;
    }

    private function getDecodeOrEncodeUrl ($url, $isdecode = true) {
        $str = "";
        // 如果是获取code的接口地址
        if (strpos($url, "redirect_uri") > 0) {
            $str = explode("redirect_uri=", $url);
            $url_left = $str[0];
            $str = $str[1];
            $str = explode("&response_type", $str);
            $url_right = $str[1];
            $str = $str[0];
            if ($isdecode) {
                $str = urldecode($str);
            } else {
                $str = urlencode($str);
            }

            $str = $url_left . "redirect_uri=" . $str . "&response_type" . $url_right;
        }
        return $str;
    }

    // 新建
    public function doAdd () {
        return self::SUCCESS;
    }

    // 新建 提交
    public function doAddPost () {
        $name = XRequest::getValue('name', '');
        $shortname = XRequest::getValue('shortname', '');
        $type = XRequest::getValue('type', 0);
        $gh = XRequest::getValue('gh', '');
        $token = XRequest::getValue('token', '');
        $appid = XRequest::getValue('appid', 0);
        $secret = XRequest::getValue('secret', '');
        $encodingaeskey = XRequest::getValue('encodingaeskey', '');

        if ($name && $gh && $appid && $secret) {
            $row = array();
            $row["name"] = $name;
            $row["shortname"] = $shortname;
            $row["type"] = $type;
            $row["gh"] = $gh;
            $row["token"] = $token;
            $row["appid"] = $appid;
            $row["secret"] = $secret;
            $row["encodingaeskey"] = $encodingaeskey;
            $wxshop = WxShop::createByBiz($row);
        }

        XContext::setJumpPath("/wxshopmgr/list");
        return self::SUCCESS;
    }

    // 修改
    public function doModify () {
        $wxshopid = XRequest::getValue('wxshopid', 0);
        $wxshop = WxShop::getById($wxshopid);

        // 这是一个后门--begin--
        $gh = XRequest::getValue('gh', '');
        $token = XRequest::getValue('token', '');
        $appid = XRequest::getValue('appid', '');
        $secret = XRequest::getValue('secret', '');
        $encodingaeskey = XRequest::getValue('encodingaeskey', '');
        $mchid = XRequest::getValue('mchid', '');
        $mkey = XRequest::getValue('mkey', '');

        $wxshop->gh = $gh ? $gh : $wxshop->gh;
        $wxshop->token = $token ? $token : $wxshop->token;
        $wxshop->appid = $appid ? $appid : $wxshop->appid;
        $wxshop->secret = $secret ? $secret : $wxshop->secret;
        $wxshop->encodingaeskey = $encodingaeskey ? $encodingaeskey : $wxshop->encodingaeskey;
        $wxshop->mchid = $mchid ? $mchid : $wxshop->mchid;
        $wxshop->mkey = $mkey ? $mkey : $wxshop->mkey;
        // 这是一个后门--end--

        XContext::setValue('wxshop', $wxshop);
        return self::SUCCESS;
    }

    // 修改 提交
    public function doModifyPost () {
        $diseaseid = XRequest::getValue('diseaseid', 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");

        $wxshopid = XRequest::getValue('wxshopid', 0);
        $wxshop = WxShop::getById($wxshopid);

        $wxshop->name = XRequest::getValue('name', '');
        $wxshop->shortname = XRequest::getValue('shortname', '');
        $wxshop->type = XRequest::getValue('type', 0);
        $wxshop->diseaseid = $diseaseid; // 服务号主疾病
        $wxshop->wx_email = XRequest::getValue('wx_email', '');
        $wxshop->next_cert_date = XRequest::getValue('next_cert_date', '');
        $wxshop->reg_oper_name = XRequest::getValue('reg_oper_name', '');
        $wxshop->admin_name = XRequest::getValue('admin_name', '');
        $wxshop->oper_names = XRequest::getValue('oper_names', '');
        $wxshop->open_email = XRequest::getValue('open_email', '');

        XContext::setJumpPath("/wxshopmgr/modify?wxshopid={$wxshop->id}&preMsg=" . urlencode(date("H:i:s") . " 修改已提交"));

        return self::SUCCESS;
    }
}
