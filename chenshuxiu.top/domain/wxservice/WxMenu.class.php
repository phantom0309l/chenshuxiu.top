<?php

class WxMenu
{

    public function __construct () {}

    public function getMenu ($wxshopid) {
        $access_token = $this->getAccessToken($wxshopid);
        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token={$access_token}";
        $jsonStr = XHttpRequest::curl_getUrlContents($url);
        $json = json_decode($jsonStr, true);
        $fields = $json['menu'];
        // $fields = json_encode($json['menu']);
        return $fields;
    }

    public function createMenu ($wxshopid, $menuArrStr) {
        $access_token = $this->getAccessToken($wxshopid);
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
        $jsonStr = XHttpRequest::curl_postUrlContents($url, $menuArrStr);
        $json = json_decode($jsonStr, true);
        return $json;
    }

    // 创建个性化菜单
    public function createConditional ($wxshopid, $menuArrStr) {
        $access_token = $this->getAccessToken($wxshopid);
        $url = "https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token={$access_token}";
        $jsonStr = XHttpRequest::curl_postUrlContents($url, $menuArrStr);
        $json = json_decode($jsonStr, true);
        return $json;
    }

    private function getAccessToken ($wxshopid) {
        $wxshop = WxShop::getById($wxshopid);
        return $wxshop->getAccessToken();
    }

    // ============================以下为静态方法============================
    public static function getConditionalMenus ($wxshopid, $need_filter = true) {
        $fields = self::getConditionalMenusImp($wxshopid);
        if ($need_filter) {
            // 只拿到最新的个性化菜单就够了，之前配置的老的过滤掉
            $groupid_arr = array();
            $result = array();
            foreach ($fields as $a) {
                $groupid = $a["matchrule"]["group_id"] ?? '0';
                if (in_array($groupid, $groupid_arr)) {
                    continue;
                }
                $groupid_arr[] = $groupid;
                $result[] = $a;
            }
            return $result;
        } else {
            return $fields;
        }
    }

    public static function getConditionalMenuByWxshopidGroupid ($wxshopid, $groupid) {
        $fields = self::getConditionalMenusImp($wxshopid);
        $result = array();
        foreach ($fields as $a) {
            $current_groupid = $a["matchrule"]["group_id"] ?? '0';
            if ($current_groupid == $groupid) {
                $result = $a;
                break;
            }
        }
        return $result;
    }

    public static function getConditionalMenusImp ($wxshopid) {
        $wxshop = WxShop::getById($wxshopid);
        $access_token = $wxshop->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token={$access_token}";
        $jsonStr = XHttpRequest::curl_getUrlContents($url);
        $json = json_decode($jsonStr, true);
        $arr = $json['conditionalmenu'];

        if (empty($arr)) {
            Debug::warn("WxMenu::getConditionalMenusImp : {$jsonStr}");
            $arr = [];
        }

        return $arr;
    }

    // 在相关业务场景下回调用
    public static function getSerializedMenuText ($wxshopid, $groupid, $wxuser) {
        $fields = self::getConditionalMenuByWxshopidGroupid($wxshopid, $groupid);
        $fields = $fields["button"] ?? array();
        return self::getSerializedMenuTextImp($fields, $wxuser);
    }

    private static function getSerializedMenuTextImp ($fields, $wxuser) {
        $txt = "";
        foreach ($fields as $a) {
            // 一级菜单拼接
            $txt .= self::getItemText($a, $wxuser);
            $sub_buttons = $a['sub_button'] ?? array();
            $cnt = count($sub_buttons);
            // 有二级菜单,二级菜单拼接
            if ($cnt > 0) {
                foreach ($sub_buttons as $sub_button) {
                    $txt .= self::getItemText($sub_button, $wxuser);
                }
            }
        }
        return $txt;
    }

    private static function getItemText ($item, $wxuser) {
        $name = $item['name'] ?? '';
        $url = $item['url'] ?? '';
        $url = urldecode($url);
        if ($url) {
            $openid = $wxuser->openid;
            $url = self::getFixUrl($url);
            $url = "{$url}?openid={$openid}";
            $txt = "<a href=\"{$url}\">『{$name}』</a>";
        } else {
            $txt = $name;
        }
        $txt = "{$txt}\n";
        return $txt;
    }

    private static function getFixUrl ($url) {
        $url = explode("redirect_uri=", $url);
        $url = $url[1] ?? '';
        $url = explode("?gh=", $url);
        $url = $url[0] ?? '';
        return $url;
    }
}
