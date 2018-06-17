<?php
/*
 * WxTaskItem
 */
class WxTaskItem extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'wxuserid',  // wxuserid
            'wxtaskid',  // wxtaskid
            'wxtasktplitemid',  // wxtasktplitemid
            'pos',  // 序号
            'starttime',  // starttime
            'endtime',  // endtime
            'signtime',  // signtime
            'ename',  // 英文名称
            'status'); // 状态

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid',
            'wxtasktplitemid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid");
        $this->_belongtos["wxtasktplitem"] = array(
            "type" => "WxTaskTplItem",
            "key" => "wxtasktplitemid");
    }

    // $row = array();
    // $row["wxuserid"] = $wxuserid;
    // $row["wxtasktplitemid"] = $wxtasktplitemid;
    // $row["pos"] = $pos;
    // $row["starttime"] = $starttime;
    // $row["endtime"] = $endtime;
    // $row["signtime"] = $signtime;
    // $row["ename"] = $ename;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "WxTaskItem::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["wxtaskid"] = 0;
        $default["wxtasktplitemid"] = 0;
        $default["pos"] = '';
        $default["starttime"] = '0000-00-00 00:00:00';
        $default["endtime"] = '0000-00-00 00:00:00';
        $default["signtime"] = '0000-00-00 00:00:00';
        $default["ename"] = '';
        $default["status"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getStatusStr () {
        $s = strtotime($this->starttime);
        $e = strtotime($this->endtime);
        $n = time();
        $str = "";
        if ($this->isSigned()) {
            $str = "<div class=\"listbox-item-r green\">签到成功</div>";
        } else {
            if ($n >= $s && $n <= $e) {
                $str = "<div class=\"listbox-item-r blue\">正在进行</div>";
            } else
                if ($n > $e) {
                    $str = "<div class=\"listbox-item-r gray\">已错过</div>";
                } else
                    if ($n < $s) {
                        $str = "<div class=\"listbox-item-r gray\">未开始</div>";
                    }
        }
        return $str;
    }

    public function getSignBtn () {
        $s = strtotime($this->starttime);
        $e = strtotime($this->endtime);
        $n = time();
        $str = "";
        if ($this->isSigned()) {
            $str = "<div class=\"bigbtn bigbtn-active\">已签到</div>";
        } else {
            if ($n >= $s && $n <= $e) {
                $str = "<div class=\"bigbtn bigbtn-active signBtn\">签到</div>";
            } else
                if ($n > $e) {
                    $str = "<div class=\"bigbtn bigbtn-default\">已错过</div>";
                } else
                    if ($n < $s) {
                        $str = "<div class=\"bigbtn bigbtn-not\">20:00以后才可以签到</div>";
                    }
        }
        return $str;
    }

    public function getDateStr () {
        $s = strtotime($this->starttime);
        $e = strtotime($this->endtime);
        $str1 = date("m-d", $s);
        $str2 = date("H:i", $s);
        $str3 = date("H:i", $e);
        return "{$str1} {$str2} - 24:00";
    }

    public function isSigned () {
        return $this->signtime != "0000-00-00 00:00:00";
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
