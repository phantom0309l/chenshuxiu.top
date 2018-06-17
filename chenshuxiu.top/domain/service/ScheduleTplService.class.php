<?php

/*
 * ScheduleTplService 医生出诊表
 */
class ScheduleTplService
{

    // 给医生显示日历
    public static function getTableForDoctor (array $scheduletpls) {
        $table = array();
        for ($i = 1; $i < 8; $i ++) {
            $arr = array();
            $arr['weekday'] = ScheduleTpl::get_wdayImp($i);
            $arr['am'] = "";
            $arr['pm'] = "";
            $arr['night'] = "";
            $table[$i] = $arr;
        }

        foreach ($scheduletpls as $a) {

            if (false == $a->isOnline()) {
                continue;
            }

            $row = self::scheduletplToTableRow($a);

            $i = $a->wday;

            foreach ($row as $k => $v) {
                $table[$i][$k] = $v; // 可能会覆盖
            }
        }

        return $table;
    }

    // toSimpleStr
    private static function scheduletplToTableRow (ScheduleTpl $scheduletpl) {
        $row = array();

        switch ($scheduletpl->day_part) {
            case 'am':
            case 'pm':
            case 'night':
                $row[$scheduletpl->day_part] = $scheduletpl->toSimpleStr();
                break;
            case 'all_day':
                $row['am'] = $scheduletpl->toSimpleStr();
                $row['pm'] = $scheduletpl->toSimpleStr();
                break;
        }

        return $row;
    }

    // 根据 scheduletpl 生成 数据数组, 用于生成 schedule 实体
    public static function getDateArray (ScheduleTpl $scheduletpl, $weekCnt = 100, $date = '') {

        // from date 的 weekCnt * 7 day
        $loop_date = self::get_loop_date($weekCnt * 7, $date);

        // 逐日匹配是否负责scheduletpl
        $arr = array();
        foreach ($loop_date as $the_datetime) {

            // 有效 且 未过期
            if (false == $scheduletpl->isOnline()) {
                continue;
            }

            // 过滤假期
            if (true == self::inHoliday($the_datetime)) {
                continue;
            }

            $isMatch = false;
            switch ($scheduletpl->op_hz) {
                case 'weekly':
                    $isMatch = self::weeklyHelperOne($scheduletpl, $the_datetime);
                    break;
                case 'interval':
                    $isMatch = self::intervalHelperOne($scheduletpl, $the_datetime);
                    break;
                case 'temp':
                    $isMatch = self::tempHelperOne($scheduletpl, $the_datetime);
                    break;
                default:
                    break;
            }

            // 日期匹配上模板
            if ($isMatch) {
                $arr[] = date('Y-m-d', $the_datetime);
            }
        }

        return $arr;
    }

    // from date 的 weekCnt * 7 day
    private static function get_loop_date ($daycnt = 14, $date = '') {
        $loop_date = array();

        $base = XDateTime::getTheMondayBeginTime($date);
        for ($i = 0; $i < $daycnt; $i ++) {
            $loop_date[] = $base + $i * 3600 * 24;
        }
        return $loop_date;
    }

    // 过滤假期
    private static function inHoliday ($time) {
        $arr = array(
            "2016-02-08",
            "2016-02-09",
            "2016-02-10",
            "2016-02-11",
            "2016-02-12",
            "2016-02-13",
            "2016-02-14");

        $in = false;
        foreach ($arr as $a) {
            if (strtotime($a) == $time) {
                $in = true;
                break;
            }
        }
        return $in;
    }

    // 日期是否匹配每周出诊
    private static function weeklyHelperOne (ScheduleTpl $scheduletpl, $the_datetime) {
        // 星期几
        $w = date("w", $the_datetime);

        if ($w == 0) {
            $w = 7;
        }

        if ($scheduletpl->wday == $w) {
            return true;
        }

        return false;
    }

    // 日期是否匹配隔周出诊
    private static function intervalHelperOne (ScheduleTpl $scheduletpl, $the_datetime) {
        $op_datetime = strtotime($scheduletpl->op_date);
        $diff_day = self::diff_day($the_datetime, $op_datetime);

        // 两周取余
        if ($diff_day % 14 == 0) {
            return true;
        }

        return false;
    }

    // 日期是否匹配临时出诊(精准匹配)
    private static function tempHelperOne (ScheduleTpl $scheduletpl, $the_datetime) {
        $op_datetime = strtotime($scheduletpl->op_date);
        $diff_day = self::diff_day($the_datetime, $op_datetime);

        // 精准匹配
        if ($diff_day == 0) {
            return true;
        }

        return false;
    }

    private static function diff_day ($time1, $time2) {
        return ($time1 - $time2) / (3600 * 24);
    }
}
