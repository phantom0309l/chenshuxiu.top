<?php
// BatMsg
// 建议废弃 by sjp 20160627

// owner by xxx
// review by sjp 20160627
// TODO rework

class BatMsg extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'userid',  //
            'typestr',  //
            'content',  //
            'status',  // 审核结果
            'issend',  // 是否发送
            'sendbegintime',  // 发送开始时间
            'sendendtime',  // 发送结束时间
            'auditstatus',  // 是否审核了
            'auditorid',  // auditorid
            'auditremark'); //

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'userid',
            'auditorid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["userid"] = $userid;
    // $row["typestr"] = $typestr;
    // $row["content"] = $content;
    // $row["status"] = $status;
    // $row["issend"] = $issend;
    // $row["sendbegintime"] = $sendbegintime;
    // $row["sendendtime"] = $sendendtime;
    // $row["auditstatus"] = $auditstatus;
    // $row["auditorid"] = $auditorid;
    // $row["auditremark"] = $auditremark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "BatMsg::createByBiz row cannot empty");

        $default = array();
        $default["userid"] = 0;
        $default["typestr"] = '';
        $default["content"] = '';
        $default["status"] = 0;
        $default["issend"] = 0;
        $default["sendbegintime"] = '0000-00-00 00:00:00';
        $default["sendendtime"] = '0000-00-00 00:00:00';
        $default["auditstatus"] = 0;
        $default["auditorid"] = 0;
        $default["auditremark"] = '';

        $row += $default;
        return new self($row);
    }

    // createByContent
    public static function createByContent ($userid, $typestr, $content) {
        $row = array();
        $row["userid"] = $userid;
        $row["typestr"] = $typestr;
        $row["content"] = $content;

        return self::createByBiz($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getType () {
        $typestr = $this->typestr;
        $arr = array(
            "DoctorToPatient" => "群发消息",
            "DoctorSetTip" => "门诊消息",
            "DoctorSetTemp" => "临时出诊");
        return $arr[$typestr];
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
