<?php
/*
 * Wxqrcode
 */
class WxQrcode extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxshopid',
            'action_name',  // 二维码类型，QR_SCENE为临时,QR_LIMIT_SCENE为永久,QR_LIMIT_STR_SCENE为永久的字符串参数值
            'scene_id',  // 场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
            'scene_str',  // 场景值ID（字符串形式的ID），字符串类型，长度限制为1到64，仅永久二维码支持此字段
            'expire_seconds',  // 该二维码有效时间，以秒为单位。
                              // 最大不超过2592000（即30天），此字段如果不填，则默认有效期为30秒。
            'ticket',  // 获取的二维码ticket，凭借此ticket可以在有效时间内换取二维码。
            'url',  // 二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片
            'pcode',  // pcode
            'objtype',  // objtype
            'objid',  // objid
            'pictureid'); // pictureid

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'objid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
        $this->_belongtos["wxshop"] = array(
            "type" => "WxShop",
            "key" => "wxshopid");
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
    }

    private function updateTicket () {
        $fields = array(
            "expire_seconds" => $this->expire_seconds,
            "action_name" => $this->action_name,
            "action_info" => array(
                "scene" => array(
                    "scene_id" => $this->scene_id)));

        $fields = urldecode(json_encode($fields));

        $wxshop = WxShop::getById(3);
        $access_token = $wxshop->getAccessToken();
        $qrcodebeg_url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";
        $jsonStr = XHttpRequest::curl_postUrlContents($qrcodebeg_url, $fields);
        $json = json_decode($jsonStr, true);
        $this->ticket = $json['ticket'];
    }

    // $row = array();
    // $row["action_name"] = $action_name;
    // $row["scene_id"] = $scene_id;
    // $row["scene_str"] = $scene_str;
    // $row["expire_seconds"] = $expire_seconds;
    // $row["ticket"] = $ticket;
    // $row["url"] = $url;
    // $row["pcode"] = $pcode;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Wxqrcode::createByBiz row cannot empty");

        $default = array();
        $default["wxshopid"] = 0;
        $default["action_name"] = '';
        $default["scene_id"] = 0;
        $default["scene_str"] = '';
        $default["expire_seconds"] = 0;
        $default["ticket"] = '';
        $default["url"] = '';
        $default["pcode"] = '';
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["pictureid"] = 0;

        $row += $default;
        return new self($row);
    }

    public static function createOrGetTempByObj ($wxshopid, $pcode, $obj) {
        $qrcode = self::getByPcodeObj($wxshopid, $pcode, $obj);

        if ($qrcode instanceof WxQrcode) {
            $today = date("Y-m-d");
            $span = XDateTime::getDaySpan($qrcode->updatetime, $today);
            if ($span <= 20) {
                return $qrcode;
            } else {
                $qrcode->updateTicket();
                return $qrcode;
            }
        }

        return self::createTempOne($wxshopid, $pcode, $obj);
    }

    // TODO
    public static function createOrGetForeverByObj ($wxshopid, $pcode, $obj) {}

    public static function createTempOne ($wxshopid, $pcode, $obj) {
        $wxshop = WxShop::getById($wxshopid);

        $wx_uri = Config::getConfig("wx_uri");

        $row = array(
            "wxshopid" => $wxshopid,
            "action_name" => "QR_SCENE",
            "scene_id" => self::generateScene_id(),
            "expire_seconds" => 2592000,
            "pcode" => $pcode,
            "objtype" => get_class($obj),
            "objid" => $obj->id,
            "ticket" => "");
        $qrcode = self::createByBiz($row);

        $fields = array(
            "expire_seconds" => $qrcode->expire_seconds,
            "action_name" => $qrcode->action_name,
            "action_info" => array(
                "scene" => array(
                    "scene_id" => $qrcode->scene_id)));
        $fields = urldecode(json_encode($fields));

        FUtil::safeGuardNtimes(
                function  () use( $wxshop, $fields, $qrcode) {
                    $access_token = $wxshop->getAccessToken();
                    $qrcodebeg_url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";
                    $jsonStr = XHttpRequest::curl_postUrlContents($qrcodebeg_url, $fields);
                    $json = json_decode($jsonStr, true);
                    $ticket = $json['ticket'];
                    if (empty($ticket)) {
                        $wxshop->access_token = "";
                        return false;
                    } else {
                        $qrcode->ticket = $ticket;
                        return true;
                    }
                }, 5);

        Debug::trace("=====[ ticket beg ]=====");
        Debug::trace($qrcode->ticket);
        Debug::trace("=====[ ticket end ]=====");

        $qrcode->url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . "{$qrcode->ticket}";
        return $qrcode;
    }

    // TODO
    public static function createForeverOne ($wxshopid, $pcode, $obj) {}

    // TODO 最好能使用 WxUser 中的 disposeQrCodeEventKey 来调用
    public static function analyzeEventKey4Subscribe ($eventkey) {}

    // TODO EventKey 事件KEY值，是一个32位无符号整数，即创建二维码时的二维码scene_id --注：微信接口文档
    public static function analyzeEventKey4Scan ($eventkey) {}

    public static function getByScene_id ($scene_id) {
        $cond = " and scene_id=:scene_id ";

        $bind = array(
            ":scene_id" => $scene_id);

        return Dao::getEntityByCond('WxQrcode', $cond, $bind);
    }

    // TODO
    public static function getByScene_str ($scene_str) {}

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    public static function getByPcodeObj ($wxshopid, $pcode, $obj) {
        $cond = " and wxshopid=:wxshopid and pcode=:pcode and objtype=:objtype and objid=:objid ";

        $bind = array(
            ":wxshopid" => $wxshopid,
            ":pcode" => $pcode,
            ":objtype" => get_class($obj),
            ":objid" => $obj->id);

        return Dao::getEntityByCond('WxQrcode', $cond, $bind);
    }

    public static function getListByPcode ($wxshopid, $pcode) {
        $cond = " and wxshopid=:wxshopid and pcode=:pcode";
        $bind = array(
            ":wxshopid" => $wxshopid,
            ":pcode" => $pcode);

        return Dao::getEntityListByCond('WxQrcode', $cond, $bind);
    }

    private static function generateScene_id () {
        return XCode::getNextCode('qrcode_scene_id');
    }

}
