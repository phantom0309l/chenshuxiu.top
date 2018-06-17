<?php

/**
 * Created by Atom.
 * User: Jerry
 * Date: 2018/4/25
 * Time: 9:10
 */
class ShopProductNotice extends Entity
{
    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid'    //
        , 'userid'    //
        , 'patientid'    //
        , 'shopproductid'    //shopproductid
        , 'cnt'    //数量
        , 'status'    //状态：0:初始态；1:已提醒；2:过期（有效时期2周）
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array('wxuserid', 'userid', 'patientid', 'shopproductid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array("type" => "WxUser", "key" => "wxuserid");
        $this->_belongtos["user"] = array("type" => "User", "key" => "userid");
        $this->_belongtos["patient"] = array("type" => "Patient", "key" => "patientid");
        $this->_belongtos["shopproduct"] = array("type" => "ShopProduct", "key" => "shopproductid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["shopproductid"] = $shopproductid;
    // $row["cnt"] = $cnt;
    // $row["status"] = $status;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "ShopProductNotice::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["shopproductid"] = 0;
        $default["cnt"] = 0;
        $default["status"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getStatusStr() {
        $arr = self::getStatusArr();
        return $arr[$this->status];
    }

    public function isNotNotice() {
        return 0 == $this->status;
    }

    public function canNotice() {
        $today = date("Y-m-d", time());
        $createday = date("Y-m-d", strtotime($this->createtime));

        $diff = XDateTime::getDateDiff($today, $createday);

        return $diff <= 14;
    }

    public function notice() {
        $this->status = 1;

        $wxuser = $this->wxuser;
        $patient = $this->patient;
        $str = "{$patient->doctor->name}医生助理";
        $content = "您好，{$this->shopproduct->title}药品已经到货，如有需要，可以去开药门诊操作。";
        $first = array(
            "value" => "到货提醒",
            "color" => "#ff6600");
        $keywords = array(
            array(
                "value" => $str,
                "color" => "#aaa"),
            array(
                "value" => $content,
                "color" => "#ff6600"));
        $content = WxTemplateService::createTemplateContent($first, $keywords);

        $wx_uri = Config::getConfig("wx_uri");
        $url = $wx_uri . "/shopmedicine/chuFangApply?openid={$wxuser->openid}";

        PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
    }

    public function overtime() {
        $this->status = 2;
    }
    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getStatusArr() {
        $arr = [
            0 => '未通知',
            1 => '已通知',
            2 => '已过期',
        ];
        return $arr;
    }

}
