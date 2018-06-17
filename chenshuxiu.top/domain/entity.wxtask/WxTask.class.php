<?php
/*
 * WxTask
 */
class WxTask extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'wxtasktplid',  // wxtasktplid
            'starttime',  // starttime
            'endtime',  // endtime
            'ename',  // 英文名称
            'status'); // 状态

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'wxtasktplid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["wxtasktpl"] = array(
            "type" => "WxTaskTpl",
            "key" => "wxtasktplid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["wxtasktplid"] = $wxtasktplid;
    // $row["starttime"] = $starttime;
    // $row["endtime"] = $endtime;
    // $row["ename"] = $ename;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "WxTask::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["wxtasktplid"] = 0;
        $default["starttime"] = '0000-00-00 00:00:00';
        $default["endtime"] = '0000-00-00 00:00:00';
        $default["ename"] = '';
        $default["status"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getDetailStartEndTime () {
        $result = array();
        $s = strtotime($this->starttime);
        $e = strtotime($this->endtime);

        $tail1 = date("H:i:s", $s);
        $tail2 = date("H:i:s", $e);

        $head1 = date("Y-m-d", $s);
        $head2 = date("Y-m-d", $e);
        $onedaytime = 24 * 60 * 60;
        $day = 1 + (strtotime($head2) - strtotime($head1)) / $onedaytime;

        for ($i = 0; $i < $day; $i ++) {
            $arr = array();
            $time = strtotime($head1) + $i * $onedaytime;
            $arr['starttime'] = date("Y-m-d", $time) . " {$tail1}";
            $arr['endtime'] = date("Y-m-d", $time) . " {$tail2}";
            $result[] = $arr;
        }
        return $result;
    }

    public function getCurrItem () {
        $starttime = date("Y-m-d", time()) . " 20:00:00";
        return WxTaskItemDao::getCurrItem($this->id, $starttime);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
