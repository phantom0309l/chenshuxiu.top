<?php

/*
 * DoctorWxShopRef
 */
class DoctorWxShopRef extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'doctorid',  // doctorid
            'wxshopid',  // wxshopid
            'diseaseid',  // diseaseid
            'qr_ticket'); // 微信二维码ticket
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'doctorid',
            'wxshopid');
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["wxshop"] = array(
            "type" => "WxShop",
            "key" => "wxshopid");
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["wxshopid"] = $wxshopid;
    // $row["diseaseid"] = $diseaseid;
    // $row["qr_ticket"] = $qr_ticket;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "DoctorWxShopRef::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] = 0;
        $default["wxshopid"] = 0;
        $default["diseaseid"] = 0;
        $default["qr_ticket"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 检查 qr_ticket
    public function check_qr_ticket() {
        $qr_ticket = $this->qr_ticket;
        if (empty($qr_ticket)) {
            $wxshop = $this->wxshop;
            $access_token = $wxshop->getAccessToken();
            $diseaseid = $this->diseaseid;
            if($diseaseid > 0){
                $scene_str = $this->doctor->code . ":{$diseaseid}";
            }else{
                $scene_str = $this->doctor->code;
            }
            $qr_ticket = WxApi::getQrTicket($access_token, $scene_str);
            if ($qr_ticket) {
                $this->qr_ticket = $qr_ticket;
            }
        }
        return $qr_ticket;
    }

    // 获取 QrUrl
    public function getQrUrl() {
        $this->check_qr_ticket();
        return "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $this->qr_ticket;
    }

    // 获取QrURl
    public function getQrUrl4Tpl() {
        return "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $this->qr_ticket;
    }

    //是否默认二维码
    public function isDefaultQr(){
        return 0 == $this->diseaseid;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
