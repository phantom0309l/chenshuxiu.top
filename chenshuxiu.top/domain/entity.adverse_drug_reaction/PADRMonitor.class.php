<?php

/*
 * PADRMonitor
 * 患者药物不良反应监测
 */
class PADRMonitor extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'diseaseid',  // diseaseid
            'medicineid',  // 创建时，计算plan_date用的medicineid
            'type',  // 任务类型
            'weekday',  // 星期，1-7
            'prev_date',  // 上一次监测日期
            'plan_date',  // 计划监测日期
            'the_date',  // 监测日期
            'adrmonitorruleitem_ename',  // 监测项目，不用外键的原因是因为ruleitem可能会被删除，修改
            'submit_time',  // 提交时间
            'status'); // 0未发送，1已发送，2已填写, 3关闭
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'userid',
            'patientid',
            'diseaseid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid");
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid");
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
        $this->_belongtos["medicine"] = array(
            "type" => "Medicine",
            "key" => "medicineid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["diseaseid"] = $diseaseid;
    // $row["medicineid"] = $medicineid;
    // $row["type"] = $type;
    // $row["weekday"] = $weekday;
    // $row["prev_date"] = $prev_date;
    // $row["plan_date"] = $plan_date;
    // $row["the_date"] = $the_date;
    // $row["adrmonitorruleitem_ename"] = $adrmonitorruleitem_ename;
    // $row["submit_time"] = $submit_time;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PADRMonitor::createByBiz row cannot empty");

        $default = [];
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["diseaseid"] = 0;
        $default["medicineid"] = 0;
        $default["type"] = '';
        $default["weekday"] = 0;
        $default["prev_date"] = '0000-00-00';
        $default["plan_date"] = '0000-00-00';
        $default["the_date"] = '0000-00-00';
        $default["adrmonitorruleitem_ename"] = '';
        $default["submit_time"] = '0000-00-00 00:00:00';
        $default["status"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getHistory () {
        $padrmonitors = PADRMonitorDao::getListByPatientidAndEname($this->patientid, $this->adrmonitorruleitem_ename);
        $arr = [];
        foreach ($padrmonitors as $padrmonitor) {
            if ($padrmonitor->id != $this->id) {
                $arr[] = $padrmonitor;
            }
        }
        return $arr;
    }

    public function getObjPictures () {
        if ($this->adrmonitorruleitem_ename == "xuechanggui") {
            $objpictures = WxPicMsgDao::getListByObj($this);
        } elseif ($this->adrmonitorruleitem_ename == "ganshengong") {
            $objpictures = LiverPictureDao::getListByObj($this);
        } else {
            $objpictures = BasicPictureDao::getListByObj($this);
        }

        return $objpictures ?? [];
    }

    public function getTypeStr () {
        return self::getTypes()[$this->type];
    }

    public function getStatusStr () {
        $status_arr = [
            0 => '待发送',
            1 => '已发送',
            2 => '已填写',
            3 => '已关闭'];
        return $status_arr[$this->status];
    }

    public function getEnameStr () {
        return ADRMonitorRuleItem::getItemStr($this->adrmonitorruleitem_ename);
    }

    public function getOpTask() {
        return OpTaskDao::getOneByObj($this);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getTypes () {
        return [
            'monitor' => '监测',
            'observe' => '观察',
            'second_observe' => '二次观察',
            'visit' => '就诊'];
    }

    // 获取 硫锉嘌呤 药品ids
    public static function getLcplMids () {
        return [
            5,
            20];
    }

    // 获取 吗替麦考酚酯 药品ids
    public static function getMtmkfzMids () {
        return [
            4,
            11,
            71];
    }

    // 获取 羟氯喹 药品ids
    public static function getQlkMids () {
        return [
            15,
            75,
            145];
    }

    // 获取 硫锉嘌呤 药品ids
    public static function getLcplMidsStr () {
        return "5, 20";
    }

    // 获取 吗替麦考酚酯 药品ids
    public static function getMtmkfzMidsStr () {
        return "4, 11, 71";
    }

    // 获取 羟氯喹 药品ids
    public static function getQlkMidsStr () {
        return "15, 75, 145";
    }
}
