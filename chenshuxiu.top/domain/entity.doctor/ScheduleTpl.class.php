<?php

/*
 * ScheduleTpl 医生出诊表, 模板
 */
class ScheduleTpl extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctorid',  // doctorid
            'diseaseid',  // diseaseid
            'op_hz',  // 旧字段, weekly(每周） , interval（隔周）, temp（临时）
            'day_part',  // 旧字段,am 上午 , pm 下午 ,all_day 全天， night 晚上
            'op_type',  // 旧字段, normal, special, special
            'wday',  // 旧字段, 1-7
            'op_date',  // 旧字段, 临时和隔周是有意义的 2015-08-15
            'begin_hour_str',  // 开始门诊时间点 例如8:00 用字符串
            'tip',  // 旧字段
            'maxcnt',  // 最大可预约数
            'is_show_p_wx_json',  // 用来控制选项是否在患者微信端显示，json格式
            'scheduletpl_mobile',  // 门诊电话（医生的电话）
            'scheduletpl_cost',  // 费用（单位元）
            'see_patienttagtplids',  // 门诊对于某些标签患者可见
            'status',  // 状态
            'xprovinceid',  // 省id
            'xcityid',  //  市id
            'xcountyid',    //  区id
            'content',  //  具体地址
            'auditstatus',  // 审核状态
            'auditorid',  // auditorid
            'auditremark'); // 审核备注
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'doctorid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
        $this->_belongtos["xprovince"] = array(
            "type" => "Xprovince",
            "key" => "xprovinceid");
        $this->_belongtos["xcity"] = array(
            "type" => "Xcity",
            "key" => "xcityid");
        $this->_belongtos["xcounty"] = array(
            "type" => "Xcounty",
            "key" => "xcountyid");
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["diseaseid"] = $diseaseid;
    // $row["op_hz"] = $op_hz;
    // $row["day_part"] = $day_part;
    // $row["op_type"] = $op_type;
    // $row["wday"] = $wday;
    // $row["op_date"] = $op_date;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "ScheduleTpl::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] = 0;
        $default["diseaseid"] = 0;
        $default["op_hz"] = '';
        $default["day_part"] = '';
        $default["op_type"] = '';
        // $default ["wday"] = '';//必须传
        $default["op_date"] = '0000-00-00';
        $default["begin_hour_str"] = '';
        $default["tip"] = '';
        $default["maxcnt"] = 0;

        $default["is_show_p_wx_json"] = '';
        $default["scheduletpl_mobile"] = '';
        $default["scheduletpl_cost"] = 0;
        $default["see_patienttagtplids"] = '';

        $default["status"] = 1;
        $default["xprovinceid"] = 0;
        $default["xcityid"] = 0;
        $default["xcountyid"] = 0;
        $default["content"] = '';
        $default["auditstatus"] = 0;
        $default["auditorid"] = 0;
        $default["auditremark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 临时出诊有用，刚好是今天
    public function opdateIsToday () {
        return substr($this->op_date, 0, 10) == date("Y-m-d");
    }

    public function isOnline () {
        return $this->status == 1 && false == $this->opdateIsPass();
    }

    // 临时出诊有用
    public function opdateIsPass () {
        if ($this->op_hz != 'temp') {
            return false;
        }

        $optime = strtotime($this->op_date);
        $todaytime = strtotime(date("Y-m-d"));

        return $optime < $todaytime;
    }

    // 获取简单格式 ：专家(每周)
    public function toSimpleStr () {
        $str = $this->get_type() . "(" . $this->get_hz() . ")";

        if ('temp' == $this->op_hz) {
            $str . " " . $this->op_date;
        }

        $str .= " ({$this->disease->name})";

        return $str;
    }

    // 获取简单格式 ：每周周一上午 普通门诊
    public function toStr () {
        $str = $this->get_hz();

        if ('temp' == $this->op_hz) {
            $str .= "({$this->op_date})";
        }

        $str .= " " . $this->get_wday();
        $str .= " " . $this->get_day_part();
        $str .= " " . $this->get_type();
        $str .= " ({$this->disease->name})";

        return $str;
    }

    public function getBegin_hour_str_Str () {
        $begin_hour_str_Arr = json_decode($this->begin_hour_str, true);

        if (isset($begin_hour_str_Arr['begin']) || isset($begin_hour_str_Arr['end'])) { 
            $result = isset($begin_hour_str_Arr['begin']) ? $begin_hour_str_Arr['begin'] : '';
            if (empty($result)) {
                $result = isset($begin_hour_str_Arr['end']) ? $begin_hour_str_Arr['end'] : ''; 
            }else {
                $result.= isset($begin_hour_str_Arr['end']) && $begin_hour_str_Arr['end'] != '' ? '-'. $begin_hour_str_Arr['end'] : '';
            }
            return $result;
        }

        $begin_hour_str_Arr[0] = $begin_hour_str_Arr[0] ? $begin_hour_str_Arr[0] : '';
        $begin_hour_str_Arr[1] = $begin_hour_str_Arr[1] ? $begin_hour_str_Arr[1] : '';

        if ($begin_hour_str_Arr[0] && $begin_hour_str_Arr[1]) {
            return $begin_hour_str_Arr[0] . "-" . $begin_hour_str_Arr[1];
        } else {
            $str = $begin_hour_str_Arr[0] == '' ? $begin_hour_str_Arr[1] : $begin_hour_str_Arr[0];

            return $str;
        }

        return '';
    }

    // 获取简单格式 ：每周周一上午 普通门诊
    public function toMoreStr () {
        $str = $this->get_hz();

        if ('temp' == $this->op_hz) {
            $str .= "({$this->op_date})";
        }

        $str .= " ";
        $str .= $this->get_wday();
        $str .= " ";
        $str .= $this->get_day_part();
        $str .= "<br>";
        $str .= " " . $this->get_type() . "门诊";
        $str .= " ({$this->disease->name})";

        $str .= " " . $this->getScheduleAddressStr();

        return $str;
    }

    // 临时出诊显示日期,否则为空
    public function getTheDateStr () {
        $str = '';

        if ('temp' == $this->op_hz) {
            $str .= "({$this->op_date})";
        }

        return $str;
    }

    // get_hz
    public function get_hz () {
        return $this->get_op_hz();
    }

    // get_op_hz
    public function get_op_hz () {
        return self::get_op_hzImp($this->op_hz);
    }

    // get_wday
    public function get_wday () {
        return self::get_wdayImp($this->wday);
    }

    // get_day_part
    public function get_day_part () {
        return self::get_day_partImp($this->day_part);
    }

    // get_op_type
    public function get_op_type () {
        return self::get_op_typeImp($this->op_type);
    }

    // get_type
    public function get_type () {
        return $this->get_op_type();
    }

    // 出诊表实例数目
    public function getScheduleCnt () {
        return ScheduleDao::getCntOfScheduleTpl($this);
    }

    // 出诊表实例数目
    public function getScheduleCntGtToday () {
        return ScheduleDao::getCntOfScheduleTplGtToday($this);
    }

    // 出诊表实例,最小
    public function getMinSchedule () {
        return ScheduleDao::getMinOneByScheduleTpl($this);
    }

    // 出诊表实例,最大
    public function getMaxSchedule () {
        return ScheduleDao::getMaxOneByScheduleTpl($this);
    }

    // 获取加号单数目
    public function getRevisitTktCnt () {
        return RevisitTktDao::getCntOfScheduleTpl($this->id);
    }

    public function getRevisitTktCntGtToday () {
        return RevisitTktDao::getCntOfScheduleTplGtToday($this->id);
    }

    // 获取门诊地址
    public function getScheduleAddressStr () {
        $four = [110000, 120000, 310000, 500000];
        if (in_array($this->xprovinceid, $four)) {
            $xprovince_name = $this->xprovince->name;
            $xcity_name = "";
        } else {
            $xprovince_name = $this->xprovince->name;
            $xcity_name = $this->xcity->name;
        }
        $xcounty_name = $this->xcounty->name;
        $content = $this->content;

        return "{$xprovince_name}{$xcity_name}{$xcounty_name}{$content}";
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function get_op_hzArray () {
        $arr = array(
            'weekly' => '每周',
            'interval' => '隔周',
            'temp' => '临时');

        return $arr;
    }

    public static function get_wdayArray () {
        $arr = array(
            '周日',
            '周一',
            '周二',
            '周三',
            '周四',
            '周五',
            '周六',
            '周日');
        return $arr;
    }

    public static function get_day_partArray () {
        $arr = array(
            'am' => '上午',
            'pm' => '下午',
            'all_day' => '全天',
            'night' => '晚上');
        return $arr;
    }

    public static function get_op_typeArray () {
        $arr = array(
            'normal' => '普通',
            'expert' => '专家',
            'special' => '特需');
        return $arr;
    }

    // get_hzImp
    public static function get_hzImp ($op_hz) {
        return self::get_op_hzImp($op_hz);
    }

    // get_op_hzImp
    public static function get_op_hzImp ($op_hz) {
        $arr = self::get_op_hzArray();
        return $arr[$op_hz];
    }

    // get_wdayImp
    public static function get_wdayImp ($wday) {
        $arr = self::get_wdayArray();
        return $arr[$wday];
    }

    // get_day_partImp
    public static function get_day_partImp ($day_part) {
        $arr = self::get_day_partArray();
        return $arr[$day_part];
    }

    // get_typeImp
    public static function get_typeImp ($op_type) {
        return self::get_op_typeImp($op_type);
    }

    // get_op_typeImp
    public static function get_op_typeImp ($op_type) {
        $arr = self::get_op_typeArray();
        return $arr[$op_type];
    }
}
