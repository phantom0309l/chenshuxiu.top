<?php

/*
 * Schedule
 */
class Schedule extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'doctorid',  // doctorid
            'diseaseid',  // diseaseid
            'scheduletplid',
            'thedate',  //  出诊日期
            'daypart',  //  上下午，冗余
            'dow',  // 周几
            'tkttype',  // 对应 op_type; 为: normal, expert, special
            'maxcnt',  // 最多预约数目
            'status'); // 状态
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'doctorid',
            'scheduletplid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid");
        $this->_belongtos["disease"] = array(
            "type" => "Disease",
            "key" => "diseaseid");
        $this->_belongtos["scheduletpl"] = array(
            "type" => "ScheduleTpl",
            "key" => "scheduletplid");
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["diseaseid"] = $diseaseid;
    // $row["scheduletplid"] = $scheduletplid;
    // $row["thedate"] = $thedate;
    // $row["daypart"] = $daypart;
    // $row["dow"] = $dow;
    // $row["tkttype"] = $tkttype;
    // $row["maxcnt"] = $maxcnt;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Schedule::createByBiz row cannot empty");

        $entity = Schedule::getByScheduleTplIdAndThedate($row["scheduletplid"], $row["thedate"]);
        if ($entity instanceof Schedule) {
            return $entity;
        }

        $default = array();
        $default["doctorid"] = 0;
        $default["diseaseid"] = 0;
        $default["scheduletplid"] = 0;
        $default["thedate"] = '0000-00-00';
        $default["daypart"] = '';
        $default["dow"] = 0;
        $default["tkttype"] = '';
        $default["maxcnt"] = 0;
        $default["status"] = 1;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getDayOfWeek () {
        $datearr = explode("-", $this->thedate);
        // 将传来的时间使用“-”分割成数组
        $year = $datearr[0];
        // //获取年份
        $month = sprintf('%02d', $datearr[1]);
        // //获取月份
        $day = sprintf('%02d', $datearr[2]);
        // //获取日期
        $hour = $minute = $second = 0;
        // //默认时分秒均为0
        $dayofweek = mktime($hour, $minute, $second, $month, $day, $year);
        // //将时间转换成时间戳
        return date("w", $dayofweek); // 获取星期值
    }

    public function getDay_partValue () {
        if ($this->scheduletpl instanceof ScheduleTpl) {
            return $this->scheduletpl->day_part;
        } elseif ($this->daypart) {
            return $this->daypart;
        } else {
            return '';
        }
    }

    public function getDaypartStr () {
        $scheduletpl = $this->scheduletpl;
        $arr = self::getDaypartArray();
        if ($scheduletpl instanceof ScheduleTpl) {
            return $arr[$scheduletpl->day_part] . $scheduletpl->getBegin_hour_str_Str();
        } elseif ($this->daypart) {
            return $arr[$this->daypart];
        } else {
            return '';
        }
    }

    public function getDowStr () {
        $arr = self::getDowArray();
        return $arr[$this->dow];
    }

    public function getTkttypeStr () {
        $arr = self::getTkttypeArray();
        if ($this->scheduletpl instanceof ScheduleTpl) {
            return $arr[$this->scheduletpl->op_type];
        } elseif ($this->tkttype) {
            return $arr[$this->tkttype];
        } else {
            return '';
        }
    }

    public function getDescStr4Revisittkt () {
        $str = "";
        $str .= "{$this->thedate}" . " ";
        $str .= $this->getDowStr() . " ";
        $str .= $this->getDaypartStr() . " ";
        $str .= $this->getTkttypeStr() . " ";

        return $str;
    }

    // 加号单数目
    public function getRevisitTktCnt ($status = 'all', $isclosed = 'all') {
        return RevisitTktDao::getCntOfSchedule($this, $status, $isclosed);
    }

    public function getIsHaveCnt () {
        $cnt = $this->maxcnt - $this->getIdleCnt();
        if ($cnt < 0) {
            $cnt = 0;
        }
        return $cnt;
    }

    public function getCntColor () {
        $cnt = RevisitTktDao::getCntByScheduleidDoctorid($this->id, $this->doctorid);
        $maxcnt = $this->maxcnt;
        $maxcntdesc = $maxcnt;
        if ($maxcnt < 1) {
            $maxcntdesc = '不限';
            $bgtype = 'green';
        } else {
            $rate = (double) ($cnt / $maxcnt);
            if ($cnt < 1) {
                $bgtype = 'green';
            } elseif ($rate < 0.5) {
                $bgtype = 'blue';
            } else {
                $bgtype = 'orange';
            }
        }

        return $bgtype;
    }

    public function getTkttypeColor () {
        $tkttyptcolor = '';

        if ($this->tkttype == 'normal') {
            $tkttyptcolor = '';
        } elseif ($this->tkttype == 'expert') {
            $tkttyptcolor = '#1996ea';
        } elseif ($this->tkttype == 'special') {
            $tkttyptcolor = '#ff6666';
        }

        return $tkttyptcolor;
    }

    public function getIdleCnt () {
        $cnt = RevisitTktDao::getCntByScheduleidDoctorid($this->id, $this->doctorid);
        $maxcnt = $this->maxcnt;

        if ($maxcnt > 0) {
            return $maxcnt - $cnt;
        }

        return 10000;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getDaypartArray () {
        $arr = array(
            'am' => '上午 ',
            'pm' => '下午 ',
            'all_day' => '全天',
            'night' => '晚上');
        return $arr;
    }

    public static function getDowArray () {
        $arr = array(
            0 => '周日',
            1 => '周一',
            2 => '周二',
            3 => '周三',
            4 => '周四',
            5 => '周五',
            6 => '周六',
            7 => '周日');
        return $arr;
    }

    public static function getTkttypeArray () {
        $arr = array(
            'normal' => '普通',
            'expert' => '专家',
            'special' => '特需');
        return $arr;
    }

    // 2元式
    public static function getByScheduleTplIdAndThedate ($scheduletplId, $thedate) {
        $cond = ' AND scheduletplid = :scheduletplid AND thedate = :thedate ';
        $bind = [];
        $bind[':scheduletplid'] = $scheduletplId;
        $bind[':thedate'] = $thedate;

        return Dao::getEntityByCond('Schedule', $cond, $bind);
    }

    // 批量创建
    public static function batCreateByScheduleTpl (ScheduleTpl $scheduletpl, $endday = '2020-12-31', $beginday = '') {
        if (empty($beginday)) {
            $beginday = date('Y-m-d');
        }

        $w1 = XDateTime::getWFromFirstDate($beginday);
        $w2 = XDateTime::getWFromFirstDate($endday);
        $wcnt = $w2 - $w1;

        $arr = ScheduleTplService::getDateArray($scheduletpl, $wcnt, $beginday);

        $schedules = [];
        foreach ($arr as $date) {
            $row = array();
            $row["doctorid"] = $scheduletpl->doctorid;
            $row["diseaseid"] = $scheduletpl->diseaseid;
            $row["scheduletplid"] = $scheduletpl->id;
            $row["thedate"] = $date;
            $row["daypart"] = $scheduletpl->day_part;
            $row["dow"] = $scheduletpl->wday;
            $row["tkttype"] = $scheduletpl->op_type;
            $row["maxcnt"] = $scheduletpl->maxcnt;
            $schedules[] = Schedule::createByBiz($row);
        }

        return $schedules;
    }
}
