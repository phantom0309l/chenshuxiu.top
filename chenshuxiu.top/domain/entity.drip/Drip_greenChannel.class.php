<?php

/*
 * Drip_greenChannel
 */

class Drip_greenChannel extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'wxuserid'    //wxuserid
        , 'userid'    //userid
        , 'patientid'    //patientid
        , 'diseasestr'    //疾病
        , 'xcityid'    //城市ID
        , 'expecteddate'    //期望日期
        , 'bounddate'    //期望边界日期
        , 'actualdate'    //实际日期
        , 'hospitalid'    //医院
        , 'status'    //状态
        , 'content'    //内容
        , 'remark'    //运营备注
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array(
            'wxuserid', 'userid', 'patientid', 'xcityid', 'hospitalid',);
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array("type" => "WxUser", "key" => "wxuserid");
        $this->_belongtos["user"] = array("type" => "User", "key" => "userid");
        $this->_belongtos["patient"] = array("type" => "Patient", "key" => "patientid");
        $this->_belongtos["xcity"] = array("type" => "Xcity", "key" => "xcityid");
        $this->_belongtos["hospital"] = array("type" => "Hospital", "key" => "hospitalid");
    }

    // $row = array(); 
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["diseasestr"] = $diseasestr;
    // $row["xcityid"] = $xcityid;
    // $row["expecteddate"] = $expecteddate;
    // $row["bounddate"] = $bounddate;
    // $row["actualdate"] = $actualdate;
    // $row["hospitalid"] = $hospitalid;
    // $row["status"] = $status;
    // $row["content"] = $content;
    // $row["remark"] = $remark;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "Drip_greenChannel::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["diseasestr"] = '';
        $default["xcityid"] = 0;
        $default["expecteddate"] = '0000-00-00';
        $default["bounddate"] = '0000-00-00';
        $default["actualdate"] = '0000-00-00';
        $default["hospitalid"] = 0;
        $default["status"] = 1;
        $default["content"] = '';
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    public static function getAllStatus() {
        return [
            0 => '无效',
            1 => '待确认',
            2 => '已确认',
        ];
    }

    /**
     * 获取未完成的绿色通道申请
     *
     * @param $patientid
     * @return null | Drip_greenChannel
     */
    public static function getUnfinished($patientid) {
        $drip_greenChannel = Drip_greenChannelDao::getLastOneByPatientid($patientid);
        if ($drip_greenChannel instanceof Drip_greenChannel && $drip_greenChannel->isUnfinished()) {
            return $drip_greenChannel;
        }
        return null;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    /**
     * 未完成
     */
    public function isUnfinished() {
        $today = date('Y-m-d');
        if ($this->status == 1 || ($this->status == 2 && $this->actualdate >= $today)) {
            return true;
        } else {
            return false;
        }
    }

    public function getStatusStr() {
        return self::getAllStatus()[$this->status];
    }
}
