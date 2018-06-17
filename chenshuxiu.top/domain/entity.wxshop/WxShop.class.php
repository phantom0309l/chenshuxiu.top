<?php
include_once(ROOT_TOP_PATH . "/../core/tools/AESCrypt.php");
// 微信服务号
// 多个微信服务号绑定在一个微信开放平台账号上(最多10个服务号)
class WxShop extends Entity
{

    const WxShopId_ADHD = 1;

    const WxShopId_Doctor = 2;

    const WxShopId_Fckt = 3;

    const WxShopId_Cancer = 19;

    public static $aeskey = '0054444944';   // 用于测试环境拉取线上环境的access_token的aes加解密的密匙

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'diseaseid',  // 公众号对应的疾病
            'name',  // 公众号名称
            'shortname',  // 短名称
            'type',  // 公众号类型
            'photoid',  // 公众号头像
            'gh',  // gh
            'token',  // token
            'appid',  // appid
            'secret',  // secret
            'encodingaeskey',  // EncodingAESKey
            'mchid',  // MCHID,商户号
            'mkey',  // KEY,商户支付密钥
            'access_token',  // access_token 获取到的凭证
            'access_in',  // 获取时的时间戳
            'jsapi_ticket',
            'jsapi_ticket_access_in',
            'expires_in',  // 凭证有效时间，单位：秒
            'wx_email',  // 服务号后台登录邮箱
            'next_cert_date',  // 服务号下次认证开始日期
            'reg_oper_name',  // 服务号注册运营者
            'admin_name',  // 服务号管理员
            'oper_names',  // 服务号运营者,可登录
            'open_email'); // 关联开放平台账号
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'photoid');
    }

    protected function init_belongtos() {
        $this->_belongtos = array();
        $this->_belongtos["photo"] = array(
            "type" => "Photo",
            "key" => "photoid");
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
    }

    // $row = array();
    // $row["name"] = $name;
    // $row["shortname"] = $shortname;
    // $row["type"] = $type;
    // $row["gh"] = $gh;
    // $row["token"] = $token;
    // $row["appid"] = $appid;
    // $row["secret"] = $secret;
    // $row["encodingaeskey"] = $encodingaeskey;
    // $row["mchid"] = $mchid;
    // $row["mkey"] = $mkey;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "WxShop::createByBiz row cannot empty");

        $maxid = Dao::queryValue(" select max(id) as maxid from wxshops ");
        if (empty($maxid)) {
            $maxid = 0;
        }

        $default = array();
        $default["id"] = $maxid + 1;
        $default["diseaseid"] = 0;
        $default["name"] = '';
        $default["shortname"] = '';
        $default["type"] = 0;
        $default["photoid"] = 0;
        $default["gh"] = '';
        $default["token"] = '';
        $default["appid"] = '';
        $default["secret"] = '';
        $default["encodingaeskey"] = '';
        $default["mchid"] = '';
        $default["mkey"] = '';
        $default["access_token"] = '';
        $default["access_in"] = 0;
        $default['jsapi_ticket'] = '';
        $default['jsapi_ticket_access_in'] = 1;
        $default["expires_in"] = 0;
        $default["wx_email"] = '';
        $default["next_cert_date"] = '2015-01-01';
        $default["reg_oper_name"] = '';
        $default["admin_name"] = '';
        $default["oper_names"] = '';
        $default["open_email"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function initWxPayConfig() {
        $wxshop = $this;
        WxPayConfig::$shopid = $wxshop->id;
        WxPayConfig::$appid = $wxshop->appid;
        WxPayConfig::$appsecret = $wxshop->secret;
        WxPayConfig::$mchid = $wxshop->mchid;
        WxPayConfig::$key = $wxshop->mkey;
    }

    public function getWxToken() {
        return $this->token;
        // $md5 = md5 ( 'fcqx' . $this->id );
        // return 'fcqx' . substr ( $md5, 3, 5 );
    }

    public function validateWxToken($wxtoken) {
        return $wxtoken == $this->getWxToken();
    }

    public function getTypeDesc() {
        $arr = self::typeDescs();
        return $arr[$this->type];
    }

    // 是认证服务号
    public function isAuthServiceNo() {
        return $this->type == 1;
    }

    public function getFromUserName() {
        return $this->gh;
    }

    public function getNext_cert_dateFix() {
        if (strtotime($this->next_cert_date) < time()) {

            return "<span class='red'>{$this->next_cert_date}</span>";
        }

        return $this->next_cert_date;
    }

    public function getAccessToken() {
        $nowTime = time();
        if (($this->expires_in + $this->access_in - 600) > $nowTime && $this->access_token) {
            return $this->access_token;
        }

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appid . "&secret=" . $this->secret;

        $json = XHttpRequest::curl_getUrlContents($url);

        $json = json_decode($json, true);

        $this->access_token = isset($json['access_token']) ? $json['access_token'] : '';
        $this->access_in = $nowTime;
        $this->expires_in = $json['expires_in'] ? $json['expires_in'] : 0;

        // 同步至开发环境
        if ('production' == Config::getConfig('env')) {
            $this->postAccessTokenToDevelop();
        }

        return $this->access_token;
    }

    private function postAccessTokenToDevelop() {
        $url = "http://wx.fangcunhulian.cn/wxshop/recievejson";

        $access_token = $this->access_token;

        $aes = new AESCrypt(WxShop::$aeskey);
        $access_token = $aes->encrypt($access_token);

        $data = [];
        $data['wxshopid'] = $this->id;
        $data['access_token'] = $access_token;
        $data['access_in'] = $this->access_in;
        $data['expires_in'] = $this->expires_in;

        $date = json_encode($data, true);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function getJsApiTicket() {
        $nowTime = time();
        if (($this->expires_in + $this->jsapi_ticket_access_in - 600) > $nowTime && $this->jsapi_ticket) {
            return $this->jsapi_ticket;
        }
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $this->getAccessToken() . "&type=jsapi";
        $jsonarr = json_decode(XHttpRequest::curl_getUrlContents($url), true);

        if ($jsonarr["errcode"] == 0) {
            $this->jsapi_ticket = $jsonarr["ticket"];
            $this->jsapi_ticket_access_in = $nowTime;
            return $this->jsapi_ticket;
        }

        return "";
    }

    public function getJsApiSignature($url, $timestamp) {
        $jsapi_ticket = $this->getJsApiTicket();
        $noncestr = self::$nonceStr;
        $str = "jsapi_ticket={$jsapi_ticket}&noncestr={$noncestr}&timestamp={$timestamp}&url={$url}";
        // cho $str . "|||||";
        return sha1($str);
    }

    // 是否是肿瘤公众号
    public function isCancerWxshop() {
        $ids = self::getCancerWxshopids();

        return in_array($this->id, $ids);
    }

    public function getWxTemplateByEname($ename) {
        return WxTemplateDao::getByEname($this->id, $ename);
    }

    public function getWxTemplateOfAdminNoticeOrFollowupNotice() {
        $wxtemplate = $this->getWxTemplateByEname("adminNotice");
        if (false == $wxtemplate instanceof WxTemplate) {
            $wxtemplate = $this->getWxTemplateByEname("followupNotice");
        }
        return $wxtemplate;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    // FIXME NOT SAFE
    public static $nonceStr = "sM4AOVdWfPE4DxkXGEs8VMCPGGVi4C3";

    public static function getJSSDKConfig() {
        $wxshop = WxShop::getById(1);

        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $url = $http_type . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $timestamp = time();
        $jsapi_signature = $wxshop->getJsApiSignature($url, $timestamp);
        return [
            'appId' => $wxshop->appid,
            'timestamp' => $timestamp,
            'nonceStr' => self::$nonceStr,
            'signature' => $jsapi_signature
        ];
    }

    public static function getJSSDKConfigJson() {
        return json_encode(self::getJSSDKConfig());
    }

    public static function typeDescs() {
        $arr = array();
        $arr[0] = '未知类型';
        $arr[1] = '认证服务号';
        $arr[2] = '服务号';
        $arr[3] = '认证订阅号';
        $arr[4] = '订阅号';
        return $arr;
    }

    // 获取肿瘤方向的所有wxshopid
    public static function getCancerWxshopids() {
        $ids = [12, 15, 19, 21];

        return $ids;
    }

}
